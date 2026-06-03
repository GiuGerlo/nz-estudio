#!/usr/bin/env bash
#
# Deploy script para nz-estudio.
# Llamado por .github/workflows/deploy.yml (auto) y redeploy.yml (manual).
#
# Variables de entorno requeridas:
#   SSH_HOST, SSH_PORT, SSH_USER, DEPLOY_PATH
#   SSH agent ya cargado con la key (handled por el workflow).
#
# Flags:
#   --mode=sequential|bulk   (default: sequential)
#   --from-sha=<sha|INITIAL> (opcional; si no, se lee del server)
#   --to-sha=<sha>           (default: HEAD)
#   --update-sha=true|false  (default: true)

set -euo pipefail

# ──────────────────────────────────────────────────────────────────────
# Parseo de flags
# ──────────────────────────────────────────────────────────────────────
MODE="sequential"
FROM_SHA=""
TO_SHA="HEAD"
UPDATE_SHA="true"

for arg in "$@"; do
    case "$arg" in
        --mode=*)       MODE="${arg#*=}" ;;
        --from-sha=*)   FROM_SHA="${arg#*=}" ;;
        --to-sha=*)     TO_SHA="${arg#*=}" ;;
        --update-sha=*) UPDATE_SHA="${arg#*=}" ;;
        *) echo "Flag desconocido: $arg" >&2; exit 1 ;;
    esac
done

: "${SSH_HOST:?SSH_HOST no definido}"
: "${SSH_PORT:?SSH_PORT no definido}"
: "${SSH_USER:?SSH_USER no definido}"
: "${DEPLOY_PATH:?DEPLOY_PATH no definido}"

DEPLOYIGNORE="${DEPLOYIGNORE:-.deployignore}"
SUMMARY="${GITHUB_STEP_SUMMARY:-/dev/stdout}"
BACKUP_DIR_REMOTE="$DEPLOY_PATH/../backups"
SHA_FILE_REMOTE="$DEPLOY_PATH/.deployed_sha"
START_TS=$(date +%s)

# Contadores globales para el resumen final
TOTAL_ADDED=0
TOTAL_MODIFIED=0
TOTAL_DELETED=0
TOTAL_BACKUPS=0
TOTAL_COMMITS_OK=0

# Resolver TO_SHA si vino como referencia simbólica
TO_SHA=$(git rev-parse "$TO_SHA")

# ──────────────────────────────────────────────────────────────────────
# ETAPA 1/5: Configuración (visible en console log)
# ──────────────────────────────────────────────────────────────────────
echo "═══════════════════════════════════════════════════════════════"
echo "🚀 DEPLOY A PRODUCCIÓN — nz-estudio"
echo "═══════════════════════════════════════════════════════════════"
echo ""
echo "📋 ETAPA 1/5: Configuración"
echo "   ├─ 📍 Servidor:    $SSH_USER@$SSH_HOST:$SSH_PORT"
echo "   ├─ 📁 Path:        $DEPLOY_PATH"
echo "   ├─ 💾 Backups dir: $BACKUP_DIR_REMOTE"
echo "   ├─ ⚙️  Modo:        $MODE"
echo "   ├─ 🎯 SHA destino: ${TO_SHA:0:7}"
echo "   └─ 🔄 Update SHA:  $UPDATE_SHA"
echo ""

# ──────────────────────────────────────────────────────────────────────
# Helpers SSH/SCP
# ──────────────────────────────────────────────────────────────────────
SSH_OPTS=(-p "$SSH_PORT" -o StrictHostKeyChecking=accept-new -o BatchMode=yes)

ssh_run() {
    ssh "${SSH_OPTS[@]}" "$SSH_USER@$SSH_HOST" "$@"
}

rsync_files() {
    # $1 = archivo con lista de paths relativos al repo
    local list_file="$1"
    if [ ! -s "$list_file" ]; then return 0; fi
    rsync -az --files-from="$list_file" \
          -e "ssh ${SSH_OPTS[*]}" \
          ./ "$SSH_USER@$SSH_HOST:$DEPLOY_PATH/"
}

ssh_rm_files() {
    local list_file="$1"
    if [ ! -s "$list_file" ]; then return 0; fi
    # Construye comando rm con paths citados
    local cmd="cd '$DEPLOY_PATH' && xargs -d '\n' rm -f --"
    < "$list_file" ssh_run "$cmd"
}

# ──────────────────────────────────────────────────────────────────────
# Filtro contra .deployignore
# ──────────────────────────────────────────────────────────────────────
EXCLUDE_PATTERNS=()
if [ -f "$DEPLOYIGNORE" ]; then
    while IFS= read -r line || [ -n "$line" ]; do
        line="${line%$'\r'}"             # CRLF safety
        [[ -z "$line" || "$line" == \#* ]] && continue
        EXCLUDE_PATTERNS+=("$line")
    done < "$DEPLOYIGNORE"
fi

is_excluded() {
    local file="$1"
    local pattern base
    for pattern in "${EXCLUDE_PATTERNS[@]}"; do
        if [[ "$pattern" == */ ]]; then
            # Patrón directorio: file está dentro de pattern
            local p="${pattern%/}"
            [[ "$file" == "$p" || "$file" == "$p"/* ]] && return 0
        elif [[ "$pattern" == */* ]]; then
            # Patrón con ruta: glob completo
            # shellcheck disable=SC2053
            [[ "$file" == $pattern ]] && return 0
        else
            # Patrón simple: comparar contra basename o nombre completo
            base="${file##*/}"
            # shellcheck disable=SC2053
            [[ "$base" == $pattern || "$file" == $pattern ]] && return 0
        fi
    done
    return 1
}

filter_list() {
    # stdin: paths uno por línea. stdout: paths NO excluidos.
    while IFS= read -r f; do
        [ -z "$f" ] && continue
        is_excluded "$f" || echo "$f"
    done
}

# ──────────────────────────────────────────────────────────────────────
# Backup remoto
# ──────────────────────────────────────────────────────────────────────
do_backup() {
    local label="$1"   # ej: "INITIAL" o sha corto
    local ts; ts=$(date +%Y%m%d_%H%M%S)
    local backup_name="${ts}_${label}.tar.gz"
    echo "   ├─ 💾 Backup: $backup_name"
    if ssh_run "mkdir -p '$BACKUP_DIR_REMOTE' && \
             tar --exclude='uploads' --exclude='backups' --exclude='_backups' \
                 -czf '$BACKUP_DIR_REMOTE/$backup_name' \
                 -C '$DEPLOY_PATH' . 2>/dev/null && \
             ls -1t '$BACKUP_DIR_REMOTE'/*.tar.gz | tail -n +6 | xargs -r rm --"; then
        LAST_BACKUP="$backup_name"
        TOTAL_BACKUPS=$((TOTAL_BACKUPS + 1))
    else
        echo "   ⚠️  Backup falló (continúa el deploy igual)"
        LAST_BACKUP="(falló)"
    fi
}

# ──────────────────────────────────────────────────────────────────────
# Reporte de un commit al $GITHUB_STEP_SUMMARY
# ──────────────────────────────────────────────────────────────────────
write_commit_report() {
    local sha="$1" added_file="$2" modified_file="$3" deleted_file="$4"
    local commit_num="$5" commit_total="$6"
    local sha7="${sha:0:7}"
    local subject; subject=$(git log -1 --format=%s "$sha")
    local author; author=$(git log -1 --format='%an' "$sha")
    local n_a n_m n_d total
    n_a=$(wc -l < "$added_file" | tr -d ' ')
    n_m=$(wc -l < "$modified_file" | tr -d ' ')
    n_d=$(wc -l < "$deleted_file" | tr -d ' ')
    total=$(( n_a + n_m + n_d ))

    # Console output (legible en log de Actions)
    echo "   ├─ 📊 Cambios: $n_a nuevos · $n_m modificados · $n_d eliminados ($total total)"
    echo "   ├─ ⬆️  Subiendo cambios al servidor..."

    # Markdown summary (persistente en UI de Actions)
    {
        echo ""
        echo "---"
        echo ""
        echo "### 📌 Commit $commit_num/$commit_total · \`$sha7\` — $subject"
        echo ""
        echo "**Autor:** $author"
        echo ""
        echo "| Tipo | Cantidad |"
        echo "|---|---:|"
        echo "| ➕ Nuevos | $n_a |"
        echo "| ✏️ Modificados | $n_m |"
        echo "| 🗑️ Eliminados | $n_d |"
        echo "| **📦 Total** | **$total** |"
        echo ""
        echo "**💾 Backup:** \`$LAST_BACKUP\`"
        echo ""

        if [ "$total" -gt 0 ]; then
            echo "<details><summary>📂 Ver lista de archivos</summary>"
            echo ""
            if [ "$n_a" -gt 0 ]; then
                echo "**➕ Nuevos ($n_a):**"
                echo ""
                awk '{printf "%d. `%s`\n", NR, $0}' "$added_file"
                echo ""
            fi
            if [ "$n_m" -gt 0 ]; then
                echo "**✏️ Modificados ($n_m):**"
                echo ""
                awk '{printf "%d. `%s`\n", NR, $0}' "$modified_file"
                echo ""
            fi
            if [ "$n_d" -gt 0 ]; then
                echo "**🗑️ Eliminados ($n_d):**"
                echo ""
                awk '{printf "%d. `%s`\n", NR, $0}' "$deleted_file"
                echo ""
            fi
            echo "</details>"
        fi
    } >> "$SUMMARY"

    # Actualizar contadores globales
    TOTAL_ADDED=$((TOTAL_ADDED + n_a))
    TOTAL_MODIFIED=$((TOTAL_MODIFIED + n_m))
    TOTAL_DELETED=$((TOTAL_DELETED + n_d))
}

write_header() {
    {
        echo "# 🚀 Deploy a producción — nz-estudio"
        echo ""
        echo "| Campo | Valor |"
        echo "|---|---|"
        echo "| **🖥️ Servidor** | \`$SSH_HOST:$SSH_PORT\` |"
        echo "| **📁 Path** | \`$DEPLOY_PATH\` |"
        echo "| **⚙️ Modo** | \`$MODE\` |"
        echo "| **🎯 SHA destino** | \`${TO_SHA:0:7}\` |"
        echo "| **🔄 Update SHA** | \`$UPDATE_SHA\` |"
    } >> "$SUMMARY"
}

write_final_summary() {
    local end_ts; end_ts=$(date +%s)
    local duration=$((end_ts - START_TS))
    local total_files=$((TOTAL_ADDED + TOTAL_MODIFIED + TOTAL_DELETED))

    echo ""
    echo "═══════════════════════════════════════════════════════════════"
    echo "📋 ETAPA 5/5: Resumen final"
    echo "═══════════════════════════════════════════════════════════════"
    echo "   ├─ ✅ Commits aplicados:   $TOTAL_COMMITS_OK"
    echo "   ├─ 📦 Archivos totales:    $total_files"
    echo "   │   ├─ ➕ Nuevos:           $TOTAL_ADDED"
    echo "   │   ├─ ✏️  Modificados:     $TOTAL_MODIFIED"
    echo "   │   └─ 🗑️  Eliminados:      $TOTAL_DELETED"
    echo "   ├─ 💾 Backups creados:     $TOTAL_BACKUPS"
    echo "   └─ ⏱️  Duración total:      ${duration}s"
    echo "═══════════════════════════════════════════════════════════════"

    {
        echo ""
        echo "---"
        echo ""
        echo "## ✅ Resumen final"
        echo ""
        echo "| Métrica | Valor |"
        echo "|---|---:|"
        echo "| 🟢 Commits aplicados | $TOTAL_COMMITS_OK |"
        echo "| 📦 Archivos totales | **$total_files** |"
        echo "| ➕ Nuevos | $TOTAL_ADDED |"
        echo "| ✏️ Modificados | $TOTAL_MODIFIED |"
        echo "| 🗑️ Eliminados | $TOTAL_DELETED |"
        echo "| 💾 Backups creados | $TOTAL_BACKUPS |"
        echo "| ⏱️ Duración | ${duration}s |"
    } >> "$SUMMARY"
}

# ──────────────────────────────────────────────────────────────────────
# Lectura del SHA viejo
# ──────────────────────────────────────────────────────────────────────
read_remote_sha() {
    ssh_run "cat '$SHA_FILE_REMOTE' 2>/dev/null || echo INITIAL"
}

write_remote_sha() {
    local sha="$1"
    if [ "$UPDATE_SHA" != "true" ]; then
        echo "⏭️  update_sha=false → no se actualiza .deployed_sha"
        return
    fi
    echo "$sha" | ssh_run "cat > '$SHA_FILE_REMOTE'"
}

# ──────────────────────────────────────────────────────────────────────
# Deploy de un commit (parte sequential y bulk)
# ──────────────────────────────────────────────────────────────────────
deploy_range() {
    local prev_sha="$1" target_sha="$2" label="$3"
    local commit_num="${4:-1}" commit_total="${5:-1}"

    do_backup "$label"

    local tmp; tmp=$(mktemp -d)
    local added="$tmp/added.txt" modified="$tmp/modified.txt" deleted="$tmp/deleted.txt"

    # name-status outputs: A\tpath, M\tpath, D\tpath, R<num>\told\tnew (renames = D + A)
    git diff --name-status "$prev_sha" "$target_sha" | while IFS=$'\t' read -r status p1 p2; do
        case "$status" in
            A) echo "$p1" >> "$added" ;;
            M|T) echo "$p1" >> "$modified" ;;
            D) echo "$p1" >> "$deleted" ;;
            R*) echo "$p1" >> "$deleted"; echo "$p2" >> "$added" ;;
            C*) echo "$p2" >> "$added" ;;
        esac
    done

    : > "$added.f"; : > "$modified.f"; : > "$deleted.f"
    [ -f "$added" ]    && filter_list < "$added"    > "$added.f"
    [ -f "$modified" ] && filter_list < "$modified" > "$modified.f"
    [ -f "$deleted" ]  && filter_list < "$deleted"  > "$deleted.f"

    local total
    total=$(( $(wc -l < "$added.f") + $(wc -l < "$modified.f") + $(wc -l < "$deleted.f") ))

    if [ "$total" -eq 0 ]; then
        echo "   └─ ⏭️  Skipped: sólo cambios en archivos excluidos"
        {
            echo ""
            echo "---"
            echo ""
            echo "### ⏭️ Commit $commit_num/$commit_total · \`${target_sha:0:7}\` — _skipped_"
            echo ""
            echo "Sólo cambios en archivos excluidos por \`.deployignore\`."
        } >> "$SUMMARY"
        rm -rf "$tmp"
        return 0
    fi

    # Hay que checkoutear el commit target para que rsync envíe el contenido correcto
    git checkout --quiet "$target_sha"

    # Combinar added + modified para rsync
    cat "$added.f" "$modified.f" > "$tmp/upload.txt"
    rsync_files "$tmp/upload.txt"
    ssh_rm_files "$deleted.f"

    write_commit_report "$target_sha" "$added.f" "$modified.f" "$deleted.f" "$commit_num" "$commit_total"
    echo "   └─ ✅ Commit ${target_sha:0:7} deployado OK"

    rm -rf "$tmp"
}

deploy_full_initial() {
    echo "   ├─ 📦 Modo INITIAL: subiendo TODO el repo (primera vez)"
    do_backup "INITIAL"

    local tmp; tmp=$(mktemp -d)
    git ls-files | filter_list > "$tmp/upload.txt"
    local n; n=$(wc -l < "$tmp/upload.txt" | tr -d ' ')
    echo "   ├─ 📊 Archivos a subir (filtrados por .deployignore): $n"
    echo "   ├─ ⬆️  Subiendo..."

    rsync_files "$tmp/upload.txt"
    TOTAL_ADDED=$n
    TOTAL_COMMITS_OK=1

    {
        echo ""
        echo "---"
        echo ""
        echo "### 🆕 Deploy inicial (INITIAL)"
        echo ""
        echo "Primera vez que se deploya. Se subió **todo el repositorio** filtrado por \`.deployignore\`."
        echo ""
        echo "| Métrica | Valor |"
        echo "|---|---:|"
        echo "| 📦 Archivos subidos | **$n** |"
        echo "| 💾 Backup | \`$LAST_BACKUP\` |"
        echo ""
        echo "<details><summary>📂 Ver lista completa de $n archivos</summary>"
        echo ""
        awk '{printf "%d. `%s`\n", NR, $0}' "$tmp/upload.txt"
        echo ""
        echo "</details>"
    } >> "$SUMMARY"

    echo "   └─ ✅ Deploy inicial completado: $n archivos"
    rm -rf "$tmp"
}

# ──────────────────────────────────────────────────────────────────────
# Main
# ──────────────────────────────────────────────────────────────────────
write_header

# ETAPA 2: lectura del estado remoto
echo "📋 ETAPA 2/5: Lectura del estado del servidor"
if [ -z "$FROM_SHA" ]; then
    FROM_SHA=$(read_remote_sha)
    echo "   ├─ 🔎 SHA encontrado en server: $FROM_SHA"
else
    echo "   ├─ 🔎 FROM_SHA manual: $FROM_SHA"
fi
echo "   └─ 🎯 SHA destino: ${TO_SHA:0:7}"
echo ""

# Caso 1: primer deploy (INITIAL)
if [ "$FROM_SHA" = "INITIAL" ] || [ -z "$FROM_SHA" ]; then
    echo "📋 ETAPA 3/5: Deploy inicial (INITIAL)"
    deploy_full_initial
    echo ""
    echo "📋 ETAPA 4/5: Actualizar .deployed_sha en server"
    write_remote_sha "$TO_SHA"
    write_final_summary
    exit 0
fi

# Validar que FROM_SHA existe en historial local
if ! git cat-file -e "$FROM_SHA^{commit}" 2>/dev/null; then
    echo "   ⚠️  FROM_SHA '$FROM_SHA' no existe en el historial local. Fallback a INITIAL."
    echo ""
    echo "📋 ETAPA 3/5: Deploy inicial (fallback)"
    deploy_full_initial
    echo ""
    echo "📋 ETAPA 4/5: Actualizar .deployed_sha en server"
    write_remote_sha "$TO_SHA"
    write_final_summary
    exit 0
fi

# Caso 2: bulk
if [ "$MODE" = "bulk" ]; then
    echo "📋 ETAPA 3/5: Deploy en modo BULK (diff único)"
    echo "   ├─ 📦 Rango: ${FROM_SHA:0:7} → ${TO_SHA:0:7}"
    deploy_range "$FROM_SHA" "$TO_SHA" "${TO_SHA:0:7}" 1 1
    TOTAL_COMMITS_OK=1
    echo ""
    echo "📋 ETAPA 4/5: Actualizar .deployed_sha en server"
    write_remote_sha "$TO_SHA"
    write_final_summary
    exit 0
fi

# Caso 3: sequential (default)
COMMITS=$(git rev-list --reverse "$FROM_SHA..$TO_SHA")
if [ -z "$COMMITS" ]; then
    echo "📋 ETAPA 3/5: Análisis de cambios"
    echo "   └─ ℹ️  No hay commits nuevos. El server ya está al día."
    {
        echo ""
        echo "---"
        echo ""
        echo "### ℹ️ Sin cambios para deployar"
        echo ""
        echo "El servidor ya está al día con el último commit (\`${TO_SHA:0:7}\`)."
    } >> "$SUMMARY"
    write_final_summary
    exit 0
fi

TOTAL_COMMITS=$(echo "$COMMITS" | wc -l | tr -d ' ')
echo "📋 ETAPA 3/5: Deploy commit-por-commit ($TOTAL_COMMITS commits)"
echo ""

PREV="$FROM_SHA"
LAST_OK="$FROM_SHA"
COUNT=0

for C in $COMMITS; do
    COUNT=$((COUNT + 1))
    SUBJECT=$(git log -1 --format=%s "$C")
    echo "━━━ Commit $COUNT/$TOTAL_COMMITS · ${C:0:7} ━━━"
    echo "   ├─ 📝 Mensaje: \"$SUBJECT\""

    if deploy_range "$PREV" "$C" "${C:0:7}" "$COUNT" "$TOTAL_COMMITS"; then
        write_remote_sha "$C"
        LAST_OK="$C"
        PREV="$C"
        TOTAL_COMMITS_OK=$((TOTAL_COMMITS_OK + 1))
        echo ""
    else
        echo "   └─ ❌ FALLÓ. Fail-fast activado: los siguientes commits NO se intentan."
        {
            echo ""
            echo "---"
            echo ""
            echo "## ❌ Deploy abortado en commit \`${C:0:7}\`"
            echo ""
            echo "**Último SHA OK en server:** \`${LAST_OK:0:7}\`"
            echo ""
            echo "El próximo push (o redeploy manual) retomará desde el último SHA OK y deployará los commits faltantes."
        } >> "$SUMMARY"
        write_final_summary
        exit 1
    fi
done

echo "📋 ETAPA 4/5: Verificación final"
echo "   └─ ✅ Todos los commits aplicados correctamente"
write_final_summary

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

# Resolver TO_SHA si vino como referencia simbólica
TO_SHA=$(git rev-parse "$TO_SHA")

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
    echo "💾 Backup: $backup_name"
    ssh_run "mkdir -p '$BACKUP_DIR_REMOTE' && \
             tar --exclude='uploads' --exclude='backups' --exclude='_backups' \
                 -czf '$BACKUP_DIR_REMOTE/$backup_name' \
                 -C '$DEPLOY_PATH' . 2>/dev/null && \
             ls -1t '$BACKUP_DIR_REMOTE'/*.tar.gz | tail -n +6 | xargs -r rm --"
    LAST_BACKUP="$backup_name"
}

# ──────────────────────────────────────────────────────────────────────
# Reporte de un commit al $GITHUB_STEP_SUMMARY
# ──────────────────────────────────────────────────────────────────────
write_commit_report() {
    local sha="$1" added_file="$2" modified_file="$3" deleted_file="$4"
    local sha7="${sha:0:7}"
    local subject; subject=$(git log -1 --format=%s "$sha")
    local n_a n_m n_d total
    n_a=$(wc -l < "$added_file" | tr -d ' ')
    n_m=$(wc -l < "$modified_file" | tr -d ' ')
    n_d=$(wc -l < "$deleted_file" | tr -d ' ')
    total=$(( n_a + n_m + n_d ))

    {
        echo ""
        echo "### Commit \`$sha7\` — $subject"
        echo ""
        echo "**📊 $total archivos: $n_a nuevos · $n_m modificados · $n_d eliminados**"

        if [ "$n_a" -gt 0 ]; then
            echo ""
            echo "#### ➕ Nuevos ($n_a)"
            sed 's/^/- `/; s/$/`/' "$added_file"
        fi
        if [ "$n_m" -gt 0 ]; then
            echo ""
            echo "#### ✏️ Modificados ($n_m)"
            sed 's/^/- `/; s/$/`/' "$modified_file"
        fi
        if [ "$n_d" -gt 0 ]; then
            echo ""
            echo "#### 🗑️ Eliminados ($n_d)"
            sed 's/^/- `/; s/$/`/' "$deleted_file"
        fi
        if [ -n "${LAST_BACKUP:-}" ]; then
            echo ""
            echo "💾 Backup: \`$LAST_BACKUP\`"
        fi
    } >> "$SUMMARY"
}

write_header() {
    {
        echo "## 🚀 Deploy a producción"
        echo ""
        echo "**Modo:** \`$MODE\`"
        echo "**Rango:** \`${FROM_SHA:-auto}\` → \`${TO_SHA:0:7}\`"
        echo "**Update SHA:** \`$UPDATE_SHA\`"
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
        echo "ℹ️  Commit ${target_sha:0:7}: sin cambios deployables (todo excluido)"
        {
            echo ""
            echo "### Commit \`${target_sha:0:7}\` — _skipped (sólo cambios excluidos)_"
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

    write_commit_report "$target_sha" "$added.f" "$modified.f" "$deleted.f"

    rm -rf "$tmp"
}

deploy_full_initial() {
    do_backup "INITIAL"

    local tmp; tmp=$(mktemp -d)
    git ls-files | filter_list > "$tmp/upload.txt"
    local n; n=$(wc -l < "$tmp/upload.txt" | tr -d ' ')
    echo "📦 Deploy inicial: $n archivos"

    rsync_files "$tmp/upload.txt"

    {
        echo ""
        echo "### 🆕 Deploy inicial"
        echo ""
        echo "**$n archivos subidos** (todo el repo, filtrado por \`.deployignore\`)"
        echo ""
        echo "💾 Backup: \`$LAST_BACKUP\`"
    } >> "$SUMMARY"

    rm -rf "$tmp"
}

# ──────────────────────────────────────────────────────────────────────
# Main
# ──────────────────────────────────────────────────────────────────────
write_header

# Resolver FROM_SHA
if [ -z "$FROM_SHA" ]; then
    FROM_SHA=$(read_remote_sha)
    echo "🔎 SHA remoto: $FROM_SHA"
fi

# Caso 1: primer deploy (INITIAL)
if [ "$FROM_SHA" = "INITIAL" ] || [ -z "$FROM_SHA" ]; then
    deploy_full_initial
    write_remote_sha "$TO_SHA"
    echo "✅ Deploy inicial completado"
    exit 0
fi

# Validar que FROM_SHA existe en historial local
if ! git cat-file -e "$FROM_SHA^{commit}" 2>/dev/null; then
    echo "⚠️  FROM_SHA '$FROM_SHA' no existe en el historial local. Fallback a INITIAL."
    deploy_full_initial
    write_remote_sha "$TO_SHA"
    exit 0
fi

# Caso 2: bulk
if [ "$MODE" = "bulk" ]; then
    echo "📦 Modo BULK: diff único $FROM_SHA → $TO_SHA"
    deploy_range "$FROM_SHA" "$TO_SHA" "${TO_SHA:0:7}"
    write_remote_sha "$TO_SHA"
    echo "✅ Deploy bulk completado"
    exit 0
fi

# Caso 3: sequential (default)
echo "🔁 Modo SEQUENTIAL: iterando commits $FROM_SHA → $TO_SHA"
COMMITS=$(git rev-list --reverse "$FROM_SHA..$TO_SHA")
if [ -z "$COMMITS" ]; then
    echo "ℹ️  No hay commits nuevos para deployar."
    {
        echo ""
        echo "### ℹ️ Sin cambios"
        echo "El server ya está al día con el último commit."
    } >> "$SUMMARY"
    exit 0
fi

PREV="$FROM_SHA"
LAST_OK="$FROM_SHA"
COUNT=0
TOTAL=$(echo "$COMMITS" | wc -l | tr -d ' ')

for C in $COMMITS; do
    COUNT=$((COUNT + 1))
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "[$COUNT/$TOTAL] Deployando commit ${C:0:7}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

    if deploy_range "$PREV" "$C" "${C:0:7}"; then
        write_remote_sha "$C"
        LAST_OK="$C"
        PREV="$C"
    else
        echo "❌ Falló en commit ${C:0:7}. Fail-fast: NO se intentan los siguientes."
        {
            echo ""
            echo "### ❌ Deploy abortado en commit \`${C:0:7}\`"
            echo "Último SHA OK en server: \`${LAST_OK:0:7}\`. Próximo push retomará desde ahí."
        } >> "$SUMMARY"
        exit 1
    fi
done

echo ""
echo "✅ Deploy completado: $COUNT commits aplicados"
{
    echo ""
    echo "---"
    echo "✅ **Deploy completado**: $COUNT commits aplicados secuencialmente."
} >> "$SUMMARY"

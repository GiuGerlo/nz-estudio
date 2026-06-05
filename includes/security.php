<?php
/**
 * includes/security.php — helpers de seguridad para nz-estudio.
 *
 * Incluido desde config/config.php para estar disponible en todas las páginas
 * (públicas y admin).
 *
 * Funciones:
 *   - nz_csrf_token()     → string token (lo genera si no existe).
 *   - nz_csrf_field()     → string <input hidden> listo para forms.
 *   - nz_csrf_verify($t)  → bool comparación timing-safe.
 *   - nz_csrf_require()   → muere con 403/JSON si el token falta o no matchea.
 *   - nz_set_secure_headers() → X-Frame-Options, X-Content-Type-Options, etc.
 *   - nz_require_admin()  → redirige a /login.php si no hay sesión válida.
 *   - nz_validate_upload($file, $opts) → ['ok' => bool, 'reason' => string].
 *   - nz_is_prod()        → bool (basado en HTTP_HOST).
 *
 * Convenciones:
 *   - Prefijo nz_ para evitar colisiones con funciones globales del proyecto.
 *   - Las funciones no asumen session_start() previo: la sesión ya viene
 *     iniciada por config/config.php.
 */

// ─── Entorno ───────────────────────────────────────────────────────────
function nz_is_prod(): bool
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    return !str_contains($host, 'localhost')
        && !str_contains($host, '.test')
        && !str_contains($host, '.local')
        && !str_contains($host, ':8080');
}

// ─── CSRF ──────────────────────────────────────────────────────────────
function nz_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function nz_csrf_field(): string
{
    $t = htmlspecialchars(nz_csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $t . '">';
}

function nz_csrf_verify(?string $token): bool
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Lee el token de $_POST['csrf_token'] o del header X-CSRF-Token (AJAX JSON).
 * Si falla, responde 403 y muere. Para endpoints que devuelven JSON,
 * setea Content-Type apropiado.
 */
function nz_csrf_require(bool $json = true): void
{
    $token = $_POST['csrf_token']
          ?? $_SERVER['HTTP_X_CSRF_TOKEN']
          ?? null;

    // Soporte para requests JSON (body raw): leer del body si vino así
    if (!$token && ($_SERVER['CONTENT_TYPE'] ?? '') && str_contains($_SERVER['CONTENT_TYPE'], 'application/json')) {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        $token = $data['csrf_token'] ?? null;
    }

    if (!nz_csrf_verify($token)) {
        http_response_code(403);
        if ($json) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Token CSRF inválido o ausente. Recargá la página.',
            ]);
        } else {
            echo 'Forbidden: invalid CSRF token.';
        }
        exit;
    }
}

// ─── Headers seguros ───────────────────────────────────────────────────
function nz_set_secure_headers(): void
{
    if (headers_sent()) {
        return;
    }
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

    if (nz_is_prod()) {
        // HSTS sólo en producción (HTTPS confirmado).
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    // CSP: pendiente de afinar. Si lo activamos full, hay que whitelistear todos
    // los CDNs que usa el proyecto. Por ahora dejamos un report-only laxo para
    // monitoreo, sin bloquear. Activar el header de bloqueo cuando esté afinado.
    // header("Content-Security-Policy-Report-Only: default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: blob:");
}

// ─── Guard de admin ────────────────────────────────────────────────────
/**
 * Guard para endpoints AJAX/JSON: si no hay sesión válida o expiró por
 * inactividad, responde 401 con JSON y muere. Distinto a nz_require_admin
 * (que redirige), pensado para controllers que consumen XHR.
 */
function nz_require_admin_ajax(int $idle_seconds = 3600): void
{
    $fail = function (string $msg): void {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $msg]);
        exit;
    };

    if (empty($_SESSION['user_id'])) {
        $fail('No autenticado. Recargá la página e iniciá sesión.');
    }
    $now = time();
    $last = $_SESSION['last_activity'] ?? $now;
    if (($now - $last) > $idle_seconds) {
        $_SESSION = [];
        session_destroy();
        $fail('Sesión expirada. Recargá la página e iniciá sesión.');
    }
    $_SESSION['last_activity'] = $now;
}

/**
 * Verifica sesión + timeout idle. Si falla, redirige al login.
 * $idle_seconds: tiempo de inactividad permitido (default 1h).
 */
function nz_require_admin(int $idle_seconds = 3600, string $login_url = '../login.php'): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . $login_url);
        exit;
    }

    // Timeout por inactividad
    $now = time();
    $last = $_SESSION['last_activity'] ?? $now;
    if (($now - $last) > $idle_seconds) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', $now - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header('Location: ' . $login_url . '?expired=1');
        exit;
    }
    $_SESSION['last_activity'] = $now;
}

// ─── Validación de uploads ─────────────────────────────────────────────
/**
 * Valida un archivo de $_FILES['xxx'] (un único archivo, no array multi).
 *
 * $opts:
 *   - max_bytes:      int (default 8MB)
 *   - allowed_mimes:  array de mime types válidos
 *   - allowed_exts:   array de extensiones permitidas (sin punto)
 *
 * Retorna: ['ok' => bool, 'reason' => string, 'mime' => string|null]
 */
function nz_validate_upload(array $file, array $opts = []): array
{
    $max_bytes      = $opts['max_bytes']     ?? 8 * 1024 * 1024;
    $allowed_mimes  = $opts['allowed_mimes'] ?? ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $allowed_exts   = $opts['allowed_exts']  ?? ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!isset($file['tmp_name'], $file['name'], $file['size'], $file['error'])) {
        return ['ok' => false, 'reason' => 'Archivo malformado.', 'mime' => null];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'reason' => 'Error de subida (código ' . $file['error'] . ').', 'mime' => null];
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        return ['ok' => false, 'reason' => 'Archivo no es upload válido.', 'mime' => null];
    }
    if ($file['size'] <= 0 || $file['size'] > $max_bytes) {
        return ['ok' => false, 'reason' => 'Tamaño inválido (máx ' . round($max_bytes / 1024 / 1024, 1) . 'MB).', 'mime' => null];
    }

    // Mime real (no confío en $file['type'] que viene del cliente)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']) ?: '';
    if (!in_array($mime, $allowed_mimes, true)) {
        return ['ok' => false, 'reason' => 'Tipo de archivo no permitido (' . $mime . ').', 'mime' => $mime];
    }

    // Extensión (segunda capa)
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts, true)) {
        return ['ok' => false, 'reason' => 'Extensión no permitida (.' . $ext . ').', 'mime' => $mime];
    }

    // Anti SVG: si por alguna razón pasa, rechazar
    if (str_contains($mime, 'svg') || $ext === 'svg') {
        return ['ok' => false, 'reason' => 'SVG no permitido.', 'mime' => $mime];
    }

    return ['ok' => true, 'reason' => '', 'mime' => $mime];
}

/**
 * Sanitiza un nombre de archivo: quita path separators, caracteres raros, etc.
 * NO incluye extensión opcional — usar después de pathinfo si hace falta.
 */
function nz_sanitize_filename(string $name): string
{
    $name = basename($name);  // anti path traversal
    $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
    return substr($name, 0, 100);
}

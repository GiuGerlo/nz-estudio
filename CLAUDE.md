# CLAUDE.md — nz-estudio

Brief de contexto para Claude. Sitio web del **Estudio Jurídico-Inmobiliario Nadina Zaranich** (Guatimozín, Córdoba). Catálogo público de propiedades + panel admin con CRUD. Desarrollado por Artisans Thinking.

## 1. Stack

- **PHP 8.2.12** procedural. Sin Composer. Sin framework.
- **MySQL/MariaDB** vía `mysqli` (NO PDO). Instancia global `$db` creada en `config/config.php`.
- **Frontend**: Bootstrap 5.3.2, Bootstrap Icons, AOS, SweetAlert2, jQuery 3.7.
- **Admin**: DataTables 1.13.7 (jQuery), Font Awesome 6.4.2.
- **Mapas**: Google Maps API (iframe embeds + MarkerClusterer en `templates/map.php`).
- **Analytics**: GA4, ID `G-0CG4DEM9KS` (en `config/config.php`).
- **Entorno local**: Laragon, MySQL en puerto `3307`.
- **Producción**: Hostinger.

## 2. Estructura

```
nz-estudio/
├── admin/                 # Panel admin (CRUD propiedades)
│   ├── admin.php          # Dashboard con stats
│   ├── propiedades.php    # CRUD con DataTables + modal
│   ├── categorias.php     # CRUD tipos de propiedad
│   ├── vendidas.php       # Vista propiedades vendidas
│   ├── order-propiedades.php  # Drag-drop reorden por categoría
│   ├── controllers/       # Lógica de negocio (controller_propiedades.php, controller_categorias.php)
│   ├── templates/         # modal_propiedad.php
│   └── includes/          # head.php (sidebar+navbar, guard sesión), footer.php
├── assets/
│   ├── css/  js/  scss/  img/  vendor/   # Bootstrap, AOS, jQuery, php-email-form
├── config/
│   └── config.php         # Conexión DB (detecta local vs prod), API keys
├── includes/
│   ├── head.php           # Header público, nav sticky, CDNs
│   ├── head-meta.php      # Meta tags dinámicos SEO
│   └── footer.php         # Footer + scripts
├── templates/             # Secciones reusables del home
│   ├── hero.php  about.php  services.php  contact-me.php  map.php
├── uploads/propiedades/{categoria}/{id}/  # Imágenes WebP de propiedades
│
├── index.php              # Home (compila hero+about+services+map+contact)
├── propiedades.php        # Listado + filtros + buscador en vivo
├── propiedad.php          # Detalle (carousel imgs, info, relacionadas)
├── vendidas.php           # Archivo de vendidas
├── login.php  auth.php  logout.php  404.php
├── .htaccess              # URLs amigables
└── u407412506_nzestudio.sql  # Dump completo
```

## 3. Páginas públicas

| Archivo | Función | Auth |
|---|---|---|
| `index.php` | Home secciones | No |
| `propiedades.php` | Catálogo + filtros por categoría + búsqueda live | No |
| `propiedad.php?id=N` | Detalle de propiedad + 3 relacionadas | No |
| `vendidas.php` | Listado de vendidas | No |
| `login.php` | Form de admin | No |
| `auth.php` | POST endpoint, responde JSON | No |
| `logout.php` | Destruye sesión | Sí |
| `404.php` | Error (vía `.htaccess`) | No |

## 4. Panel admin (`admin/`)

- **Dashboard** `admin.php` — totales (propiedades, categorías, imágenes) + últimas 5.
- **Propiedades** `propiedades.php` — DataTables, modal AJAX para alta/edición, multi-upload de imágenes.
- **Categorías** `categorias.php` — CRUD tipos (con check de integridad referencial).
- **Vendidas** `vendidas.php` — marcar/desmarcar `vendida = 1`.
- **Orden** `order-propiedades.php` — drag-drop jQuery, actualiza campo `orden` por categoría.

Controllers en `admin/controllers/` reciben `$db` por constructor, devuelven arrays/JSON `['estado', 'mensaje', 'data']`. AJAX desde el frontend con `$.ajax` + `FormData` para uploads.

## 5. Base de datos

Database: `nz-estudio` (local) / `u407412506_nzestudio` (prod). Charset utf8mb4. Dump: `u407412506_nzestudio.sql`.

| Tabla | Campos clave |
|---|---|
| `propiedades` | `id`, `categoria` (FK→tipos_propiedad), `titulo`, `localidad`, `ubicacion`, `tamanio`, `servicios`, `caracteristicas`, `mapa` (iframe HTML), `orden`, `vendida` (0/1), `latitud`, `longitud` |
| `tipos_propiedad` | `id`, `nombre_categoria` (7: Casas, Terrenos, Locales, Quintas, Cocheras, Departamentos, Locales comerciales con Casa) |
| `imagenes_propiedades` | `id`, `id_propiedad` (FK), `ruta_imagen` |
| `users` | `id`, `email`, `password` (bcrypt vía `password_hash`). 1 solo user. |

## 6. Config (`config/config.php` + `.env`)

Credenciales y API keys viven en `.env` (no commiteado). Plantilla en `.env.example`. Loader propio en `config/env.php` (sin Composer, usa `parse_ini_file`).

- `config/config.php` detecta entorno por `HTTP_HOST` y elige bloque `DB_LOCAL_*` o `DB_PROD_*` de `.env`.
- Acceder a vars: `env('NOMBRE', $default)`. Constantes: `GOOGLE_MAPS_API_KEY`, `GOOGLE_ANALYTICS_ID`.
- Setup: copiar `.env.example` → `.env` y completar. Si falta `.env` el sitio muere con mensaje claro.

## 7. Auth flow

1. `login.php` muestra form Bootstrap.
2. POST a `auth.php` → query `SELECT id, email, password FROM users WHERE email = ?` (prepared) → `password_verify()`.
3. Setea `$_SESSION['user_id']` y `$_SESSION['user_email']`. Responde JSON `{success, message}`.
4. Cada página de `admin/` valida sesión en `admin/includes/head.php` y redirige a `login.php` si falta.
5. `logout.php` destruye sesión + cookie y redirige.

## 8. URLs amigables (`.htaccess`)

```
RewriteRule ^vendidas/?$ vendidas.php
RewriteRule ^propiedad([0-9]+)$ propiedad.php?id=$1
RewriteRule ^([a-zA-Z0-9_-]+)$ index.php?seccion=$1
RewriteRule ^$ index.php?seccion=inicio
```

Ej: `/propiedad55` → `propiedad.php?id=55`. `/inicio` → `index.php?seccion=inicio`.

## 9. Manejo de imágenes

- Multi-upload desde modal admin.
- `convertToWebP()` en `admin/controllers/controller_propiedades.php` convierte JPG/PNG/GIF → WebP.
- Path en disco: `uploads/propiedades/{categoria}/{id}/{uniqid}.webp`.
- Solo la ruta relativa se guarda en `imagenes_propiedades.ruta_imagen`.
- Al borrar propiedad, también se eliminan archivos físicos.

## 10. Patrones de código a respetar

**Query con prepared statement** (estándar en todo el proyecto):
```php
$stmt = $db->prepare("SELECT * FROM propiedades WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
```

**Controller OOP** (`admin/controllers/controller_categorias.php`):
```php
class ControllerCategorias {
    private $db;
    private $resultado = ['estado' => '', 'mensaje' => '', 'data' => null];
    public function __construct($db) { $this->db = $db; }
}
```

**AJAX con FormData** (para uploads):
```js
$.ajax({
  url: 'controllers/controller_propiedades.php',
  type: 'POST',
  data: formData, processData: false, contentType: false,
  success: r => { const d = JSON.parse(r); if (d.success) {/*...*/} }
});
```

- Escape de salida: **siempre** `htmlspecialchars()`.
- Sanitización entrada: `$db->real_escape_string()` para texto, `(int)` para IDs.
- Convenciones: snake_case en PHP/DB, kebab-case en CSS, camelCase en JS.

## 11. Agregar una página nueva

1. Crear `.php` en raíz (o `admin/`).
2. `require_once 'config/config.php';` (carga `$db`).
3. `include_once 'includes/head.php';` (o `admin/includes/head.php` + check sesión).
4. HTML con grid Bootstrap 5.
5. `include_once 'includes/footer.php';`
6. Si va en navegación, agregar link en `includes/head.php`.

## 12. TODOs / pendientes conocidos

- **Alquiler**: desactivado, muestra modal SweetAlert "Próximamente" en nav (`includes/head.php` ~líneas 70-86).
- Sin recovery de password.
- Sin gestión de usuarios admin (solo 1 hardcodeado: `ggiuliano526@gmail.com`).
- Form de contacto sin notificación email visible.
- **Credenciales prod expuestas en git history** (commit `cd18ea3` y previos). Rotar password Hostinger y regenerar/restringir API key Google Maps. Considerar `git filter-repo` para limpiar history.
- SCSS sin pipeline de compilación automatizado en dev.
- Sin cache headers para assets estáticos.

## 13. Comandos / setup local

- Abrir vía Laragon: típicamente `http://nz-estudio.test`.
- MySQL local en puerto `3307` (config Laragon).
- Importar `u407412506_nzestudio.sql` a db `nz-estudio` antes de levantar.

## 14. Preferencias

- Borrar siempre archivos creados que ya no tengan uso (del CLAUDE.md global).
- Idioma del proyecto y comunicación: **español**.

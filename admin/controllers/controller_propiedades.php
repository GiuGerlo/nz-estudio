<?php
require_once '../../config/config.php';

// Guard: cualquier acceso (POST o GET) requiere sesión válida; responde JSON 401 si no.
nz_require_admin_ajax();

// Función para enviar respuesta JSON
function sendJsonResponse($success, $message, $data = null)
{
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function convertToWebP($source, $destination, $quality = 82)
{
    // Validar mime real con finfo (no confiar en metadata del cliente ni en getimagesize)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($source) ?: '';

    $image = null;
    switch ($mime) {
        case 'image/jpeg':
            $image = @imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = @imagecreatefrompng($source);
            if ($image) {
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }
            break;
        case 'image/gif':
            $image = @imagecreatefromgif($source);
            break;
        case 'image/webp':
            $image = @imagecreatefromwebp($source);
            break;
        default:
            return false;
    }

    if (!$image) {
        return false;
    }

    // Asegurar el directorio destino (idempotente)
    $dir = dirname($destination);
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        imagedestroy($image);
        return false;
    }

    $result = imagewebp($image, $destination, $quality);
    imagedestroy($image);
    return $result;
}

// Si es una petición POST, procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar CSRF antes de cualquier mutación
    nz_csrf_require();

    // Obtener el contenido raw del POST para solicitudes JSON
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $_POST['action'] ?? '';

    if ($action === 'update_order') {
        try {
            if (!isset($input['orden']) || !is_array($input['orden'])) {
                throw new Exception('Datos de orden inválidos');
            }

            $db->begin_transaction();

            foreach ($input['orden'] as $item) {
                $id = (int)$item['id'];
                $orden = (int)$item['orden'];

                $stmt = $db->prepare("UPDATE propiedades SET orden = ? WHERE id = ?");
                $stmt->bind_param("ii", $orden, $id);

                if (!$stmt->execute()) {
                    throw new Exception('Error al actualizar el orden');
                }
            }

            $db->commit();
            sendJsonResponse(true, 'Orden actualizado correctamente');
        } catch (Exception $e) {
            $db->rollback();
            sendJsonResponse(false, 'Error: ' . $e->getMessage());
        }
        exit;
    }

    // Eliminar propiedad (antes era GET → vulnerable a CSRF)
    if ($action === 'eliminar') {
        try {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('ID inválido');
            }

            // Borrar archivos físicos de imágenes
            $stmt = $db->prepare("SELECT ruta_imagen FROM imagenes_propiedades WHERE id_propiedad = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($img = $res->fetch_assoc()) {
                $path = '../../' . $img['ruta_imagen'];
                if (is_file($path)) {
                    @unlink($path);
                }
            }

            // Borrar registros (FK no en cascada → manual)
            $stmt = $db->prepare("DELETE FROM imagenes_propiedades WHERE id_propiedad = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();

            $stmt = $db->prepare("DELETE FROM propiedades WHERE id = ?");
            $stmt->bind_param('i', $id);
            if (!$stmt->execute()) {
                throw new Exception('Error al eliminar la propiedad');
            }

            sendJsonResponse(true, 'Propiedad eliminada correctamente');
        } catch (Exception $e) {
            sendJsonResponse(false, 'Error: ' . $e->getMessage());
        }
        exit;
    }

    // Marcar propiedad como vendida (antes era GET → vulnerable a CSRF)
    if ($action === 'vender') {
        try {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('ID inválido');
            }
            $stmt = $db->prepare("UPDATE propiedades SET vendida = 1 WHERE id = ?");
            $stmt->bind_param('i', $id);
            if (!$stmt->execute()) {
                throw new Exception('Error al marcar como vendida');
            }
            sendJsonResponse(true, 'Propiedad marcada como vendida');
        } catch (Exception $e) {
            sendJsonResponse(false, 'Error: ' . $e->getMessage());
        }
        exit;
    }

    // Eliminar una imagen individual (lo llama el modal de edición)
    if ($action === 'eliminar_imagen') {
        try {
            $img_id = (int)($_POST['id'] ?? 0);
            if ($img_id <= 0) {
                throw new Exception('ID inválido');
            }
            $stmt = $db->prepare("SELECT ruta_imagen FROM imagenes_propiedades WHERE id = ?");
            $stmt->bind_param('i', $img_id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if (!$row) {
                throw new Exception('Imagen no encontrada');
            }
            $path = '../../' . $row['ruta_imagen'];
            if (is_file($path)) {
                @unlink($path);
            }
            $stmt = $db->prepare("DELETE FROM imagenes_propiedades WHERE id = ?");
            $stmt->bind_param('i', $img_id);
            if (!$stmt->execute()) {
                throw new Exception('Error al borrar el registro');
            }
            sendJsonResponse(true, 'Imagen eliminada');
        } catch (Exception $e) {
            sendJsonResponse(false, 'Error: ' . $e->getMessage());
        }
        exit;
    }

    // Default: crear o editar propiedad (form principal del modal)
    try {
        $id              = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $titulo          = trim($_POST['titulo'] ?? '');
        $categoria       = (int)($_POST['categoria'] ?? 0);
        $localidad       = trim($_POST['localidad'] ?? '');
        $ubicacion       = trim($_POST['ubicacion'] ?? '');
        $tamanio         = trim($_POST['tamanio'] ?? '');
        $servicios       = trim($_POST['servicios'] ?? '');
        $caracteristicas = trim($_POST['caracteristicas'] ?? '');
        $mapa            = $_POST['mapa'] ?? '';

        // Validaciones mínimas
        if ($titulo === '') {
            throw new Exception('El título es obligatorio');
        }
        if ($categoria <= 0) {
            throw new Exception('Categoría inválida');
        }

        // Lat/Lng son DECIMAL en la DB → '' debe ir como NULL.
        $lat = trim($_POST['latitud']  ?? '');
        $lng = trim($_POST['longitud'] ?? '');
        $latitud  = ($lat === '' ? null : (float)$lat);
        $longitud = ($lng === '' ? null : (float)$lng);

        if ($id) {
            // Actualizar propiedad existente
            $query = "UPDATE propiedades SET
                     categoria = ?, titulo = ?, localidad = ?, ubicacion = ?,
                     tamanio = ?, servicios = ?, caracteristicas = ?, mapa = ?,
                     latitud = ?, longitud = ?
                     WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param(
                "isssssssddi",
                $categoria,
                $titulo,
                $localidad,
                $ubicacion,
                $tamanio,
                $servicios,
                $caracteristicas,
                $mapa,
                $latitud,
                $longitud,
                $id
            );
        } else {
            // Insertar nueva propiedad
            $query = "INSERT INTO propiedades
                     (categoria, titulo, localidad, ubicacion, tamanio, servicios, caracteristicas, mapa, latitud, longitud)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param(
                "isssssssdd",
                $categoria,
                $titulo,
                $localidad,
                $ubicacion,
                $tamanio,
                $servicios,
                $caracteristicas,
                $mapa,
                $latitud,
                $longitud
            );
        }

        if ($stmt->execute()) {
            $propiedad_id = $id ?: $db->insert_id;

            // Obtener la categoría de la propiedad para la estructura de carpetas
            $stmt = $db->prepare("SELECT tp.nombre_categoria FROM tipos_propiedad tp 
                                INNER JOIN propiedades p ON p.categoria = tp.id 
                                WHERE p.id = ?");
            $stmt->bind_param("i", $propiedad_id);
            $stmt->execute();
            $categoria_nombre = $stmt->get_result()->fetch_assoc()['nombre_categoria'];
            $categoria_nombre = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $categoria_nombre));

            // Procesar imágenes si se enviaron
            $upload_errors = [];
            $uploaded_count = 0;

            if (!empty($_FILES['imagenes']['name'][0])) {
                $max_files     = 20;             // límite duro por request
                $max_bytes     = 8 * 1024 * 1024; // 8MB por archivo
                $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $allowed_exts  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                $total_files = count($_FILES['imagenes']['name']);
                if ($total_files > $max_files) {
                    throw new Exception("Demasiadas imágenes (máx $max_files por subida).");
                }

                // Crear estructura de directorios (0755: dueño rw+x, resto r+x)
                $base_dir = '../../uploads/propiedades/' . $categoria_nombre . '/' . $propiedad_id;
                if (!is_dir($base_dir) && !mkdir($base_dir, 0755, true) && !is_dir($base_dir)) {
                    throw new Exception('No se pudo crear el directorio de imágenes.');
                }

                $stmt_img = $db->prepare("INSERT INTO imagenes_propiedades (id_propiedad, ruta_imagen) VALUES (?, ?)");

                for ($i = 0; $i < $total_files; $i++) {
                    // Armar struct $_FILES individual para nz_validate_upload
                    $file = [
                        'name'     => $_FILES['imagenes']['name'][$i]     ?? '',
                        'type'     => $_FILES['imagenes']['type'][$i]     ?? '',
                        'tmp_name' => $_FILES['imagenes']['tmp_name'][$i] ?? '',
                        'error'    => $_FILES['imagenes']['error'][$i]    ?? UPLOAD_ERR_NO_FILE,
                        'size'     => $_FILES['imagenes']['size'][$i]     ?? 0,
                    ];

                    $check = nz_validate_upload($file, [
                        'max_bytes'     => $max_bytes,
                        'allowed_mimes' => $allowed_mimes,
                        'allowed_exts'  => $allowed_exts,
                    ]);

                    if (!$check['ok']) {
                        $upload_errors[] = ($file['name'] ?: 'archivo') . ': ' . $check['reason'];
                        continue;
                    }

                    // Nombre final: random + sanitized base + .webp (anti path-traversal)
                    $safe_base    = nz_sanitize_filename(pathinfo($file['name'], PATHINFO_FILENAME));
                    $webp_name    = bin2hex(random_bytes(8)) . '_' . $safe_base . '.webp';
                    $dest_path    = $base_dir . '/' . $webp_name;
                    $ruta_imagen  = 'uploads/propiedades/' . $categoria_nombre . '/' . $propiedad_id . '/' . $webp_name;

                    if (convertToWebP($file['tmp_name'], $dest_path)) {
                        $stmt_img->bind_param('is', $propiedad_id, $ruta_imagen);
                        $stmt_img->execute();
                        $uploaded_count++;
                    } else {
                        $upload_errors[] = $file['name'] . ': error al convertir a WebP.';
                    }
                }
            }

            $msg = 'Propiedad guardada exitosamente';
            if ($uploaded_count > 0) {
                $msg .= " ($uploaded_count imagen(es) subida(s))";
            }
            if (!empty($upload_errors)) {
                $msg .= '. Avisos: ' . implode(' | ', $upload_errors);
            }
            sendJsonResponse(true, $msg);
        } else {
            sendJsonResponse(false, 'Error al guardar la propiedad');
        }
    } catch (Exception $e) {
        sendJsonResponse(false, 'Error: ' . $e->getMessage());
    }
}

// Si es una petición GET, procesar otras acciones
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'obtener':
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];

                // Obtener los datos de la propiedad incluyendo la categoría
                $query = "SELECT p.*, 
                        tp.nombre_categoria,
                        (SELECT GROUP_CONCAT(CONCAT(id, ':', ruta_imagen)) 
                         FROM imagenes_propiedades 
                         WHERE id_propiedad = p.id) as imagenes_data
                        FROM propiedades p 
                        LEFT JOIN tipos_propiedad tp ON p.categoria = tp.id
                        WHERE p.id = ?";

                $stmt = $db->prepare($query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $propiedad = $resultado->fetch_assoc();

                if ($propiedad) {
                    // Procesar las imágenes para incluir sus IDs
                    if ($propiedad['imagenes_data']) {
                        $imagenes_array = explode(',', $propiedad['imagenes_data']);
                        $propiedad['imagenes'] = array_map(function ($img) {
                            list($id, $ruta) = explode(':', $img);
                            return [
                                'id' => $id,
                                'ruta_imagen' => $ruta
                            ];
                        }, $imagenes_array);
                    } else {
                        $propiedad['imagenes'] = [];
                    }
                    unset($propiedad['imagenes_data']); // Limpiamos el campo temporal

                    sendJsonResponse(true, 'Propiedad encontrada', $propiedad);
                } else {
                    sendJsonResponse(false, 'Propiedad no encontrada');
                }
            }
            break;

        // Compatibilidad: si alguien llega a estas acciones por GET,
        // bloquear con 405 (deben usarse vía POST con CSRF token).
        case 'eliminar':
        case 'vender':
            http_response_code(405);
            sendJsonResponse(false, 'Método no permitido. Usar POST con CSRF token.');
            break;
    }

    header('Location: ../propiedades.php');
    exit;
}

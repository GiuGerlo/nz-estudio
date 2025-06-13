<?php
require_once '../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

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

function convertToWebP($source, $destination, $quality = 80)
{
    $info = getimagesize($source);
    $isValid = true;

    if ($info['mime'] === 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($info['mime'] === 'image/png') {
        $image = imagecreatefrompng($source);
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    } elseif ($info['mime'] === 'image/gif') {
        $image = imagecreatefromgif($source);
    } else {
        $isValid = false;
    }

    if ($isValid) {
        // Crear el directorio si no existe
        $dir = dirname($destination);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        // Convertir y guardar como WebP
        $result = imagewebp($image, $destination, $quality);
        imagedestroy($image);
        return $result;
    }
    return false;
}

// Si es una petición POST, procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el contenido raw del POST para solicitudes JSON
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['action']) && $input['action'] === 'update_order') {
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

    try {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $titulo = $db->real_escape_string($_POST['titulo']);
        $categoria = (int)$_POST['categoria'];
        $localidad = $db->real_escape_string($_POST['localidad']);
        $ubicacion = $db->real_escape_string($_POST['ubicacion']);
        $tamanio = $db->real_escape_string($_POST['tamanio']);
        $servicios = $db->real_escape_string($_POST['servicios']);
        $caracteristicas = $db->real_escape_string($_POST['caracteristicas']);
        $mapa = $_POST['mapa'];
        $latitud = isset($_POST['latitud']) ? $db->real_escape_string($_POST['latitud']) : null;
        $longitud = isset($_POST['longitud']) ? $db->real_escape_string($_POST['longitud']) : null;

        if ($id) {
            // Actualizar propiedad existente
            $query = "UPDATE propiedades SET 
                     categoria = ?, titulo = ?, localidad = ?, ubicacion = ?,
                     tamanio = ?, servicios = ?, caracteristicas = ?, mapa = ?,
                     latitud = ?, longitud = ?
                     WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param(
                "isssssssssi",
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
                "isssssssss",
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
            if (!empty($_FILES['imagenes']['name'][0])) {
                // Crear estructura de directorios
                $base_dir = '../../uploads/propiedades/' . $categoria_nombre . '/' . $propiedad_id;
                if (!file_exists($base_dir)) {
                    mkdir($base_dir, 0777, true);
                }

                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['imagenes']['error'][$key] === 0) {
                        $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $_FILES['imagenes']['name'][$key]);
                        $webp_filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                        $uploadFile = $base_dir . '/' . $webp_filename;

                        if (convertToWebP($tmp_name, $uploadFile)) {
                            $ruta_imagen = 'uploads/propiedades/' . $categoria_nombre . '/' . $propiedad_id . '/' . $webp_filename;
                            $db->query("INSERT INTO imagenes_propiedades (id_propiedad, ruta_imagen) 
                                      VALUES ($propiedad_id, '$ruta_imagen')");
                        }
                    }
                }
            }

            sendJsonResponse(true, 'Propiedad guardada exitosamente');
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

        case 'eliminar':
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];

                // Primero eliminar las imágenes asociadas
                $queryImagenes = "SELECT ruta_imagen FROM imagenes_propiedades WHERE id_propiedad = ?";
                $stmtImagenes = $db->prepare($queryImagenes);
                $stmtImagenes->bind_param("i", $id);
                $stmtImagenes->execute();
                $resultImagenes = $stmtImagenes->get_result();

                while ($imagen = $resultImagenes->fetch_assoc()) {
                    if (file_exists("../../" . $imagen['ruta_imagen'])) {
                        unlink("../../" . $imagen['ruta_imagen']);
                    }
                }

                // Eliminar registros de imágenes
                $db->query("DELETE FROM imagenes_propiedades WHERE id_propiedad = $id");

                // Eliminar la propiedad
                if ($db->query("DELETE FROM propiedades WHERE id = $id")) {
                    $_SESSION['alert'] = [
                        'type' => 'success',
                        'message' => 'Propiedad eliminada correctamente'
                    ];
                } else {
                    $_SESSION['alert'] = [
                        'type' => 'error',
                        'message' => 'Error al eliminar la propiedad'
                    ];
                }
            }
            break;

        case 'vender':
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];

                try {
                    // Iniciar transacción
                    $db->begin_transaction();

                    // Actualizar el campo 'vendida' a 1
                    $query = "UPDATE propiedades SET vendida = 1 WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("i", $id);

                    if ($stmt->execute()) {
                        $db->commit();
                        $_SESSION['alert'] = [
                            'type' => 'success',
                            'message' => 'Propiedad marcada como vendida correctamente'
                        ];
                    } else {
                        throw new Exception('Error al actualizar el estado de la propiedad');
                    }
                } catch (Exception $e) {
                    $db->rollback();
                    $_SESSION['alert'] = [
                        'type' => 'error',
                        'message' => 'Error al marcar la propiedad como vendida: ' . $e->getMessage()
                    ];
                }
            }
            break;
    }

    header('Location: ../propiedades.php');
    exit;
}

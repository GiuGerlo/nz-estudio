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

function convertToWebP($source, $destination, $quality = 80) {
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
    // Manejar la eliminación de imagen individual
    if(isset($_POST['action']) && $_POST['action'] === 'eliminar_imagen') {
        try {
            $imagen_id = (int)$_POST['imagen_id'];
            
            // Verificar que no sea la última imagen
            $stmt = $db->prepare("SELECT ip.*, p.id as propiedad_id, tp.nombre_categoria 
                                FROM imagenes_propiedades ip 
                                INNER JOIN propiedades p ON ip.id_propiedad = p.id 
                                INNER JOIN tipos_propiedad tp ON p.categoria = tp.id 
                                WHERE ip.id = ?");
            $stmt->bind_param("i", $imagen_id);
            $stmt->execute();
            $imagen = $stmt->get_result()->fetch_assoc();
            
            if($imagen) {
                // Verificar cantidad de imágenes
                $count = $db->query("SELECT COUNT(*) as total FROM imagenes_propiedades WHERE id_propiedad = {$imagen['propiedad_id']}")->fetch_assoc()['total'];
                
                if($count <= 1) {
                    sendJsonResponse(false, 'Debe mantener al menos una imagen');
                    exit;
                }
                
                // Eliminar archivo físico
                if(file_exists("../../" . $imagen['ruta_imagen'])) {
                    unlink("../../" . $imagen['ruta_imagen']);
                }
                
                // Eliminar registro de la base de datos
                $stmt = $db->prepare("DELETE FROM imagenes_propiedades WHERE id = ?");
                $stmt->bind_param("i", $imagen_id);
                
                if($stmt->execute()) {
                    sendJsonResponse(true, 'Imagen eliminada correctamente');
                } else {
                    sendJsonResponse(false, 'Error al eliminar la imagen de la base de datos');
                }
            } else {
                sendJsonResponse(false, 'Imagen no encontrada');
            }
        } catch(Exception $e) {
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
        $mapa = $db->real_escape_string($_POST['mapa']);

        if ($id) {
            // Actualizar propiedad existente
            $query = "UPDATE propiedades SET 
                     categoria = ?, titulo = ?, localidad = ?, ubicacion = ?,
                     tamanio = ?, servicios = ?, caracteristicas = ?, mapa = ?
                     WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param(
                "isssssssi",
                $categoria,
                $titulo,
                $localidad,
                $ubicacion,
                $tamanio,
                $servicios,
                $caracteristicas,
                $mapa,
                $id
            );
        } else {
            // Insertar nueva propiedad
            $query = "INSERT INTO propiedades 
                     (categoria, titulo, localidad, ubicacion, tamanio, servicios, caracteristicas, mapa)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param(
                "isssssss",
                $categoria,
                $titulo,
                $localidad,
                $ubicacion,
                $tamanio,
                $servicios,
                $caracteristicas,
                $mapa
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
                
                // Modificar la consulta para obtener los datos completos de las imágenes
                $query = "SELECT p.*, 
                        (SELECT GROUP_CONCAT(CONCAT(id, ':', ruta_imagen)) 
                         FROM imagenes_propiedades 
                         WHERE id_propiedad = p.id) as imagenes_data
                        FROM propiedades p 
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
                        $propiedad['imagenes'] = array_map(function($img) {
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

                // Obtener datos de la propiedad
                $query = "SELECT * FROM propiedades WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $propiedad = $stmt->get_result()->fetch_assoc();

                if ($propiedad) {
                    // Insertar en propiedades_vendidas
                    $query = "INSERT INTO propiedades_vendidas 
                             (id, categoria, titulo, localidad, ubicacion, servicios, caracteristicas, mapa) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param(
                        "iissssss",
                        $propiedad['id'],
                        $propiedad['categoria'],
                        $propiedad['titulo'],
                        $propiedad['localidad'],
                        $propiedad['ubicacion'],
                        $propiedad['servicios'],
                        $propiedad['caracteristicas'],
                        $propiedad['mapa']
                    );

                    if ($stmt->execute()) {
                        // Eliminar de propiedades activas
                        $db->query("DELETE FROM propiedades WHERE id = $id");
                        $_SESSION['alert'] = [
                            'type' => 'success',
                            'message' => 'Propiedad marcada como vendida correctamente'
                        ];
                    } else {
                        $_SESSION['alert'] = [
                            'type' => 'error',
                            'message' => 'Error al marcar la propiedad como vendida'
                        ];
                    }
                }
            }
            break;
    }

    header('Location: ../propiedades.php');
    exit;
}

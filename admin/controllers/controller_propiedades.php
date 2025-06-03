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

// Si es una petición POST, procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

            // Procesar imágenes si se enviaron
            if (!empty($_FILES['imagenes']['name'][0])) {
                $uploadDir = '../../uploads/propiedades/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    $filename = uniqid() . '_' . $_FILES['imagenes']['name'][$key];
                    $uploadFile = $uploadDir . $filename;

                    if (move_uploaded_file($tmp_name, $uploadFile)) {
                        $ruta_imagen = 'uploads/propiedades/' . $filename;
                        $db->query("INSERT INTO imagenes_propiedades (id_propiedad, ruta_imagen) 
                                  VALUES ($propiedad_id, '$ruta_imagen')");
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

                // Obtener datos de la propiedad
                $query = "SELECT p.*, 
                         (SELECT GROUP_CONCAT(ruta_imagen) 
                          FROM imagenes_propiedades 
                          WHERE id_propiedad = p.id) as imagenes
                         FROM propiedades p 
                         WHERE p.id = ?";

                $stmt = $db->prepare($query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $propiedad = $resultado->fetch_assoc();

                if ($propiedad) {
                    // Si hay imágenes, convertirlas en array
                    if ($propiedad['imagenes']) {
                        $imagenes = explode(',', $propiedad['imagenes']);
                        $propiedad['imagenes'] = array_map(function ($ruta) {
                            return ['ruta_imagen' => $ruta];
                        }, $imagenes);
                    } else {
                        $propiedad['imagenes'] = [];
                    }

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

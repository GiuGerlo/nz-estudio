<?php
require_once 'config/config.php';

// Consulta para obtener todas las propiedades con coordenadas
$query = "SELECT p.*, t.nombre_categoria 
          FROM propiedades p 
          LEFT JOIN tipos_propiedad t ON p.categoria = t.id 
          WHERE p.latitud IS NOT NULL AND p.longitud IS NOT NULL 
          AND p.vendida = 0";
$resultado = $db->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Propiedades - Estudio Jurídico NZ</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Maps API y MarkerClusterer -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAD4aOZDL-d6jLIq8_HfHdReWIrQEgMVBE"></script>
    <script src="https://unpkg.com/@googlemaps/markerclusterer@2.4.0/dist/index.min.js"></script>
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1400px;
            padding: 20px;
        }
        .page-title {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 600;
        }
        .map-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        #map {
            height: 700px;
            width: 100%;
            border-radius: 8px;
        }
        .property-info {
            padding: 15px;
        }
        .property-info img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .property-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            text-decoration: none;
        }
        .property-category {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        .property-location {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        .property-link {
            margin-top: 12px;
        }
        .property-link a {
            text-decoration: none;
            width: 100%;
            text-align: center;
        }
        .info-window-content {
            max-width: 300px;
            padding: 5px;
        }
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            #map {
                height: 500px;
            }
            .page-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="page-title">Mapa de Propiedades</h1>
        <div class="map-container">
            <div id="map"></div>
        </div>
    </div>

    <script>
        // Inicializar el mapa
        function initMap() {
            // Coordenadas iniciales (centro entre Rosario y Córdoba)
            const center = { lat: -32.94682, lng: -60.63932 };
            
            // Crear el mapa
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 7, // Ajustado para mostrar mejor la región
                center: center,
                mapTypeControl: true,
                streetViewControl: true,
                fullscreenControl: true
            });

            // Array para almacenar los marcadores
            const markers = [];
            const bounds = new google.maps.LatLngBounds();

            // Obtener las propiedades del PHP
            const propiedades = <?php 
                $propiedades = [];
                while($propiedad = $resultado->fetch_assoc()) {
                    $propiedades[] = $propiedad;
                }
                echo json_encode($propiedades);
            ?>;

            // Crear marcadores para cada propiedad
            propiedades.forEach(propiedad => {
                const position = {
                    lat: parseFloat(propiedad.latitud),
                    lng: parseFloat(propiedad.longitud)
                };

                // Crear el marcador
                const marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: propiedad.titulo,
                    animation: google.maps.Animation.DROP
                });

                // Crear el contenido del InfoWindow
                const contentString = `
                    <div class="info-window-content">
                        <div class="property-info">
                            <div class="property-title">${propiedad.titulo}</div>
                            <div class="property-category">${propiedad.nombre_categoria}</div>
                            <div class="property-location">${propiedad.localidad}</div>
                            <div class="property-link">
                                <a href="propiedad.php?id=${propiedad.id}" class="btn btn-primary btn-sm w-100">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                `;

                // Crear el InfoWindow
                const infowindow = new google.maps.InfoWindow({
                    content: contentString,
                    maxWidth: 300
                });

                // Agregar evento click al marcador
                marker.addListener("click", () => {
                    infowindow.open(map, marker);
                });

                // Agregar el marcador al array y ajustar los límites del mapa
                markers.push(marker);
                bounds.extend(position);
            });

            // Crear el clusterer con configuración básica
            new markerclusterer.MarkerClusterer({
                map,
                markers,
                algorithm: new markerclusterer.GridAlgorithm({
                    gridSize: 60
                })
            });

            // Ajustar el zoom para mostrar todos los marcadores
            if (markers.length > 0) {
                map.fitBounds(bounds);
            } else {
                // Si no hay marcadores, mantener el centro en la región
                map.setCenter(center);
                map.setZoom(7);
            }
        }

        // Inicializar el mapa cuando se carga la página
        window.onload = initMap;
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

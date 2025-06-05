<?php
require_once '../config/config.php';
$includeDataTablesStyles = true;
require_once 'includes/head.php';

// Verificar si la columna 'vendida' existe, si no, crearla
try {
    $db->query("SELECT vendida FROM propiedades LIMIT 1");
} catch (Exception $e) {
    // La columna no existe, la creamos
    $db->query("ALTER TABLE propiedades ADD COLUMN vendida TINYINT(1) DEFAULT 0");
    $db->query("CREATE INDEX idx_vendida ON propiedades(vendida)");
}

// Obtener las propiedades vendidas con su primera imagen
$query = "SELECT p.*, tp.nombre_categoria, 
         (SELECT ruta_imagen FROM imagenes_propiedades WHERE id_propiedad = p.id LIMIT 1) as imagen_principal
         FROM propiedades p 
         LEFT JOIN tipos_propiedad tp ON p.categoria = tp.id 
         WHERE p.vendida = 1
         ORDER BY p.id DESC";

$resultado = $db->query($query);
?>

<div class="container-fluid px-4">
    <!-- Header simplificado y mejorado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle fa-2x text-success me-3"></i>
            <div>
                <h1 class="h3 mb-0">Propiedades Vendidas</h1>
                <p class="text-muted mb-0">Listado de propiedades vendidas</p>
            </div>
        </div>
        <div>
            <a href="propiedades.php" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Volver a Propiedades
            </a>
        </div>
    </div>

    <!-- Tabla de propiedades vendidas -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaVendidas" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px">ID</th>
                            <th style="width: 100px">Imagen</th>
                            <th>Título</th>
                            <th style="width: 150px">Categoría</th>
                            <th style="width: 150px">Localidad</th>
                            <th class="text-center" style="width: 100px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($propiedad = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center align-middle"><?php echo $propiedad['id']; ?></td>
                                <td>
                                    <div class="property-image-wrapper">
                                        <?php if (!empty($propiedad['imagen_principal'])): ?>
                                            <img src="../<?php echo htmlspecialchars($propiedad['imagen_principal']); ?>"
                                                alt="Imagen de propiedad"
                                                class="property-image">
                                        <?php else: ?>
                                            <img src="../assets/img/no-image.jpg"
                                                alt="Sin imagen"
                                                class="property-image">
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="align-middle fw-semibold"><?php echo htmlspecialchars($propiedad['titulo']); ?></td>
                                <td class="align-middle">
                                    <span class="badge rounded-pill bg-success text-white">
                                        <?php echo htmlspecialchars($propiedad['nombre_categoria']); ?>
                                    </span>
                                </td>
                                <td class="align-middle"><?php echo htmlspecialchars($propiedad['localidad']); ?></td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="verDetalles(<?php echo $propiedad['id']; ?>)"
                                        title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .property-image-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .property-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .property-image:hover {
        transform: scale(1.1);
    }

    .badge {
        font-weight: 500;
        font-size: 0.85rem;
        padding: 0.5em 1em;
    }

    .table> :not(caption)>*>* {
        padding: 1rem 0.75rem;
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    /* Estilos para DataTables */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }

    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        font-weight: 500;
        color: #555;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #e9ecef !important;
        border-color: #dee2e6 !important;
        color: var(--color-accent) !important;
    }

    .dataTables_wrapper .dataTables_length select {
        min-width: 80px;
        margin: 0 5px;
    }

    .dataTables_info {
        color: #666;
        padding-top: 0.5rem;
    }

    /* Estilos para el carrusel */
    .carousel-item img {
        max-height: 400px;
        object-fit: contain;
        background-color: #f8f9fa;
    }

    .carousel-control-prev,
    .carousel-control-next {
        background-color: rgba(0, 0, 0, 0.2);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.8;
    }

    .carousel-control-prev {
        left: 15px;
    }

    .carousel-control-next {
        right: 15px;
    }

    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        opacity: 1;
        background-color: rgba(0, 0, 0, 0.3);
    }

    /* Estilos para el modal de detalles */
    .property-details {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .property-details h4 {
        color: #2c3e50;
        font-weight: 600;
    }

    .property-details .carousel-item img {
        border-radius: 0.5rem;
        height: 350px;
        object-fit: cover;
        width: 100%;
    }

    .carousel-control-prev,
    .carousel-control-next {
        background-color: rgba(0, 0, 0, 0.2);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.8;
    }

    .carousel-control-prev {
        left: 15px;
    }

    .carousel-control-next {
        right: 15px;
    }

    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        background-color: rgba(0, 0, 0, 0.4);
    }

    .carousel-indicators {
        bottom: -40px;
    }

    .carousel-indicators [data-bs-target] {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin: 0 5px;
        background-color: #95a5a6;
    }

    .carousel-indicators .active {
        background-color: #2c3e50;
    }

    @media (max-width: 991.98px) {
        .property-details .carousel-item img {
            height: 280px;
        }
    }

    .property-details {
        padding: 20px;
    }

    .property-details .image-gallery {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .property-details .image-gallery img {
        width: 100%;
        height: 400px;
        object-fit: cover;
    }

    .property-details .info-card {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        border-left: 4px solid var(--color-accent);
    }

    .property-details .info-title {
        color: var(--color-dark);
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .property-details .info-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 12px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .property-details .info-item:hover {
        background-color: #f8f9fa;
    }

    .property-details .info-item i {
        color: var(--color-accent);
        font-size: 1.2rem;
        width: 24px;
        text-align: center;
    }

    .property-details .caracteristicas-list,
    .property-details .servicios-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
        margin-top: 15px;
    }

    .property-details .caracteristica-item,
    .property-details .servicio-item {
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .property-details .mapa-container {
        border-radius: 12px;
        overflow: hidden;
        margin-top: 20px;
    }

    /* Estilos para el carrusel */
    .carousel-inner {
        border-radius: 12px;
        overflow: hidden;
    }

    .carousel-control-prev,
    .carousel-control-next {
        width: 40px;
        height: 40px;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        margin: 0 20px;
    }

    .carousel-indicators {
        margin-bottom: -30px;
    }

    .carousel-indicators [data-bs-target] {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: var(--color-accent);
    }
</style>

<!-- Modal para ver detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="modalDetallesLabel">
                    <i class="fas fa-home text-primary me-2"></i>Detalles de la Propiedad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body property-details">
                <!-- El contenido se cargará dinámicamente aquí -->
            </div>
        </div>
    </div>
</div>

<script>
function verDetalles(id) {
    // Mostrar loading dentro del modal
    $('.modal-body.property-details').html(`
        <div class="d-flex justify-content-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `);

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
    modal.show();

    // Cargar datos
    $.ajax({
        url: 'controllers/controller_propiedades.php',
        type: 'GET',
        data: { action: 'obtener', id: id },
        success: function(response) {
            let data = typeof response === 'string' ? JSON.parse(response) : response;
            
            if (data.success) {
                let html = generarContenidoModal(data.data);
                $('.modal-body.property-details').html(html);
                
                // Inicializar el carrusel después de cargar el contenido
                if (data.data.imagenes && data.data.imagenes.length > 1) {
                    new bootstrap.Carousel(document.getElementById('carouselPropiedad'));
                }
            } else {
                mostrarErrorModal('No se pudo cargar la información de la propiedad');
            }
        },
        error: function() {
            mostrarErrorModal('Error de conexión al servidor');
        }
    });
}

function generarContenidoModal(propiedad) {
    return `
        <div class="row">
            <div class="col-lg-7">
                <!-- Carrusel de imágenes -->
                <div class="image-gallery">
                    <div id="carouselPropiedad" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            ${generarSlidesCarrusel(propiedad.imagenes)}
                        </div>
                        ${propiedad.imagenes && propiedad.imagenes.length > 1 ? `
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselPropiedad" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselPropiedad" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        ` : ''}
                    </div>
                </div>
                
                <!-- Información General -->
                <div class="info-card">
                    <div class="info-title">
                        <i class="fas fa-info-circle"></i>
                        ${propiedad.titulo}
                    </div>
                    <div class="info-content">
                        <div class="info-item">
                            <i class="fas fa-tag"></i>
                            <div>
                                <strong>Categoría:</strong> ${propiedad.nombre_categoria || 'No especificada'}
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Localidad:</strong> ${propiedad.localidad || 'No especificada'}
                            </div>
                        </div>
                        ${propiedad.tamanio ? `
                            <div class="info-item">
                                <i class="fas fa-ruler-combined"></i>
                                <div>
                                    <strong>Tamaño:</strong> ${propiedad.tamanio}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5">
                ${generarSeccionCaracteristicas(propiedad.caracteristicas)}
                ${generarSeccionServicios(propiedad.servicios)}
                ${generarSeccionUbicacion(propiedad.ubicacion, propiedad.mapa)}
            </div>
        </div>
    `;
}

function generarSlidesCarrusel(imagenes) {
    if (!imagenes || imagenes.length === 0) {
        return `
            <div class="carousel-item active">
                <img src="../assets/img/no-image.jpg" class="d-block w-100" alt="Sin imagen">
            </div>
        `;
    }
    
    return imagenes.map((img, index) => `
        <div class="carousel-item ${index === 0 ? 'active' : ''}">
            <img src="../${img.ruta_imagen}" class="d-block w-100" alt="Imagen propiedad">
        </div>
    `).join('');
}

function generarSeccionCaracteristicas(caracteristicas) {
    if (!caracteristicas) return '';
    
    const items = caracteristicas.split(',').map(c => c.trim()).filter(c => c);
    if (items.length === 0) return '';

    return `
        <div class="info-card">
            <div class="info-title">
                <i class="fas fa-clipboard-list"></i>
                Características
            </div>
            <div class="caracteristicas-list">
                ${items.map(c => `
                    <div class="caracteristica-item">
                        <i class="fas fa-check text-success"></i>
                        <span>${c}</span>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

function generarSeccionServicios(servicios) {
    if (!servicios) return '';
    
    const items = servicios.split(',').map(s => s.trim()).filter(s => s);
    if (items.length === 0) return '';

    return `
        <div class="info-card">
            <div class="info-title">
                <i class="fas fa-concierge-bell"></i>
                Servicios
            </div>
            <div class="servicios-list">
                ${items.map(s => `
                    <div class="servicio-item">
                        <i class="fas fa-check-circle text-success"></i>
                        <span>${s}</span>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

function generarSeccionUbicacion(ubicacion, mapa) {
    if (!ubicacion && !mapa) return '';

    return `
        <div class="info-card">
            <div class="info-title">
                <i class="fas fa-map-marker-alt"></i>
                Ubicación
            </div>
            ${ubicacion ? `<p class="mb-3">${ubicacion}</p>` : ''}
            ${mapa ? `
                <div class="mapa-container">
                    ${mapa}
                </div>
            ` : ''}
        </div>
    `;
}

function mostrarErrorModal(mensaje) {
    $('.modal-body.property-details').html(`
        <div class="alert alert-danger m-4">
            <i class="fas fa-exclamation-circle me-2"></i>
            ${mensaje}
        </div>
    `);
}

// Inicializar DataTable
$(document).ready(function() {
    $('#tablaVendidas').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
            lengthMenu: 'Mostrar _MENU_ registros por página',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            infoEmpty: 'No hay registros disponibles',
            search: 'Buscar:',
            paginate: {
                first: '«',
                previous: '‹',
                next: '›',
                last: '»'
            }
        },
        pageLength: 25,
        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100]
        ],
        dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip',
        order: [
            [0, 'desc']
        ],
        columnDefs: [{
            targets: [0, 5],
            orderable: false
        }]
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
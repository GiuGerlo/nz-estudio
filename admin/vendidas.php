<?php
require_once '../config/config.php';
$includeDataTablesStyles = true;
require_once 'includes/head.php';

// Obtener las propiedades vendidas
$query = "SELECT p.*, tp.nombre_categoria, 
         (SELECT ruta_imagen FROM imagenes_propiedades WHERE id_propiedad = p.id LIMIT 1) as imagen_principal
         FROM propiedades p 
         LEFT JOIN tipos_propiedad tp ON p.categoria = tp.id 
         WHERE vendida = 1
         ORDER BY p.id DESC";
$resultado = $db->query($query);
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-building fa-2x text-primary me-3"></i>
            <div>
                <h1 class="h3 mb-0">Propiedades Vendidas</h1>
                <p class="text-muted mb-0">Listado de propiedades marcadas como vendidas</p>
            </div>
        </div>
    </div>

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
                            <th class="text-center" style="width: 120px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($propiedad = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center align-middle"><?php echo $propiedad['id']; ?></td>
                                <td>
                                    <div class="property-image-wrapper">
                                        <?php if ($propiedad['imagen_principal']): ?>
                                            <img src="../<?php echo $propiedad['imagen_principal']; ?>"
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
                                    <span class="badge rounded-pill bg-info text-white">
                                        <?php echo htmlspecialchars($propiedad['nombre_categoria']); ?>
                                    </span>
                                </td>
                                <td class="align-middle"><?php echo htmlspecialchars($propiedad['localidad']); ?></td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="verPropiedad(<?php echo $propiedad['id']; ?>)"
                                                title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmarEliminacionVendida(<?php echo $propiedad['id']; ?>)"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal solo lectura para ver propiedad -->
<div class="modal fade" id="modalVerPropiedad" tabindex="-1" aria-labelledby="modalVerPropiedadLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalVerPropiedadLabel">Detalle de Propiedad</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div id="detallePropiedad">
          <!-- Aquí se cargan los datos por JS -->
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Estilos refinados */
.property-image-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

.btn-group .btn {
    padding: 0.375rem 0.75rem;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}

.table tbody tr {
    transition: background-color 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

/* Personalización mejorada de DataTables */
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
</style>

<script>
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
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip',
        order: [[0, 'desc']],
        columnDefs: [
            { targets: [1], orderable: false },
            { targets: [5], orderable: false, searchable: false }
        ]
    });
});

// Doble confirmación para eliminar
function confirmarEliminacionVendida(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción eliminará la propiedad vendida.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: '¿Eliminar definitivamente?',
                text: "No podrás recuperar esta propiedad.",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }).then((res2) => {
                if (res2.isConfirmed) {
                    window.location.href = 'controllers/controller_propiedades.php?action=eliminar&id=' + id;
                }
            });
        }
    });
}

// Visualizar datos en modal solo lectura
function verPropiedad(id) {
    $.ajax({
        url: 'controllers/controller_propiedades.php',
        type: 'GET',
        data: {
            action: 'obtener',
            id: id
        },
        success: function(response) {
            let data = typeof response === 'string' ? JSON.parse(response) : response;
            if(data.success) {
                let p = data.data;
                // Usar la misma lógica de la tabla para la imagen principal
                let imagen = p.imagen_principal ? '../' + p.imagen_principal : '../assets/img/no-image.jpg';
                let html = `
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-2">${p.titulo}</h5>
                            <p><strong>Categoría:</strong> ${p.categoria_nombre || ''}</p>
                            <p><strong>Localidad:</strong> ${p.localidad}</p>
                            <p><strong>Ubicación:</strong> ${p.ubicacion || ''}</p>
                            <p><strong>Tamaño:</strong> ${p.tamanio || ''}</p>
                            <p><strong>Servicios:</strong> ${p.servicios || ''}</p>
                            <p><strong>Características:</strong> ${p.caracteristicas || ''}</p>
                            <p><strong>Mapa:</strong> ${p.mapa || ''}</p>
                            <p><strong>Latitud:</strong> ${p.latitud || ''}</p>
                            <p><strong>Longitud:</strong> ${p.longitud || ''}</p>
                        </div>
                    </div>
                `;
                $('#detallePropiedad').html(html);
                $('#modalVerPropiedad').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo cargar la propiedad'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al comunicarse con el servidor'
            });
        }
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
<?php
require_once '../config/config.php';
$includeDataTablesStyles = true;
require_once 'includes/head.php';

// Obtener las propiedades
$query = "SELECT p.*, tp.nombre_categoria, 
         (SELECT ruta_imagen FROM imagenes_propiedades WHERE id_propiedad = p.id LIMIT 1) as imagen_principal
         FROM propiedades p 
         LEFT JOIN tipos_propiedad tp ON p.categoria = tp.id 
         ORDER BY p.id DESC";
$resultado = $db->query($query);
?>

<div class="container-fluid px-4">
    <!-- Header simplificado y mejorado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-building fa-2x text-primary me-3"></i>
            <div>
                <h1 class="h3 mb-0">Gestión de Propiedades</h1>
                <p class="text-muted mb-0">Administra tus propiedades aquí</p>
            </div>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-custom-blue shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPropiedad">
                <i class="fas fa-plus-circle me-2"></i>Nueva Propiedad
            </button>
            <button type="button" class="btn btn-outline-primary shadow-sm" onclick="location.href='order-propiedades.php'">
                <i class="fas fa-sort me-2"></i>Ordenar
            </button>
        </div>
    </div>

    <!-- Tabla mejorada -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaPropiedades" class="table table-hover" style="width:100%">
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
                                        <button type="button" class="btn btn-outline-primary" 
                                                onclick="editarPropiedad(<?php echo $propiedad['id']; ?>)"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmarEliminacion(<?php echo $propiedad['id']; ?>)"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success" 
                                                onclick="marcarVendida(<?php echo $propiedad['id']; ?>)"
                                                title="Marcar como vendida">
                                            <i class="fas fa-check"></i>
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

<!-- Incluir el modal -->
<?php require_once 'templates/modal_propiedad.php'; ?>

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
        $('#tablaPropiedades').DataTable({
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
            pageLength: 25, // Cambiar a 25 registros por página
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]], // Opciones de registros por página
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip',
            order: [[0, 'desc']],
            columnDefs: [
                {
                    targets: [1],
                    orderable: false
                },
                {
                    targets: [5],
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Agregar manejador para el formulario
        $('#formPropiedad').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: 'controllers/controller_propiedades.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    let data = response;
                    if (typeof response === 'string') {
                        try {
                            data = JSON.parse(response);
                        } catch (e) {
                            console.error('Error parsing JSON:', e);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error en la respuesta del servidor'
                            });
                            return;
                        }
                    }

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Ha ocurrido un error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ha ocurrido un error en la comunicación con el servidor'
                    });
                }
            });
        });

        // Limpiar el formulario cuando se cierre el modal
        $('#modalPropiedad').on('hidden.bs.modal', function() {
            $('#formPropiedad')[0].reset();
            $('#preview-imagenes').html('');
            $('#propiedad_id').val('');
            $('#modalPropiedadLabel').text('Nueva Propiedad');
        });
    });

    function confirmarEliminacion(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'controllers/controller_propiedades.php?action=eliminar&id=' + id;
            }
        });
    }

    function marcarVendida(id) {
        Swal.fire({
            title: '¿Marcar como vendida?',
            text: "La propiedad se moverá a la sección de vendidas",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, marcar como vendida',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'controllers/controller_propiedades.php?action=vender&id=' + id;
            }
        });
    }

    function editarPropiedad(id) {
        $('#modalPropiedadLabel').text('Editar Propiedad');
        
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
                    $('#propiedad_id').val(data.data.id);
                    $('#titulo').val(data.data.titulo);
                    $('#categoria').val(data.data.categoria);
                    $('#localidad').val(data.data.localidad);
                    $('#ubicacion').val(data.data.ubicacion);
                    $('#tamanio').val(data.data.tamanio);
                    $('#servicios').val(data.data.servicios);
                    $('#caracteristicas').val(data.data.caracteristicas);
                    $('#mapa').val(data.data.mapa);
                    
                    // Usar la función global para actualizar el preview
                    if(data.data.imagenes) {
                        window.actualizarPreviewImagenes(data.data.imagenes);
                    }
                    
                    $('#modalPropiedad').modal('show');
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
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
    <h1 class="h3 mb-4">Gestión de Propiedades</h1>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de Propiedades</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPropiedad">
                <i class="fas fa-plus"></i> Nueva Propiedad
            </button>
        </div>
        <div class="card-body">
            <table id="tablaPropiedades" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Localidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($propiedad = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $propiedad['id']; ?></td>
                            <td>
                                <?php if ($propiedad['imagen_principal']): ?>
                                    <img src="../<?php echo $propiedad['imagen_principal']; ?>"
                                        alt="Imagen de propiedad"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="../assets/img/no-image.jpg"
                                        alt="Sin imagen"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($propiedad['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($propiedad['nombre_categoria']); ?></td>
                            <td><?php echo htmlspecialchars($propiedad['localidad']); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-info"
                                        onclick="editarPropiedad(<?php echo $propiedad['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmarEliminacion(<?php echo $propiedad['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success"
                                        onclick="marcarVendida(<?php echo $propiedad['id']; ?>)">
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

<!-- Incluir el modal -->
<?php require_once 'templates/modal_propiedad.php'; ?>

<script>
    $(document).ready(function() {
        $('#tablaPropiedades').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
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
        // Cambiar el título del modal
        $('#modalPropiedadLabel').text('Editar Propiedad');

        // Realizar la petición AJAX para obtener los datos
        $.ajax({
            url: 'controllers/controller_propiedades.php',
            type: 'GET',
            data: {
                action: 'obtener',
                id: id
            },
            success: function(response) {
                let data = typeof response === 'string' ? JSON.parse(response) : response;

                if (data.success) {
                    // Llenar el formulario con los datos
                    $('#propiedad_id').val(data.data.id);
                    $('#titulo').val(data.data.titulo);
                    $('#categoria').val(data.data.categoria);
                    $('#localidad').val(data.data.localidad);
                    $('#ubicacion').val(data.data.ubicacion);
                    $('#tamanio').val(data.data.tamanio);
                    $('#servicios').val(data.data.servicios);
                    $('#caracteristicas').val(data.data.caracteristicas);
                    $('#mapa').val(data.data.mapa);

                    // Mostrar imágenes existentes si las hay
                    if (data.data.imagenes) {
                        let previewHtml = '';
                        data.data.imagenes.forEach(imagen => {
                            previewHtml += `
                                <div class="col-md-3">
                                    <div class="card">
                                        <img src="../${imagen.ruta_imagen}" class="card-img-top" 
                                             style="height: 150px; object-fit: cover;">
                                    </div>
                                </div>`;
                        });
                        $('#preview-imagenes').html(previewHtml);
                    }

                    // Abrir el modal
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
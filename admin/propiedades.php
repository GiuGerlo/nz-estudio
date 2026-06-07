<?php
require_once '../config/config.php';
$includeDataTablesStyles = true;
require_once 'includes/head.php';

// Propiedades activas con imagen principal
$resultado = $db->query(
    "SELECT p.*, tp.nombre_categoria,
            (SELECT ruta_imagen FROM imagenes_propiedades WHERE id_propiedad = p.id ORDER BY orden ASC, id ASC LIMIT 1) AS imagen_principal
     FROM propiedades p
     LEFT JOIN tipos_propiedad tp ON p.categoria = tp.id
     WHERE vendida = 0
     ORDER BY p.id DESC"
);
?>

<div class="nz-page">

    <header class="nz-page-header">
        <div class="nz-page-title">
            <div class="nz-page-title-icon"><i class="fa-solid fa-building"></i></div>
            <div>
                <h1>Propiedades</h1>
                <p>Gestioná el catálogo activo de propiedades</p>
            </div>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="order-propiedades.php" class="nz-btn-sm nz-btn-ghost">
                <i class="fa-solid fa-arrows-up-down"></i> Ordenar
            </a>
            <button type="button" class="nz-btn-sm nz-btn-primary" data-bs-toggle="modal" data-bs-target="#modalPropiedad">
                <i class="fa-solid fa-plus"></i> Nueva propiedad
            </button>
        </div>
    </header>

    <section class="nz-card">
        <div class="nz-card-body" style="padding: var(--nz-sp-4);">
            <div class="nz-table-wrap">
                <table id="tablaPropiedades" class="table align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th style="width: 90px;">Imagen</th>
                            <th>Título</th>
                            <th style="width: 160px;">Categoría</th>
                            <th style="width: 160px;">Localidad</th>
                            <th style="width: 130px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($propiedad = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td class="nz-id" data-order="<?php echo (int)$propiedad['id']; ?>">#<?php echo (int)$propiedad['id']; ?></td>
                                <td>
                                    <?php if ($propiedad['imagen_principal']): ?>
                                        <img src="../<?php echo htmlspecialchars($propiedad['imagen_principal'], ENT_QUOTES, 'UTF-8'); ?>"
                                             alt=""
                                             style="width:52px;height:52px;border-radius:8px;object-fit:cover;border:1px solid var(--nz-border);">
                                    <?php else: ?>
                                        <div style="width:52px;height:52px;border-radius:8px;background:var(--nz-surface-3);display:grid;place-items:center;color:var(--nz-text-muted);">
                                            <i class="fa-solid fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($propiedad['titulo']); ?></td>
                                <td>
                                    <span class="nz-cat-pill">
                                        <?php echo htmlspecialchars($propiedad['nombre_categoria'] ?? '—'); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($propiedad['localidad']); ?></td>
                                <td>
                                    <div class="nz-table-actions" style="justify-content:center;width:100%;">
                                        <button type="button" class="nz-icon-btn"
                                                onclick="editarPropiedad(<?php echo (int)$propiedad['id']; ?>)"
                                                title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="button" class="nz-icon-btn nz-icon-btn--success"
                                                onclick="marcarVendida(<?php echo (int)$propiedad['id']; ?>)"
                                                title="Marcar como vendida">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                        <button type="button" class="nz-icon-btn nz-icon-btn--danger"
                                                onclick="confirmarEliminacion(<?php echo (int)$propiedad['id']; ?>)"
                                                title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?php require_once 'templates/modal_propiedad.php'; ?>

<script>
    $(document).ready(function() {
        $('#tablaPropiedades').DataTable({
            responsive: true,
            language: {
                decimal: ',',
                thousands: '.',
                emptyTable: 'No hay propiedades',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ propiedades',
                infoEmpty: 'Mostrando 0 propiedades',
                infoFiltered: '(filtrado de _MAX_ totales)',
                lengthMenu: 'Mostrar _MENU_',
                loadingRecords: 'Cargando...',
                processing: 'Procesando...',
                search: '',
                searchPlaceholder: 'Buscar propiedad...',
                zeroRecords: 'Sin resultados',
                paginate: {
                    first:    '<i class="fa-solid fa-angles-left"></i>',
                    previous: '<i class="fa-solid fa-chevron-left"></i>',
                    next:     '<i class="fa-solid fa-chevron-right"></i>',
                    last:     '<i class="fa-solid fa-angles-right"></i>'
                },
                aria: {
                    sortAscending: ': activar para orden ascendente',
                    sortDescending: ': activar para orden descendente'
                }
            },
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            dom: '<"nz-dt-top"<"nz-dt-top-left"l><"nz-dt-top-right"f>>rt<"nz-dt-bottom"<"nz-dt-bottom-left"i><"nz-dt-bottom-right"p>>',
            order: [[0, 'desc']],
            columnDefs: [
                { targets: [1], orderable: false },
                { targets: [5], orderable: false, searchable: false }
            ]
        });

        // Submit del form (crear/editar)
        $('#formPropiedad').on('submit', function(e) {
            e.preventDefault();
            const $btn = $(this).find('button[type=submit]');
            const btnHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Guardando...');

            const formData = new FormData(this);

            $.ajax({
                url: 'controllers/controller_propiedades.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    let data = typeof response === 'string' ? JSON.parse(response) : response;
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Listo!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1400
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo guardar', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error en la comunicación con el servidor', 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(btnHtml);
                }
            });
        });

        // Reset al cerrar modal
        $('#modalPropiedad').on('hidden.bs.modal', function() {
            $('#formPropiedad')[0].reset();
            $('#preview-imagenes').html('');
            $('#propiedad_id').val('');
            $('#modalPropiedadLabel').text('Nueva propiedad');
        });
    });

    function postAction(action, id, onOk) {
        $.ajax({
            url: 'controllers/controller_propiedades.php',
            type: 'POST',
            data: { action: action, id: id },
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                if (data.success) onOk(data);
                else Swal.fire('Error', data.message || 'Operación fallida', 'error');
            },
            error: function() {
                Swal.fire('Error', 'No se pudo comunicar con el servidor', 'error');
            }
        });
    }

    function confirmarEliminacion(id) {
        Swal.fire({
            title: '¿Eliminar propiedad?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((r) => {
            if (r.isConfirmed) {
                postAction('eliminar', id, () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminada',
                        showConfirmButton: false,
                        timer: 1200
                    }).then(() => location.reload());
                });
            }
        });
    }

    function marcarVendida(id) {
        Swal.fire({
            title: '¿Marcar como vendida?',
            text: 'La propiedad se moverá a la sección de vendidas.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, marcar',
            cancelButtonText: 'Cancelar'
        }).then((r) => {
            if (r.isConfirmed) {
                postAction('vender', id, () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Marcada como vendida',
                        showConfirmButton: false,
                        timer: 1200
                    }).then(() => location.reload());
                });
            }
        });
    }

    function editarPropiedad(id) {
        $('#modalPropiedadLabel').text('Editar propiedad');

        $.ajax({
            url: 'controllers/controller_propiedades.php',
            type: 'GET',
            data: { action: 'obtener', id: id },
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                if (data.success) {
                    $('#propiedad_id').val(data.data.id);
                    $('#titulo').val(data.data.titulo);
                    $('#categoria').val(data.data.categoria);
                    $('#localidad').val(data.data.localidad);
                    $('#ubicacion').val(data.data.ubicacion);
                    $('#tamanio').val(data.data.tamanio);
                    $('#servicios').val(data.data.servicios);
                    $('#caracteristicas').val(data.data.caracteristicas);
                    $('#mapa').val(data.data.mapa);
                    $('#latitud').val(data.data.latitud);
                    $('#longitud').val(data.data.longitud);

                    if (data.data.imagenes && window.actualizarPreviewImagenes) {
                        window.actualizarPreviewImagenes(data.data.imagenes);
                    }

                    new bootstrap.Modal(document.getElementById('modalPropiedad')).show();
                } else {
                    Swal.fire('Error', data.message || 'No se pudo cargar la propiedad', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
            }
        });
    }
</script>

<?php require_once 'includes/footer.php'; ?>

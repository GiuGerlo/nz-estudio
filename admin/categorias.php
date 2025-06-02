<?php
require_once '../config/config.php';

// Definir variable para controlar si se incluyen los estilos de DataTables
$includeDataTablesStyles = true;

// Incluir cabecera
include_once 'includes/head.php';

// Obtener todas las categorías usando el controlador
require_once 'controllers/controller_categorias.php';
$controller = new ControllerCategorias($db);
$resultado = $controller->obtenerCategorias();
$categorias = $resultado['data'] ?? null;

// Agregar scripts adicionales para DataTables
$additionalScripts = '
<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
';
?>

<!-- Contenido principal -->
<div class="container-fluid px-0">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2>Gestión de Categorías</h2>
            <button type="button" class="btn btn-custom-blue" data-bs-toggle="modal" data-bs-target="#categoriaModal">
                <i class="fas fa-plus-circle me-2"></i> Nueva Categoría
            </button>
        </div>
    </div>

    <!-- Tabla de categorías -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Categorías de Propiedades</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabla-categorias" class="table table-striped table-hover dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Propiedades</th>
                            <th data-orderable="false">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($categorias && $categorias->num_rows > 0): ?>
                            <?php while ($categoria = $categorias->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $categoria['id']; ?></td>
                                    <td><?php echo htmlspecialchars($categoria['nombre_categoria']); ?></td>
                                    <td><?php echo $categoria['total_propiedades']; ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-custom-blue editar-categoria"
                                                data-id="<?php echo $categoria['id']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($categoria['nombre_categoria']); ?>"
                                                title="Editar categoría">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($categoria['total_propiedades'] == 0): ?>
                                                <button class="btn btn-sm btn-custom-red eliminar-categoria"
                                                    data-id="<?php echo $categoria['id']; ?>"
                                                    data-nombre="<?php echo htmlspecialchars($categoria['nombre_categoria']); ?>"
                                                    title="Eliminar categoría">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-custom-red" disabled 
                                                    title="No se puede eliminar porque tiene propiedades asociadas">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay categorías disponibles</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar categoría -->
<div class="modal fade" id="categoriaModal" tabindex="-1" aria-labelledby="categoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoriaModalLabel">Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCategoria">
                <div class="modal-body">
                    <input type="hidden" id="categoria_id" name="id" value="">
                    <input type="hidden" id="accion" name="accion" value="crear">

                    <div class="mb-3">
                        <label for="nombre_categoria" class="form-label">Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-custom-blue" id="btnGuardar">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Incluir SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php echo $additionalScripts; ?>

<!-- Script para gestionar categorías -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos del DOM
        const formCategoria = document.getElementById('formCategoria');
        const categoriaModal = new bootstrap.Modal(document.getElementById('categoriaModal'));
        const modalTitle = document.getElementById('categoriaModalLabel');
        const btnGuardar = document.getElementById('btnGuardar');
        
        // Inicializar DataTables
        const tablaCategorias = $('#tabla-categorias').DataTable({
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="fas fa-download me-1"></i> Exportar',
                    className: 'btn-custom-blue',
                    buttons: [
                        {
                            extend: 'excel',
                            text: '<i class="fas fa-file-excel me-1"></i> Excel',
                            exportOptions: {
                                columns: [0, 1, 2]
                            },
                            title: 'Categorías de Propiedades'
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                            exportOptions: {
                                columns: [0, 1, 2]
                            },
                            title: 'Categorías de Propiedades'
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print me-1"></i> Imprimir',
                            exportOptions: {
                                columns: [0, 1, 2]
                            },
                            title: 'Categorías de Propiedades'
                        }
                    ]
                }
            ],
            order: [[0, 'asc']],
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 3 }
            ]
        });
        
        // Agregar botones de exportación a la tabla
        new $.fn.dataTable.Buttons(tablaCategorias, {
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="fas fa-download me-1"></i> Exportar',
                    className: 'btn-custom-blue',
                    buttons: [
                        'excel', 'pdf', 'print'
                    ]
                }
            ]
        });
        
        tablaCategorias.buttons().container().appendTo('#tabla-categorias_wrapper .dt-buttons');
        
        // Mostrar hora Argentina (UTC-3)
        actualizarHora();

        // Manejar envío del formulario
        formCategoria.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(formCategoria);

            fetch('controllers/controller_categorias.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.estado === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.mensaje,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.mensaje
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ha ocurrido un error al procesar la solicitud'
                    });
                });
        });

        // Abrir modal para editar
        document.querySelectorAll('.editar-categoria').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');

                document.getElementById('categoria_id').value = id;
                document.getElementById('nombre_categoria').value = nombre;
                document.getElementById('accion').value = 'actualizar';
                modalTitle.textContent = 'Editar Categoría';
                btnGuardar.textContent = 'Actualizar';

                categoriaModal.show();
            });
        });

        // Abrir modal para crear
        document.querySelector('[data-bs-target="#categoriaModal"]').addEventListener('click', function() {
            formCategoria.reset();
            document.getElementById('categoria_id').value = '';
            document.getElementById('accion').value = 'crear';
            modalTitle.textContent = 'Nueva Categoría';
            btnGuardar.textContent = 'Crear';
        });

        // Eliminar categoría con doble confirmación
        document.querySelectorAll('.eliminar-categoria').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');

                Swal.fire({
                    title: '¿Está seguro?',
                    text: `¿Desea eliminar la categoría "${nombre}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Segunda confirmación
                        Swal.fire({
                            title: 'Confirmar eliminación',
                            text: 'Esta acción no se puede deshacer',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, estoy seguro',
                            cancelButtonText: 'Cancelar'
                        }).then((finalResult) => {
                            if (finalResult.isConfirmed) {
                                const formData = new FormData();
                                formData.append('accion', 'eliminar');
                                formData.append('id', id);

                                fetch('controllers/controller_categorias.php', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.estado === 'success') {
                                            Swal.fire({
                                                icon: 'success',
                                                title: '¡Eliminado!',
                                                text: data.mensaje,
                                                timer: 2000,
                                                showConfirmButton: false
                                            }).then(() => {
                                                window.location.reload();
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: data.mensaje
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Ha ocurrido un error al procesar la solicitud'
                                        });
                                    });
                            }
                        });
                    }
                });
            });
        });

        // Función para actualizar la hora de Argentina (UTC-3)
        function actualizarHora() {
            const ahora = new Date();

            // Ajustar a UTC-3 (Argentina)
            const horaArgentina = new Date(ahora.getTime());

            const opciones = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
                timeZone: 'America/Argentina/Buenos_Aires'
            };

            const horaFormateada = horaArgentina.toLocaleTimeString('es-AR', opciones);

            // Actualizar el elemento en el DOM
            const elementoHora = document.getElementById('hora-actual');
            if (elementoHora) {
                elementoHora.textContent = horaFormateada;
            }

            // Actualizar cada segundo
            setTimeout(actualizarHora, 1000);
        }
    });
</script>

<?php
// Agregar estilos personalizados para DataTables
?>
<style>
    /* Estilos personalizados para DataTables */
    .dataTables_wrapper .dataTables_length select {
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 0.25rem;
        margin: 0 2px;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--custom-blue);
        border-color: var(--custom-blue);
        color: white !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
        background: #e9ecef;
        border-color: #dee2e6;
        color: #212529 !important;
    }
    
    .dataTables_wrapper .dt-buttons .btn {
        margin-right: 5px;
    }
    
    /* Estilos responsivos */
    @media (max-width: 767px) {
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            text-align: left;
            margin-bottom: 10px;
        }
        
        .dataTables_wrapper .dt-buttons {
            margin-bottom: 10px;
        }
    }
</style>

<?php
// Incluir pie de página
include_once 'includes/footer.php';
?>
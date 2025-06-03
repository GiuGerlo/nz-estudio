<!-- Modal Propiedad -->
<div class="modal fade" id="modalPropiedad" tabindex="-1" aria-labelledby="modalPropiedadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalPropiedadLabel">
                    <i class="fas fa-building me-2"></i>Nueva Propiedad
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPropiedad" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="propiedad_id">

                    <!-- Campos del formulario con estilos mejorados -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                                <label for="titulo"><i class="fas fa-heading me-2"></i>Título</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="categoria" name="categoria" required>
                                    <option value="">Seleccionar categoría</option>
                                    <?php
                                    $categorias = $db->query("SELECT * FROM tipos_propiedad ORDER BY nombre_categoria");
                                    while ($cat = $categorias->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre_categoria']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                                <label for="categoria"><i class="fas fa-tag me-2"></i>Categoría</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="localidad" name="localidad" required>
                                <label for="localidad"><i class="fas fa-map-marker-alt me-2"></i>Localidad</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="tamanio" name="tamanio">
                                <label for="tamanio"><i class="fas fa-ruler me-2"></i>Tamaño</label>
                            </div>
                        </div>
                    </div>

                    <!-- Campos de texto con estilo mejorado -->
                    <div class="card card-accent-blue mb-4">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-3 text-muted"><i class="fas fa-info-circle me-2"></i>Detalles de la Propiedad</h6>

                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="ubicacion" name="ubicacion" style="height: 100px"></textarea>
                                <label for="ubicacion"><i class="fas fa-map me-2"></i>Ubicación</label>
                            </div>

                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="servicios" name="servicios" style="height: 100px"></textarea>
                                <label for="servicios"><i class="fas fa-concierge-bell me-2"></i>Servicios</label>
                                <small class="text-muted">Separar servicios con comas</small>
                            </div>

                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="caracteristicas" name="caracteristicas" style="height: 100px"></textarea>
                                <label for="caracteristicas"><i class="fas fa-list me-2"></i>Características</label>
                                <small class="text-muted">Separar características con comas</small>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Mapa -->
                    <div class="card card-accent-green mb-4">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-3 text-muted"><i class="fas fa-map-marked-alt me-2"></i>Ubicación en Mapa</h6>
                            <div class="form-floating">
                                <textarea class="form-control" id="mapa" name="mapa" style="height: 120px"></textarea>
                                <label for="mapa">iframe de Google Maps</label>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Imágenes -->
                    <div class="card card-accent-purple">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-3 text-muted"><i class="fas fa-images me-2"></i>Imágenes de la Propiedad</h6>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-upload"></i></span>
                                    <input type="file" class="form-control" id="imagenes" name="imagenes[]" multiple accept="image/*">
                                </div>
                                <small class="text-muted">Seleccione una o más imágenes</small>
                            </div>
                            <div id="preview-imagenes" class="mt-3 row g-3"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-custom-blue">
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewDiv = document.getElementById('preview-imagenes');
        let existingImages = [];

        // Función para actualizar el preview de imágenes existentes (de la base de datos)
        function actualizarPreview(imagenes) {
            existingImages = imagenes;
            renderPreview();
        }

        // Renderiza solo las imágenes existentes (de la base de datos)
        function renderPreview() {
            let previewHtml = '';
            existingImages.forEach((imagen, index) => {
                previewHtml += `
                <div class="col-md-3" id="imagen-${index}">
                    <div class="card">
                        <img src="../${imagen.ruta_imagen}" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2">
                            <button type="button" class="btn btn-danger btn-sm w-100" 
                                    onclick="confirmarEliminarImagen(${imagen.id}, ${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
            });
            previewDiv.innerHTML = previewHtml;
        }

        // Manejar nuevas imágenes seleccionadas (solo agrega previews de nuevas imágenes)
        document.getElementById('imagenes').addEventListener('change', function(e) {
            // Primero renderiza las imágenes existentes
            renderPreview();
            // Luego agrega las nuevas imágenes seleccionadas
            for (let file of this.files) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'col-md-3';
                    div.innerHTML = `
                    <div class="card">
                        <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2">
                            <span class="badge bg-info w-100">Nueva imagen</span>
                        </div>
                    </div>
                `;
                    previewDiv.appendChild(div);
                }
                reader.readAsDataURL(file);
            }
        });

        // Exponer funciones necesarias globalmente
        window.actualizarPreviewImagenes = actualizarPreview;
        window.confirmarEliminarImagen = function(idImagen, indexImagen) {
            const totalImagenes = document.querySelectorAll('#preview-imagenes .col-md-3').length;
            if (totalImagenes <= 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe mantener al menos una imagen'
                });
                return;
            }
            Swal.fire({
                title: '¿Eliminar imagen?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    eliminarImagen(idImagen, indexImagen);
                }
            });
        }

        function eliminarImagen(idImagen, indexImagen) {
            $.ajax({
                url: 'controllers/controller_propiedades.php',
                type: 'POST',
                data: {
                    action: 'eliminar_imagen',
                    imagen_id: idImagen
                },
                success: function(response) {
                    let data = typeof response === 'string' ? JSON.parse(response) : response;
                    if (data.success) {
                        document.getElementById(`imagen-${indexImagen}`).remove();
                        // También eliminar del array de imágenes existentes
                        existingImages.splice(indexImagen, 1);
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
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
    });
</script>
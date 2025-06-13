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
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="latitud" name="latitud" placeholder="Latitud">
                                        <label for="latitud"><i class="fas fa-map-marker-alt me-2"></i>Latitud</label>
                                        <small class="text-muted">Ejemplo: -32.8768202575293</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="longitud" name="longitud" placeholder="Longitud">
                                        <label for="longitud"><i class="fas fa-map-marker-alt me-2"></i>Longitud</label>
                                        <small class="text-muted">Ejemplo: -61.026038894990506</small>
                                    </div>
                                </div>
                            </div>

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

        // Función para renderizar el preview
        function renderPreview() {
            previewDiv.innerHTML = '';
            existingImages.forEach((imagen, index) => {
                const col = document.createElement('div');
                col.className = 'col-md-3';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="../${imagen.ruta_imagen}" class="img-thumbnail" alt="Preview">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                                onclick="eliminarImagen(${imagen.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                previewDiv.appendChild(col);
            });
        }

        // Función para eliminar imagen existente
        window.eliminarImagen = function(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'controllers/controller_propiedades.php',
                        type: 'POST',
                        data: {
                            action: 'eliminar_imagen',
                            id: id
                        },
                        success: function(response) {
                            let data = typeof response === 'string' ? JSON.parse(response) : response;
                            if(data.success) {
                                existingImages = existingImages.filter(img => img.id !== id);
                                renderPreview();
                                Swal.fire('¡Eliminado!', 'La imagen ha sido eliminada.', 'success');
                            } else {
                                Swal.fire('Error', data.message || 'Error al eliminar la imagen', 'error');
                            }
                        }
                    });
                }
            });
        };

        // Preview de nuevas imágenes
        document.getElementById('imagenes').addEventListener('change', function(e) {
            const files = e.target.files;
            previewDiv.innerHTML = '';

            for(let i = 0; i < files.length; i++) {
                const file = files[i];
                if(file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    const col = document.createElement('div');
                    col.className = 'col-md-3';

                    reader.onload = function(e) {
                        col.innerHTML = `
                            <div class="position-relative">
                                <img src="${e.target.result}" class="img-thumbnail" alt="Preview">
                            </div>
                        `;
                    };

                    reader.readAsDataURL(file);
                    previewDiv.appendChild(col);
                }
            }
        });

        // Función global para actualizar el preview de imágenes
        window.actualizarPreviewImagenes = function(imagenes) {
            actualizarPreview(imagenes);
        };
    });
</script>
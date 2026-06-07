<!-- Modal Propiedad -->
<div class="modal fade" id="modalPropiedad" tabindex="-1" aria-labelledby="modalPropiedadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPropiedadLabel">
                    <i class="fa-solid fa-building"></i> Nueva propiedad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="formPropiedad" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="propiedad_id">

                    <!-- Sección: Datos básicos -->
                    <div class="nz-form-section">
                        <div class="nz-form-section-head">
                            <i class="fa-solid fa-circle-info"></i>
                            <h6>Datos básicos</h6>
                        </div>
                        <div class="nz-form-section-body">
                            <div class="nz-form-grid">
                                <div class="nz-field-group full">
                                    <label for="titulo">Título <span class="req">*</span></label>
                                    <input type="text" class="nz-input" id="titulo" name="titulo" required>
                                </div>
                                <div class="nz-field-group">
                                    <label for="categoria">Categoría <span class="req">*</span></label>
                                    <select class="nz-select" id="categoria" name="categoria" required>
                                        <option value="">Seleccionar categoría</option>
                                        <?php
                                        $categorias = $db->query("SELECT * FROM tipos_propiedad ORDER BY nombre_categoria");
                                        while ($cat = $categorias->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo (int)$cat['id']; ?>">
                                                <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="nz-field-group">
                                    <label for="localidad">Localidad</label>
                                    <input type="text" class="nz-input" id="localidad" name="localidad">
                                </div>
                                <div class="nz-field-group full">
                                    <label for="tamanio">Tamaño</label>
                                    <input type="text" class="nz-input" id="tamanio" name="tamanio" placeholder="Ej: 250 m²">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Detalles -->
                    <div class="nz-form-section">
                        <div class="nz-form-section-head">
                            <i class="fa-solid fa-align-left"></i>
                            <h6>Detalles</h6>
                        </div>
                        <div class="nz-form-section-body">
                            <div class="nz-field-group" style="margin-bottom: var(--nz-sp-4);">
                                <label for="ubicacion">Ubicación</label>
                                <textarea class="nz-textarea" id="ubicacion" name="ubicacion" rows="3"></textarea>
                            </div>
                            <div class="nz-field-group" style="margin-bottom: var(--nz-sp-4);">
                                <label for="servicios">Servicios</label>
                                <textarea class="nz-textarea" id="servicios" name="servicios" rows="3"></textarea>
                                <small class="nz-field-hint">Separar con comas (ej: Agua, Luz, Gas)</small>
                            </div>
                            <div class="nz-field-group">
                                <label for="caracteristicas">Características</label>
                                <textarea class="nz-textarea" id="caracteristicas" name="caracteristicas" rows="3"></textarea>
                                <small class="nz-field-hint">Separar con comas (ej: 3 dormitorios, Pileta, Cochera)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Ubicación en mapa -->
                    <div class="nz-form-section">
                        <div class="nz-form-section-head">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <h6>Ubicación en mapa</h6>
                        </div>
                        <div class="nz-form-section-body">
                            <div class="nz-form-grid" style="margin-bottom: var(--nz-sp-4);">
                                <div class="nz-field-group">
                                    <label for="latitud">Latitud</label>
                                    <input type="text" class="nz-input" id="latitud" name="latitud" placeholder="-32.876820">
                                    <small class="nz-field-hint">Decimal, entre -90 y 90</small>
                                </div>
                                <div class="nz-field-group">
                                    <label for="longitud">Longitud</label>
                                    <input type="text" class="nz-input" id="longitud" name="longitud" placeholder="-61.026038">
                                    <small class="nz-field-hint">Decimal, entre -180 y 180</small>
                                </div>
                            </div>
                            <div class="nz-field-group">
                                <label for="mapa">Iframe de Google Maps</label>
                                <textarea class="nz-textarea" id="mapa" name="mapa" rows="3"
                                          placeholder='<iframe src="https://www.google.com/maps/embed?..."></iframe>'></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Imágenes -->
                    <div class="nz-form-section">
                        <div class="nz-form-section-head">
                            <i class="fa-solid fa-images"></i>
                            <h6>Imágenes</h6>
                        </div>
                        <div class="nz-form-section-body">
                            <label for="imagenes" class="nz-file-drop">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <p>Click o arrastrá imágenes acá</p>
                                <small>JPG, PNG, GIF, WebP · máx 8MB c/u · 20 archivos máximo</small>
                                <input type="file" id="imagenes" name="imagenes[]" multiple accept="image/*">
                            </label>
                            <div id="preview-imagenes" class="nz-img-preview-grid"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> Cancelar
                    </button>
                    <button type="submit" class="btn nz-btn-sm nz-btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewDiv = document.getElementById('preview-imagenes');
        const fileInput  = document.getElementById('imagenes');
        const dropZone   = fileInput ? fileInput.closest('.nz-file-drop') : null;
        let existingImages = [];

        // Preview de imágenes ya guardadas (modo edición). La primera es la "principal".
        function renderPreview() {
            previewDiv.innerHTML = '';
            existingImages.forEach((imagen, idx) => {
                const wrap = document.createElement('div');
                wrap.className = 'nz-img-preview' + (idx === 0 ? ' is-main' : '');
                wrap.dataset.id = imagen.id;
                wrap.innerHTML = `
                    <img src="../${imagen.ruta_imagen}" alt="" draggable="false">
                    ${idx === 0 ? '<span class="nz-img-badge">Principal</span>' : ''}
                    <span class="nz-img-handle" title="Arrastrar para reordenar"><i class="fa-solid fa-up-down-left-right"></i></span>
                    <button type="button" class="nz-img-preview-del" title="Eliminar">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                `;
                wrap.querySelector('.nz-img-preview-del').addEventListener('click', () => eliminarImagen(imagen.id));
                previewDiv.appendChild(wrap);
            });

            // Sortable: drag-drop entre items, persistir orden en server
            if (window.Sortable && previewDiv._sortable) {
                previewDiv._sortable.destroy();
            }
            if (window.Sortable && existingImages.length > 1) {
                previewDiv._sortable = Sortable.create(previewDiv, {
                    animation: 150,
                    handle: '.nz-img-handle',
                    ghostClass: 'nz-img-ghost',
                    onEnd: function () {
                        const ids = Array.from(previewDiv.querySelectorAll('.nz-img-preview'))
                                         .map(el => parseInt(el.dataset.id, 10));
                        persistImageOrder(ids);
                    }
                });
            }
        }

        function persistImageOrder(ids) {
            $.ajax({
                url: 'controllers/controller_propiedades.php',
                type: 'POST',
                data: { action: 'update_image_order', imagenes: JSON.stringify(ids) },
                success: function (resp) {
                    const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
                    if (data.success) {
                        // Reordenar el array local y rerender para mover el badge "Principal"
                        existingImages.sort((a, b) => ids.indexOf(a.id) - ids.indexOf(b.id));
                        renderPreview();
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo guardar el orden', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo guardar el orden', 'error');
                }
            });
        }

        window.eliminarImagen = function(id) {
            Swal.fire({
                title: '¿Eliminar imagen?',
                text: 'No se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((r) => {
                if (!r.isConfirmed) return;
                $.ajax({
                    url: 'controllers/controller_propiedades.php',
                    type: 'POST',
                    data: { action: 'eliminar_imagen', id: id },
                    success: function(response) {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            existingImages = existingImages.filter(img => img.id !== id);
                            renderPreview();
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminada',
                                showConfirmButton: false,
                                timer: 1000
                            });
                        } else {
                            Swal.fire('Error', data.message || 'No se pudo eliminar', 'error');
                        }
                    }
                });
            });
        };

        // Preview de nuevas imágenes seleccionadas
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const files = e.target.files;
                previewDiv.innerHTML = '';
                existingImages = [];
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (!file.type.startsWith('image/')) continue;
                    const reader = new FileReader();
                    const wrap = document.createElement('div');
                    wrap.className = 'nz-img-preview';
                    reader.onload = function(ev) {
                        wrap.innerHTML = `<img src="${ev.target.result}" alt="">`;
                    };
                    reader.readAsDataURL(file);
                    previewDiv.appendChild(wrap);
                }
            });
        }

        // Drag & drop visual feedback
        if (dropZone) {
            ['dragenter', 'dragover'].forEach(evt => {
                dropZone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    dropZone.classList.add('is-dragover');
                });
            });
            ['dragleave', 'drop'].forEach(evt => {
                dropZone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('is-dragover');
                });
            });
        }

        // API global usada por editarPropiedad()
        window.actualizarPreviewImagenes = function(imagenes) {
            existingImages = imagenes;
            renderPreview();
        };
    });
</script>

<!-- Modal Propiedad -->
<div class="modal fade" id="modalPropiedad" tabindex="-1" aria-labelledby="modalPropiedadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPropiedadLabel">Nueva Propiedad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPropiedad" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="propiedad_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                        <div class="col-md-6">
                            <label for="categoria" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="">Seleccionar categoría</option>
                                <?php
                                $categorias = $db->query("SELECT * FROM tipos_propiedad ORDER BY nombre_categoria");
                                while ($cat = $categorias->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre_categoria']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="localidad" class="form-label">Localidad</label>
                            <input type="text" class="form-control" id="localidad" name="localidad" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tamanio" class="form-label">Tamaño</label>
                            <input type="text" class="form-control" id="tamanio" name="tamanio">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Ubicación</label>
                        <textarea class="form-control" id="ubicacion" name="ubicacion" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="servicios" class="form-label">Servicios</label>
                        <textarea class="form-control" id="servicios" name="servicios" rows="3"></textarea>
                        <small class="text-muted">Separar servicios con comas</small>
                    </div>

                    <div class="mb-3">
                        <label for="caracteristicas" class="form-label">Características</label>
                        <textarea class="form-control" id="caracteristicas" name="caracteristicas" rows="3"></textarea>
                        <small class="text-muted">Separar características con comas</small>
                    </div>

                    <div class="mb-3">
                        <label for="mapa" class="form-label">Mapa (iframe de Google Maps)</label>
                        <textarea class="form-control" id="mapa" name="mapa" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="imagenes" class="form-label">Imágenes</label>
                        <input type="file" class="form-control" id="imagenes" name="imagenes[]" multiple accept="image/*">
                        <div id="preview-imagenes" class="mt-2 row g-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('imagenes').addEventListener('change', function(e) {
        const previewDiv = document.getElementById('preview-imagenes');
        previewDiv.innerHTML = '';

        for (let file of this.files) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'col-md-3';
                div.innerHTML = `
                <div class="card">
                    <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                </div>
            `;
                previewDiv.appendChild(div);
            }
            reader.readAsDataURL(file);
        }
    });

    // Función para cargar datos en el modal para edición
    function cargarDatosPropiedad(id) {
        fetch(`controllers/controller_propiedades.php?action=obtener&id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('propiedad_id').value = data.id;
                document.getElementById('titulo').value = data.titulo;
                document.getElementById('categoria').value = data.categoria;
                document.getElementById('localidad').value = data.localidad;
                document.getElementById('tamanio').value = data.tamanio;
                document.getElementById('ubicacion').value = data.ubicacion;
                document.getElementById('servicios').value = data.servicios;
                document.getElementById('caracteristicas').value = data.caracteristicas;
                document.getElementById('mapa').value = data.mapa;

                const modal = new bootstrap.Modal(document.getElementById('modalPropiedad'));
                modal.show();
            });
    }
</script>
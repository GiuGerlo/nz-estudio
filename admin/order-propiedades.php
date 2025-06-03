<?php
require_once '../config/config.php';
require_once 'includes/head.php';

// Obtener categorías
$query_categorias = "SELECT * FROM tipos_propiedad ORDER BY nombre_categoria";
$categorias = $db->query($query_categorias);
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-sort fa-2x text-primary me-3"></i>
            <div>
                <h1 class="h3 mb-0">Ordenar Propiedades</h1>
                <p class="text-muted mb-0">Arrastra las propiedades para ordenarlas</p>
            </div>
        </div>
        <button type="button" class="btn btn-outline-primary" onclick="location.href='propiedades.php'">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </button>
    </div>

    <div class="row">
        <?php while ($categoria = $categorias->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><?php echo htmlspecialchars($categoria['nombre_categoria']); ?></h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group sortable" data-categoria="<?php echo $categoria['id']; ?>">
                            <?php
                            $query_propiedades = "SELECT id, titulo, orden FROM propiedades 
                                                WHERE categoria = {$categoria['id']} 
                                                ORDER BY orden ASC, id DESC";
                            $propiedades = $db->query($query_propiedades);
                            while ($propiedad = $propiedades->fetch_assoc()):
                            ?>
                                <li class="list-group-item" data-id="<?php echo $propiedad['id']; ?>">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-grip-vertical me-3 text-muted handle"></i>
                                        <span><?php echo htmlspecialchars($propiedad['titulo']); ?></span>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<style>
.sortable {
    min-height: 50px;
    padding: 0;
    list-style: none;
}

.sortable .list-group-item {
    cursor: grab;
    background-color: white;
    border: 1px solid rgba(0,0,0,.125);
    margin-bottom: 5px;
    transition: all 0.2s;
}

.sortable .list-group-item:active {
    cursor: grabbing;
    background-color: #e9ecef;
}

.sortable .list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.handle {
    cursor: move;
    color: #6c757d;
}

.sortable-ghost {
    opacity: 0.5;
    background-color: #e9ecef;
}

.sortable-chosen {
    background-color: #f8f9fa;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    // Inicializar Sortable en cada lista
    document.querySelectorAll('.sortable').forEach(function(el) {
        new Sortable(el, {
            group: 'propiedades', // Permite ordenar dentro del mismo grupo
            animation: 150,
            handle: '.handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            forceFallback: false,
            onEnd: function(evt) {
                updateOrder(evt.to);
            }
        });
    });

    function updateOrder(container) {
        const items = container.querySelectorAll('.list-group-item');
        const ordenData = Array.from(items).map((item, index) => ({
            id: item.dataset.id,
            orden: index + 1
        }));

        Swal.fire({
            title: 'Actualizando...',
            text: 'Guardando nuevo orden',
            didOpen: () => {
                Swal.showLoading();
            },
            allowOutsideClick: false
        });

        fetch('controllers/controller_propiedades.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'update_order',
                categoria: container.dataset.categoria,
                orden: ordenData
            })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                Toast.fire({
                    icon: 'success',
                    title: 'Orden actualizado'
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Error al actualizar el orden'
            });
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
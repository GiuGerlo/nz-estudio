<?php
require_once '../config/config.php';

// Obtener estadísticas para el dashboard
$totalPropiedades = $db->query("SELECT COUNT(*) as total FROM propiedades")->fetch_assoc()['total'];
$totalCategorias = $db->query("SELECT COUNT(*) as total FROM tipos_propiedad")->fetch_assoc()['total'];
$totalImagenes = $db->query("SELECT COUNT(*) as total FROM imagenes_propiedades")->fetch_assoc()['total'];

// Obtener últimas propiedades agregadas
$ultimasPropiedades = $db->query("SELECT id, titulo, localidad FROM propiedades ORDER BY id DESC LIMIT 5");

// Incluir cabecera
include_once 'includes/head.php';
?>

        <!-- Dashboard Content -->
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Panel de Control</h1>
                    <p class="text-muted">Bienvenido al panel de administración</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fas fa-building"></i>
                        <div class="stat-number"><?php echo $totalPropiedades; ?></div>
                        <div class="stat-label">Propiedades Activas</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fas fa-tags"></i>
                        <div class="stat-number"><?php echo $totalCategorias; ?></div>
                        <div class="stat-label">Categorías</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fas fa-images"></i>
                        <div class="stat-number"><?php echo $totalImagenes; ?></div>
                        <div class="stat-label">Imágenes Totales</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Grid -->
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <h5 class="mb-4">Acciones Rápidas</h5>
                </div>
                <div class="col-md-3">
                    <a href="propiedades.php" class="text-decoration-none">
                        <div class="quick-action-card">
                            <div class="quick-action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <h6 class="mb-2">Nueva Propiedad</h6>
                            <p class="text-muted small mb-0">Agregar una nueva propiedad</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="categorias.php" class="text-decoration-none">
                        <div class="quick-action-card">
                            <div class="quick-action-icon">
                                <i class="fas fa-folder-plus"></i>
                            </div>
                            <h6 class="mb-2">Categorías</h6>
                            <p class="text-muted small mb-0">Gestionar categorías</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="vendidas.php" class="text-decoration-none">
                        <div class="quick-action-card">
                            <div class="quick-action-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h6 class="mb-2">Propiedades Vendidas</h6>
                            <p class="text-muted small mb-0">Ver propiedades vendidas</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="order-propiedades.php" class="text-decoration-none">
                        <div class="quick-action-card">
                            <div class="quick-action-icon">
                                <i class="fas fa-sort"></i>
                            </div>
                            <h6 class="mb-2">Ordenar Propiedades</h6>
                            <p class="text-muted small mb-0">Organizar el listado</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Properties Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card recent-properties-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Últimas Propiedades</h5>
                            <a href="propiedades.php" class="btn btn-sm btn-custom-blue">
                                Ver Todas <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Localidad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ultimasPropiedades && $ultimasPropiedades->num_rows > 0): ?>
                                        <?php while ($propiedad = $ultimasPropiedades->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $propiedad['id']; ?></td>
                                                <td><?php echo htmlspecialchars($propiedad['titulo']); ?></td>
                                                <td><?php echo htmlspecialchars($propiedad['localidad']); ?></td>
                                                <td>
                                                    <a href="../propiedad<?php echo $propiedad['id']; ?>" class="btn btn-sm btn-custom-blue" target="_blank"><i class="fas fa-eye"></i></a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No hay propiedades disponibles</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
// Incluir pie de página
include_once 'includes/footer.php';
?>
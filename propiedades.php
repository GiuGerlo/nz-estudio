<?php
require_once 'config/config.php';
include_once 'includes/head.php';

// Obtener todas las categorías
$categorias = $db->query("SELECT * FROM tipos_propiedad ORDER BY nombre_categoria");

// Query base para propiedades
$base_query = "SELECT p.*, tp.nombre_categoria,
              (SELECT ruta_imagen FROM imagenes_propiedades WHERE id_propiedad = p.id LIMIT 1) as imagen_principal,
              (SELECT COUNT(*) FROM imagenes_propiedades WHERE id_propiedad = p.id) as total_imagenes
              FROM propiedades p 
              LEFT JOIN tipos_propiedad tp ON p.categoria = tp.id
              WHERE p.vendida = 0 ";

?>

<link rel="stylesheet" href="assets/css/propiedades.css">

<!-- Header de la página -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 data-aos="fade-up">Nuestras Propiedades</h1>
                <p data-aos="fade-up" data-aos-delay="100">
                    Descubre la propiedad perfecta para ti. Ofrecemos una amplia variedad de opciones
                    para vos
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Filtros Mejorados -->
<section class="filters-section" id="filters-section">
    <div class="container">
        <div class="filters-container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8">
                    <div class="filters-scroll" id="filters-scroll">
                        <button class="filter-btn active" data-filter="all">
                            <i class="bi bi-house-door me-1"></i>
                            Todas
                        </button>
                        <?php
                        $categorias->data_seek(0);
                        while ($cat = $categorias->fetch_assoc()):
                            $count_query = "SELECT COUNT(*) as total FROM propiedades p WHERE p.categoria = " . $cat['id'] . " AND p.vendida = 0";
                            $count_result = $db->query($count_query);
                            $count = $count_result->fetch_assoc()['total'];
                        ?>
                            <button class="filter-btn" data-filter="<?php echo strtolower($cat['nombre_categoria']); ?>">
                                <i class="bi bi-building me-1"></i>
                                <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                                <span class="filter-count"><?php echo $count; ?></span>
                            </button>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección de Propiedades -->
<section class="properties-section">
    <div class="container">
        <?php
        // Resetear el puntero de categorías
        $categorias->data_seek(0);

        // Por cada categoría
        while ($categoria = $categorias->fetch_assoc()):
            // Obtener propiedades de esta categoría
            $query = $base_query . " AND p.categoria = " . $categoria['id'] . " ORDER BY p.orden ASC, p.id DESC";
            $propiedades = $db->query($query);

            if ($propiedades->num_rows > 0):
        ?>

                <div class="category-section" id="<?php echo strtolower($categoria['nombre_categoria']); ?>">
                    <div class="category-header" data-aos="fade-up">
                        <h2 class="category-title"><?php echo htmlspecialchars($categoria['nombre_categoria']); ?></h2>
                    </div>

                    <div class="row">
                        <?php while ($propiedad = $propiedades->fetch_assoc()): ?>
                            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up">
                                <div class="property-card">
                                    <div class="property-image">
                                        <?php if ($propiedad['imagen_principal']): ?>
                                            <img src="<?php echo $propiedad['imagen_principal']; ?>"
                                                alt="<?php echo htmlspecialchars($propiedad['titulo']); ?>">
                                        <?php else: ?>
                                            <img src="assets/img/no-image.jpg" alt="Sin imagen">
                                        <?php endif; ?>
                                    </div>
                                    <div class="property-content">
                                        <h3 class="property-title"><?php echo htmlspecialchars($propiedad['titulo']); ?></h3>
                                        <div class="property-info">
                                            <?php if ($propiedad['localidad']): ?>
                                                <div class="info-item">
                                                    <i class="bi bi-geo-alt"></i>
                                                    <span><?php echo htmlspecialchars($propiedad['localidad']); ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($propiedad['ubicacion']): ?>
                                                <div class="info-item">
                                                    <i class="bi bi-map"></i>
                                                    <span><?php echo htmlspecialchars($propiedad['ubicacion']); ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($propiedad['tamanio']): ?>
                                                <div class="info-item">
                                                    <i class="bi bi-rulers"></i>
                                                    <span><?php echo htmlspecialchars($propiedad['tamanio']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="property-footer">
                                            <a href="propiedad<?php echo $propiedad['id']; ?>" class="btn-view-property">
                                                Ver más <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
        <?php
            endif;
        endwhile;
        ?>
    </div>
</section>

<!-- Sección CTA -->
<section class="cta-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h3 data-aos="fade-up">¿No encontraste lo que buscabas?</h3>
                <p data-aos="fade-up" data-aos-delay="100">
                    Contáctanos y te ayudaremos a encontrar la propiedad perfecta para ti.
                    Tenemos una amplia base de datos con más opciones disponibles.
                </p>
                <div class="mt-4" data-aos="fade-up" data-aos-delay="200">
                    <a href="inicio#contacto" class="btn btn-light me-3">Contactar ahora</a>
                    <a href="https://wa.me/5493468525227" class="btn" style="background: white; color: var(--accent-color);">
                        <i class="bi bi-whatsapp me-2"></i>WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="assets/js/propiedades.js"></script>

<?php
include_once 'includes/footer.php';
?>
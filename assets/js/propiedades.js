// JAVASCRIPT - PROPIEDADES
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const categoryContainers = document.querySelectorAll('.category-section');
    const noResultsMessage = document.querySelector('.no-results-message');
    const categoryNameSpan = document.querySelector('.category-name');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Actualizar botones
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filterValue = btn.dataset.filter;

            // Filtrar categorías
            categoryContainers.forEach(container => {
                if (filterValue === 'all') {
                    container.style.display = '';
                } else if (container.dataset.category === filterValue) {
                    container.style.display = '';
                } else {
                    container.style.display = 'none';
                }
            });

            // Mostrar/ocultar mensaje de no resultados
            const visibleCategories = Array.from(categoryContainers).filter(
                container => container.style.display !== 'none'
            );

            if (visibleCategories.length === 0 && filterValue !== 'all') {
                categoryNameSpan.textContent = btn.dataset.categoryName;
                noResultsMessage.style.display = 'block';
            } else {
                noResultsMessage.style.display = 'none';
            }
        });
    });

    // Lazy loading para imágenes
    const images = document.querySelectorAll('.property-image img');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
});
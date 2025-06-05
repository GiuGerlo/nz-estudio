    // FILTROS MEJORADOS - JAVASCRIPT
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const categorySections = document.querySelectorAll('.category-section');
        const filtersScroll = document.getElementById('filters-scroll');
        const scrollLeft = document.getElementById('scroll-left');
        const scrollRight = document.getElementById('scroll-right');
        const propertiesCount = document.getElementById('properties-count');
        const countText = document.getElementById('count-text');
        const filtersSection = document.getElementById('filters-section');

        // Función para actualizar el contador
        function updateCounter(filter, count) {
            if (propertiesCount && countText) {
                if (filter === 'all') {
                    countText.textContent = 'Todas las propiedades';
                } else {
                    const btnText = document.querySelector(`[data-filter="${filter}"]`).textContent.trim();
                    countText.textContent = `${count} ${btnText.toLowerCase()}`;
                }
            }
        }

        // Función para manejar el scroll horizontal
        function updateScrollIndicators() {
            if (!filtersScroll || window.innerWidth < 768) return;

            const canScrollLeft = filtersScroll.scrollLeft > 0;
            const canScrollRight = filtersScroll.scrollLeft < (filtersScroll.scrollWidth - filtersScroll.clientWidth);

            if (scrollLeft) {
                scrollLeft.style.opacity = canScrollLeft ? '1' : '0.3';
                scrollLeft.style.pointerEvents = canScrollLeft ? 'auto' : 'none';
            }

            if (scrollRight) {
                scrollRight.style.opacity = canScrollRight ? '1' : '0.3';
                scrollRight.style.pointerEvents = canScrollRight ? 'auto' : 'none';
            }
        }

        // Event listeners para scroll indicators
        if (scrollLeft) {
            scrollLeft.addEventListener('click', () => {
                filtersScroll.scrollBy({
                    left: -200,
                    behavior: 'smooth'
                });
            });
        }

        if (scrollRight) {
            scrollRight.addEventListener('click', () => {
                filtersScroll.scrollBy({
                    left: 200,
                    behavior: 'smooth'
                });
            });
        }

        // Event listener para el scroll del contenedor de filtros
        if (filtersScroll) {
            filtersScroll.addEventListener('scroll', updateScrollIndicators);
        }

        // Función principal de filtrado
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remover clase active de todos los botones
                filterBtns.forEach(b => b.classList.remove('active'));
                // Agregar clase active al botón clickeado
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');
                const count = this.getAttribute('data-count') || 0;

                // Mostrar loading state
                filtersSection.classList.add('filters-loading');

                // Simular delay para mejor UX
                setTimeout(() => {
                    // Mostrar/ocultar secciones según el filtro
                    let visibleCount = 0;
                    categorySections.forEach(section => {
                        if (filter === 'all') {
                            section.style.display = 'block';
                            // Contar propiedades visibles
                            visibleCount += section.querySelectorAll('.property-card').length;
                        } else {
                            if (section.id === filter) {
                                section.style.display = 'block';
                                visibleCount += section.querySelectorAll('.property-card').length;
                            } else {
                                section.style.display = 'none';
                            }
                        }
                    });

                    // Actualizar contador
                    updateCounter(filter, filter === 'all' ? visibleCount : count);

                    // Remover loading state
                    filtersSection.classList.remove('filters-loading');

                    // Scroll suave hacia las propiedades en móvil
                    if (window.innerWidth <= 768) {
                        const propertiesSection = document.querySelector('.properties-section');
                        if (propertiesSection) {
                            propertiesSection.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                }, 150);
            });
        });

        // Auto-centrar el botón activo en el scroll horizontal
        function centerActiveButton() {
            const activeBtn = document.querySelector('.filter-btn.active');
            if (activeBtn && filtersScroll) {
                const btnRect = activeBtn.getBoundingClientRect();
                const scrollRect = filtersScroll.getBoundingClientRect();
                const scrollLeft = activeBtn.offsetLeft - (scrollRect.width / 2) + (btnRect.width / 2);

                filtersScroll.scrollTo({
                    left: scrollLeft,
                    behavior: 'smooth'
                });
            }
        }

        // Manejar cambios de orientación y resize
        window.addEventListener('resize', () => {
            updateScrollIndicators();
            centerActiveButton();
        });

        // Inicializar
        updateScrollIndicators();
        updateCounter('all', document.querySelectorAll('.property-card').length);

        // Touch/swipe support para móviles
        if ('ontouchstart' in window) {
            let startX = 0;
            let scrollLeftStart = 0;

            filtersScroll.addEventListener('touchstart', (e) => {
                startX = e.touches[0].pageX;
                scrollLeftStart = filtersScroll.scrollLeft;
            });

            filtersScroll.addEventListener('touchmove', (e) => {
                e.preventDefault();
                const x = e.touches[0].pageX;
                const walk = (x - startX) * 2;
                filtersScroll.scrollLeft = scrollLeftStart - walk;
            });
        }

        // Smooth scroll para navegación interna
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - (filtersSection ? filtersSection.offsetHeight : 0) - 20;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Lazy loading mejorado
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px'
        });

        document.querySelectorAll('.property-image img').forEach(img => {
            imageObserver.observe(img);
        });

        // Mejorar performance en scroll
        let ticking = false;

        function updateOnScroll() {
            updateScrollIndicators();
            ticking = false;
        }

        document.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateOnScroll);
                ticking = true;
            }
        });
    });

    // Funciones de utilidad
    function showAllProperties() {
        document.querySelector('[data-filter="all"]').click();
    }

    function filterByCategory(category) {
        const btn = document.querySelector(`[data-filter="${category}"]`);
        if (btn) btn.click();
    }

    // Smooth scroll para navegación
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Lazy loading para imágenes
    const images = document.querySelectorAll('.property-image img');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.src; // Trigger load
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));

    // Efecto hover para las cards
    document.querySelectorAll('.property-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
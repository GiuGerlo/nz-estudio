// Toggle sidebar on mobile
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar on mobile
    document.querySelector('.toggle-sidebar')?.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('show');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('.toggle-sidebar');
        
        if (window.innerWidth < 768 && 
            sidebar.classList.contains('show') && 
            !sidebar.contains(event.target) && 
            !toggleBtn.contains(event.target)) {
            sidebar.classList.remove('show');
        }
    });

    // Responsive adjustments
    function adjustLayout() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        if (window.innerWidth < 768) {
            sidebar.classList.remove('show');
            mainContent.style.marginLeft = '0';
        } else if (window.innerWidth < 992) {
            mainContent.style.marginLeft = '70px';
        } else {
            mainContent.style.marginLeft = '250px';
        }
    }

    // Initial call and event listener
    window.addEventListener('resize', adjustLayout);
    adjustLayout();

    // Mostrar la hora actual en Argentina (UTC-3)
    function actualizarHora() {
        const ahora = new Date();
        
        // Configurar para Argentina (UTC-3)
        const opcionesHora = { 
            timeZone: 'America/Argentina/Buenos_Aires',
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        
        const opcionesFecha = {
            timeZone: 'America/Argentina/Buenos_Aires',
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        };
        
        const horaActual = ahora.toLocaleTimeString('es-AR', opcionesHora);
        const fechaActual = ahora.toLocaleDateString('es-AR', opcionesFecha);
        
        // Si existe un elemento para mostrar la hora, actualizarlo
        const elementoHora = document.getElementById('hora-actual');
        if (elementoHora) {
            elementoHora.textContent = `${fechaActual} - ${horaActual}`;
        }
    }
    
    // Actualizar la hora cada segundo si existe el elemento
    if (document.getElementById('hora-actual')) {
        actualizarHora();
        setInterval(actualizarHora, 1000);
    }
});
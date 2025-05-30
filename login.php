<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: inicio');
    exit;
}

$pageTitle = 'Iniciar Sesión';
include 'includes/head.php';
?>

<main class="main">
    <section class="login-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img src="assets/img/logo.png" alt="Logo NZ Estudio" class="mb-4" style="max-height: 80px;">
                                <h2>Bienvenido de nuevo</h2>
                                <p class="text-muted">Ingresa tus credenciales para acceder al panel</p>
                            </div>

                            <form id="loginForm" action="auth.php" method="POST" class="needs-validation" novalidate>
                                <div class="input-group">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-envelope"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               placeholder="tu@email.com" 
                                               required>
                                    </div>
                                </div>

                                <div class="input-group">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password" 
                                               name="password" 
                                               placeholder="••••••••" 
                                               required
                                               minlength="6">
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Validación del formulario
(function () {
    'use strict'
    
    // Obtener todos los formularios a los que queremos aplicar estilos de validación de Bootstrap
    var forms = document.querySelectorAll('.needs-validation')
    
    // Bucle sobre ellos y evitar el envío
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php include 'includes/footer.php'; ?>

<?php
require_once '../config/config.php';
require_once 'includes/head.php';

// Datos del usuario logueado
$stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-user-shield fa-2x text-primary me-3"></i>
            <div>
                <h1 class="h3 mb-0">Mi Perfil</h1>
                <p class="text-muted mb-0">Datos de tu cuenta y cambio de contraseña</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Datos de la cuenta -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Datos</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase">Email</label>
                        <p class="mb-0 fw-semibold"><?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cambio de contraseña -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Cambiar contraseña</h5>
                </div>
                <div class="card-body">
                    <form id="formCambiarPassword" autocomplete="off">
                        <?php echo nz_csrf_field(); ?>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña actual</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                            <small class="text-muted">Mínimo 8 caracteres.</small>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cambiar contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    $('#formCambiarPassword').on('submit', function (e) {
        e.preventDefault();
        const $btn = $(this).find('button[type=submit]');
        const $form = $(this);

        if ($('#new_password').val() !== $('#confirm_password').val()) {
            Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Procesando...');

        $.ajax({
            url: 'controllers/controller_perfil.php',
            type: 'POST',
            data: $form.serialize(),
            success: function (resp) {
                const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (data.success) {
                    Swal.fire('Listo', data.message || 'Contraseña actualizada', 'success')
                        .then(() => $form[0].reset());
                } else {
                    Swal.fire('Error', data.message || 'No se pudo cambiar la contraseña', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'No se pudo comunicar con el servidor', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Cambiar contraseña');
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>

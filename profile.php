<?php
// profile.php
session_start();
require_once 'config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$errors = [];

// Obtener información actual del usuario
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar si el email ya está en uso por otro usuario
    if ($new_email !== $user['email']) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$new_email, $user_id]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Este correo electrónico ya está en uso";
        }
    }

    // Si se ingresó una nueva contraseña
    if (!empty($new_password)) {
        if (strlen($new_password) < 8) {
            $errors[] = "La nueva contraseña debe tener al menos 8 caracteres";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "Las contraseñas no coinciden";
        }
    }

    // Verificar la contraseña actual
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $current_user = $stmt->fetch();

    if (!password_verify($current_password, $current_user['password'])) {
        $errors[] = "La contraseña actual es incorrecta";
    }

    // Si no hay errores, actualizar el perfil
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Actualizar email
            if ($new_email !== $user['email']) {
                $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->execute([$new_email, $user_id]);
            }

            // Actualizar contraseña si se proporcionó una nueva
            if (!empty($new_password)) {
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$password_hash, $user_id]);
            }

            $pdo->commit();
            $success_message = "Perfil actualizado correctamente";
            
            // Actualizar la información del usuario
            $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Error al actualizar el perfil. Por favor, intenta más tarde.";
        }
    }
}

// Obtener estadísticas del usuario
$stmt = $pdo->prepare("SELECT 
    COUNT(*) as total_tasks,
    SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed_tasks
    FROM tasks 
    WHERE user_id = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | Gestor de Tareas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .profile-section {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin-bottom: 2rem;
        }
        .stats-card {
            transition: transform 0.2s;
            cursor: default;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .password-strength {
            height: 5px;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
        <div class="container">
            <a class="navbar-brand" href="tasks.php">
                <i class="fas fa-check-circle text-primary me-2"></i>
                Gestor de Tareas
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars($user['username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="tasks.php">
                            <i class="fas fa-tasks me-2"></i>Mis Tareas
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row">
            <!-- Sección de Estadísticas -->
            <div class="col-md-4">
                <div class="profile-section p-4 mb-4">
                    <h4 class="mb-4">Estadísticas</h4>
                    
                    <div class="stats-card card mb-3 bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-tasks me-2"></i>
                                Total de Tareas
                            </h5>
                            <h2 class="mb-0"><?php echo $stats['total_tasks']; ?></h2>
                        </div>
                    </div>

                    <div class="stats-card card mb-3 bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-check-circle me-2"></i>
                                Tareas Completadas
                            </h5>
                            <h2 class="mb-0"><?php echo $stats['completed_tasks']; ?></h2>
                        </div>
                    </div>

                    <div class="stats-card card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-chart-line me-2"></i>
                                Tasa de Completado
                            </h5>
                            <h2 class="mb-0">
                                <?php 
                                    echo $stats['total_tasks'] > 0 
                                        ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100) 
                                        : 0;
                                ?>%
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Perfil -->
            <div class="col-md-8">
                <div class="profile-section p-4">
                    <h4 class="mb-4">Mi Perfil</h4>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($success_message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Nombre de usuario</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" 
                                   disabled>
                            <div class="form-text">El nombre de usuario no se puede cambiar</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña actual</label>
                            <input type="password" class="form-control" id="current_password" 
                                   name="current_password" required>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Cambiar contraseña</h5>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" id="new_password" 
                                   name="new_password" minlength="8">
                            <div class="form-text">Deja en blanco si no deseas cambiar la contraseña</div>
                        </div>

                        <div class="password-strength mb-2"></div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" class="form-control" id="confirm_password" 
                                   name="confirm_password">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Validación de formulario -->
    <script>
        // Validación del formulario
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // Validación de contraseña
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        const strengthBar = document.querySelector('.password-strength');

        function updatePasswordStrength(password) {
            if (!password) {
                strengthBar.style.width = '0';
                return;
            }

            let strength = 0;
            if (password.length >= 8) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/)) strength += 25;
            if (password.match(/[^A-Za-z0-9]/)) strength += 25;

            strengthBar.style.width = strength + '%';
            strengthBar.style.backgroundColor = 
                strength < 50 ? '#dc3545' : 
                strength < 75 ? '#ffc107' : '#198754';
        }

        newPassword.addEventListener('input', () => {
            updatePasswordStrength(newPassword.value);
            
            if (confirmPassword.value) {
                if (newPassword.value === confirmPassword.value) {
                    confirmPassword.setCustomValidity('');
                } else {
                    confirmPassword.setCustomValidity('Las contraseñas no coinciden');
                }
            }
        });

        confirmPassword.addEventListener('input', () => {
            if (newPassword.value === confirmPassword.value) {
                confirmPassword.setCustomValidity('');
            } else {
                confirmPassword.setCustomValidity('Las contraseñas no coinciden');
            }
        });
    </script>
</body>
</html>
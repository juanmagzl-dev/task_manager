<?php
// register.php
session_start();
require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = trim($_POST['email']);

    // Validaciones
    if (strlen($username) < 4) {
        $errors[] = "El nombre de usuario debe tener al menos 4 caracteres";
    }

    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El correo electrónico no es válido";
    }

    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "El nombre de usuario o correo electrónico ya está en uso";
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$username, $email, $password_hash]);
            $_SESSION['register_success'] = true;
            header("Location: login.php");
            exit();
        } catch(PDOException $e) {
            $errors[] = "Error al registrar usuario. Por favor, intenta más tarde.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Mi Aplicación</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .form-register {
            width: 100%;
            max-width: 450px;
            padding: 15px;
            margin: auto;
        }
        .form-register .card {
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .form-register .form-floating {
            margin-bottom: 1rem;
        }
        .password-strength {
            height: 5px;
            transition: all 0.3s ease;
        }
        .logo {
            width: 80px;
            margin-bottom: 1.5rem;
        }
        .requirements {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .requirement-item.valid {
            color: #198754;
        }
        .requirement-item.invalid {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <main class="form-register">
        <div class="card">
            <div class="card-body p-4 p-sm-5">
                <div class="text-center mb-4">
                    <!-- Aquí puedes agregar tu logo -->
                    <img src="path/to/your/logo.png" alt="Logo" class="logo">
                    <h1 class="h3 mb-3 fw-normal">Crear Cuenta</h1>
                </div>

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

                <form method="POST" class="needs-validation" novalidate>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Usuario" required minlength="4" 
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                        <label for="username">Nombre de usuario</label>
                        <div class="invalid-feedback">
                            El nombre de usuario debe tener al menos 4 caracteres
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="nombre@ejemplo.com" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        <label for="email">Correo electrónico</label>
                        <div class="invalid-feedback">
                            Por favor, ingresa un correo electrónico válido
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Contraseña" required minlength="8">
                        <label for="password">Contraseña</label>
                    </div>

                    <div class="password-strength mb-2"></div>
                    <div class="requirements mb-3">
                        <p class="mb-2">La contraseña debe contener:</p>
                        <div class="requirement-item" data-requirement="length">
                            <i class="fas fa-check-circle me-2"></i>Mínimo 8 caracteres
                        </div>
                        <div class="requirement-item" data-requirement="number">
                            <i class="fas fa-check-circle me-2"></i>Al menos un número
                        </div>
                        <div class="requirement-item" data-requirement="uppercase">
                            <i class="fas fa-check-circle me-2"></i>Al menos una mayúscula
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="confirm_password" 
                               name="confirm_password" placeholder="Confirmar contraseña" required>
                        <label for="confirm_password">Confirmar contraseña</label>
                        <div class="invalid-feedback">
                            Las contraseñas no coinciden
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            Acepto los <a href="#" class="text-decoration-none">términos y condiciones</a>
                        </label>
                        <div class="invalid-feedback">
                            Debes aceptar los términos y condiciones
                        </div>
                    </div>

                    <button class="w-100 btn btn-primary mb-3" type="submit">
                        <i class="fas fa-user-plus me-2"></i>Crear cuenta
                    </button>

                    <div class="text-center">
                        <p class="mb-0">¿Ya tienes cuenta? 
                            <a href="login.php" class="text-decoration-none">Inicia sesión aquí</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Validación de formulario y contraseña -->
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

        // Validación de contraseña en tiempo real
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const requirements = document.querySelectorAll('.requirement-item');
        const strengthBar = document.querySelector('.password-strength');

        function updatePasswordStrength(password) {
            let strength = 0;
            const patterns = {
                length: /.{8,}/,
                number: /\d/,
                uppercase: /[A-Z]/
            };

            Object.keys(patterns).forEach(pattern => {
                const requirement = document.querySelector(`[data-requirement="${pattern}"]`);
                if (patterns[pattern].test(password)) {
                    requirement.classList.add('valid');
                    requirement.classList.remove('invalid');
                    strength += 33.33;
                } else {
                    requirement.classList.add('invalid');
                    requirement.classList.remove('valid');
                }
            });

            strengthBar.style.width = `${strength}%`;
            strengthBar.style.backgroundColor = 
                strength < 33 ? '#dc3545' : 
                strength < 66 ? '#ffc107' : '#198754';
        }

        password.addEventListener('input', () => {
            updatePasswordStrength(password.value);
            
            // Verificar coincidencia de contraseñas
            if (confirmPassword.value) {
                if (password.value === confirmPassword.value) {
                    confirmPassword.setCustomValidity('');
                } else {
                    confirmPassword.setCustomValidity('Las contraseñas no coinciden');
                }
            }
        });

        confirmPassword.addEventListener('input', () => {
            if (password.value === confirmPassword.value) {
                confirmPassword.setCustomValidity('');
            } else {
                confirmPassword.setCustomValidity('Las contraseñas no coinciden');
            }
        });
    </script>
</body>
</html>
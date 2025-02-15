<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: tasks.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>TaskManager | Gestión de Tareas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 1rem;
            text-align: center;
        }

        .logo {
            color: #2c3e50;
            font-size: 2rem;
            font-weight: bold;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
            flex: 1;
        }

        .hero {
            background: white;
            border-radius: 10px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .hero h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .hero p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .feature-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2rem;
            color: #3498db;
            margin-bottom: 1rem;
        }

        .buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .button-primary {
            background: #3498db;
            color: white;
        }

        .button-secondary {
            background: white;
            color: #3498db;
            border: 2px solid #3498db;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .footer {
            background: white;
            padding: 1rem;
            text-align: center;
            color: #666;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .hero {
                padding: 2rem;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <i class="fas fa-check-circle"></i> TaskManager
        </div>
    </header>

    <div class="container">
        <div class="hero">
            <h1>Gestiona tus tareas de forma eficiente</h1>
            <p>Organiza, prioriza y completa tus tareas diarias con nuestra intuitiva plataforma.</p>
            
            <div class="buttons">
                <a href="login.php" class="button button-primary">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
                <a href="register.php" class="button button-secondary">
                    <i class="fas fa-user-plus"></i> Registrarse
                </a>
            </div>
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-list-check"></i>
                </div>
                <h3>Organización Simple</h3>
                <p>Crea y organiza tus tareas de manera intuitiva y eficiente.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Seguimiento en Tiempo Real</h3>
                <p>Mantén un registro actualizado de tus tareas completadas.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Acceso Desde Cualquier Lugar</h3>
                <p>Accede a tus tareas desde cualquier dispositivo.</p>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> TaskManager. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
<?php
// tasks.php
session_start();
require_once 'config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Agregar tarea
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_task'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];
    
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $title, $description]);
        header("Location: tasks.php");
        exit();
    }
}

// Marcar tarea como completada
if (isset($_GET['complete'])) {
    $task_id = $_GET['complete'];
    $stmt = $pdo->prepare("UPDATE tasks SET is_completed = NOT is_completed WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    header("Location: tasks.php");
    exit();
}

// Eliminar tarea
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    header("Location: tasks.php");
    exit();
}

// Listar tareas
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$tasks = $stmt->fetchAll();

// Contar tareas completadas y pendientes
$completed_tasks = array_filter($tasks, function($task) {
    return $task['is_completed'] == 1;
});
$pending_tasks = array_filter($tasks, function($task) {
    return $task['is_completed'] == 0;
});
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Tareas | Panel Principal</title>
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
        .task-card {
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 1rem;
        }
        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,.1);
        }
        .task-card.completed {
            background-color: #f8f9fa;
        }
        .task-card.completed .task-title {
            text-decoration: line-through;
            color: #6c757d;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,.05);
        }
        .add-task-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,.05);
        }
        .progress {
            height: 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-check-circle text-primary me-2"></i>
                Gestor de Tareas
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user me-2"></i>Perfil
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

    <div class="container">
        <div class="row">
            <!-- Sidebar con estadísticas -->
            <div class="col-md-3">
                <div class="stats-card">
                    <h5 class="mb-3">Resumen</h5>
                    <div class="mb-3">
                        <p class="mb-2">Progreso General</p>
                        <div class="progress">
                            <?php 
                            $completion_rate = count($tasks) > 0 
                                ? (count($completed_tasks) / count($tasks)) * 100 
                                : 0;
                            ?>
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: <?php echo $completion_rate; ?>%" 
                                 aria-valuenow="<?php echo $completion_rate; ?>" 
                                 aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tareas Completadas</span>
                        <span class="badge bg-success rounded-pill">
                            <?php echo count($completed_tasks); ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Tareas Pendientes</span>
                        <span class="badge bg-warning rounded-pill">
                            <?php echo count($pending_tasks); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="col-md-9">
                <!-- Formulario para agregar tarea -->
                <div class="add-task-card">
                    <h5 class="mb-3">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        Nueva Tarea
                    </h5>
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="title" 
                                   placeholder="¿Qué necesitas hacer?" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa un título para la tarea.
                            </div>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="description" rows="3" 
                                      placeholder="Descripción (opcional)"></textarea>
                        </div>
                        <button type="submit" name="add_task" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Agregar Tarea
                        </button>
                    </form>
                </div>

                <!-- Lista de tareas -->
                <div class="tasks">
                    <?php if (empty($tasks)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tienes tareas pendientes.</p>
                            <p class="text-muted">¡Comienza agregando una nueva tarea!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($tasks as $task): ?>
                            <div class="card task-card <?php echo $task['is_completed'] ? 'completed' : ''; ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title task-title mb-0">
                                            <?php echo htmlspecialchars($task['title']); ?>
                                        </h5>
                                        <div class="btn-group">
                                            <a href="?complete=<?php echo $task['id']; ?>" 
                                               class="btn btn-sm btn-outline-secondary">
                                                <?php if ($task['is_completed']): ?>
                                                    <i class="fas fa-undo me-1"></i>Desmarcar
                                                <?php else: ?>
                                                    <i class="fas fa-check me-1"></i>Completar
                                                <?php endif; ?>
                                            </a>
                                            <a href="?delete=<?php echo $task['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('¿Estás seguro de eliminar esta tarea?');">
                                                <i class="fas fa-trash-alt me-1"></i>Eliminar
                                            </a>
                                        </div>
                                    </div>
                                    <?php if (!empty($task['description'])): ?>
                                        <p class="card-text mt-2 mb-0">
                                            <?php echo htmlspecialchars($task['description']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>
                                        Creada el <?php echo date('d/m/Y', strtotime($task['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Validación de formularios -->
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
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
</body>
</html>
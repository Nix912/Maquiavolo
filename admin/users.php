<?php
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
requireAdmin();

// Procesar actualización de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_usuario'])) {
    $usuarioId = $_POST['usuario_id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $esAdmin = isset($_POST['es_admin']) ? 1 : 0;
    
    // Actualizar usuario en la base de datos
    $resultado = actualizarUsuario($usuarioId, $nombre, $apellido, $email, $esAdmin);
    $mensaje = $resultado ? "Usuario actualizado correctamente." : "Error al actualizar el usuario.";
}

// Procesar eliminación de usuario
if (isset($_GET['eliminar'])) {
    $resultado = eliminarUsuario($_GET['eliminar']);
    $mensaje = $resultado ? "Usuario eliminado correctamente." : "Error al eliminar el usuario.";
}

$usuarios = obtenerUsuarios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="admin-container">
        <aside class="admin-sidebar">
            <h3>Panel de Administración</h3>
            <nav>
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="products.php"><i class="fas fa-gamepad"></i> Productos</a></li>
                    <li class="active"><a href="users.php"><i class="fas fa-users"></i> Usuarios</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Pedidos</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <h1>Gestionar Usuarios</h1>
            
            <?php if (isset($mensaje)): ?>
                <div class="alert <?= strpos($mensaje, 'correctamente') !== false ? 'alert-success' : 'alert-error' ?>">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>

            <div class="users-list">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= $usuario['ID_Usuario'] ?></td>
                            <td><?= htmlspecialchars($usuario['Nombre']) ?></td>
                            <td><?= htmlspecialchars($usuario['Apellido']) ?></td>
                            <td><?= htmlspecialchars($usuario['Correo']) ?></td>
                            <td>
                                <?php if ($usuario['es_admin']): ?>
                                    <span class="badge-admin">Administrador</span>
                                <?php else: ?>
                                    <span class="badge-user">Usuario</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-small edit-user" 
                                        data-id="<?= $usuario['ID_Usuario'] ?>"
                                        data-nombre="<?= htmlspecialchars($usuario['Nombre']) ?>"
                                        data-apellido="<?= htmlspecialchars($usuario['Apellido']) ?>"
                                        data-email="<?= htmlspecialchars($usuario['Correo']) ?>"
                                        data-admin="<?= $usuario['es_admin'] ?>">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <?php if ($usuario['ID_Usuario'] != $_SESSION['user_id']): ?>
                                <a href="users.php?eliminar=<?= $usuario['ID_Usuario'] ?>" class="btn btn-small btn-danger" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal para editar usuario -->
    <div id="user-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Editar Usuario</h2>
            <form id="user-form" method="post">
                <input type="hidden" id="user-id" name="usuario_id">
                
                <div class="form-group">
                    <label for="user-name">Nombre:</label>
                    <input type="text" id="user-name" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="user-lastname">Apellido:</label>
                    <input type="text" id="user-lastname" name="apellido" required>
                </div>
                
                <div class="form-group">
                    <label for="user-email">Email:</label>
                    <input type="email" id="user-email" name="email" required>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="user-admin" name="es_admin">
                    <label for="user-admin">Administrador</label>
                </div>
                
                <button type="submit" name="actualizar_usuario" class="btn">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </form>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    <script src="../../assets/js/admin-users.js"></script>
</body>
</html>
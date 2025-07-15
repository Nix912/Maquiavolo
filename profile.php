<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
requireLogin();

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_perfil'])) {
    // Lógica para actualizar perfil
}

// Procesar cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_contrasena'])) {
    // Lógica para cambiar contraseña
}

$historialCompras = obtenerHistorialCompras($_SESSION['user_id']);
$totalGastado = 0;
foreach ($historialCompras as $compra) {
    $totalGastado += $compra['Precio_Unitario'] * $compra['Cantidad'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar" style="background-image: url('https://i.pinimg.com/originals/9e/3e/09/9e3e09f8261d0a0f3ea53ae1d9d9f6c3.jpg');"></div>
                <div class="profile-info">
                    <h1><?= htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']) ?></h1>
                    <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($_SESSION['email']) ?></p>
                    <?php if (isAdmin()): ?>
                        <p><i class="fas fa-shield-alt"></i> <span class="badge-admin">Administrador</span></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="profile-sections">
                <div class="profile-section">
                    <h2><i class="fas fa-user-edit"></i> Editar Perfil</h2>
                    <form method="post">
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($_SESSION['nombre']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido">Apellido:</label>
                            <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($_SESSION['apellido']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['email']) ?>" required>
                        </div>
                        <button type="submit" name="actualizar_perfil" class="btn">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </form>
                </div>

                <div class="profile-section">
                    <h2><i class="fas fa-key"></i> Cambiar Contraseña</h2>
                    <form method="post">
                        <div class="form-group">
                            <label for="contrasena_actual">Contraseña Actual:</label>
                            <input type="password" id="contrasena_actual" name="contrasena_actual" required>
                        </div>
                        <div class="form-group">
                            <label for="nueva_contrasena">Nueva Contraseña:</label>
                            <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmar_contrasena">Confirmar Nueva Contraseña:</label>
                            <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>
                        </div>
                        <button type="submit" name="cambiar_contrasena" class="btn">
                            <i class="fas fa-sync-alt"></i> Cambiar Contraseña
                        </button>
                    </form>
                </div>

                <div class="profile-section">
                    <h2><i class="fas fa-history"></i> Historial de Compras</h2>
                    <?php if (empty($historialCompras)): ?>
                        <p class="empty-message">Aún no tienes compras registradas.</p>
                    <?php else: ?>
                        <div class="purchase-history">
                            <?php 
                            $facturaActual = null;
                            foreach ($historialCompras as $compra): 
                                if ($facturaActual !== $compra['ID_Factura']): 
                                    $facturaActual = $compra['ID_Factura'];
                            ?>
                            <div class="purchase-header">
                                <h3>Factura #<?= $compra['ID_Factura'] ?></h3>
                                <p>Fecha: <?= date('d/m/Y H:i', strtotime($compra['Fecha_Factura'])) ?></p>
                                <p>Total: $<?= number_format($compra['Precio_Final'], 2) ?></p>
                            </div>
                            <?php endif; ?>
                            <div class="purchase-item">
                                <img src="<?= $compra['Imagen_URL'] ?>" alt="<?= htmlspecialchars($compra['Nombre_Producto']) ?>">
                                <div class="purchase-item-info">
                                    <h4><?= htmlspecialchars($compra['Nombre_Producto']) ?></h4>
                                    <p>Cantidad: <?= $compra['Cantidad'] ?></p>
                                    <p>Precio unitario: $<?= number_format($compra['Precio_Unitario'], 2) ?></p>
                                    <p>Subtotal: $<?= number_format($compra['Precio_Unitario'] * $compra['Cantidad'], 2) ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="total-spent">
                            <p>Total gastado hasta la fecha: <strong>$<?= number_format($totalGastado, 2) ?></strong></p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (isAdmin()): ?>
                <div class="profile-section">
                    <h2><i class="fas fa-shield-alt"></i> Panel de Administración</h2>
                    <div class="admin-links">
                        <a href="admin/dashboard.php" class="btn">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="admin/products.php" class="btn">
                            <i class="fas fa-gamepad"></i> Gestionar Productos
                        </a>
                        <a href="admin/users.php" class="btn">
                            <i class="fas fa-users"></i> Gestionar Usuarios
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
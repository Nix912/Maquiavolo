<?php
require_once '../../includes/auth.php';
requireAdmin();

// Obtener estadísticas
$totalProductos = obtenerTotalProductos();
$totalUsuarios = obtenerTotalUsuarios();
$ventasHoy = obtenerVentasHoy();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - <?= APP_NAME ?></title>
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
                    <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="products.php"><i class="fas fa-gamepad"></i> Productos</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Usuarios</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Pedidos</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <h1>Dashboard de Administración</h1>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Productos</h3>
                        <p><?= $totalProductos ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Usuarios</h3>
                        <p><?= $totalUsuarios ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Ventas Hoy</h3>
                        <p>$<?= number_format($ventasHoy, 2) ?></p>
                    </div>
                </div>
            </div>

            <div class="recent-orders">
                <h2>Pedidos Recientes</h2>
                <?php $pedidos = obtenerPedidosRecientes(); ?>
                <?php if (!empty($pedidos)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td><?= $pedido['ID_Factura'] ?></td>
                                <td><?= htmlspecialchars($pedido['Nombre'] . ' ' . htmlspecialchars($pedido['Apellido']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($pedido['Fecha_Factura'])) ?></td>
                                <td>$<?= number_format($pedido['Precio_Final'], 2) ?></td>
                                <td>
                                    <a href="order_details.php?id=<?= $pedido['ID_Factura'] ?>" class="btn btn-small">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay pedidos recientes.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['eliminar'])) {
        eliminarDelCarrito($_SESSION['user_id'], $_POST['producto_id']);
    } elseif (isset($_POST['actualizar'])) {
        actualizarCantidadCarrito($_SESSION['user_id'], $_POST['producto_id'], $_POST['cantidad']);
    } elseif (isset($_POST['checkout'])) {
        $productos = obtenerCarritoUsuario($_SESSION['user_id']);
        $facturaId = crearFactura($_SESSION['user_id'], $productos);
        
        if ($facturaId) {
            header("Location: profile.php?factura=$facturaId");
            exit();
        } else {
            $error = "Error al procesar la compra. Inténtalo de nuevo.";
        }
    }
}

$productosCarrito = obtenerCarritoUsuario($_SESSION['user_id']);
$total = 0;
foreach ($productosCarrito as $producto) {
    $total += $producto['Precio_Producto'] * $producto['Cantidad_Producto'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1 class="section-title">Tu Carrito de Compras</h1>
        
        <?php if (empty($productosCarrito)): ?>
            <div class="empty-cart-message">
                <p>Tu carrito está vacío, mi amigo. ¡Es hora de llenarlo!</p>
                <a href="products.php" class="btn">Explorar Productos</a>
            </div>
        <?php else: ?>
            <table class="cart-items">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productosCarrito as $producto): ?>
                    <tr>
                        <td>
                            <div class="cart-item-info">
                                <img src="<?= $producto['Imagen_URL'] ?>" alt="<?= htmlspecialchars($producto['Nombre_Producto']) ?>" class="cart-item-image">
                                <span><?= htmlspecialchars($producto['Nombre_Producto']) ?></span>
                            </div>
                        </td>
                        <td>$<?= number_format($producto['Precio_Producto'], 2) ?></td>
                        <td>
                            <form method="post" class="cart-item-quantity">
                                <input type="hidden" name="producto_id" value="<?= $producto['ID_Producto'] ?>">
                                <button type="button" class="quantity-btn decrement">-</button>
                                <input type="number" name="cantidad" value="<?= $producto['Cantidad_Producto'] ?>" min="1" class="quantity-input">
                                <button type="button" class="quantity-btn increment">+</button>
                                <button type="submit" name="actualizar" class="update-btn" style="display:none;">Actualizar</button>
                            </form>
                        </td>
                        <td>$<?= number_format($producto['Precio_Producto'] * $producto['Cantidad_Producto'], 2) ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="producto_id" value="<?= $producto['ID_Producto'] ?>">
                                <button type="submit" name="eliminar" class="remove-item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$<?= number_format($total, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span>Impuestos (IVA 16%):</span>
                    <span>$<?= number_format($total * 0.16, 2) ?></span>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span>$<?= number_format($total * 1.16, 2) ?></span>
                </div>
            </div>

            <div class="cart-actions">
                <a href="products.php" class="continue-shopping">
                    <i class="fas fa-chevron-left"></i> Continuar Comprando
                </a>
                <form method="post">
                    <button type="submit" name="checkout" class="btn checkout-btn">
                        <i class="fas fa-credit-card"></i> Proceder al Pago
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/cart.js"></script>
</body>
</html>
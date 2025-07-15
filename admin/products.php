<?php
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
requireAdmin();

// Procesar formulario de añadir/editar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $imagen = $_POST['imagen'];
    $descripcion = $_POST['descripcion'];
    
    if (isset($_POST['producto_id']) {
        // Editar producto existente
        $resultado = actualizarProducto($_POST['producto_id'], $nombre, $precio, $imagen, $descripcion);
        $mensaje = $resultado ? "Producto actualizado correctamente." : "Error al actualizar el producto.";
    } else {
        // Añadir nuevo producto
        $resultado = añadirProducto($nombre, $precio, $imagen, $descripcion);
        $mensaje = $resultado ? "Producto añadido correctamente." : "Error al añadir el producto.";
    }
}

// Procesar eliminación de producto
if (isset($_GET['eliminar'])) {
    $resultado = eliminarProducto($_GET['eliminar']);
    $mensaje = $resultado ? "Producto eliminado correctamente." : "Error al eliminar el producto.";
}

$productos = obtenerProductos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Productos - <?= APP_NAME ?></title>
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
                    <li class="active"><a href="products.php"><i class="fas fa-gamepad"></i> Productos</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Usuarios</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Pedidos</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <h1>Gestionar Productos</h1>
            
            <?php if (isset($mensaje)): ?>
                <div class="alert <?= strpos($mensaje, 'correctamente') !== false ? 'alert-success' : 'alert-error' ?>">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>

            <div class="admin-actions">
                <button id="add-product-btn" class="btn">
                    <i class="fas fa-plus"></i> Añadir Producto
                </button>
            </div>

            <div class="products-list">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?= $producto['ID_Producto'] ?></td>
                            <td>
                                <img src="<?= $producto['Imagen_URL'] ?>" alt="<?= htmlspecialchars($producto['Nombre_Producto']) ?>" class="product-thumbnail">
                            </td>
                            <td><?= htmlspecialchars($producto['Nombre_Producto']) ?></td>
                            <td>$<?= number_format($producto['Precio_Producto'], 2) ?></td>
                            <td><?= htmlspecialchars(substr($producto['Descripcion'], 0, 50)) ?>...</td>
                            <td>
                                <button class="btn btn-small edit-product" 
                                        data-id="<?= $producto['ID_Producto'] ?>"
                                        data-nombre="<?= htmlspecialchars($producto['Nombre_Producto']) ?>"
                                        data-precio="<?= $producto['Precio_Producto'] ?>"
                                        data-imagen="<?= $producto['Imagen_URL'] ?>"
                                        data-descripcion="<?= htmlspecialchars($producto['Descripcion']) ?>">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <a href="products.php?eliminar=<?= $producto['ID_Producto'] ?>" class="btn btn-small btn-danger" onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal para añadir/editar producto -->
    <div id="product-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modal-title">Añadir Nuevo Producto</h2>
            <form id="product-form" method="post">
                <input type="hidden" id="product-id" name="producto_id">
                
                <div class="form-group">
                    <label for="product-name">Nombre:</label>
                    <input type="text" id="product-name" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="product-price">Precio:</label>
                    <input type="number" id="product-price" name="precio" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="product-image">URL de la Imagen:</label>
                    <input type="text" id="product-image" name="imagen" required>
                </div>
                
                <div class="form-group">
                    <label for="product-description">Descripción:</label>
                    <textarea id="product-description" name="descripcion" required></textarea>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </form>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    <script src="../../assets/js/admin-products.js"></script>
</body>
</html>
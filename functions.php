<?php
require_once 'db.php';

$db = new Database();
$conn = $db->getConnection();

function obtenerProductos() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM productos");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductoPorId($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM productos WHERE ID_Producto = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function añadirProducto($nombre, $precio, $imagen, $descripcion) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO productos (Nombre_Producto, Precio_Producto, Imagen_URL, Descripcion) 
                           VALUES (:nombre, :precio, :imagen, :descripcion)");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':imagen', $imagen);
    $stmt->bindParam(':descripcion', $descripcion);
    return $stmt->execute();
}

function actualizarProducto($id, $nombre, $precio, $imagen, $descripcion) {
    global $conn;
    $stmt = $conn->prepare("UPDATE productos SET 
                           Nombre_Producto = :nombre, 
                           Precio_Producto = :precio, 
                           Imagen_URL = :imagen, 
                           Descripcion = :descripcion 
                           WHERE ID_Producto = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':imagen', $imagen);
    $stmt->bindParam(':descripcion', $descripcion);
    return $stmt->execute();
}

function eliminarProducto($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM productos WHERE ID_Producto = :id");
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

function obtenerUsuarios() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM usuarios");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerCarritoUsuario($usuarioId) {
    global $conn;
    $stmt = $conn->prepare("SELECT c.*, p.Nombre_Producto, p.Precio_Producto, p.Imagen_URL 
                           FROM carrito c 
                           JOIN productos p ON c.ID_Producto = p.ID_Producto 
                           WHERE c.ID_Usuario = :usuarioId");
    $stmt->bindParam(':usuarioId', $usuarioId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function agregarAlCarrito($usuarioId, $productoId, $cantidad = 1) {
    global $conn;
    
    // Verificar si el producto ya está en el carrito
    $stmt = $conn->prepare("SELECT * FROM carrito 
                           WHERE ID_Usuario = :usuarioId AND ID_Producto = :productoId");
    $stmt->bindParam(':usuarioId', $usuarioId);
    $stmt->bindParam(':productoId', $productoId);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Actualizar cantidad si ya existe
        $stmt = $conn->prepare("UPDATE carrito 
                               SET Cantidad_Producto = Cantidad_Producto + :cantidad 
                               WHERE ID_Usuario = :usuarioId AND ID_Producto = :productoId");
    } else {
        // Insertar nuevo registro
        $stmt = $conn->prepare("INSERT INTO carrito (ID_Usuario, ID_Producto, Cantidad_Producto) 
                               VALUES (:usuarioId, :productoId, :cantidad)");
    }
    
    $stmt->bindParam(':usuarioId', $usuarioId);
    $stmt->bindParam(':productoId', $productoId);
    $stmt->bindParam(':cantidad', $cantidad);
    
    return $stmt->execute();
}

function actualizarCantidadCarrito($usuarioId, $productoId, $cantidad) {
    global $conn;
    
    if ($cantidad <= 0) {
        return eliminarDelCarrito($usuarioId, $productoId);
    }
    
    $stmt = $conn->prepare("UPDATE carrito 
                           SET Cantidad_Producto = :cantidad 
                           WHERE ID_Usuario = :usuarioId AND ID_Producto = :productoId");
    $stmt->bindParam(':usuarioId', $usuarioId);
    $stmt->bindParam(':productoId', $productoId);
    $stmt->bindParam(':cantidad', $cantidad);
    
    return $stmt->execute();
}

function eliminarDelCarrito($usuarioId, $productoId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM carrito 
                           WHERE ID_Usuario = :usuarioId AND ID_Producto = :productoId");
    $stmt->bindParam(':usuarioId', $usuarioId);
    $stmt->bindParam(':productoId', $productoId);
    return $stmt->execute();
}

function vaciarCarrito($usuarioId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM carrito WHERE ID_Usuario = :usuarioId");
    $stmt->bindParam(':usuarioId', $usuarioId);
    return $stmt->execute();
}

function crearFactura($usuarioId, $productos) {
    global $conn;
    
    try {
        $conn->beginTransaction();
        
        // Calcular total
        $total = 0;
        foreach ($productos as $producto) {
            $total += $producto['Precio_Producto'] * $producto['Cantidad_Producto'];
        }
        
        // Insertar factura
        $stmt = $conn->prepare("INSERT INTO factura (ID_Usuario, Precio_Final) 
                               VALUES (:usuarioId, :total)");
        $stmt->bindParam(':usuarioId', $usuarioId);
        $stmt->bindParam(':total', $total);
        $stmt->execute();
        
        $facturaId = $conn->lastInsertId();
        
        // Insertar detalles de factura
        foreach ($productos as $producto) {
            $stmt = $conn->prepare("INSERT INTO detalle_factura 
                                   (ID_Factura, ID_Producto, Cantidad, Precio_Unitario) 
                                   VALUES (:facturaId, :productoId, :cantidad, :precio)");
            $stmt->bindParam(':facturaId', $facturaId);
            $stmt->bindParam(':productoId', $producto['ID_Producto']);
            $stmt->bindParam(':cantidad', $producto['Cantidad_Producto']);
            $stmt->bindParam(':precio', $producto['Precio_Producto']);
            $stmt->execute();
        }
        
        // Vaciar carrito
        vaciarCarrito($usuarioId);
        
        $conn->commit();
        return $facturaId;
    } catch (Exception $e) {
        $conn->rollBack();
        return false;
    }
}

function obtenerHistorialCompras($usuarioId) {
    global $conn;
    $stmt = $conn->prepare("SELECT f.ID_Factura, f.Fecha_Factura, f.Precio_Final, 
                           df.ID_Producto, df.Cantidad, df.Precio_Unitario, 
                           p.Nombre_Producto, p.Imagen_URL
                           FROM factura f
                           JOIN detalle_factura df ON f.ID_Factura = df.ID_Factura
                           JOIN productos p ON df.ID_Producto = p.ID_Producto
                           WHERE f.ID_Usuario = :usuarioId
                           ORDER BY f.Fecha_Factura DESC");
    $stmt->bindParam(':usuarioId', $usuarioId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
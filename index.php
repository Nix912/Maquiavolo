<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$productos = obtenerProductos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - La Hermandad de los Videojuegos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="search-bar">
        <input type="text" placeholder="Buscar juegos, productos...">
        <button><i class="fas fa-search"></i> Buscar</button>
    </div>
    
    <div class="container">
        <section id="home" class="content-section active">
            <div class="hero-banner">
                <div class="hero-content">
                    <h2>Requiescat in Pace, Gamers</h2>
                    <p>Únete a la Hermandad de los Videojuegos. <?= APP_NAME ?> te ofrece la mejor selección de títulos de Assassin's Creed, productos exclusivos y recargas para tus plataformas favoritas. Inspirados en el Renacimiento italiano y la saga de Ezio Auditore.</p>
                    <div>
                        <a href="#" class="btn nav-link" data-section="games">Explorar Juegos</a>
                        <a href="#" class="btn btn-outline nav-link" data-section="stores">Recargar Saldo</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="services content-section">
            <h2 class="section-title">Nuestros Servicios</h2>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-hood-cloak"></i>
                    </div>
                    <h3>Juegos de la Hermandad</h3>
                    <p>Toda la saga Assassin's Creed, desde los clásicos hasta los últimos lanzamientos, con ediciones especiales y coleccionistas.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h3>Recargas Digitales</h3>
                    <p>Recarga saldo al instante en tus plataformas favoritas como Steam, PlayStation Network y más, con códigos inmediatos.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-feather-alt"></i>
                    </div>
                    <h3>Coleccionables Exclusivos</h3>
                    <p>Figuras, espadas ocultas, atuendos y más productos oficiales de Assassin's Creed que no encontrarás en otro lugar.</p>
                </div>
            </div>
        </section>

        <section id="games" class="content-section">
            <h2 class="section-title">Juegos de la Hermandad</h2>
            <div class="filters">
                <div class="filter-group">
                    <label for="platform-filter">Plataforma:</label>
                    <select id="platform-filter">
                        <option value="all">Todas</option>
                        <option value="pc">PC</option>
                        <option value="ps5">PlayStation 5</option>
                        <option value="xbox">Xbox Series X</option>
                        <option value="switch">Nintendo Switch</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="genre-filter">Género:</label>
                    <select id="genre-filter">
                        <option value="all">Todos</option>
                        <option value="action">Acción</option>
                        <option value="rpg">RPG</option>
                        <option value="adventure">Aventura</option>
                        <option value="stealth">Sigilo</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="sort-by">Ordenar por:</label>
                    <select id="sort-by">
                        <option value="popular">Más populares</option>
                        <option value="newest">Más recientes</option>
                        <option value="price-low">Precio: menor a mayor</option>
                        <option value="price-high">Precio: mayor a menor</option>
                    </select>
                </div>
            </div>
            <div class="products-grid">
                <?php foreach ($productos as $producto): ?>
                <div class="product-card" data-product-id="<?= $producto['ID_Producto'] ?>" data-price="<?= $producto['Precio_Producto'] ?>">
                    <div class="product-image" style="background-image: url('<?= $producto['Imagen_URL'] ?>');">
                        <span class="product-badge">Oferta</span>
                    </div>
                    <div class="product-info">
                        <div class="product-platforms">
                            <span class="platform-icon" title="PlayStation 5"><i class="fab fa-playstation"></i></span>
                            <span class="platform-icon" title="Xbox Series X"><i class="fab fa-xbox"></i></span>
                            <span class="platform-icon" title="PC"><i class="fas fa-desktop"></i></span>
                        </div>
                        <h3 class="product-title"><?= htmlspecialchars($producto['Nombre_Producto']) ?></h3>
                        <p class="product-description"><?= htmlspecialchars($producto['Descripcion']) ?></p>
                        <div class="product-price">
                            <span class="price-main">$<?= number_format($producto['Precio_Producto'], 2) ?></span>
                        </div>
                        <div class="product-actions">
                            <button class="btn add-to-cart" 
                                    data-name="<?= htmlspecialchars($producto['Nombre_Producto']) ?>" 
                                    data-price="<?= $producto['Precio_Producto'] ?>">
                                <i class="fas fa-cart-plus"></i> Añadir
                            </button>
                            <button class="btn view-details">
                                <i class="fas fa-info-circle"></i> Detalles
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Otras secciones (products, stores, preorders, profile) -->
        <?php include 'includes/sections.php'; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/modals.php'; ?>

    <script src="assets/js/main.js"></script>
</body>
</html>
<?php
require_once 'includes/auth.php';

// Si el usuario ya está logueado, redirigir a la página principal
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = '';

// Procesar formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validar que las contraseñas coincidan
    if ($password !== $confirmPassword) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Intentar registrar al usuario
        if (registerUser($nombre, $apellido, $email, $password)) {
            // Iniciar sesión automáticamente después del registro
            if (loginUser($email, $password)) {
                header("Location: index.php");
                exit();
            }
        } else {
            $error = "El email ya está registrado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-logo">
            <img src="assets/img/logo.png" alt="<?= APP_NAME ?>">
            <h1><?= APP_NAME ?></h1>
            <p><?= APP_TAGLINE ?></p>
        </div>

        <div class="auth-form">
            <h2>Registrarse</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label for="nombre"><i class="fas fa-user"></i> Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="apellido"><i class="fas fa-user"></i> Apellido:</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirmar Contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-user-plus"></i> Registrarse
                </button>
            </form>

            <div class="auth-footer">
                <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
        </div>
    </div>
</body>
</html>
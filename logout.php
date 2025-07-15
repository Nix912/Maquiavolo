<?php
require_once 'includes/auth.php';

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: login.php");
exit();
?>
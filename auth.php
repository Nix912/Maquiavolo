<?php
session_start();
require_once 'db.php';

$db = new Database();
$conn = $db->getConnection();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['es_admin'] == 1;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: index.php");
        exit();
    }
}

function loginUser($email, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Correo = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['Contrasena'])) {
        $_SESSION['user_id'] = $user['ID_Usuario'];
        $_SESSION['nombre'] = $user['Nombre'];
        $_SESSION['apellido'] = $user['Apellido'];
        $_SESSION['email'] = $user['Correo'];
        $_SESSION['es_admin'] = $user['es_admin'];
        return true;
    }
    
    return false;
}

function registerUser($nombre, $apellido, $email, $password) {
    global $conn;
    
    // Verificar si el email ya existe
    $stmt = $conn->prepare("SELECT ID_Usuario FROM usuarios WHERE Correo = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        return false; // Usuario ya existe
    }
    
    // Hash de la contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertar nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (Nombre, Apellido, Correo, Contrasena, es_admin) 
                           VALUES (:nombre, :apellido, :email, :contrasena, 0)");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contrasena', $hashedPassword);
    
    return $stmt->execute();
}
?>
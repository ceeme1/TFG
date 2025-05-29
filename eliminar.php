<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Verificar que se ha enviado el formulario con el nombre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];

    // Eliminar la cerveza del usuario
    $sql = "DELETE FROM cervezas WHERE nombre = ? AND usuario_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $user_id]);
}

// Redirigir de nuevo al panel principal
header("Location: panel.php");
exit;
?>
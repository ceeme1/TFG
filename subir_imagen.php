<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['nueva_imagen']) && isset($_POST['cerveza_id'])) {
    $cerveza_id = (int)$_POST['cerveza_id'];
    $user_id = $_SESSION['user_id'];
    $archivo = $_FILES['nueva_imagen'];

    if ($archivo['error'] === UPLOAD_ERR_OK) {
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if (in_array($extension, $extensiones_permitidas)) {
            if ($archivo['size'] <= 2 * 1024 * 1024) {
                $nuevoNombre = uniqid('img_') . '.' . $extension;
                $rutaDestino = 'uploads/' . $nuevoNombre;

                if (!is_dir('uploads')) {
                    mkdir('uploads', 0755, true);
                }

                move_uploaded_file($archivo['tmp_name'], $rutaDestino);

                $stmt = $pdo->prepare("UPDATE cervezas SET imagen = ? WHERE id = ? AND usuario_id = ?");
                $stmt->execute([$rutaDestino, $cerveza_id, $user_id]);
            }
        }
    }
}

header("Location: panel.php");
exit;
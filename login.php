<?php
session_start();
require_once 'conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $contra = $_POST['contra'];

    $stmt = $pdo->prepare("SELECT id, contra, es_admin FROM users WHERE nombre = ?");
    $stmt->execute([$nombre]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($contra, $usuario['contra'])) {
        $_SESSION['user_id'] = $usuario['id'];

        // Verificación de administrador
        if (!empty($usuario['es_admin'])) {
            $_SESSION['admin'] = true;
            header("Location: admin_usuarios.php");
        } else {
            header("Location: panel.php");
        }
        exit;
    } else {
        $error = "Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>OTRAPP!!!</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        body {
            text-align:center;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('assets/beerapp.gif') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-section {
            background: #da920d9f;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
        }

        h2, h3 {
            text-align: center;
            color: #5d3c0a;
        }

        .error {
            color: #c0392b;
            text-align: center;
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #da920d9f;
            border-radius: 4px;
            font-size: 16px;
            background-color: #da920d9f;
            color: #5d3c0a;
        }

        .form-buttons {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        input[type="submit"] {
            background-color: #da920d;
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #eea321;
        }

        @media (max-width: 480px) {
            .form-section {
                padding: 20px;
            }

            input[type="text"],
            input[type="password"],
            input[type="submit"] {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <section class="form-section">
            <h2>Iniciar sesión</h2>

            <?php if (!empty($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <h3>OTRA!!</h3>
            <form method="POST" class="login-form">
                <div class="form-group">
                    <input type="text" name="nombre" placeholder="Usuario" required>
                </div>
                <div class="form-group">
                    <input type="password" name="contra" placeholder="Contraseña" required>
                </div>
                <div class="form-buttons">
                    <input type="submit" value="OtrAPP!!!">
                </div>
            </form>

            <form action="registro.php" class="register-form" method="get">
                <div class="form-buttons">
                    <input type="submit" value="Crear usuario">
                </div>
            </form>
            <p><br>
            <p><br>
            <p><br>
        <h6>C.M.A.S Dev/OtrAPP!!!-v.0.79<h6>
        </section>
        
    </div>

</body>
</html>

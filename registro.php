<?php
require_once 'conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $contra_plana = $_POST['contra'];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE nombre = ?");
    $stmt->execute([$nombre]);
    $existe = $stmt->fetchColumn();

    if ($existe) {
        $mensaje = "El nombre de usuario ya está registrado. <a href='login.php'>Iniciar sesión</a>";
    } else {
        $contra = password_hash($contra_plana, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (nombre, contra) VALUES (?, ?)");
        $stmt->execute([$nombre, $contra]);
        $mensaje = "Usuario creado correctamente. <a href='login.php'>Iniciar sesión</a>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('https://i.makeagif.com/media/3-21-2019/I1s5Qr.gif') center center fixed;
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
            background:  #da920d9f;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            text-align: center;
        }

        h2 {
            color: #5d3c0a;
            margin-bottom: 15px;
        }

        .mensaje {
            color:rgb(3, 3, 2);
            margin-bottom: 15px;
        }
        .mensaje {
        font-size: 16px;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        font-weight: bold;
        }

        .mensaje.error {
            background-color: #ffdddd;
            color: #d8000c;
            border: 1px solid #d8000c;
        }

        .mensaje.exito {
            background-color: #ddffdd;
            color: #270;
            border: 1px solid #270;
        }

        .centrado {
            text-align: center;
            margin-top: 10px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #da920d9f;
            border-radius: 4px;
            font-size: 16px;
            background-color:  #da920d9f;
            color: #5d3c0a;
            margin-bottom: 15px;
        }

        input[type="submit"],
        .btn-volver {
            background-color: #da920d;
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            text-decoration: none;
            display: inline-block;
        }

        input[type="submit"]:hover,
        .btn-volver:hover {
            background-color: #eea321;
        }

        a {
            color: #eea321;
            font-weight: bold;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            input[type="text"],
            input[type="password"],
            input[type="submit"],
            .btn-volver {
                font-size: 1rem;
            }

            .form-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <section class="form-section">
            <h2>Registro de Usuario</h2>

            <?php if (!empty($mensaje)): ?><p><br>
                <div class="mensaje"><?= $mensaje ?></div>
            <?php endif; ?>

            <form method="POST">
                <input name="nombre" type="text" placeholder="Usuario" required>
                <input name="contra" type="password" placeholder="Contraseña" required>
                <input type="submit" value="Registrarse">
            </form>

            <a href="login.php" class="btn-volver">Volver a inicio</a>
        </section>
    </div>
</body>
</html>
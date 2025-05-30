<?php
require_once 'conexion.php';
session_start();

// Protección de acceso
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

$mensaje = '';

// Eliminar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $nombre = $_POST['nombre'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE nombre = ?");
    $stmt->execute([$nombre]);
    $mensaje = "Usuario '$nombre' eliminado correctamente.";
}

// Paginación
$usuariosPorPagina = 10;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina - 1) * $usuariosPorPagina;

$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPaginas = ceil($totalUsuarios / $usuariosPorPagina);

$stmt = $pdo->prepare("SELECT nombre FROM users ORDER BY nombre ASC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $usuariosPorPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Usuarios OTRAPP!!!</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('assets/beerapp.gif') center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-container {
            width: 100%;
            max-width: 600px;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .admin-section {
            background: #da920d9f;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            text-align: center;
        }

        h2 {
            color: #5d3c0a;
            margin-bottom: 20px;
        }

        .usuario-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px 15px;
            background:rgba(231, 193, 55, 0.65);
            border: 1px solid #e0c891;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .usuario-item:hover {
            background-color: #f3ead2;
        }

        .usuario-nombre {
            color: #5d3c0a;
            font-weight: bold;
        }

        .btn-eliminar {
            background-color: #cc0000;
            color: black;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-eliminar:hover {
            background-color: #e60000;
        }

        .mensaje {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .mensaje.exito {
            background-color: #ddffdd;
            color: #270;
            border: 1px solid #270;
        }

        .btn-volver {
            background-color: #da920d;
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .btn-volver:hover {
            background-color: #eea321;
        }

        .btn-cerrar-sesion {
            background-color:rgb(208, 148, 19);
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 12px;
        }

        .btn-cerrar-sesion:hover {
            background-color:rgb(179, 116, 0);
        }
    </style>
</head>
<body>
    <div class="main-container">
        <section class="admin-section">
            <h2>Administrar Usuarios</h2>

            <?php if (!empty($mensaje)): ?>
                <div class="mensaje exito"><?= $mensaje ?></div>
            <?php endif; ?>

            <?php foreach ($usuarios as $usuario): ?>
                <div class="usuario-item">
                    <span class="usuario-nombre"><?= htmlspecialchars($usuario['nombre']) ?></span>
                    <form method="POST" style="margin: 0;">
                        <input type="hidden" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>">
                        <button type="submit" name="eliminar" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar a <?= htmlspecialchars($usuario['nombre']) ?>?')">Eliminar</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <!-- Paginación -->
            <div style="margin-top: 20px;">
                <?php if ($pagina > 1): ?>
                    <a class="btn-volver" href="?pagina=<?= $pagina - 1 ?>">Anterior</a>
                <?php endif; ?>

                <?php if ($pagina < $totalPaginas): ?>
                    <a class="btn-volver" href="?pagina=<?= $pagina + 1 ?>">Siguiente</a>
                <?php endif; ?>
            </div>

            
            <a href="logout.php" class="btn-cerrar-sesion">Cerrar sesión</a>
        </section>
    </div>
</body>
</html>
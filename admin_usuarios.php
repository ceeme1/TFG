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

// Actualizar usuario (editar perfil)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $nombreViejo = $_POST['nombre_viejo'];
    $nombreNuevo = $_POST['nombre_nuevo'];

    if ($nombreNuevo === '') {
        $mensaje = "El nombre nuevo no puede estar vacío.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nombre = ? WHERE nombre = ?");
        $stmt->execute([$nombreNuevo, $nombreViejo]);
        $mensaje = "Usuario '$nombreViejo' actualizado a '$nombreNuevo' correctamente.";
    }
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
            flex-grow: 1;
            text-align: left;
        }

        .btn-eliminar, .btn-editar, .btn-guardar, .btn-cancelar {
            background-color: #cc0000;
            color: black;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-left: 5px;
        }

        .btn-editar {
            background-color: #f0ad4e;
        }

        .btn-guardar {
            background-color: #4CAF50;
        }

        .btn-cancelar {
            background-color: #888;
        }

        .btn-eliminar:hover {
            background-color: #e60000;
        }

        .btn-editar:hover {
            background-color: #ec971f;
        }

        .btn-guardar:hover {
            background-color: #45a049;
        }

        .btn-cancelar:hover {
            background-color: #666;
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

        /* Formulario inline para editar */
        .editar-form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-grow: 1;
        }

        .editar-form input[type="text"] {
            padding: 5px;
            font-size: 1rem;
            flex-grow: 1;
        }
    </style>
    <script>
        function mostrarEditar(id) {
            document.getElementById('nombre-display-' + id).style.display = 'none';
            document.getElementById('botones-display-' + id).style.display = 'none';
            document.getElementById('form-editar-' + id).style.display = 'flex';
        }

        function cancelarEditar(id) {
            document.getElementById('nombre-display-' + id).style.display = 'block';
            document.getElementById('botones-display-' + id).style.display = 'flex';
            document.getElementById('form-editar-' + id).style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="main-container">
        <section class="admin-section">
            <h2>Administrar Usuarios</h2>

            <?php if (!empty($mensaje)): ?>
                <div class="mensaje exito"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <?php foreach ($usuarios as $usuario): 
                $id = md5($usuario['nombre']); // ID único para HTML (evitar espacios y caracteres raros)
            ?>
                <div class="usuario-item" id="usuario-<?= $id ?>">
                    <span class="usuario-nombre" id="nombre-display-<?= $id ?>"><?= htmlspecialchars($usuario['nombre']) ?></span>
                    <div id="botones-display-<?= $id ?>" style="display: flex; gap: 5px;">
                        <button class="btn-editar" onclick="mostrarEditar('<?= $id ?>')">Editar</button>
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>">
                            <button type="submit" name="eliminar" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar a <?= htmlspecialchars($usuario['nombre']) ?>?')">Eliminar</button>
                        </form>
                    </div>

                    <!-- Formulario de edición oculto -->
                    <form method="POST" class="editar-form" id="form-editar-<?= $id ?>" style="display: none;">
                        <input type="hidden" name="nombre_viejo" value="<?= htmlspecialchars($usuario['nombre']) ?>">
                        <input type="text" name="nombre_nuevo" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                        <button type="submit" name="editar" class="btn-guardar">Guardar</button>
                        <button type="button" class="btn-cancelar" onclick="cancelarEditar('<?= $id ?>')">Cancelar</button>
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
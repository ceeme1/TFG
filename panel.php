<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Obtener el nombre del usuario desde la base de datos
$stmtUser = $pdo->prepare("SELECT nombre FROM users WHERE id = ?");
$stmtUser->execute([$user_id]);
$userData = $stmtUser->fetch();

$username = $userData ? $userData['nombre'] : "Usuario";

$dbStatus = $pdo ? "Conexión a BD: OK" : "Conexión a BD: ERROR";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva'])) {
    $sql = "INSERT INTO cervezas (nombre, procedencia, fermentacion, graduacion, imagen, sitios, cuantas, usuario_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['nombre'],
        $_POST['procedencia'],
        $_POST['fermentacion'],
        $_POST['graduacion'],
        $_POST['imagen'],
        $_POST['sitios'],
        $_POST['cuantas'],
        $user_id
    ]);
}

$stmt = $pdo->prepare("SELECT * FROM cervezas WHERE usuario_id = ?");
$stmt->execute([$user_id]);
$cervezas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi colección de cervezas</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        body {
            padding: 20px;
            color: black;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* Contenedor cabecera superior */
        .header-top {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #ff9500a0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            font-weight: bold;
            color: black;
        }

        .header-left {
            font-size: 1rem;
        }

        .header-right {
            font-size: 1rem;
        }

        /* Para que el contenido no quede oculto tras la cabecera fija */
        .main-container {
            display: flex;
            flex-direction: column;
            gap: 30px;
            align-items: center;
            color: black;
            padding-top: 120px; /* espacio para la cabecera fija */
        }

        .contenido-flex {
            display: flex;
            gap: 20px;
            flex-wrap: nowrap;
            width: 100%;
            justify-content: center;
            color: black;
        }

        .form-section {
            background: #daaf2241;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px #da920d9f;
            flex: 1;
            min-width: 280px;
            max-width: 400px;
            color: black;
        }

        .table-section {
            background: #ff9500a0;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px #da920d9f;
            flex: 2;
            min-width: 320px;
            color: black;
        }

        /* Aquí el scroll con altura fija y scroll vertical */
        .table-scroll {
            max-height: 400px; /* altura fija para scroll */
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: black;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #da920d9f;
            color: black;
        }

        /* Cabecera sticky */
        table th {
            position: sticky;
            top: 0;
            background-color: #eea321;
            color: black;
            z-index: 10;
        }

        table img {
            max-height: 120px;
            border-radius: 4px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            color: black;
        }

        select, input, button {
            padding: 8px;
            color: black;
        }

        .formularios-abajo {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 20px;
            justify-content: center;
            color: black;
        }

        .inicio {
            margin-top: 30px;
            text-align: center;
            color: black;
        }

        /* Resaltado enlace cerrar sesión */
        .inicio a {
            color: #eea321;
            font-weight: bold;
            text-decoration: none;
            font-size: 1.1rem;
            padding: 8px 16px;
            border: 2px solid #eea321;
            border-radius: 6px;
            transition: background-color 0.3s, color 0.3s;
        }
        .inicio a:hover {
            background-color: #eea321;
            color: black;
        }

        @media (max-width: 991px) {
            .contenido-flex {
                flex-direction: column !important;
                align-items: center;
                gap: 20px;
                width: 100%;
            }

            .form-section, .table-section {
                max-width: 100%;
                width: 100%;
                flex: none;
            }

            .formularios-abajo {
                flex-direction: column !important;
                gap: 15px;
                width: 100%;
                max-width: 100%;
            }
        }

        @media (max-width: 767px) {
            form select, form input, form button {
                font-size: 1.1rem;
            }

            table {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="header-top">
        <div class="header-left"><?= htmlspecialchars($dbStatus) ?></div>
        <div class="header-right">Usuario: <?= htmlspecialchars($username) ?></div>
    </div>

    <div class="main-container">
        <h2 style="color:black;">¡OTRA!</h2>

        <div class="contenido-flex">
            <!-- Formulario de inserción -->
            <form method="POST" class="form-section">
                <input name="nombre" placeholder="Nombre" required type="text">
                <input name="procedencia" placeholder="Procedencia" type="text">
                <input name="fermentacion" placeholder="Fermentación" type="text">
                <input name="graduacion" placeholder="Graduación" type="text">
                <input name="imagen" placeholder="URL Imagen" type="url">
                <input name="sitios" placeholder="Sitios" type="text">
                <input name="cuantas" type="number" value="1" min="1">
                <button name="nueva" type="submit">Agregar</button>
            </form>

            <!-- Tabla -->
            <div class="table-section">
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Procedencia</th>
                                <th>Fermentación</th>
                                <th>Graduación</th>
                                <th>Imagen</th>
                                <th>Llevas</th>
                                <th>Sitios</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cervezas as $c): ?>
                                <tr>
                                    <td><?= htmlspecialchars($c['nombre']) ?></td>
                                    <td><?= htmlspecialchars($c['procedencia']) ?></td>
                                    <td><?= htmlspecialchars($c['fermentacion']) ?></td>
                                    <td><?= htmlspecialchars($c['graduacion']) ?>%</td>
                                    <td><img src="<?= htmlspecialchars($c['imagen']) ?>" alt="Imagen de <?= htmlspecialchars($c['nombre']) ?>"></td>
                                    <td><?= htmlspecialchars($c['cuantas']) ?></td>
                                    <td><?= htmlspecialchars($c['sitios']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Formularios debajo del scroll -->
        <div class="formularios-abajo">
            <!-- Formulario de incrementar -->
            <form method="post" action="unamas.php" class="form-section">
                <label for="nombre">¿Cuál sumas?</label>
                <select name="nombre" id="nombre">
                    <?php foreach ($cervezas as $c): ?>
                        <option value="<?= htmlspecialchars($c['nombre']) ?>">
                            <?= htmlspecialchars($c['nombre']) ?> (<?= $c['cuantas'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="incrementar">+1</button>
            </form>

            <!-- Formulario de eliminar -->
            <form method="post" action="eliminar.php" class="form-section">
                <label for="nombre">¿Quieres quitar alguna?</label>
                <select name="nombre" id="nombre">
                    <?php foreach ($cervezas as $c): ?>
                        <option value="<?= htmlspecialchars($c['nombre']) ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="eliminar">Eliminar</button>
            </form>
        </div>

        <!-- Cierre de sesión -->
        <div class="inicio">
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>
</body>
</html>
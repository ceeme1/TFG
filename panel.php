<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function descargarTabla() {
            const boton = document.querySelector('#boton-descarga');
            const tabla = document.getElementById('captura-tabla');

            boton.style.visibility = 'hidden';

            html2canvas(tabla, {
                useCORS: true,
                scale: 2
            }).then(canvas => {
                boton.style.visibility = 'visible';
                const enlace = document.createElement('a');
                enlace.download = 'mi_tabla_de_cervezas.png';
                enlace.href = canvas.toDataURL('image/png');
                enlace.click();
            });
        }
    </script>
    <style>
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
            margin: 0;
            color: black;
        }

        .header-top {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #ff9500a0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            font-weight: bold;
        }

        .main-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 120px;
            gap: 30px;
        }

        .contenido-flex {
            display: flex;
            gap: 20px;
            flex-wrap: nowrap;
            justify-content: center;
            width: 100%;
        }

        .form-section {
            background: #daaf2241;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px #da920d9f;
            flex: 1;
            min-width: 280px;
            max-width: 400px;
        }

        .table-section {
            background: #ff9500a0;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px #da920d9f;
            flex: 2;
            min-width: 320px;
        }

        .table-scroll {
            max-height: 400px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #da920d9f;
        }

        table th {
            position: sticky;
            top: 0;
            background-color: #eea321;
            z-index: 10;
        }

        table img {
            max-height: 120px;
            border-radius: 4px;
            cursor: pointer;
        }

        .formularios-abajo {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }

        .inicio {
            margin-top: 30px;
            text-align: center;
        }

        .inicio a {
            color:rgb(135, 95, 26);
            font-weight: bold;
            text-decoration: none;
            padding: 8px 16px;
            border: 2px solidrgb(77, 50, 3);
            border-radius: 6px;
            transition: background-color 0.3s, color 0.3s;
        
        }

        .inicio a:hover {
            background-color: #eea321;
            color: black;
        }

        @media (max-width: 991px) {
            .contenido-flex {
                flex-direction: column;
                align-items: center;
                width: 100%;
            }

            .form-section, .table-section {
                width: 100%;
            }

            .formularios-abajo {
                flex-direction: column;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="header-top">
    <div><?= htmlspecialchars($dbStatus) ?></div>
    <div>Usuario: <?= htmlspecialchars($username) ?></div>
</div>

<div class="main-container">
    <h2>¡OTRA!</h2>

    <div class="contenido-flex">
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

        <div class="table-section">
            <button id="boton-descarga" onclick="descargarTabla()">📥 Descargar tabla</button>

            <div id="captura-tabla">
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
                                    <td>
                                        <img src="<?= htmlspecialchars($c['imagen']) ?>" alt="Imagen" onclick="document.getElementById('fileInput<?= $c['id'] ?>').click();">
                                        <form method="POST" action="subir_imagen.php" enctype="multipart/form-data" style="display:none;">
                                            <input type="hidden" name="cerveza_id" value="<?= $c['id'] ?>">
                                            <input type="file" name="nueva_imagen" id="fileInput<?= $c['id'] ?>" accept="image/*" onchange="this.form.submit();">
                                        </form>
                                    </td>
                                    <td><?= htmlspecialchars($c['cuantas']) ?></td>
                                    <td><?= htmlspecialchars($c['sitios']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="formularios-abajo">
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

    <div class="inicio">
        <a href="logout.php">Cerrar sesión</a>
    </div>
</div>

</body>
</html>
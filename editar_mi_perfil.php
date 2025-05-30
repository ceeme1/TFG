<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$mensaje = '';

// Obtener los datos actuales del usuario
$stmt = $pdo->prepare("SELECT nombre, es_admin FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    die("Usuario no encontrado.");
}

// Si se envía el formulario de edición del perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_perfil'])) {
    $nuevo_nombre = $_POST['nombre'];
    $nueva_contra = $_POST['contra'];

    if (!empty($nueva_contra)) {
        $hash = password_hash($nueva_contra, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET nombre = ?, contra = ? WHERE id = ?");
        $stmt->execute([$nuevo_nombre, $hash, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nombre = ? WHERE id = ?");
        $stmt->execute([$nuevo_nombre, $user_id]);
    }

    $mensaje = "Perfil actualizado correctamente.";
    $_SESSION['username'] = $nuevo_nombre;
}

// Si se envía el formulario para editar cerveza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_cerveza'])) {
    $id = $_POST['id'];
    $sql = "UPDATE cervezas SET 
                nombre = ?, procedencia = ?, fermentacion = ?, graduacion = ?, imagen = ?, sitios = ?, cuantas = ? 
            WHERE id = ? AND usuario_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['nombre_cerveza'],
        $_POST['procedencia'],
        $_POST['fermentacion'],
        $_POST['graduacion'],
        $_POST['imagen'],
        $_POST['sitios'],
        $_POST['cuantas'],
        $id,
        $user_id
    ]);
    $mensaje = "Cerveza actualizada correctamente.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Perfil</title>
    <style>
        :root {
            --color-principal: #eea321;
            --color-secundario: #000000; /* negro */
            --color-fondo-transparente: #ff9500a0;
            --color-texto: #000000; /* texto negro para todo */
            --color-borde: rgba(212, 155, 24, 0.6);
            --color-input-fondo: rgba(255, 255, 255, 0.95);
            --color-boton-hover: #cc7a00;
            --color-sombra: rgba(0, 0, 0, 0.15);
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('assets/beerapp.gif') no-repeat center center fixed;
            background-size: cover;
            color: var(--color-texto);
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .contenido {
            background-color: var(--color-fondo-transparente);
            padding: 30px 35px;
            border-radius: 14px;
            box-shadow: 0 8px 24px var(--color-sombra);
            width: 90%;
            max-width: 960px;
            margin: 40px auto 10px auto;
            backdrop-filter: saturate(180%) blur(12px);
        }

        h2 {
            text-align: center;
            color: var(--color-texto); /* negro */
            font-weight: 700;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 7px;
            color: var(--color-texto); /* negro */
            text-align: justify;
        }

        input[type="text"],
        input[type="password"],
        input[type="url"],
        input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            border: 1.8px solid var(--color-borde);
            border-radius: 8px;
            background-color: var(--color-input-fondo);
            font-size: 15px;
            color: var(--color-texto);
            transition: border-color 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--color-principal);
            box-shadow: 0 0 6px var(--color-principal);
        }

        button {
            background-color: var(--color-principal);
            color: #000000; /* texto negro en botones */
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
            transition: background-color 0.25s ease;
            box-shadow: 0 3px 8px var(--color-borde);
        }

        button:hover {
            background-color: var(--color-boton-hover);
        }

        .volver {
            text-align: center;
            margin-top: 30px;
        }

        .volver a {
            text-decoration: none;
            color: var(--color-texto); /* negro */
            font-weight: 700;
        }

        .volver a:hover {
            color: var(--color-principal);
        }

        .tabla-wrapper {
            width: 100%;
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 1000px;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 15px;
            border-radius: 10px;
            background-color: var(--color-fondo-transparente);
            backdrop-filter: saturate(180%) blur(10px);
            margin: 0 auto;
            color: var(--color-texto);
        }

        th, td {
            padding: 14px 18px;
            border-bottom: 1px solid var(--color-borde);
            color: var(--color-texto);
        }

        th {
            background-color:rgba(0, 0, 0, 0); /* header negro */
            color: var(--color-texto); /* texto negro */
            font-weight: bold;
            text-align: left;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            .contenido {
                padding: 20px;
            }

            table {
                font-size: 13px;
                width: 100%;
            }

            input, button {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="contenido">
        <h2>Editar mi perfil</h2>

        <?php if ($mensaje): ?>
            <p style="color:#000000; text-align:center;"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="editar_perfil" value="1" />
            <div class="form-group">
                <label for="nombre">Nombre de usuario:</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required />
            </div>

            <div class="form-group">
                <label for="contra">Nueva contraseña (opcional):</label>
                <input type="password" name="contra" />
            </div>

            <button type="submit">Guardar cambios</button>
        </form>

        <div class="volver">
            <a href="panel.php">← Volver al panel</a>
        </div>
    </div>

    <div class="tabla-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Procedencia</th>
                    <th>Fermentación</th>
                    <th>Graduación</th>
                    <th>Imagen</th>
                    <th>Sitios</th>
                    <th>Cuántas</th>
                    <th>Guardar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmtCervezas = $pdo->prepare("SELECT * FROM cervezas WHERE usuario_id = ?");
                $stmtCervezas->execute([$user_id]);
                $cervezas = $stmtCervezas->fetchAll();

                foreach ($cervezas as $c):
                ?>
                <tr>
                    <form method="POST">
                        <input type="hidden" name="editar_cerveza" value="1" />
                        <input type="hidden" name="id" value="<?= $c['id'] ?>" />
                        <td><input type="text" name="nombre_cerveza" value="<?= htmlspecialchars($c['nombre']) ?>" required /></td>
                        <td><input type="text" name="procedencia" value="<?= htmlspecialchars($c['procedencia']) ?>" /></td>
                        <td><input type="text" name="fermentacion" value="<?= htmlspecialchars($c['fermentacion']) ?>" /></td>
                        <td><input type="text" name="graduacion" value="<?= htmlspecialchars($c['graduacion']) ?>" /></td>
                        <td><input type="url" name="imagen" value="<?= htmlspecialchars($c['imagen']) ?>" /></td>
                        <td><input type="text" name="sitios" value="<?= htmlspecialchars($c['sitios']) ?>" /></td>
                        <td><input type="number" name="cuantas" value="<?= $c['cuantas'] ?>" min="0" /></td>
                        <td><button type="submit">💾</button></td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
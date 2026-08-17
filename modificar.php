<?php

session_start();
require_once 'conexion.php';

// Por seguridad, solo usuarios logueados como administradores pueden ver esta página.
if (isset($_SESSION['usuario_administrador']) == false) {
    header('Location: index.php');
    exit();
}

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identificador = $_POST['identificador'];
    $numero = $_POST['numero'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $imagen_ruta = $_POST['imagen_ruta'];
    $tipo_identificador = $_POST['tipo_identificador'];

    $sql_actualizacion = "UPDATE pokemon
                          SET numero = ?, nombre = ?, descripcion = ?, imagen_ruta = ?, tipo_identificador = ?
                          WHERE identificador = ?";

    $stmt_actualizacion = $conexion->prepare($sql_actualizacion);
    $stmt_actualizacion->bind_param("isssii", $numero, $nombre, $descripcion, $imagen_ruta, $tipo_identificador, $identificador, $tipo_identificador);

    if ($stmt_actualizacion->execute() == true) {
        $mensaje = "<p style='color: green;'>¡Pokémon actualizado con éxito!</p>";
    } else {
        $mensaje = "<p style='color: red;'>Error al actualizar: " . $conexion->error . "</p>";
    }
}

//  Obtener los datos actuales del pokémon.
    $identificador_pokemon = 0;
    if (isset($_GET['identificador']) == true) {
        $identificador_pokemon = (int)$_GET['identificador'];
    }

    if (isset($_POST['identificador']) == true) {
        $identificador_pokemon = (int)$_POST['identificador'];
    }

    if ($identificador_pokemon === 0) {
        header('Location: index.php');
        exit();
    }

    $sql_pokemon = "SELECT * 
                    FROM pokemon 
                    WHERE identificador = ?";

    $stmt_pokemon = $conexion->prepare($sql_pokemon);
    $stmt_pokemon->bind_param("i", $identificador_pokemon);
    $stmt_pokemon->execute();

    $resultado_pokemon = $stmt_pokemon->get_result();
    if ($resultado_pokemon->num_rows === 0) {
        echo "<h3>Error: Pokémon no encontrado.</h3>";
        echo '<a href="index.php">Volver al inicio</a>';
        exit();
    }

    $pokemon = $resultado_pokemon->fetch_assoc();

    $sql_tipos = "SELECT identificador, nombre
                  FROM tipos";

    $resultado_tipos = $conexion->query($sql_tipos);
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Modificar pokémon</title>

    <!-- Fuente Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700;900&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-image: url(assets/img/fondos/modificar-fondo.jpg);
            min-height: 100vh;
            padding: 20px;
            color: #333;
            background-size: cover;
            background-position: center;
        }

        .edit-card {
            width: 100%;
            max-width: 600px;
            margin: auto;
            margin-top: 50px;
            zoom: 1.1;
            border-radius: 10px;
            border: 4px solid #695b0c;
            box-shadow: 0px 7px 0px #695b0c, 0px 10px 50px 10px #00000085;
            padding: 35px 40px;
            background: linear-gradient(180deg, #fad81c 50%, #faeead 70%);
            background-color: #fad81c;
        }

        .edit-card h2 {
            color: #fd401f;
            font-size: 38px;
            text-transform: uppercase;
            font-weight: 900;
            margin-bottom: 25px;
            text-shadow: 0px 3px 0px rgba(0, 0, 0, 0.2), 0.7px 0px 0px white;
            text-decoration: underline;
            font-family: 'Montserrat';
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
            font-size: 15px;
        }

        .alert.exito {
            background: linear-gradient(to bottom, #d4edda, #c3e6cb);
            border: 2px solid #28a745;
            color: #155724;
            box-shadow: 0px 4px 0px #1557244d;
        }

        .alert.error {
            background: linear-gradient(to bottom, #ffcccc, #ff9999);
            border: 2px solid #e3350d;
            color: #941700;
            box-shadow: 0px 4px 0px #9417004d;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 700;
            font-size: 17px;
            color: #912512;
            margin-bottom: 5px;
            display: block;
            text-transform: uppercase;
            font-family: 'Montserrat';
            text-shadow: 0.5px 0px 0px #912512;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            border: 2px solid #912512;
            border-radius: 7px;
            font-size: 15px;
            box-shadow: 0px 5px 0px rgb(145 37 18);
            padding: 12px 15px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            color: #333;
            transition: all 0.2s ease;
            background-color: #fcfcfc;
            margin-top: -20px;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #316eb5;
            box-shadow: 0px 5px 0px #316eb5;
            background-color: #fff;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 7px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            color: white;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 20px;
            box-shadow: 0px 5px 0px #912512;
            border: 1px solid #ffffff30;
            text-shadow: 0px 3px 0px black, 0px 0px 15px #ffffffa8;
            margin-top: 10px;
            margin-bottom: -10px;
            transition: transform 0.1s, filter 0.2s;
            background: linear-gradient(to bottom, #fd401f 60%, #fd7a64 95%);
        }

        .btn-submit:hover {
            transform: translateY(2px);
            filter: brightness(1.1);
        }

        .btn-back {
            padding: 15px;
            text-decoration: none;
            font-weight: 900;
            font-size: 17px;
            color: white;
            text-transform: uppercase;
            border-radius: 7px;
            background: linear-gradient(to bottom, #868e96, #6c757d);
            box-shadow: 0px 5px 0px #495057;
            border: 1px solid #ffffff30;
            text-shadow: 0px 2px 0px black, 0px 0px 15px #ffffff9e;
            transition: transform 0.1s, filter 0.2s, box-shadow 0.1s;
            display: flex;
            justify-content: center;
            margin: auto;
            align-items: center;
            max-width: 255px;
            margin-top: 35px;
        }

        .btn-back:hover {
            transform: translateY(2px);
            filter: brightness(1.1);
        }
    </style>
</head>

<body style="font-family: Arial, sans-serif;">

<div class="edit-card">
<h2 style="text-align: center;">Editar a <?= $pokemon['nombre'] ?></h2>

    <?php if ($mensaje != ""): ?>
        <div class="alert <?= $tipo_mensaje ?>">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="modificar.php">

        <input type="hidden" name="id" value="<?= $pokemon['identificador'] ?>">

        <div class="form-group">
            <label>Número de Pokédex:</label><br>
            <input type="number" name="numero" value="<?= $pokemon['numero'] ?>" required style="width: 100%;">
        </div>

        <div class="form-group">
            <label>Nombre:</label><br>
            <input type="text" name="nombre" value="<?= $pokemon['nombre'] ?>" required style="width: 100%;">
        </div>

        <div class="form-group">
            <label>Descripción:</label><br>
            <textarea name="descripcion" rows="4" required
                      style="width: 100%;"><?= $pokemon['descripcion'] ?></textarea>
        </div>

        <div class="form-group">
            <label>Ruta de la imagen:</label><br>
            <input type="text" name="imagen_ruta" value="<?= $pokemon['imagen_ruta'] ?>" required style="width: 100%;">
        </div>

        <div class="form-group">
            <label>Tipo:</label><br>
            <select name="tipo_identificador" required style="width: 100%; padding: 5px;">
                <option value="">-- Seleccioná un tipo --</option>
                <?php
                if ($resultado_tipos && $resultado_tipos->num_rows > 0) {
                    while ($tipo = $resultado_tipos->fetch_assoc()) {

                        $seleccionado = "";
                        if (($tipo['identificador'] == $pokemon['tipo_identificador']) == true) {
                            $seleccionado = "selected";
                        }

                        echo "<option value='" . $tipo['id'] . "' $seleccionado>" . $tipo['nombre'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn-submit">
            Guardar Cambios
        </button>
    </form>
	
	<a href="index.php" class="btn-back">Volver a la Pokédex</a>

</body>
</html>


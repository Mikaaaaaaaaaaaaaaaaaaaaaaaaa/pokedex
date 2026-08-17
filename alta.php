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
    $numero = $_POST['numero'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $imagen_ruta = $_POST['imagen_ruta'];
    $tipo_identificador = $_POST['tipo_identificador'];

    $sql = "INSERT INTO pokemon (numero, nombre, descripcion, imagen_ruta, tipo_identificador)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("isssi", $numero, $nombre, $descripcion, $imagen_ruta, $tipo_identificador); // "isssi" significa: Integer, String, String, String, Integer.

    if ($stmt->execute() == true) {
        $mensaje = "<p style='color: green;'>¡Pokémon agregado con éxito!</p>";
    } else {
        $mensaje = "<p style='color: red;'>Error al guardar: " . $conexion->error . "</p>";
    }
}

$sql_tipos = "SELECT identificador, nombre
              FROM tipos";

$resultado_tipos = $conexion->query($sql_tipos);

?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Alta de pokémon</title>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-image: url(assets/img/fondos/alta-fondo.jpg);
            min-height: 94vh;
            padding: 20px;
            color: #333;
            background-size: cover;
            background-position: center;
        }

        a {
            padding: 15px;
            text-decoration: none;
            font-weight: 900;
            font-size: 19px;
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
            margin-bottom: -10px;
        }

        a:hover {
            transform: translateY(2px);
            filter: brightness(1.1);
        }

        hr {
            display: none !important;
        }

        h2 {
            color: #865c71;
            font-size: 39px;
            text-transform: uppercase;
            font-weight: 900;
            margin-bottom: 25px;
            text-shadow: 0px 3px 0px rgba(0, 0, 0, 0.2), 0.7px 0px 0px white;
            text-decoration: underline;
            font-family: 'Montserrat';
        }

        body > div {
            width: 100% !important;
            max-width: 600px;
            margin-top: -1px !important;
            border-radius: 10px !important;
            border: 4px solid #865c71 !important;
            box-shadow: 0px 7px 0px #865c71, 0px 10px 50px 10px #00000085;
            padding: 35px 40px !important;
            background: linear-gradient(180deg, #fbacc1 50%, #bfcadf 70%);
            background-color: #fbacc1;
            padding-bottom: 55px !important;
            padding-top: 0px !important;
            margin-top: 15px !important;
        }

        label {
            font-weight: 700;
            font-size: 17px;
            color: #865c71;
            margin-bottom: -7px;
            display: block;
            text-transform: uppercase;
            font-family: 'Montserrat';
            text-shadow: 0.5px 0px 0px #865c71;
            margin-top: 25px;
        }

        input[type="text"], input[type="number"], textarea, select {
            box-sizing: border-box !important;
            background-color: #f8fafc !important;
            border-radius: 8px !important;
            padding: 12px 15px !important;
            font-size: 15px !important;
            color: #2d3748 !important;
            transition: all 0.3s ease !important;
            font-family: 'Montserrat' !important;
            border: 2px solid #865c71;
            outline: none !important;
            font-weight: 600;
            box-shadow: 0px 5px 0px #865c71;
            margin-bottom: -5px;
        }

        input[type="text"]::placeholder {
            opacity: 30%;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: #316eb5 !important;
            background-color: white !important;
            box-shadow: 0 0 0 3px rgba(49, 110, 181, 0.15) !important;
        }

        button[type="submit"] {
            background: linear-gradient(0deg, #fbc0d0 15%, #fb8fac 50%) !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            font-size: 25px !important;
            border-radius: 7px !important;
            padding: 15px !important;
            margin-top: 15px !important;
            box-shadow: 0px 5px 0px #865c71 !important;
            transition: all 0.1s ease !important;
            font-weight: 900 !important;
            color: white;
            border: 1px solid #ffffff30 !important;
            text-shadow: 0px 3px 0px black, 0px 0px 15px #ffffff;
        }

        button[type="submit"]:hover {
            background: linear-gradient(180deg, #f5a623 0%, #ffcb05 100%) !important;
            transform: translateY(2px) !important;
            box-shadow: 0px 2px 0px #c78b16 !important;
        }

        button[type="submit"]:active {
            transform: translateY(4px) !important;
            box-shadow: 0px 0px 0px #c78b16 !important;
        }

        p[style*="green"] {
            background-color: #def7ec !important;
            color: #03543f !important;
            padding: 15px !important;
            border-radius: 8px !important;
            font-weight: bold !important;
            text-align: center !important;
            border: 1px solid #8ce0bd !important;
        }

        p[style*="red"] {
            background-color: #fde8e8 !important;
            color: #9b1c1c !important;
            padding: 15px !important;
            border-radius: 8px !important;
            font-weight: bold !important;
            text-align: center !important;
            border: 1px solid #f8b4b4 !important;
        }
    </style>
</head>

<body style="font-family: Arial, sans-serif;">

<div style="width: 400px; margin: 0 auto; border: 1px solid #ccc; padding: 20px; border-radius: 10px;">

<h2 style="text-align: center;">Agregar Nuevo Pokémon</h2>

    <?= $mensaje ?>
    <form method="POST" action="alta.php">
        <div style="margin-bottom: 15px;">
            <label>Número de Pokédex:</label><br>
            <input type="number" name="numero" required style="width: 100%;">
        </div>

        <div style="margin-bottom: 15px;">
            <label>Nombre:</label><br>
            <input type="text" name="nombre" required style="width: 100%;">
        </div>

        <div style="margin-bottom: 15px;">
            <label>Descripción:</label><br>
            <textarea name="descripcion" rows="4" required style="width: 100%;"></textarea>
        </div>

        <div style="margin-bottom: 15px;">
            <label>Ruta de la imagen:</label><br>
            <input type="text" name="imagen_ruta" placeholder="ej: assets/img/pokemon/025.png" required style="width: 100%;">
        </div>

        <div style="margin-bottom: 15px">
            <label>Tipo:</label>

            <br>

            <select name="tipo_identificador" required style="width: 100%; padding: 5px;">
                <option value="">Seleccioná un tipo: </option>

                <?php
                if ($resultado_tipos == true && $resultado_tipos->num_rows > 0) {
                    while ($tipo = $resultado_tipos->fetch_assoc()) {
                        echo "<option value='" . $tipo["identificador"] . "'>" . $tipo["nombre"] . "</option>";
                    }
                }
                ?>

            </select>
        </div>

        <button type="submit" style="width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; cursor: pointer;">
            Guardar Pokémon
        </button>
		
		<a href="index.php" class="boton_volver" ">Volver a la Pokédex</a>
    </form>
</div>

</body>
</html>
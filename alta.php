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
</head>

<body style="font-family: Arial, sans-serif;">

<a href="index.php" style="text-decoration: none; font-size: 18px;">⬅ Volver a la Pokédex</a>
<hr>

<h2 style="text-align: center;">Agregar Nuevo Pokémon</h2>

<div style="width: 400px; margin: 0 auto; border: 1px solid #ccc; padding: 20px; border-radius: 10px;">

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
    </form>
</div>

</body>
</html>

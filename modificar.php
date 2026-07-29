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
</head>

<body style="font-family: Arial, sans-serif;">

<a href="index.php" style="text-decoration: none; font-size: 18px;">⬅ Volver a la Pokédex</a>
<hr>

<h2 style="text-align: center;">Editar a <?= $pokemon['nombre'] ?></h2>

<div style="width: 400px; margin: 0 auto; border: 1px solid #ccc; padding: 20px; border-radius: 10px;">

    <?= $mensaje ?>
    <form method="POST" action="modificar.php">

        <input type="hidden" name="id" value="<?= $pokemon['id'] ?>">

        <div style="margin-bottom: 15px;">
            <label>Número de Pokédex:</label><br>
            <input type="number" name="numero" value="<?= $pokemon['numero'] ?>" required style="width: 100%;">
        </div>

        <div style="margin-bottom: 15px;">
            <label>Nombre:</label><br>
            <input type="text" name="nombre" value="<?= $pokemon['nombre'] ?>" required style="width: 100%;">
        </div>

        <div style="margin-bottom: 15px;">
            <label>Descripción:</label><br>
            <textarea name="descripcion" rows="4" required
                      style="width: 100%;"><?= $pokemon['descripcion'] ?></textarea>
        </div>

        <div style="margin-bottom: 15px;">
            <label>Ruta de la imagen:</label><br>
            <input type="text" name="imagen_ruta" value="<?= $pokemon['imagen_ruta'] ?>" required style="width: 100%;">
        </div>

        <div style="margin-bottom: 15px;">
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

        <button type="submit"
                style="width: 100%; padding: 10px; background-color: #0056b3; color: white; border: none; cursor: pointer;">
            Guardar Cambios
        </button>
    </form>

</body>
</html>


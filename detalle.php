<?php

require("conexion.php");

$identificador = isset($_GET['identificador']);
if ($identificador == true && is_numeric($identificador) == true) {
    $identificador = $_GET['identificador'];
} else {
    $identificador = 0;
}

// Si alguien entra a detalle.php con un id inexistente, lo redirige al inicio.
if ($identificador === 0) {
    header("location: index.php");
    exit();
}

$sql = "SELECT pok.numero, pok.nombre, pok.descripcion, pok.imagen_ruta AS pokemon_imagen, tip.imagen_ruta AS tipo_imagen, tip.nombre AS tipo_imagen
            FROM pokemon pok
            JOIN tipos tip ON pok.tipo_identificador = tip.identificador
            WHERE pok.identificador = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $identificador);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<h3>Error: Pokémon no encontrado en la base de datos.</h3>";
    echo "<a href='index.php'>Volver al inicio.</a>";
    exit();
}

$pokemon = $resultado->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de <?= $pokemon['nombre'] ?></title>
</head>

<body style="font-family: Arial, sans-serif;">

<a href="index.php">⬅ VOLVER A LA POKÉDEX</a> <!--cambia esto a button creo xd-->

<hr>

<h1>Nº <?= str_pad($pokemon['numero'], 3, "0", STR_PAD_LEFT) ?> - <?= $pokemon['nombre'] ?></h1>

<img src="<?= $pokemon['pokemon_imagen'] ?>" alt="<?= $pokemon['nombre'] ?>" width="250" style="margin-bottom: 15px;">

<div>
    <strong>Tipo:</strong><br><br>
    <img src="<?= $pokemon['tipo_imagen'] ?>" alt="<?= $pokemon['nombre'] ?>" title="<?= $pokemon['nombre'] ?>" width="80">
</div>

<div style="text-align: left; margin-top: 20px; background: #f9f9f9; padding: 15px; border-radius: 5px;">
    <h3 style="margin-top: 0;">Descripción:</h3>
    <p style="line-height: 1.6;"><?= $pokemon['descripcion'] ?></p>
</div>

</div>
</body>
</html>
<?php

session_start();
require_once 'conexion.php';

$mensaje_error = "";

if (isset($_GET['buscar']) == true) {
    $busqueda = $_GET['buscar'];
} else {
    $busqueda = "";
}

// Evaluamos si el usuario ingresó algo en el buscador, en este caso como primer paso evaluamos que no haya ingresado nada.
if (empty($busqueda) == false) {
    $sql = "SELECT pok.identificador, pok.numero, pok.nombre, pok.imagen_ruta AS pokemon_imagen, tip.imagen_ruta AS tipo_imagen 
            FROM pokemon pok 
            JOIN tipos tip ON pok.tipo_identificador = tip.identificador 
            WHERE pok.nombre LIKE ? OR pok.numero = ? 
            ORDER BY pok.numero ASC";

    $stmt = $conexion->prepare($sql);

    $termino_like = "%" . $busqueda . "%";

    $stmt->bind_param("ss", $termino_like, $busqueda);
    $stmt->execute();
    $resultado = $stmt->get_result();

//  Si no hay resultados mostramos todos los pokemones.
    if ($resultado->num_rows === 0) {
        $mensaje_error = "Pokémon no encontrado.";

        $sql_todos = "SELECT pok.identificador, pok.numero, pok.nombre, pok.imagen_ruta AS pokemon_imagen, tip.imagen_ruta AS tipo_imagen 
                        FROM pokemon pok 
                        JOIN tipos tip ON pok.tipo_identificador = tip.identificador
                        ORDER BY pok.numero ASC";

        $resultado = $conexion->query($sql_todos);
    }
} else {
    $sql_todos = "SELECT pok.identificador, pok.numero, pok.nombre, pok.imagen_ruta AS pokemon_imagen, tip.imagen_ruta AS tipo_imagen 
                    FROM pokemon pok
                    JOIN tipos tip ON pok.tipo_identificador = tip.identificador
                    ORDER BY pok.numero ASC";

    $resultado = $conexion->query($sql_todos);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pokédex.</title>
</head>

<body>
<h1>Mi Pokédex</h1>

<?php if (isset($_SESSION['usuario_administrador']) == true): ?>
    <div style="background-color: #d4edda; padding: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <span>Hola, <strong><?= $_SESSION['usuario_administrador']; ?></strong> (ADMINISTRADOR)</span>
        <div>
            <a href="alta.php" style="background-color: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;">+ Agregar Pokémon</a>
            <a href="logout.php" style="color: #dc3545; text-decoration: none; font-weight: bold;">Cerrar Sesión</a>
        </div>
    </div>

<?php else: ?>
<div style="margin-bottom: 20px;">
    <a href="login.php">Iniciar sesión (ADMINISTRADOR)</a>
</div>
<?php endif; ?>

<form method="GET" action="index.php" style="margin-bottom: 20px;">
    <input type="text" name="buscar" placeholder="Buscar por nombre o número..." value="<?= htmlspecialchars($busqueda) ?>">
    <button type="submit">Buscar</button>
    <a href="index.php"><button type="button">Limpiar</button></a>
</form>

<!-- Mensaje de error si no se encuentra -->
<?php
if (empty($mensaje_error) == false) {
    ?>
    <h3 style="color: red;"><?= $mensaje_error ?></h3>
    <?php
}
?>

<div style="display: flex; gap: 20px; flex-wrap: wrap;">

    <?php
    if ($resultado && $resultado->num_rows > 0):
        while($pokemon = $resultado->fetch_assoc()):
            ?>

            <div style="border: 1px solid #ccc; padding: 10px; width: 200px; text-align: center;">
                <img src="<?= $pokemon['pokemon_imagen'] ?>" alt="<?= $pokemon['nombre'] ?>" width="100">
                <h2>Nº <?= str_pad($pokemon['numero'], 3, "0", STR_PAD_LEFT) ?></h2>
                <h3><?= $pokemon['nombre'] ?></h3>

                <img src="<?= $pokemon['tipo_imagen'] ?>" alt="Tipo" width="50">

                <br><br>

                <a href="detalle.php?identificador=<?= $pokemon['identificador'] ?>">Ver detalles</a>

                <?php if (isset($_SESSION['usuario_administrador']) == true): ?>
                <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">

                    <a href="modificar.php?identificador=<?= $pokemon['identificador'] ?>" style="color: #0056b3;">Editar</a>

                    <a href="baja.php?identificador=<?= $pokemon['identificador'] ?>" style="color: red;" onclick="return confirm('¿Estás seguro de que querés borrar a <?= $pokemon['nombre'] ?>?');">Borrar</a>
                <?php endif; ?>
            </div>

        <?php
        endwhile;
    endif;
    ?>

</div>
</body>
</html>
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Pokédex</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-image: url(assets/img/fondos/index-fondo.jpg);
            min-height: 100vh;
            padding: 20px;
            color: #333;
            background-size: cover;
            background-position: center;
        }

        h1.titulo-pokedex {
            text-align: center;
            font-family: 'Montserrat', sans-serif;
            color: #ffcb05;
            font-size: 5rem;
            margin-bottom: 30px;
            letter-spacing: 5px;
            text-shadow: 0px 7px 0px #1c477a, 0px 10px 10px rgba(0, 0, 0, 0.5);
            text-transform: capitalize;
        }

        .admin-panel {
            background: linear-gradient(180deg, #ffffff 70%, #f6f5ff 100%);
            border-radius: 7px;
            box-shadow: 0px 6px 0px rgb(84 74 74 / 88%), 0px 10px 10px rgb(0 0 0 / 15%);
            max-width: 900px;
            margin: 0 auto 40px auto;
            padding: 10px 10px 13px 10px;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            font-weight: 600;
            font-family: 'Montserrat';
            border: 1px solid #757575;
        }

        .btn-add, .btn-logout, .btn-login {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 7px;
            font-weight: 700;
            color: white;
            transition: transform 0.1s, filter 0.3s;
            display: inline-block;
            text-shadow: 0px 2px 0px black, 0px 0px 15px #ffffffbd;
            text-transform: uppercase;
        }

        .btn-add {
            background: linear-gradient(180deg, #2fbc4e 70%, #39e05e 100%);
            box-shadow: 0px 5px 0px #1e7e34;
            margin-right: 10px;
            border: 1px solid #26ed54;
        }

        .btn-logout {
            background: linear-gradient(180deg, #bd2130 70%, #ed293c 100%);
            box-shadow: 0px 5px 0px #bd2130;
            border: 1px solid #ff4d63;
        }

        .login-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .btn-login {
            background: linear-gradient(180deg, #495057 40%, #343a40);
            box-shadow: 0px 4px 0px #23272b, 0px 5px 15px rgba(0,0,0,0.3);
            font-size: 1.1rem;
        }

        .btn-add:hover, .btn-logout:hover, .btn-login:hover {
            transform: translateY(2px);
            filter: brightness(1.1);
        }

        .search-form {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 40px;
            align-items: center;
        }

        input[type="text"]::placeholder {
            opacity: 20%;
        }

        .search-form input {
            width: 380px;
            border: 1px solid #757575;
            border-radius: 7px;
            font-size: 15px;
            box-shadow: 0px 5px 0px rgb(84 74 74 / 88%);
            text-align: center;
            padding: 6px 15px 6px 15px;
            max-width: 300px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }

        .search-form input:focus {
            outline: none;
            border-color: #e3350d;
        }

        .search-form button {
            padding: 6px 15px 6px 15px;
            border: none;
            border-radius: 7px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: white;
            cursor: pointer;
            text-transform: uppercase;
            transition: transform 0.1s;
            border: 1px solid #ffffff30;
            font-size: 15px;
            text-shadow: 0px 2px 0px black, 0px 0px 10px #ffffff5c;
        }

        .btn-search {
            background: linear-gradient(to bottom, #4a8cd9, #316eb5);
            box-shadow: 0px 5px 0px #1c477a;
        }

        .btn-clear {
            background: linear-gradient(to bottom, #868e96, #6c757d);
            box-shadow: 0px 5px 0px #495057;
        }

        .search-form button:hover {
            transform: translateY(2px);
            filter: brightness(1.1);
        }

        .error-message {
            text-align: center;
            background: linear-gradient(to bottom, #ffcccc, #ff9999);
            padding: 15px;
            border-radius: 12px;
            border: 2px solid #e3350d;
            max-width: 500px;
            margin: 0 auto 30px auto;
            color: #941700;
            box-shadow: 0px 5px 0px #9417004d;
        }

        .pokemon-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
            padding-bottom: 80px;
        }

        .pokemon-card {
            background: linear-gradient(180deg, #ffca00 60%, #f1fd3b);
            border: 3px solid #cc9900;
            border-radius: 15px;
            box-shadow: 0px 10px 0px 0px #cc9900, 0px -10px 0px #cc9900;
            padding: 30px 10px 30px 10px;
            width: 240px;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .pokemon-card:hover {
            transform: translateY(-8px);
        }

        .pokemon-card img.pkmn-img {
            width: 165px;
            object-fit: contain;
            background: radial-gradient(circle, #ffffff 30%, #e0e0e0 100%);
            border-radius: 5%;
            padding: 5px;
            margin: 0 auto 30px auto;
            display: block;
            border: 3px solid #a67300b8;
            box-shadow: 0px 7px 0px 0px #a67300, 0px 0px 30px 20px #f1fd3b;
            transition: transform 0.3s ease;
        }

        .pokemon-card:hover img.pkmn-img {
            transform: scale(1.1) rotate(3deg);
        }

        .pkmn-number {
            font-size: 15px;
            color: #997300;
            background-color: #ffe680;
            padding: 2px 10px;
            border-radius: 5px;
            max-width: 100px;
            margin: auto;
            margin-bottom: 15px !important;
            margin-top: 17px !important;
            box-shadow: 0px 4px 0px #a67300;
            font-weight: 900;
            border: 1px solid #a673008c;
        }

        .pkmn-name {
            margin-top: 10px;
            margin-bottom: 20px !important;
            font-size: 25px;
            text-transform: uppercase;
            border-radius: 5px;
            box-shadow: 0px 5px 0px black;
            text-align: center;
            font-weight: 900;
            text-shadow: 0px 3px 0px black, 0px 0px 3px #ffffff;
            color: white;
            border: 1px solid #e2e2e2;
            background: linear-gradient(0deg, #59a6ff 0%, #316eb5 100%);
        }

        h3.pkmn-name {
            padding: 3px 5px 3px 5px;
        }

        .pkmn-type {
            border-radius: 10px;
            box-shadow: 0px 4px 0px #1c477a;
            padding: 6px 15px;
            background: linear-gradient(180deg, #6bb0ff 0%, #316eb5 100%);
            display: inline-block;
            height: 35px;
        }

        .pkmn-type img {
            height: 100%;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0px 2px 0px rgba(0, 0, 0, 0.6));
        }

        .btn-detail {
            display: block;
            width: 95%;
            margin: 20px auto 10px auto;
            padding: 10px 0;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            color: #ffffff;
            text-transform: uppercase;
            background: linear-gradient(180deg, #ffda40 0%, #f5a623 100%);
            box-shadow: 0px 5px 0px 0px #c27d00, 0px 5px 10px #c27d00;
            transition: transform 0.1s;
            text-shadow: 0px 2px 0px black, 0px 0px 10px #ffffff;
            font-size: 17px;
        }

        .btn-detail:hover {
            transform: translateY(3px);
            filter: brightness(1.1);
        }

        .admin-divider {
            border: 0;
            border-top: 4px groove #cca300;
            margin: 25px 0px 10px 0px;
            opacity: 0.5;
        }

        .admin-actions-card a {
            display: inline-block;
            width: 100px;
            margin: 5px 1% 0 1%;
            padding: 7px 0px 7px 0px;
            border-radius: 7px;
            text-decoration: none;
            color: white;
            text-transform: uppercase;
            font-weight: 800;
            transition: transform 0.1s;
            font-size: 15px;
            text-shadow: 0px 2px 0px black, 0px 0px 10px #ffffff5c;
        }

        .btn-edit {
            background: linear-gradient(180deg, #31a7d4 0%, #1a7b9e 100%);
            box-shadow: 0px 4px 0px #0f4f66;
        }

        .btn-delete {
            background: linear-gradient(180deg, #ff6b4a 0%, #dc3545 100%);
            box-shadow: 0px 4px 0px #911e29;
        }

        .btn-edit:hover, .btn-delete:hover {
            transform: translateY(2px);
            filter: brightness(1.2);
        }

        img.logo-pokedex {
            display: flex;
            margin: 0 auto;
            width: 715px;
            margin-bottom: -30px;
            margin-top: -60px;
        }
    </style>
</head>

<body>
<img src="assets/img/pokedex-texto.png" alt="Mi Pokédex" class="logo-pokedex">

<?php if (isset($_SESSION['usuario_administrador']) == true): ?>
    <div class="admin-panel">
        <span>Hola, <strong><?= htmlspecialchars($_SESSION['usuario_administrador']); ?></strong> (ADMINISTRADOR)</span>
        <div>
            <a href="alta.php" class="btn-add">+ Agregar Pokémon</a>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </div>
<?php else: ?>
    <div class="login-container">
        <a href="login.php" class="btn-login">Iniciar sesión (ADMINISTRADOR)</a>
    </div>
<?php endif; ?>

<form method="GET" action="index.php" class="search-form">
    <input type="text" name="buscar" placeholder="Buscar por nombre o número..." value="<?= htmlspecialchars($busqueda) ?>">
    <button type="submit" class="btn-search">Buscar</button>
    <a href="index.php"><button type="button" class="btn-clear">Limpiar</button></a>
</form>

<?php if (!empty($mensaje_error)): ?>
    <h3 class="error-message"><?= htmlspecialchars($mensaje_error) ?></h3>
<?php endif; ?>

<div class="pokemon-grid">
    <?php
    if ($resultado && $resultado->num_rows > 0):
        while($pokemon = $resultado->fetch_assoc()):
            ?>
            <div class="pokemon-card">
                <img class="pkmn-img" src="<?= htmlspecialchars($pokemon['pokemon_imagen']) ?>" alt="<?= htmlspecialchars($pokemon['nombre']) ?>">

                <h2 class="pkmn-number">Nº <?= str_pad($pokemon['numero'], 3, "0", STR_PAD_LEFT) ?></h2>
                <h3 class="pkmn-name"><?= htmlspecialchars($pokemon['nombre']) ?></h3>

                <span class="pkmn-type">
                    <img src="<?= htmlspecialchars($pokemon['tipo_imagen']) ?>" alt="Tipo">
                </span>

                <a class="btn-detail" href="detalle.php?identificador=<?= htmlspecialchars($pokemon['identificador']) ?>">Ver detalles</a>

                <?php if (isset($_SESSION['usuario_administrador']) == true): ?>
                    <hr class="admin-divider">
                    <div class="admin-actions-card">
                        <a class="btn-edit" href="modificar.php?identificador=<?= htmlspecialchars($pokemon['identificador']) ?>">Editar</a>
                        <a class="btn-delete" href="baja.php?identificador=<?= htmlspecialchars($pokemon['identificador']) ?>" onclick="return confirm('¿Estás seguro de que querés borrar a <?= htmlspecialchars($pokemon['nombre']) ?>?');">Borrar</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php
        endwhile;
    endif;
    ?>
</div>

</body>
</html>
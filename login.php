<?php

session_start();
require_once 'conexion.php';

// Si el usuario ya está logueado, entonces se lo manda al inicio para que no vuelva a iniciar sesión xd.
if (isset($_SESSION['usuario_administrador']) == true) {
    header('Location: index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_ingresado = $_POST['usuario'];
    $password_ingresada = $_POST['password'];

    $sql = "SELECT identificador, usuario, password
            FROM usuarios
            WHERE usuario = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $usuario_ingresado);
    $stmt->execute();

    $resultado_obtenido = $stmt->get_result();

    if ($resultado_obtenido->num_rows === 1) { // acá el 1 significa que si encuentra un resultado o que 1 significa que si encontro algo?, tipo true o false o algo asi o on off xd
        $usuario_datos = $resultado_obtenido->fetch_assoc();

        if (password_verify($password_ingresada, $usuario_datos['password']) == true) {
            $_SESSION['usuario_administrador'] = $usuario_datos['usuario'];
            $_SESSION['administrador_identificador'] = $usuario_datos['identificador'];

            header("Location: index.php");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "El usuario ingresado no existe.";
    }
}
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login de administrador</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;900&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Usamos el mismo fondo de la Pokédex */
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #ffcb05;
            background-image: linear-gradient(rgba(0, 0, 0, 0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 0, 0, 0.04) 1px, transparent 1px);
            background-size: 30px 30px;
            background-attachment: fixed;
            min-height: 100vh;
            color: #333;
            display: flex;
            align-items: center; /* Centra la tarjeta verticalmente */
            justify-content: center; /* Centra la tarjeta horizontalmente */
            padding: 20px;
        }

        .login-card {
            background-color: white;
            border: 3px solid #1c477a;
            border-radius: 15px;
            box-shadow: 0px 7px 0px #1c477a, 0px 10px 20px rgba(0, 0, 0, 0.4);
            padding: 40px 30px;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        .login-card h2 {
            font-size: 35px;
            color: #316eb5;
            text-transform: uppercase;
            font-weight: 900;
            margin-bottom: 25px;
            text-shadow: 0px 2px 0px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            font-weight: 700;
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
        }

        .form-group input {
            width: 100%;
            border: 2px solid #757575;
            border-radius: 7px;
            font-size: 17px;
            box-shadow: 0px 5px 0px rgb(84 74 74 / 88%);
            padding: 10px 15px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .form-group input::placeholder {
            opacity: 20%;
        }

        .form-group input:focus {
            outline: none;
            border-color: #316eb5;
            box-shadow: 0px 5px 0px #316eb5;
        }

        .btn-submit {
            width: 100%;
            padding: 13px 0px 13px 0px;
            border: none;
            border-radius: 7px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            color: white;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 20px;
            background: linear-gradient(to top, #4b8edb, #316eb5 40%);
            box-shadow: 0px 5px 0px #1c477a;
            border: 1px solid #ffffff75;
            text-shadow: 0px 3px 0px black, 0px 0px 15px #ffffffa8;
            margin-top: 10px;
            margin-bottom: 20px;
            transition: transform 0.1s, filter 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(2px);
            filter: brightness(1.1);
        }

        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 15px;
            text-decoration: none;
            font-weight: 900;
            font-size: 16px;
            color: white;
            text-transform: uppercase;
            border-radius: 7px;
            background: linear-gradient(to bottom, #868e96, #6c757d);
            box-shadow: 0px 5px 0px #495057;
            border: 1px solid #ffffff30;
            text-shadow: 0px 2px 0px black;
            transition: transform 0.1s, filter 0.2s, box-shadow 0.1s;
        }

        .btn-back:hover {
            transform: translateY(2px);
            box-shadow: 0px 3px 0px #495057;
            color: white;
        }


        .error-message {
            background: linear-gradient(to bottom, #ffcccc, #ff9999);
            padding: 10px;
            border-radius: 8px;
            border: 2px solid #e3350d;
            color: #941700;
            box-shadow: 0px 4px 0px #9417004d;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>
<div class="login-card">
<h2>Ingreso para Administradores</h2>

<!-- Si existe un error en el login, se muestra. -->
<?php if ($error): ?>
    <p class="error-message" style="color: red;"><?= $error ?></p>
<?php endif; ?>

<form method="POST" action="login.php">
    <div class="form-group">
        <label>Usuario:</label>
        <input type="text" name="usuario" placeholder="Ej: administrador" required>
    </div>

    <div class="form-group">
        <label>Contraseña:</label>
        <input type="password" name="password" placeholder="••••••••" required>
    </div>

    <button type="submit" class="btn-submit">Ingresar</button>
    <br><br>
    <a href="index.php" class="btn-back">Volver a la Pokédex</a>
</form>
</div>

</body>
</html>
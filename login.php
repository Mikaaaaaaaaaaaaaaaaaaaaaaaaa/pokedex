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

    if ($resultado_obtenido->num_rows === 1) {
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

        body {
            font-family: 'Montserrat', sans-serif;
            background-image: url(assets/img/fondos/login-fondo.jpg);
            min-height: 100vh;
            padding: 20px;
            color: #333;
            background-size: cover;
            background-position: center;
        }

        .login-card {
            background-color: #dac29d;
            border: 4px solid #5a320e;
            border-radius: 10px;
            box-shadow: 0px 7px 0px #5a320e, 0px 10px 50px 10px #00000085;
            padding: 45px 40px;
            width: 100%;
            max-width: 500px;
            zoom: 1.3;
            background: linear-gradient(180deg, #e8d0ab 60%, #e8e2d8 80%);
            background-color: #e8d0ab;
            margin: auto;
            margin-top: 90px;
            text-align: center;
        }

        .login-card h2 {
            font-size: 35px;
            color: #5a320e;
            text-transform: uppercase;
            font-weight: 900;
            margin-bottom: 25px;
            text-shadow: 0px 3px 0px rgba(0, 0, 0, 0.2), 0.7px 0px 0px white;
            text-decoration: underline;
            font-family: 'Montserrat';
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            font-weight: 700;
            font-size: 15px;
            color: #5a320e;
            margin-bottom: 5px;
            display: block;
            text-transform: uppercase;
            font-family: 'Montserrat';
            text-shadow: 0.5px 0px 0px #5a320e;
        }

        .form-group input {
            width: 100%;
            border: 2px solid #5a320e;
            border-radius: 7px;
            font-size: 15px;
            box-shadow: 0px 5px 0px rgb(90 50 14);
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
            border-color: #5a320e;
            box-shadow: 0px 5px 0px #5a320e;
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
            background: linear-gradient(to top, #dac29d69, #5a320e 50%);
            box-shadow: 0px 5px 0px #5a320e;
            border: 1px solid #ffffff75;
            text-shadow: 0px 3px 0px black, 0px 0px 15px #ffffffa8;
            margin-top: 10px;
            margin-bottom: 20px;
            transition: transform 0.1s, filter 0.2s;
            background-color: #5a320e;
        }

        .btn-submit:hover {
            transform: translateY(2px);
            filter: brightness(1.1);
        }

        .btn-back {
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
            text-shadow: 0px 2px 0px black, 0px 0px 15px #ffffff9e;
            transition: transform 0.1s, filter 0.2s, box-shadow 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 250px;
            margin: auto;
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
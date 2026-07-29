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
</head>

<body>
<body style="font-family: Arial, sans-serif; text-align: center; margin-top: 50px;">

<h2>Ingreso para Administradores</h2>

<!-- Si existe un error en el login, se muestra. -->
<?php if ($error): ?>
    <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<form method="POST" action="login.php" style="border: 1px solid #ccc; padding: 20px; width: 300px; margin: 0 auto; border-radius: 10px;">
    <div style="margin-bottom: 15px;">
        <label>Usuario:</label><br>
        <input type="text" name="usuario" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label>Contraseña:</label><br>
        <input type="password" name="password" required>
    </div>

    <button type="submit">Ingresar</button>
    <br><br>
    <a href="index.php">Volver a la Pokédex</a>
</form>

</body>
</html>
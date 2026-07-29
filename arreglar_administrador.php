<?php

require_once 'conexion.php';

$hash_real = password_hash("123456", PASSWORD_DEFAULT);

$sql = "UPDATE usuarios
        SET password = '$hash_real'
        WHERE usuario = 'administrador'";

if ($conexion->query($sql) == true) {
    echo "Listo, el administrador fue actualizado con un hash real válido.";
    echo '<a href="login.php">Ir al Login</a>';
} else {
    echo "Error. " . $conexion->error;
}

?>
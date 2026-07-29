<?php

session_start();
require_once 'conexion.php';

// Por seguridad, solo usuarios logueados como administradores pueden ver esta página.
if (isset($_SESSION['usuario_administrador']) == false) {
    header('Location: index.php');
    exit();
}

$identificador = isset($_GET['identificador']);
if ($identificador == true && is_numeric($identificador) == true) {
    $identificador = $_GET['identificador'];
} else {
    $identificador = 0;
}

if ($identificador > 0) {
    $sql = "DELETE FROM usuarios
            WHERE identificador = $identificador";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $identificador);
    $stmt->execute();
}

header("Location: index.php");
exit();

?>
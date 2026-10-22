<?php
require_once __DIR__ . "/../config/conexion.php";

$correo    = $_POST['correo'] ?? '';
$nueva     = $_POST['nueva'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';

if ($nueva !== $confirmar) {
    header("Location: ../login.php?error=Las contraseñas no coinciden");
    exit();
}

$hash = password_hash($nueva, PASSWORD_BCRYPT);

$sql = "UPDATE usuarios SET password = ? WHERE email = ?";
$stmt = sqlsrv_prepare($conn, $sql, [$hash, $correo]);
if (sqlsrv_execute($stmt)) {
    header("Location: ../index.php?exito=Contraseña actualizada correctamente");
    exit();
} else {
    header("Location: ../index.php?error=Error al actualizar la contraseña");
    exit();
}

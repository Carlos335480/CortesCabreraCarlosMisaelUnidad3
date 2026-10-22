<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/conexion.php";

$usuario = $_SESSION['usuario'] ?? null;

if (!$usuario) {
    header("Location: ../index.php?error=Debes iniciar sesión");
    exit();
}

$correoEncargado = "encargado@gmail.com";

if (isset($usuario['email']) && strtolower(trim($usuario['email'])) === strtolower($correoEncargado)) {
    $sql = "SELECT m.id, m.asunto, m.mensaje, m.fecha, u.name AS usuario
            FROM mensajes m
            INNER JOIN usuarios u ON m.usuario_id = u.id
            ORDER BY m.fecha DESC";
    $stmt = sqlsrv_query($conn, $sql);
} else {
    $sql = "SELECT id, asunto, mensaje, fecha 
            FROM mensajes 
            WHERE usuario_id = ?
            ORDER BY fecha DESC";
    $stmt = sqlsrv_prepare($conn, $sql, [$usuario['id']]);
    sqlsrv_execute($stmt);
}

$mensajes = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $mensajes[] = $row;
    }
}

include __DIR__ . "/../ver_buzon.php";

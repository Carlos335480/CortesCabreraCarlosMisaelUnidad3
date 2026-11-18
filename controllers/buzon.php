<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/conexion.php";

$usuario = $_SESSION['usuario'] ?? null;

if (!$usuario || !isset($usuario['id'])) {
    header("Location: ../index.php?error=" . urlencode("Debes iniciar sesión"));
    exit();
}

$correoEncargado = trim((string) (getenv('ADMIN_EMAIL') ?: ($_ENV['ADMIN_EMAIL'] ?? $_SERVER['ADMIN_EMAIL'] ?? '')));
$correoUsuario   = isset($usuario['email']) ? strtolower(trim((string) $usuario['email'])) : '';

// Si 'email' no estaba guardado en $_SESSION['usuario'], obtenerlo de la BD por su id
if ($correoUsuario === '') {
    $stmtUser = sqlsrv_prepare($conn, "SELECT email FROM usuarios WHERE id = ?", [(int) $usuario['id']]);
    if ($stmtUser && sqlsrv_execute($stmtUser)) {
        $rowUser = sqlsrv_fetch_array($stmtUser, SQLSRV_FETCH_ASSOC);
        if ($rowUser && !empty($rowUser['email'])) {
            $correoUsuario = strtolower(trim((string) $rowUser['email']));
            $_SESSION['usuario']['email'] = $correoUsuario;
        }
        sqlsrv_free_stmt($stmtUser);
    }
}

if ($correoEncargado !== '' && $correoUsuario !== '' && hash_equals(strtolower($correoEncargado), $correoUsuario)) {
    $sql = "SELECT m.id, m.asunto, m.mensaje, m.fecha, COALESCE(u.name, 'Usuario') AS usuario
            FROM mensajes m
            LEFT JOIN usuarios u ON m.usuario_id = u.id
            ORDER BY m.fecha DESC";
    $stmt = sqlsrv_query($conn, $sql);
} else {
    $sql = "SELECT id, asunto, mensaje, fecha 
            FROM mensajes 
            WHERE usuario_id = ?
            ORDER BY fecha DESC";
    $stmt = sqlsrv_prepare($conn, $sql, [(int) $usuario['id']]);
    if ($stmt && !sqlsrv_execute($stmt)) {
        error_log(print_r(sqlsrv_errors(), true));
        sqlsrv_free_stmt($stmt);
        $stmt = false;
    }
}

$mensajes = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $mensajes[] = $row;
    }
    sqlsrv_free_stmt($stmt);
} elseif ($stmt === false) {
    error_log(print_r(sqlsrv_errors(), true));
}

include __DIR__ . "/../ver_buzon.php";
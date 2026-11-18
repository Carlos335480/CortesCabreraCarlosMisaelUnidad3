<?php
require_once __DIR__ . "/../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit();
}

$token = $_POST['token'] ?? '';
$nueva = $_POST['nueva'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';

if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    header("Location: ../index.php?error=" . urlencode("Token de recuperación inválido"));
    exit();
}

if (empty($nueva) || strlen($nueva) < 8) {
    header("Location: ../reset.php?token=" . urlencode($token) . "&error=" . urlencode("La contraseña debe tener al menos 8 caracteres"));
    exit();
}

if ($nueva !== $confirmar) {
    header("Location: ../reset.php?token=" . urlencode($token) . "&error=" . urlencode("Las contraseñas no coinciden"));
    exit();
}

$tokenHash = hash('sha256', $token);

$sqlToken = "SELECT correo, expira FROM tokens_recuperacion WHERE token = ?";
$stmtToken = sqlsrv_prepare($conn, $sqlToken, [$tokenHash]);

if (!$stmtToken || !sqlsrv_execute($stmtToken)) {
    error_log(print_r(sqlsrv_errors(), true));
    if ($stmtToken) sqlsrv_free_stmt($stmtToken);
    sqlsrv_close($conn);
    header("Location: ../index.php?error=" . urlencode("Error interno del servidor"));
    exit();
}

$row = sqlsrv_fetch_array($stmtToken, SQLSRV_FETCH_ASSOC);
sqlsrv_free_stmt($stmtToken);

$valido = false;
if ($row) {
    $expira = $row['expira'];
    if ($expira instanceof DateTime) {
        $valido = $expira->getTimestamp() > time();
    } elseif (is_string($expira)) {
        $valido = strtotime($expira) > time();
    }
}

if (!$valido) {
    sqlsrv_close($conn);
    header("Location: ../index.php?error=" . urlencode("El enlace de recuperación es inválido o ha expirado"));
    exit();
}

$correo = $row['correo'];
$hash = password_hash($nueva, PASSWORD_DEFAULT);

$sql = "UPDATE usuarios SET password = ? WHERE email = ?";
$stmt = sqlsrv_prepare($conn, $sql, [$hash, $correo]);

if ($stmt && sqlsrv_execute($stmt)) {
    sqlsrv_free_stmt($stmt);

    $sqlDel = "DELETE FROM tokens_recuperacion WHERE token = ?";
    $stmtDel = sqlsrv_prepare($conn, $sqlDel, [$tokenHash]);
    if ($stmtDel) {
        sqlsrv_execute($stmtDel);
        sqlsrv_free_stmt($stmtDel);
    }

    sqlsrv_close($conn);
    header("Location: ../index.php?exito=" . urlencode("Contraseña actualizada correctamente"));
    exit();
} else {
    error_log(print_r(sqlsrv_errors(), true));
    if ($stmt) sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    header("Location: ../index.php?error=" . urlencode("Error al actualizar la contraseña"));
    exit();
}
?>
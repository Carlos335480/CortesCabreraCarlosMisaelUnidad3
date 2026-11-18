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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje_id = filter_input(INPUT_POST, 'mensaje_id', FILTER_VALIDATE_INT);
    $respuesta  = trim($_POST['respuesta'] ?? '');

    if (!$mensaje_id || $mensaje_id <= 0 || empty($respuesta) || mb_strlen($respuesta, 'UTF-8') > 1000) {
        header("Location: ../controllers/buzon.php?error=" . urlencode("Respuesta inválida"));
        exit();
    }

    $sql = "INSERT INTO respuestas (mensaje_id, usuario_id, respuesta, fecha)
            VALUES (?, ?, ?, GETDATE())";
    $params = [$mensaje_id, (int) $usuario['id'], $respuesta];
    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if ($stmt && sqlsrv_execute($stmt)) {
        sqlsrv_free_stmt($stmt);
        sqlsrv_close($conn);
        header("Location: ../controllers/buzon.php?success=" . urlencode("Respuesta enviada"));
        exit();
    } else {
        error_log(print_r(sqlsrv_errors(), true));
        if ($stmt) {
            sqlsrv_free_stmt($stmt);
        }
        sqlsrv_close($conn);
        header("Location: ../controllers/buzon.php?error=" . urlencode("No se pudo guardar la respuesta"));
        exit();
    }
} else {
    header("Location: ../controllers/buzon.php");
    exit();
}
?>
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje_id = $_POST['mensaje_id'] ?? null;
    $respuesta  = trim($_POST['respuesta'] ?? '');

    if (!$mensaje_id || empty($respuesta)) {
        header("Location: ../controllers/buzon.php?error=Respuesta inválida");
        exit();
    }

    $sql = "INSERT INTO respuestas (mensaje_id, usuario_id, respuesta, fecha)
            VALUES (?, ?, ?, GETDATE())";
    $params = [$mensaje_id, $usuario['id'], $respuesta];
    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if ($stmt && sqlsrv_execute($stmt)) {
        header("Location: ../controllers/buzon.php?success=Respuesta enviada");
        exit();
    } else {
        header("Location: ../controllers/buzon.php?error=No se pudo guardar la respuesta");
        exit();
    }
} else {
    header("Location: ../controllers/buzon.php");
    exit();
}

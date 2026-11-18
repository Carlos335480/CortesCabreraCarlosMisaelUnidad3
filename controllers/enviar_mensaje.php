<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/conexion.php"; 

$usuario_id = (!empty($_SESSION['usuario']) && isset($_SESSION['usuario']['id'])) ? (int) $_SESSION['usuario']['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asunto  = trim($_POST['asunto'] ?? "");
    $mensaje = trim($_POST['mensaje'] ?? "");

    if ($usuario_id && $usuario_id > 0 && $asunto !== "" && $mensaje !== "" && mb_strlen($asunto, 'UTF-8') <= 150 && mb_strlen($mensaje, 'UTF-8') <= 2000) {

        $sql = "INSERT INTO mensajes (usuario_id, asunto, mensaje, fecha) VALUES (?, ?, ?, GETDATE())";
        $params = [$usuario_id, $asunto, $mensaje];
        $stmt = sqlsrv_prepare($conn, $sql, $params);

        if ($stmt && sqlsrv_execute($stmt)) {
            sqlsrv_free_stmt($stmt);
            sqlsrv_close($conn);
            header("Location: ../contactanos.php?exito=" . urlencode("Mensaje enviado correctamente"));
            exit();
        } else {
            error_log(print_r(sqlsrv_errors(), true));
            if ($stmt) {
                sqlsrv_free_stmt($stmt);
            }
            sqlsrv_close($conn);
            header("Location: ../contactanos.php?error=" . urlencode("Error al enviar el mensaje"));
            exit();
        }
    } else {
        header("Location: ../contactanos.php?error=" . urlencode("Datos incompletos o exceden el límite permitido"));
        exit();
    }
}
?>
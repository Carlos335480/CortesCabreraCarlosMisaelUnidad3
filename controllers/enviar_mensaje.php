<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/conexion.php"; 

$usuario_id = !empty($_SESSION['usuario']) ? $_SESSION['usuario']['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asunto  = trim($_POST['asunto'] ?? "");
    $mensaje = trim($_POST['mensaje'] ?? "");

    if ($usuario_id && $asunto && $mensaje) {

        $sql = "INSERT INTO mensajes (usuario_id, asunto, mensaje, fecha) VALUES (?, ?, ?, GETDATE())";
        $params = [$usuario_id, $asunto, $mensaje];
        $stmt = sqlsrv_prepare($conn, $sql, $params);

        if ($stmt && sqlsrv_execute($stmt)) {

            header("Location: ../contactanos.php?exito=Mensaje enviado correctamente");
            exit();
        } else {
            header("Location: ..contactanos.php?error=Error al enviar el mensaje");
            exit();
        }
    } else {
        header("Location: ../contactanos.php?error=Datos incompletos");
        exit();
    }
}

<?php
require_once "../config/conexion.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual    = trim($_POST['actual']);
    $nueva     = trim($_POST['nueva']);
    $confirmar = trim($_POST['confirmar']);
    $captcha   = trim($_POST['captcha']);

    if (!isset($_SESSION['usuario'])) {
        die("Debes iniciar sesión.");
    }

    if (!isset($_SESSION['captcha_text']) || $captcha !== $_SESSION['captcha_text']) {
        die("Captcha incorrecto. Intenta de nuevo.");
    }

    $usuario = $_SESSION['usuario'];
    $id      = $usuario['id'];

    if ($nueva !== $confirmar) {
        die("Las contraseñas no coinciden.");
    }

    $sql = "SELECT password FROM usuarios WHERE id = ?";
    $stmt = sqlsrv_prepare($conn, $sql, [$id]);
    sqlsrv_execute($stmt);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($row && password_verify($actual, $row['password'])) {
        
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $sqlUpdate = "UPDATE usuarios SET password = ? WHERE id = ?";
        $stmtUpdate = sqlsrv_prepare($conn, $sqlUpdate, [$hash, $id]);
        if (sqlsrv_execute($stmtUpdate)) {
            echo "Contraseña actualizada correctamente.";
        } else {
            echo "Error al actualizar la contraseña.";
        }
    } else {
        echo "La contraseña actual es incorrecta.";
    }
}
?>

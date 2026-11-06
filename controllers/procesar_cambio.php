<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (empty($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
        header("Location: ../index.php?error=" . urlencode("Debes iniciar sesión para realizar esta acción"));
        exit();
    }

    $actual = trim($_POST['actual'] ?? '');
    $nueva = trim($_POST['nueva'] ?? '');
    $confirmar = trim($_POST['confirmar'] ?? '');
    $captcha = trim($_POST['captcha'] ?? '');

    $session_captcha = $_SESSION['captcha_text'] ?? '';
    unset($_SESSION['captcha_text']);

    if (empty($captcha) || empty($session_captcha) || !hash_equals($session_captcha, $captcha)) {
        header("Location: ../cambiar_contraseña.php?error=" . urlencode("Captcha incorrecto. Inténtalo de nuevo."));
        exit();
    }

    if (empty($actual) || empty($nueva) || empty($confirmar)) {
        header("Location: ../cambiar_contraseña.php?error=" . urlencode("Por favor, llena todos los campos"));
        exit();
    }

    if ($nueva !== $confirmar) {
        header("Location: ../cambiar_contraseña.php?error=" . urlencode("Las contraseñas nuevas no coinciden"));
        exit();
    }

    if (strlen($nueva) < 8) {
        header("Location: ../cambiar_contraseña.php?error=" . urlencode("La nueva contraseña debe tener al menos 8 caracteres"));
        exit();
    }

    $id_usuario = $_SESSION['usuario']['id'];

    $sql  = "SELECT password FROM usuarios WHERE id = ?";
    $stmt = sqlsrv_prepare($conn, $sql, [$id_usuario]);

    if ($stmt && sqlsrv_execute($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        if ($row && password_verify($actual, $row['password'])) {
            sqlsrv_free_stmt($stmt);

            $hash       = password_hash($nueva, PASSWORD_DEFAULT);
            $sqlUpdate  = "UPDATE usuarios SET password = ? WHERE id = ?";
            $stmtUpdate = sqlsrv_prepare($conn, $sqlUpdate, [$hash, $id_usuario]);

            if ($stmtUpdate && sqlsrv_execute($stmtUpdate)) {
                sqlsrv_free_stmt($stmtUpdate);
                sqlsrv_close($conn);

                session_regenerate_id(true);

                header("Location: ../cambiar_contraseña.php?exito=" . urlencode("Contraseña actualizada correctamente"));
                exit();
            } else {
                error_log(print_r(sqlsrv_errors(), true));
                if (isset($conn) && $conn) {
                    sqlsrv_close($conn);
                }

                header("Location: ../cambiar_contraseña.php?error=" . urlencode("Error al actualizar la contraseña en el servidor"));
                exit();
            }

        } else {
            sqlsrv_free_stmt($stmt);
            if (isset($conn) && $conn) {
                sqlsrv_close($conn);
            }

            header("Location: ../cambiar_contraseña.php?error=" . urlencode("La contraseña actual es incorrecta"));
            exit();
        }

    } else {
        error_log(print_r(sqlsrv_errors(), true));
        if (isset($conn) && $conn) {
            sqlsrv_close($conn);
        }

        header("Location: ../cambiar_contraseña.php?error=" . urlencode("Error de conexión con el servidor"));
        exit();
    }

} else {
    header("Location: ../index.php");
    exit();
}

?>
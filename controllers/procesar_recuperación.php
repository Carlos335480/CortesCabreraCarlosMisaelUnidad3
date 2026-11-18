<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/conexion.php";
require __DIR__ . "/../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function obtenerEnv(string $clave, string $default = ''): string {
    $val = getenv($clave);
    if ($val !== false && $val !== '') {
        return trim((string) $val);
    }
    if (!empty($_ENV[$clave])) {
        return trim((string) $_ENV[$clave]);
    }
    if (!empty($_SERVER[$clave])) {
        return trim((string) $_SERVER[$clave]);
    }
    $rutaEnv = __DIR__ . '/../.env';
    if (file_exists($rutaEnv)) {
        foreach (file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
            $linea = trim($linea);
            if ($linea === '' || str_starts_with($linea, '#')) {
                continue;
            }
            if (str_starts_with($linea, $clave . '=')) {
                return trim(substr($linea, strlen($clave) + 1), " \t\n\r\0\x0B\"'");
            }
        }
    }
    return $default;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (!$correo) {
        header("Location: ../recuperar.php?error=" . urlencode("Por favor, ingresa un correo electrónico válido."));
        exit();
    }

    $mensajeExito = "Si el correo está registrado, recibirás un enlace de recuperación en unos minutos.";

    $sqlUser = "SELECT id FROM usuarios WHERE email = ?";
    $stmtUser = sqlsrv_prepare($conn, $sqlUser, [$correo]);

    if (!$stmtUser || !sqlsrv_execute($stmtUser)) {
        error_log(print_r(sqlsrv_errors(), true));
        sqlsrv_close($conn);
        header("Location: ../recuperar.php?error=" . urlencode("Error interno del servidor. Inténtalo más tarde."));
        exit();
    }

    $usuarioExiste = sqlsrv_fetch_array($stmtUser, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmtUser);

    if (!$usuarioExiste) {
        sqlsrv_close($conn);
        header("Location: ../recuperar.php?exito=" . urlencode($mensajeExito));
        exit();
    }

    $sqlDelete = "DELETE FROM tokens_recuperacion WHERE correo = ?";
    $stmtDelete = sqlsrv_prepare($conn, $sqlDelete, [$correo]);
    if ($stmtDelete) {
        sqlsrv_execute($stmtDelete);
        sqlsrv_free_stmt($stmtDelete);
    }

    $tokenPlano = bin2hex(random_bytes(32));
    $tokenHash  = hash('sha256', $tokenPlano);
    $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

    $sqlInsert = "INSERT INTO tokens_recuperacion (correo, token, expira) VALUES (?, ?, ?)";
    $stmtInsert = sqlsrv_prepare($conn, $sqlInsert, [$correo, $tokenHash, $expira]);

    if (!$stmtInsert || !sqlsrv_execute($stmtInsert)) {
        error_log(print_r(sqlsrv_errors(), true));
        if ($stmtInsert) sqlsrv_free_stmt($stmtInsert);
        sqlsrv_close($conn);
        header("Location: ../recuperar.php?error=" . urlencode("No se pudo procesar la solicitud. Inténtalo más tarde."));
        exit();
    }

    sqlsrv_free_stmt($stmtInsert);
    sqlsrv_close($conn);

    $mail = new PHPMailer(true);
    try {
        $smtpHost = obtenerEnv('SMTP_HOST', 'smtp.gmail.com');
        $smtpUser = obtenerEnv('SMTP_USER');
        $smtpPass = str_replace(' ', '', obtenerEnv('SMTP_PASS'));
        $smtpPort = (int) obtenerEnv('SMTP_PORT', '587');

        if ($smtpUser === '' || $smtpPass === '') {
            throw new Exception("Faltan las variables SMTP_USER o SMTP_PASS en el archivo .env");
        }

        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = ($smtpPort === 465) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $smtpPort;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($smtpUser, 'Soporte');
        $mail->addReplyTo($smtpUser, 'Soporte');
        $mail->addAddress($correo);

        $appUrl = rtrim(obtenerEnv('APP_URL', 'http://localhost:8080'), '/');
        $enlace = $appUrl . "/cambiar_contraseña.php?token=" . urlencode($tokenPlano);
        $enlaceSeguro = htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8');

        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de contraseña';
        $mail->Body = "
            <h2>Recuperación de contraseña</h2>
            <p>Hola,</p>
            <p>Recibimos una solicitud para restablecer tu contraseña.</p>
            <p>Haz clic en el siguiente enlace para continuar:</p>
            <p><a href='{$enlaceSeguro}'>{$enlaceSeguro}</a></p>
            <p>Este enlace expira en 1 hora.</p>
            <p>Si no solicitaste esto, ignora este mensaje.</p>
            <br>
            <p>Saludos,<br>Equipo de soporte</p>
        ";
        $mail->AltBody = "Recibimos una solicitud para restablecer tu contraseña. Abre este enlace (expira en 1 hora): {$enlace}";

        $mail->send();
        header("Location: ../recuperar.php?exito=" . urlencode($mensajeExito));
        exit();

    } catch (Exception $e) {
        error_log("Error PHPMailer: " . $mail->ErrorInfo . " | " . $e->getMessage());
        header("Location: ../recuperar.php?error=" . urlencode("No se pudo enviar el correo de recuperación. Inténtalo más tarde."));
        exit();
    }

} else {
    header("Location: ../recuperar.php");
    exit();
}
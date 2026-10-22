<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";
require __DIR__ . "/../vendor/autoload.php"; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['email']); 

    $token = bin2hex(random_bytes(16));
    $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

    $sql = "INSERT INTO tokens_recuperacion (correo, token, expira) VALUES (?, ?, ?)";
    $stmt = sqlsrv_prepare($conn, $sql, [$correo, $token, $expira]);
    sqlsrv_execute($stmt);


    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        $mail->Username   = '23040067@alumno.utc.edu.mx';      
        $mail->Password   = 'gagu cvxs neth wpot';          

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('23040067@alumno.utc.edu.mx', 'Soporte');
        $mail->addReplyTo('23040067@alumno.utc.edu.mx', 'Soporte'); 
        $mail->addAddress($correo);

        $enlace = "http://localhost:8080/reset.php?token=$token";

        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de contraseña';
        $mail->Body = "
            <h2>Recuperación de contraseña</h2>
            <p>Hola,</p>
            <p>Recibimos una solicitud para restablecer tu contraseña.</p>
            <p>Haz clic en el siguiente enlace para continuar:</p>
            <p><a href='$enlace'>$enlace</a></p>
            <p>Este enlace expira en 1 hora.</p>
            <p>Si no solicitaste esto, ignora este mensaje.</p>
            <br>
            <p>Saludos,<br>Equipo de soporte</p>
        ";

        $mail->send();
        header("Location: ../recuperar.php?exito=Se ha enviado un enlace de recuperación a tu correo");
        exit();
    } catch (Exception $e) {
        header("Location: ../recuperar.php?error=Error al enviar el correo: {$mail->ErrorInfo}");
        exit();
    }
}

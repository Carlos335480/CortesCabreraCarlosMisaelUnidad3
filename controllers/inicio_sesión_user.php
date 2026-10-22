<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    if (empty($recaptcha_response)) {
        header("Location: ../index.php?error=" . urlencode("Por favor, confirma que no eres un robot"));
        exit();
    }

    $secret_key = getenv('RECAPTCHA_SECRET_KEY');

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret'   => $secret_key,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context  = stream_context_create($options);
    $verify   = file_get_contents($url, false, $context);
    $captcha_success = json_decode($verify, true);

    if (!$captcha_success['success']) {
        header("Location: ../index.php?error=" . urlencode("Error en la verificación de reCAPTCHA. Inténtalo de nuevo."));
        exit();
    }

$email    = trim($_POST['email']);
$password = trim($_POST['password']);

$sql = "SELECT id, nameuser, name, email, password
        FROM usuarios
        WHERE email = ?";

$params = array($email);

$stmt = sqlsrv_prepare($conn, $sql, $params);

if ($stmt && sqlsrv_execute($stmt)) {
 
    $usuario = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($usuario) {

        if (password_verify($password, $usuario['password'])) {

            $_SESSION['usuario'] = $usuario;

            header("Location: ../controllers/obtener_inventario.php?Inventario");
            exit();
        }

        header("Location: ../index.php?error=Contraseña incorrecta");
        exit();

    } else {

        header("Location: ../index.php?error=Usuario no encontrado");
        exit();
    }

} else {

    die(print_r(sqlsrv_errors(), true));
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
}
?>
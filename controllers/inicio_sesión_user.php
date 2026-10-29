<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    if (empty($email) || empty($password)){
        header("Location: ../index.php?error=" . urlencode("Por favor, llena todos los campos"));
        exit();
    }

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

    if (!$captcha_success || empty($captcha_success['success'])) {
        header("Location: ../index.php?error=" . urlencode("Error en la verificación de reCAPTCHA. Inténtalo de nuevo."));
        exit();
    }

$sql = "SELECT id, nameuser, name, email, password
        FROM usuarios
        WHERE email = ?";

$params = array($email);
$stmt = sqlsrv_prepare($conn, $sql, $params);

if ($stmt && sqlsrv_execute($stmt)) {
 
    $usuario = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        
        session_regenerate_id(true);

        unset($usuario['password']);
        $_SESSION['usuario'] = $usuario;

        sqlsrv_free_stmt($stmt);
        sqlsrv_close($conn);

        header("Location: ../controllers/obtener_inventario.php?Inventario");
        exit();

    } else {
        sqlsrv_free_stmt($stmt);
        sqlsrv_close($conn);
        
        header("Location: ../index.php?error=" . urlencode("Correo o contraseña incorrectos"));
        exit();
    }
} else {
    error_log(print_r(sqlsrv_errors(), true));
    if (isset($conn) && $conn) {
        sqlsrv_close($conn);
    }
    header("Location: ../index.php?error=" . urlencode("Ocurrió un error en el servidor. Inténtalo más tarde."));
    exit();
}
} else {
    header("Location: ../index.php");
    exit();
}
?>
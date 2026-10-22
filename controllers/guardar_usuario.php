<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    if (empty($recaptcha_response)) {
        header("Location: ../registro.php?error=" . urlencode("Por favor, confirma que no eres un robot"));
        exit();
    }

    $secret_key = getenv('RECAPTCHA_SECRET_KEY') ?: ($_ENV['RECAPTCHA_SECRET_KEY'] ?? $_SERVER['RECAPTCHA_SECRET_KEY'] ?? '');

    $url  = 'https://www.google.com/recaptcha/api/siteverify';
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

    $context = stream_context_create($options);
    $verify  = file_get_contents($url, false, $context);
    $captcha_success = json_decode($verify, true);

    if (!$captcha_success['success']) {
        header("Location: ../registro.php?error=" . urlencode("Verificación de reCAPTCHA fallida. Inténtalo de nuevo."));
        exit();
    }

$nameuser = trim($_POST['nameuser']);
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

$camposVacios = 0;

if (empty($nameuser)) $camposVacios++;
if (empty($name)) $camposVacios++;
if (empty($email)) $camposVacios++;
if (empty($password)) $camposVacios++;

if ($camposVacios > 1) {
    header("Location: ../registro.php?error=Todos los campos son obligatorios");
    exit();
} else if (empty($nameuser)) {
    header("Location: ../registro.php?error=El nombre de usuario es obligatorio");
    exit();
} else if (empty($name)) {
    header("Location: ../registro.php?error=El nombre es obligatorio");
    exit();
} else if (empty($email)) {
    header("Location: ../registro.php?error=El correo es obligatorio");
    exit();
} else if (empty($password)) {
    header("Location: ../registro.php?error=La contraseña es obligatoria");
    exit();
} else {

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO usuarios (nameuser, name, email, password) VALUES (?, ?, ?, ?)";
    
    $params = array($nameuser, $name, $email, $passwordHash);
    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if ($stmt && sqlsrv_execute($stmt)) {
        header("Location: ../index.php?exito=Usuario guardado con éxito");
        exit();
    } else {
        header("Location: ../registro.php?error=Error al guardar el usuario");
        exit();
    }

}
}
?>

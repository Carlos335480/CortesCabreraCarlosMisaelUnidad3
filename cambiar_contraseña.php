<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Capturar el token desde $_GET o directo de REQUEST_URI (por si la 'ñ' afecta el query string)
$token = '';
if (!empty($_GET['token']) && is_string($_GET['token'])) {
    $token = trim($_GET['token']);
} elseif (!empty($_SERVER['REQUEST_URI'])) {
    $queryStr = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    if ($queryStr) {
        parse_str($queryStr, $params);
        if (!empty($params['token']) && is_string($params['token'])) {
            $token = trim($params['token']);
        }
    }
}

// 2. Respaldar el token en sesión para que no se pierda al enviar el formulario
if ($token !== '') {
    $_SESSION['reset_token'] = $token;
} elseif (!empty($_SESSION['reset_token'])) {
    $token = $_SESSION['reset_token'];
}

$esRecuperacion = ($token !== '');

// 3. Si NO viene de recuperación por correo y NO tiene sesión iniciada, mandarlo al login
if (!$esRecuperacion && empty($_SESSION['usuario'])) {
    header("Location: index.php?error=" . urlencode("Debes iniciar sesión o usar el enlace enviado a tu correo."));
    exit();
}

$nombre = !empty($_SESSION['usuario']) ? $_SESSION['usuario']['name'] : "Invitado";

// Obtener la Site Key de reCAPTCHA v2 desde el entorno o desde .env
$recaptchaSiteKey = trim((string) (getenv('RECAPTCHA_SITE_KEY') ?: ($_ENV['RECAPTCHA_SITE_KEY'] ?? $_SERVER['RECAPTCHA_SITE_KEY'] ?? '')));

if ($recaptchaSiteKey === '' && file_exists(__DIR__ . '/.env')) {
    foreach (file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
        $linea = trim($linea);
        if ($linea === '' || str_starts_with($linea, '#')) {
            continue;
        }
        if (str_starts_with($linea, 'RECAPTCHA_SITE_KEY=')) {
            $recaptchaSiteKey = trim(substr($linea, strlen('RECAPTCHA_SITE_KEY=')), " \t\n\r\0\x0B\"'");
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= $esRecuperacion ? 'Restablecer Contraseña' : 'Cambiar Contraseña' ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #000;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .container {
      background: #000;
      border: 2px solid #fff;
      border-radius: 10px;
      padding: 20px;
      max-width: 400px;
      width: 100%;
      text-align: center;
      margin-top: 60px;
    }
    h2 {
      color: #fff;
      margin-bottom: 20px;
    }
    .input-icon {
      position: relative;
      margin-bottom: 15px;
    }
    .input-icon input {
      width: 100%;
      padding: 10px 35px 10px 10px;
      border: 2px solid #fff;
      border-radius: 8px;
      background: transparent;
      color: #fff;
      box-sizing: border-box;
      outline: none;
    }
    .input-icon i {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #6a0dad;
    }
    .captcha-container {
      display: flex;
      justify-content: center;
      margin-bottom: 15px;
    }
    button[type="submit"] {
      width: 100%;
      padding: 10px;
      background: #6a0dad;
      border: 2px solid #fff;
      border-radius: 8px;
      color: #fff;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.3s;
    }
    button[type="submit"]:hover {
      background: #8e44ad;
    }
    .menu {
      background: #000;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-family: 'Poppins', sans-serif;
      color: #fff;
      position: fixed;        
      top: 0;                    
      left: 0;                  
      width: 96%;               
      z-index: 1000; 
    }
    .menu nav {
      display: flex;
      gap: 25px;
    }
    .menu a {
      display: inline-flex;      
      align-items: center;        
      gap: 6px;                   
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s ease;
    }
    .menu a i {
      font-size: 16px;            
    }
    .menu a:hover {
      color: #5b42f3; 
    }
  </style>
</head>
<body>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <?php if (isset($_GET['error']) && is_string($_GET['error'])): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: <?= json_encode($_GET['error'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        confirmButtonColor: '#d33'
      });
    </script>
  <?php endif; ?>

  <?php if (isset($_GET['exito']) && is_string($_GET['exito'])): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: <?= json_encode($_GET['exito'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        confirmButtonColor: '#28a745'
      });
    </script>
  <?php endif; ?>

  <header class="menu">
    <div>
      <span><i class="fas fa-user"></i> <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <nav>
      <?php if (!empty($_SESSION['usuario'])): ?>
        <a href="controllers/buzon.php"><i class="fa-solid fa-bell"></i> Buzón</a>
        <a href="contactanos.php"><i class="fa-solid fa-phone"></i> Contáctanos</a>
        <a href="agregar_ropa.php"><i class="fa-solid fa-plus"></i> Agregar Ropa</a>
        <a href="controllers/obtener_inventario.php"><i class="fa-solid fa-box"></i> Inventario</a>
        <a href="mapa.php"><i class="fa-solid fa-map"></i> Mapa</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
      <?php else: ?>
        <a href="index.php"><i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión</a>
      <?php endif; ?>
    </nav>
  </header>

  <div class="container">
    <h2><?= $esRecuperacion ? 'Restablecer Contraseña' : 'Cambiar Contraseña' ?></h2>
    <form action="controllers/procesar_cambio.php" method="POST">
      <?php if ($esRecuperacion): ?>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
      <?php else: ?>
        <div class="input-icon">
          <input type="password" name="actual" placeholder="Contraseña actual" required>
          <i class="fas fa-lock"></i>
        </div>
      <?php endif; ?>

      <div class="input-icon">
        <input type="password" name="nueva" placeholder="Nueva contraseña" minlength="8" required>
        <i class="fas fa-key"></i>
      </div>
      <div class="input-icon">
        <input type="password" name="confirmar" placeholder="Confirmar nueva contraseña" minlength="8" required>
        <i class="fas fa-check"></i>
      </div>

      <div class="captcha-container">
        <div class="g-recaptcha" data-theme="dark" data-sitekey="<?= htmlspecialchars($recaptchaSiteKey, ENT_QUOTES, 'UTF-8') ?>"></div>
      </div>

      <button type="submit"><i class="fas fa-save"></i> Guardar cambios</button>
    </form>
  </div>
</body>
</html>
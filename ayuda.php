<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['usuario'])) {
    $nombre = $_SESSION['usuario']['name'];
} else {
    $nombre = "Invitado";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ayuda</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #000;
      color: #fff;
      margin: 0;
      padding: 20px;
    }
    .container {
        max-width: 800px;
        margin: 100px auto 0; 
        background: #000;
        border: 2px solid #fff;
        border-radius: 10px;
        padding: 20px;
    }
    h1 {
      text-align: center;
      margin-bottom: 20px;
      color: #fff;
    }
    h2 {
      color: #6a0dad;
      margin-top: 20px;
    }
    p {
      font-size: 14px;
      color: #ddd;
      line-height: 1.5;
    }
    .btn-back {
      display: block;
      width: fit-content;
      margin: 20px auto 0;
      padding: 10px 20px;
      background-color: #6a0dad;
      border: 2px solid #fff;
      border-radius: 8px;
      color: #fff;
      font-weight: 500;
      text-align: center;
      text-decoration: none;
    }
    .btn-back:hover {
      background-color: #8e44ad;
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
    <header class="menu">
        <div>
        <span><i class="fas fa-user"></i> <?= isset($nombre) ? htmlspecialchars($nombre) : "Invitado" ?></span>
        </div>
        <nav>
            <a href = "../controllers/obtener_inventario.php"><i class="fa-solid fa-box"></i> Inventario</a>
            <a href = "../controllers/buzon.php"><i class="fa-solid fa-bell"></i> Buzón</a>
            <a href = "../contactanos.php"><i class="fa-solid fa-phone"></i> Contáctanos</a>
            <a href = "../agregar_ropa.php"><i class="fa-solid fa-plus"></i> Agregar Ropa</a>
            <a href = "../mapa.php"><i class="fa-solid fa-map"></i> Mapa</a>
            <a href = "../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
        </nav>
    </header>

    <div class="container">
        <h1>Centro de Ayuda</h1>

        <h2>¿Cómo registrarse?</h2>
        <p>Haz clic en <strong>Registrar</strong> en el menú principal y completa el formulario con tus datos. Recibirás un correo de confirmación.</p>

        <h2>¿Cómo iniciar sesión?</h2>
        <p>Ve a <strong>Inicio de sesión</strong>, ingresa tu correo y contraseña. Si olvidaste tu contraseña, usa la opción <strong>Recuperación de contraseña</strong>.</p>

        <h2>¿Cómo usar el buzón?</h2>
        <p>En la sección <strong>Buzón</strong> podrás ver tus mensajes. Si eres encargado, verás todos los mensajes; si eres usuario normal, solo los tuyos. También puedes responder directamente desde ahí.</p>

        <h2>¿Cómo contactar al soporte?</h2>
        <p>Usa la sección <strong>Contáctanos</strong> para enviar un mensaje al administrador. También puedes usar el <strong>Chat</strong> para soporte rápido.</p>

        <h2>Mapa del sitio</h2>
        <p>Consulta el <strong>Mapa del sitio</strong> para ver todas las secciones disponibles.</p>

        
        <a href="../controllers/obtener_inventario.php" class="btn-back">⬅ Regresar al inicio</a>
    </div>
</body>
</html>

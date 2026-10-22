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
  <title>Cambiar Contraseña</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
    button {
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
    button:hover {
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
    <header class="menu">
        <div>
        <span><i class="fas fa-user"></i> <?= isset($nombre) ? htmlspecialchars($nombre) : "Invitado" ?></span>
        </div>
        <nav>
        <a href = "../controllers/buzon.php"><i class="fa-solid fa-bell"></i> Buzón</a>
        <a href = "../contactanos.php"><i class="fa-solid fa-phone"></i> Contáctanos</a>
        <a href = "../agregar_ropa.php"><i class="fa-solid fa-plus"></i> Agregar Ropa</a>
        <a href = "../controllers/obtener_inventario.php"><i class="fa-solid fa-box"></i> Inventario</a>
        <a href = "../mapa.php"><i class="fa-solid fa-map"></i> Mapa</a>
        <a href = "../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
        </nav>
  </header>

  <div class="container">
    <h2>Cambiar Contraseña</h2>
    <form action="../controllers/procesar_cambio.php" method="POST">
      <div class="input-icon">
        <input type="password" name="actual" placeholder="Contraseña actual" required>
        <i class="fas fa-lock"></i>
      </div>
      <div class="input-icon">
        <input type="password" name="nueva" placeholder="Nueva contraseña" required>
        <i class="fas fa-key"></i>
      </div>
      <div class="input-icon">
        <input type="password" name="confirmar" placeholder="Confirmar nueva contraseña" required>
        <i class="fas fa-check"></i>
      </div>
      <div class="input-icon">
        <img src="../captcha.php" alt="Captcha" 
            style="margin-bottom:10px; border:2px solid #fff; border-radius:5px; width:100%;">
        <input type="text" name="captcha" placeholder="Escribe el texto de la imagen" required>
        <i class="fas fa-shield-alt"></i>
      </div>

      <button type="submit"><i class="fas fa-save"></i> Guardar cambios</button>
    </form>
  </div>
</body>
</html>

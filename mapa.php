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
  <title>Mapa del Sitio</title>
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
        text-align:center
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
      color: #fff;
    }
    ul {
      list-style: none;
      padding: 0;
    }
    li {
      margin: 8px 0;
    }
    a {
      color: #6a0dad;
      text-decoration: none;
      font-weight: 500;
    }
    a:hover {
      color: #8e44ad;
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
      <a href = "../controllers/buzon.php"><i class="fa-solid fa-bell"></i> Buzón</a>
      <a href = "../contactanos.php"><i class="fa-solid fa-phone"></i> Contáctanos</a>
      <a href = "../agregar_ropa.php"><i class="fa-solid fa-plus"></i> Agregar Ropa</a>
      <a href = "../controllers/obtener_inventario.php"><i class="fa-solid fa-box"></i> Inventario</a>
      <a href = "../ayuda.php"><i class="fas fa-concierge-bell"></i> Ayuda</a>
      <a href = "../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
    </nav>
  </header>
  <div class="container">
    <h1>Mapa del Sitio</h1>
    <ul>
      <li><a href="index.php">Inicio</a></li>
      <li><a href="registro.php">Registrar</a></li>
      <li><a href="login.php">Inicio de Sesión</a></li>
      <li><a href="buzon.php">Buzón</a></li>
      <li><a href="ayuda.php">Ayuda</a></li>
      <li><a href="contacto.php">Contáctanos</a></li>
      <li><a href="cambiar_contraseña.php">Cambiar Contraseña</a></li>
      <li><a href="mapa.php">Mapa del Sitio</a></li>
      <li><a href="error.php">Página de Error</a></li>
    </ul>

    
    <a href="../controllers/obtener_inventario.php" class="btn-back">⬅ Regresar</a>
  </div>
</body>
</html>

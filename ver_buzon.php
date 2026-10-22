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
  <title>Buzón de Mensajes</title>
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
      margin: auto;
      background: #000;
      border: 2px solid #fff;
      border-radius: 10px;
      padding: 20px;
    }
    h1 {
      text-align: center;
      margin-bottom: 10px;
      color: #fff;
    }
    .usuario-activo {
      text-align: center;
      margin-bottom: 20px;
      font-weight: 600;
    }
    .mensaje-card {
      border: 2px solid #fff;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
      background: #111;
    }
    .asunto { font-size: 16px; font-weight: 500; color: #fff; margin-bottom: 5px; }
    .mensaje { font-size: 14px; color: #ddd; margin-bottom: 10px; line-height: 1.4; }
    .fecha { font-size: 12px; color: #aaa; margin-bottom: 10px; }
    textarea {
      width: 100%;
      border: 2px solid #fff;
      border-radius: 8px;
      padding: 10px;
      background: #000;
      color: #fff;
      resize: vertical;
      margin-bottom: 10px;
    }
    .btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: #6a0dad;
      border: 2px solid #fff;
      border-radius: 8px;
      color: #fff;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.3s;
      text-decoration: none;
    }
    .btn:hover { background-color: #8e44ad; }
    .btn-back {
      display: block;
      width: fit-content;
      margin: 20px auto 0;
      text-align: center;
    }
    .respuesta-card {
      background: #222;
      border-left: 3px solid #6a0dad;
      padding: 8px;
      margin-top: 8px;
      border-radius: 5px;
      font-size: 13px;
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
      <a href = "../agregar_ropa.php"><i class="fa-solid fa-plus"></i> Agregar Ropa</a>
      <a href = "../contactanos.php"><i class="fa-solid fa-phone"></i> Contáctanos</a>
      <a href = "../mapa.php"><i class="fa-solid fa-map"></i> Mapa</a>
      <a href = "../ayuda.php"><i class="fas fa-concierge-bell"></i> Ayuda</a>
      <a href = "../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
    </nav>
  </header>
  <div class="container">
    <h1>Buzón de Mensajes</h1>

    <div class="usuario-activo">
      Usuario activo: <?= htmlspecialchars($usuario['name'] ?? 'Invitado') ?>
    </div>

    <?php if (empty($mensajes)): ?>
      <p>No tienes mensajes en tu buzón.</p>
    <?php else: ?>
      <?php foreach ($mensajes as $m): ?>
        <div class="mensaje-card">
          <div class="asunto"><?= htmlspecialchars($m['asunto']) ?></div>
          <div class="mensaje"><?= nl2br(htmlspecialchars($m['mensaje'])) ?></div>
          <div class="fecha">
            <?php 
            if ($m['fecha'] instanceof DateTime) {
                echo $m['fecha']->format('d/m/Y H:i');
            } else {
                echo htmlspecialchars($m['fecha']);
            }
            ?>
          </div>

          <!-- Formulario para responder -->
          <form action="../controllers/responder_mensaje.php" method="POST">
            <input type="hidden" name="mensaje_id" value="<?= $m['id'] ?>">
            <textarea name="respuesta" rows="3" placeholder="Escribe tu respuesta..." required></textarea>
            <button type="submit" class="btn">Responder</button>
          </form>

          <!-- Mostrar respuestas -->
          <?php
          $sqlResp = "SELECT r.respuesta, r.fecha, u.name AS usuario
                      FROM respuestas r
                      INNER JOIN usuarios u ON r.usuario_id = u.id
                      WHERE r.mensaje_id = ?
                      ORDER BY r.fecha ASC";
          $stmtResp = sqlsrv_prepare($conn, $sqlResp, [$m['id']]);
          sqlsrv_execute($stmtResp);
          while ($resp = sqlsrv_fetch_array($stmtResp, SQLSRV_FETCH_ASSOC)) {
              echo "<div class='respuesta-card'><strong>"
                   . htmlspecialchars($resp['usuario']) . ":</strong> "
                   . htmlspecialchars($resp['respuesta']) .
                   " <small>";
              if ($resp['fecha'] instanceof DateTime) {
                  echo $resp['fecha']->format('d/m/Y H:i');
              } else {
                  echo htmlspecialchars($resp['fecha']);
              }
              echo "</small></div>";
          }
          ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Botón de regresar -->
    <a href="../controllers/obtener_inventario.php" class="btn btn-back">⬅ Regresar</a>
  </div>
</body>
</html>

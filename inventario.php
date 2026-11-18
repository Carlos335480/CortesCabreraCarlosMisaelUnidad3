<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['usuario'])) {
    $nombre = $_SESSION['usuario']['name'];
} else {
    $nombre = "Invitado";
}

$marca  = $marca ?? '';
$result = $result ?? [];
?>


<!DOCTYPE html>
<html lang = "es">
<head>
  <meta charset = "UTF-8">
  <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
  <title>Inventario</title>
  <link rel = "stylesheet" href = "../assets/css/estilos_inventario.css">
  <link href = "https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Poppins:wght@500&family=Open+Sans:wght@400&display=swap" rel = "stylesheet">
  <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <script src = "https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <?php if (isset($_GET['error']) && is_string($_GET['error'])): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: <?= json_encode($_GET['error'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        confirmButtonColor: '#d33',
        timer: 3000,
        timerProgressBar: true
      });
      if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.pathname);
      }
    </script>
  <?php endif; ?>

  <?php if (isset($_GET['exito']) && is_string($_GET['exito'])): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: <?= json_encode($_GET['exito'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        confirmButtonColor: '#28a745',
        timer: 3000,
        timerProgressBar: true
      });
      if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.pathname);
      }
    </script>
  <?php endif; ?>

  <header class = "menu">
    <div>
      <span><i class = "fas fa-user"></i> <?= isset($nombre) ? htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') : "Invitado" ?></span>
    </div>

    <nav>
      <a href = "../controllers/buzon.php"><i class="fa-solid fa-bell"></i> Buzón</a>
      <a href = "../agregar_ropa.php"><i class="fa-solid fa-plus"></i> Agregar Ropa</a>
      <a href = "../contactanos.php"><i class="fa-solid fa-phone"></i> Contáctanos</a>
      <a href = "../mapa.php"><i class="fa-solid fa-map"></i> Mapa</a>
      <a href = "../ayuda.php"><i class="fas fa-concierge-bell"></i> Ayuda</a>
      <a href = "../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
    </nav>

  </header>

  <main>
    <h1><i class = "fas fa-tshirt"></i> Stock</h1>

    <form action = "" method = "GET" class = "busqueda">
      <div class = "input-icon">
        <input type = "text" name = "marca" placeholder = "Buscar por marca" value = "<?= htmlspecialchars($marca, ENT_QUOTES, 'UTF-8') ?>">
      </div>
      <button type = "submit"><i class = "fas fa-search"></i> Buscar</button>
    </form>

    <section class = "inventario">
      <?php foreach($result as $row): ?>
        <article class = "card">
          <img src = "<?= htmlspecialchars($row['imagen'] ?? '', ENT_QUOTES, 'UTF-8') ?>" alt = "<?= htmlspecialchars($row['modelo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <div class = "info">
            <h2><i class = "fas fa-tag"></i> <?= htmlspecialchars($row['marca'] ?? '', ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($row['modelo'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
            <p><i class = "fas fa-ruler"></i> Talla: <?= htmlspecialchars($row['talla'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <p><i class = "fas fa-dollar-sign"></i> Precio: $<?= htmlspecialchars((string)($row['precio'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            <a href = "detalle_producto.php?id=<?= urlencode((string)($row['id'] ?? '')) ?>"><i class = "fas fa-eye"></i> Ver más</a>
          </div>
        </article>
      <?php endforeach; ?>
    </section>
  </main>

</body>
</html>
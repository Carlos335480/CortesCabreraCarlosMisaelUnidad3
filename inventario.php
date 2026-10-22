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

  <?php if (isset($_GET['error'])): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= htmlspecialchars($_GET['error']) ?>',
        confirmButtonColor: '#d33',
        timer: 3000,
        timerProgressBar: true
      });
      if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.pathname);
      }
    </script>
  <?php endif; ?>

  <?php if (isset($_GET['exito'])): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: '<?= htmlspecialchars($_GET['exito']) ?>',
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
      <span><i class = "fas fa-user"></i> <?= isset($nombre) ? htmlspecialchars($nombre) : "Invitado" ?></span>
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
        <input type = "text" name = "marca" placeholder = "Buscar por marca" value = "<?= htmlspecialchars($marca) ?>">
      </div>
      <button type = "submit"><i class = "fas fa-search"></i> Buscar</button>
    </form>

    <section class = "inventario">
      <?php foreach($result as $row): ?>
        <article class = "card">
          <img src = "<?= $row['imagen'] ?>" alt = "<?= $row['modelo'] ?>">
          <div class = "info">
            <h2><i class = "fas fa-tag"></i> <?= $row['marca'] ?> <?= $row['modelo'] ?></h2>
            <p><i class = "fas fa-ruler"></i> Talla: <?= $row['talla'] ?></p>
            <p><i class = "fas fa-dollar-sign"></i> Precio: $<?= $row['precio'] ?></p>
            <a href = "detalle_producto.php?id=<?= $row['id'] ?>"><i class = "fas fa-eye"></i> Ver más</a>
          </div>
        </article>
      <?php endforeach; ?>
    </section>
  </main>

</body>
</html>

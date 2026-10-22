<?php

if (empty($_SESSION['usuario'])) {
    header("Location: ../index.php?error=Debes iniciar sesión");
    exit();
}
$nombre = $_SESSION['usuario']['name'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalles del Producto</title>
  <link rel="stylesheet" href="../assets/css/estilos_detalle.css">
</head>
<body>
  <div class="detalle-container">
    <p class="usuario">Bienvenido, <?= htmlspecialchars($nombre) ?></p>

    <h1><?= htmlspecialchars($producto['marca']) ?> <?= htmlspecialchars($producto['modelo']) ?></h1>
    <img src="<?= $producto['imagen'] ?>" alt="<?= $producto['modelo'] ?>" class="producto-img">

    <p><strong>Talla:</strong> <?= htmlspecialchars($producto['talla']) ?></p>
    <p><strong>Precio:</strong> $<?= htmlspecialchars($producto['precio']) ?></p>
    <p><strong>Descripción:</strong> <?= htmlspecialchars($producto['descripcion']) ?></p>

    <form action="../controllers/comprar_producto.php" method="POST" class="form-compra">
      <input type="hidden" name="accion" value="comprar">
      <input type="hidden" name="id" value="<?= $producto['id'] ?>">

      <label for="nombre_tarjeta">Nombre en la tarjeta:</label>
      <input type="text" name="nombre_tarjeta" id="nombre_tarjeta" placeholder="Nombre completo" required>

      <label for="numero_tarjeta">Número de tarjeta:</label>
      <input type="text" name="numero_tarjeta" id="numero_tarjeta" placeholder="XXXX-XXXX-XXXX-XXXX" required>

      <label for="fecha_exp">Fecha de expiración:</label>
      <input type="text" name="fecha_exp" id="fecha_exp" placeholder="MM/AA" required>

      <label for="cvv">CVV:</label>
      <input type="text" name="cvv" id="cvv" placeholder="123" required>

      <label for="direccion">Dirección de envío:</label>
      <textarea name="direccion" id="direccion" rows="3" placeholder="Calle, número, colonia, ciudad" required></textarea>

      <button type="submit" class="btn-comprar">Comprar</button>
    </form>

    <a href="../controllers/obtener_inventario.php" class="btn-volver">Volver al Inventario</a>
  </div>
</body>
</html>

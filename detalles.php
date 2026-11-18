<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario'])) {
    header("Location: ../index.php?error=" . urlencode("Debes iniciar sesión"));
    exit();
}
$nombre = $_SESSION['usuario']['name'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalles del Producto</title>
  <link rel="stylesheet" href="../assets/css/estilos_detalle.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
  .pago-card {
      background: #18181b;
      border: 1px solid #3f3f46;
      border-radius: 12px;
      padding: 20px;
      margin: 18px 0;
      text-align: left;
    }
    .pago-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid #3f3f46;
    }
    .pago-header h3 {
      margin: 0;
      font-size: 1.05rem;
      color: #fff;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .pago-header h3 i {
      color: rgb(168, 85, 247);
    }
    .tarjetas-iconos {
      display: flex;
      gap: 10px;
      font-size: 1.5rem;
      color: #d4d4d8;
    }
    .pago-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
    }
    .pago-full {
      grid-column: 1 / -1;
    }
    .form-compra .campo-pago label {
      display: block;
      font-size: 0.85rem;
      font-weight: 600;
      color: #e4e4e7;
      margin-bottom: 6px;
    }
    .input-wrap {
      position: relative;
      display: flex;
      align-items: center;
    }
    .input-wrap i {
      position: absolute;
      left: 12px;
      color: #a1a1aa;
      pointer-events: none;
    }
    .form-compra .input-wrap input,
    .form-compra .campo-pago textarea {
      width: 100%;
      padding: 10px 12px 10px 36px;
      margin-bottom: 0;
      border: 1px solid #52525b;
      border-radius: 8px;
      font-size: 0.95rem;
      background: #27272a;
      color: #ffffff;
      box-sizing: border-box;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-compra .campo-pago textarea {
      padding-left: 12px;
      resize: vertical;
    }
    .form-compra .input-wrap input:focus,
    .form-compra .campo-pago textarea:focus {
      outline: none;
      border-color: rgb(147, 51, 234);
      box-shadow: 0 0 0 3px rgba(108, 0, 180, 0.35);
    }
    .form-compra .input-wrap input::placeholder,
    .form-compra .campo-pago textarea::placeholder {
      color: #a1a1aa;
    }
    .nota-seguridad {
      margin-top: 14px;
      font-size: 0.8rem;
      color: #a1a1aa;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    @media (max-width: 520px) {
      .pago-grid {
        grid-template-columns: 1fr;
      }
    }
    </style>
</head>
<body>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <?php if (isset($_GET['error']) && is_string($_GET['error'])): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Revisa los datos',
        text: <?= json_encode($_GET['error'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        confirmButtonColor: '#d33'
      });
      if (window.history.replaceState) {
        const url = new URL(window.location.href);
        url.searchParams.delete('error');
        window.history.replaceState(null, null, url.toString());
      }
    </script>
  <?php endif; ?>

  <div class="detalle-container">
    <p class="usuario">Bienvenido, <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></p>

    <h1><?= htmlspecialchars($producto['marca'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($producto['modelo'], ENT_QUOTES, 'UTF-8') ?></h1>
    <img src="<?= htmlspecialchars($producto['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($producto['modelo'], ENT_QUOTES, 'UTF-8') ?>" class="producto-img">

    <p><strong>Talla:</strong> <?= htmlspecialchars($producto['talla'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Precio:</strong> $<?= htmlspecialchars((string)$producto['precio'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Descripción:</strong> <?= htmlspecialchars($producto['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>

    <form action="../controllers/comprar_producto.php" method="POST" class="form-compra">
      <input type="hidden" name="accion" value="comprar">
      <input type="hidden" name="id" value="<?= htmlspecialchars((string)$producto['id'], ENT_QUOTES, 'UTF-8') ?>">

      <div class="pago-card">
        <div class="pago-header">
          <h3><i class="fa-solid fa-lock"></i> Pago y Envío Seguro</h3>
          <div class="tarjetas-iconos">
            <i class="fa-brands fa-cc-visa"></i>
            <i class="fa-brands fa-cc-mastercard"></i>
            <i class="fa-brands fa-cc-amex"></i>
          </div>
        </div>

        <div class="pago-grid">
          <div class="campo-pago pago-full">
            <label for="nombre_tarjeta">Titular de la tarjeta</label>
            <div class="input-wrap">
              <i class="fa-solid fa-user"></i>
              <input type="text" name="nombre_tarjeta" id="nombre_tarjeta" maxlength="100" placeholder="Como aparece en la tarjeta" required>
            </div>
          </div>

          <div class="campo-pago pago-full">
            <label for="numero_tarjeta">Número de tarjeta (16 dígitos)</label>
            <div class="input-wrap">
              <i class="fa-regular fa-credit-card"></i>
              <input type="text" name="numero_tarjeta" id="numero_tarjeta" inputmode="numeric" maxlength="19" placeholder="0000 0000 0000 0000" required>
            </div>
          </div>

          <div class="campo-pago">
            <label for="fecha_exp">Expiración (MM/AA)</label>
            <div class="input-wrap">
              <i class="fa-regular fa-calendar"></i>
              <input type="text" name="fecha_exp" id="fecha_exp" inputmode="numeric" maxlength="5" pattern="(0[1-9]|1[0-2])\/\d{2}" placeholder="08/28" required>
            </div>
          </div>

          <div class="campo-pago">
            <label for="cvv">Código CVV</label>
            <div class="input-wrap">
              <i class="fa-solid fa-shield-halved"></i>
              <input type="password" name="cvv" id="cvv" inputmode="numeric" maxlength="4" pattern="\d{3,4}" placeholder="123" required>
            </div>
          </div>

          <div class="campo-pago pago-full">
            <label for="direccion">Dirección de envío</label>
            <textarea name="direccion" id="direccion" rows="3" maxlength="255" placeholder="Calle, número, colonia, ciudad y código postal" required></textarea>
          </div>
        </div>

        <div class="nota-seguridad">
          <i class="fa-solid fa-circle-check" style="color:#16a34a;"></i>
          <span>Tus datos viajan cifrados y el número de tarjeta se almacena enmascarado.</span>
        </div>
      </div>

      <button type="submit" class="btn-comprar"><i class="fa-solid fa-bag-shopping"></i> Confirmar Compra</button>
    </form>

    <a href="../controllers/obtener_inventario.php" class="btn-volver">Volver al Inventario</a>
  </div>

  <script>
    const inputTarjeta = document.getElementById('numero_tarjeta');
    inputTarjeta.addEventListener('input', function () {
      let digitos = this.value.replace(/\D/g, '').slice(0, 16);
      this.value = digitos.replace(/(\d{4})(?=\d)/g, '$1 ');
    });

    // Formateo automático MM/AA en fecha de expiración
    const inputFecha = document.getElementById('fecha_exp');
    inputFecha.addEventListener('input', function () {
      let digitos = this.value.replace(/\D/g, '').slice(0, 4);
      if (digitos.length >= 3) {
        this.value = digitos.slice(0, 2) + '/' + digitos.slice(2);
      } else {
        this.value = digitos;
      }
    });

    const inputCvv = document.getElementById('cvv');
    inputCvv.addEventListener('input', function () {
      this.value = this.value.replace(/\D/g, '').slice(0, 4);
    });
  </script>
</body>
</html>
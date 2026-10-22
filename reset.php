<?php
require_once __DIR__ . "/config/conexion.php";

$token = $_GET['token'] ?? '';

$sql = "SELECT correo, expira FROM tokens_recuperacion WHERE token = ?";
$stmt = sqlsrv_prepare($conn, $sql, [$token]);
sqlsrv_execute($stmt);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

$valido = false;
$correo = null;

if ($row) {
    $expira = $row['expira'];
    if ($expira instanceof DateTime) {
        $valido = $expira->getTimestamp() > time();
    }
    $correo = $row['correo'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Restablecer Contraseña</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/estilos_reset.css">
</head>
<body>
  <header class="menu">
    <div>
      <span><i class="fas fa-key"></i> Recuperación</span>
    </div>
    <nav>
      <a href="../index.php"><i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión</a>
    </nav>
  </header>

  <div class="container">
    <h1>Restablecer Contraseña</h1>
    <?php if ($valido): ?>
      <form action="controllers/guardar_nueva.php" method="POST">
        <input type="hidden" name="correo" value="<?= htmlspecialchars($correo) ?>">
        <div class="input-icon">
          <input type="password" name="nueva" placeholder="Nueva contraseña" required>
          <i class="fas fa-lock"></i>
        </div>
        <div class="input-icon">
          <input type="password" name="confirmar" placeholder="Confirmar contraseña" required>
          <i class="fas fa-lock"></i>
        </div>
        <button type="submit" class="btn"><i class="fas fa-save"></i> Guardar</button>
      </form>
    <?php else: ?>
      <p>El enlace de recuperación es inválido o ha expirado.</p>
    <?php endif; ?>
  </div>
</body>
</html>

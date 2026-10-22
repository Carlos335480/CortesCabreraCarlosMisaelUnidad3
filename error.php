<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Error - Página no encontrada</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #000;
      color: #fff;
      margin: 0;
      padding: 20px;
      text-align: center;
    }
    .container {
      max-width: 600px;
      margin: auto;
      background: #000;
      border: 2px solid #fff;
      border-radius: 10px;
      padding: 40px;
    }
    h1 {
      font-size: 32px;
      margin-bottom: 20px;
      color: #fff;
    }
    p {
      font-size: 16px;
      color: #ddd;
      margin-bottom: 30px;
    }
    .btn {
      display: inline-block;
      padding: 12px 24px;
      background-color: #6a0dad;
      border: 2px solid #fff;
      border-radius: 8px;
      color: #fff;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.3s;
      text-decoration: none;
    }
    .btn:hover {
      background-color: #8e44ad;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>⚠️ Error</h1>
    <p>Lo sentimos, la página que intentas acceder no existe o ha ocurrido un problema.</p>
    <a href="../controllers/obtener_inventario.php" class="btn">⬅ Regresar al inventario</a>
  </div>
</body>
</html>

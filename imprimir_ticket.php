<?php
session_start();

if (!isset($_SESSION['ticket'])) {
    echo "No hay ticket disponible para imprimir.";
    exit();
}

$ticket = $_SESSION['ticket'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #000;
            background-color: #fff; /* Fondo blanco para impresión */
        }
        .ticket-container {
            border: 2px dashed #000; /* estilo recibo */
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f9f9f9;
            color: #000;
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            margin: 5px 0;
        }
        .ticket-image {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
            margin: 0 auto 20px;
            display: block;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }
        .btn-back {
            display: block;
            width: auto;
            padding: 8px 12px;
            background-color: #333;
            color: #fff;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
            margin: 20px auto;
            max-width: 150px;
        }
        .btn-back:hover {
            background-color: #555;
        }
        @media print {
            body {
                background-color: #000000;
                color: #ffffff;
            }
            .btn-back {
                display: none; /* Ocultar botón al imprimir */
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="ticket-container">
        <h1>Ticket de Compra</h1>
        <img src="<?php echo htmlspecialchars($ticket['imagen']); ?>" alt="Imagen del producto" class="ticket-image">
        <p><strong>ID de Compra:</strong> <?php echo htmlspecialchars($ticket['id']); ?></p>
        <p><strong>Marca:</strong> <?php echo htmlspecialchars($ticket['marca']); ?></p>
        <p><strong>Modelo:</strong> <?php echo htmlspecialchars($ticket['modelo']); ?></p>
        <p><strong>Talla:</strong> <?php echo htmlspecialchars($ticket['talla']); ?></p>
        <p><strong>Precio:</strong> $<?php echo htmlspecialchars($ticket['precio']); ?></p>
        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($ticket['descripcion']); ?></p>
        <p><strong>Nombre en Tarjeta:</strong> <?php echo htmlspecialchars($ticket['nombre_tarjeta']); ?></p>
        <p><strong>Tarjeta:</strong> <?php echo htmlspecialchars($ticket['numero_tarjeta']); ?></p>
        <p><strong>Dirección de envío:</strong> <?php echo htmlspecialchars($ticket['direccion']); ?></p>
        <div class="footer">¡Gracias por tu compra!</div>
    </div>
    <a href="../controllers/obtener_inventario.php" class="btn-back">Regresar</a>
</body>
</html>

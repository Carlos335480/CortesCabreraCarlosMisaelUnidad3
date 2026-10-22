<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (empty($_SESSION['usuario'])) {
    header("Location: ../index.php?error=Debes iniciar sesión");
    exit();
}

$usuario_id = $_SESSION['usuario']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'comprar') {
    $id            = trim($_POST['id'] ?? "");
    $nombre_tarjeta = trim($_POST['nombre_tarjeta'] ?? "");
    $numero_tarjeta = trim($_POST['numero_tarjeta'] ?? "");
    $fecha_exp      = trim($_POST['fecha_exp'] ?? "");
    $cvv            = trim($_POST['cvv'] ?? "");
    $direccion      = trim($_POST['direccion'] ?? "");

    if (!empty($id) && !empty($numero_tarjeta) && !empty($nombre_tarjeta) && !empty($fecha_exp) && !empty($cvv) && !empty($direccion)) {
        try {
            $sql = "SELECT * FROM ropa WHERE id = ?";
            $params = [$id];
            $stmt = sqlsrv_prepare($conn, $sql, $params);
            sqlsrv_execute($stmt);
            $ropa = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

            if ($ropa) {
                $compra_id = uniqid();

                $sqlInsert = "INSERT INTO compra 
                    (id, marca, modelo, talla, precio, descripcion, imagen, usuario_id, nombre_tarjeta, numero_tarjeta, fecha_exp, cvv, direccion)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $paramsInsert = [
                    $compra_id,
                    $ropa['marca'],
                    $ropa['modelo'],
                    $ropa['talla'],
                    $ropa['precio'],
                    $ropa['descripcion'],
                    $ropa['imagen'],
                    $usuario_id,
                    $nombre_tarjeta,
                    $numero_tarjeta,
                    $fecha_exp,
                    $cvv,
                    $direccion
                ];

                $stmtInsert = sqlsrv_prepare($conn, $sqlInsert, $paramsInsert);

                if ($stmtInsert && sqlsrv_execute($stmtInsert)) {
                    $_SESSION['ticket'] = [
                        'id'          => $compra_id,
                        'marca'       => $ropa['marca'],
                        'modelo'      => $ropa['modelo'],
                        'talla'       => $ropa['talla'],
                        'precio'      => $ropa['precio'],
                        'descripcion' => $ropa['descripcion'],
                        'imagen'      => $ropa['imagen'],
                        'nombre_tarjeta' => $nombre_tarjeta,
                        'numero_tarjeta' => $numero_tarjeta,
                        'fecha_exp'      => $fecha_exp,
                        'cvv'            => $cvv,
                        'direccion'      => $direccion
                    ];

                    header("Location: ../imprimir_ticket.php?exito=Compra realizada");
                    exit();
                } else {
                    header("Location: ../controllers/detalle_producto.php?error=Error al guardar la compra");
                    exit();
                }
            }
        } catch (Exception $e) {
            header("Location: ../controllers/obtener_inventario.php?error=Error en la inserción: " . $e->getMessage());
            exit();
        }
    } else {
        header("Location: ../controllers/detalle_producto.php?error=Datos incompletos");
        exit();
    }
}
?>

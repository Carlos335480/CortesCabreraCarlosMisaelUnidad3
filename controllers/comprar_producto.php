<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (empty($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
    header("Location: ../index.php?error=" . urlencode("Debes iniciar sesión"));
    exit();
}

$usuario_id = (int) $_SESSION['usuario']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'comprar') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nombre_tarjeta = trim($_POST['nombre_tarjeta'] ?? "");
    $numero_tarjeta = preg_replace('/\D/', '', $_POST['numero_tarjeta'] ?? "");
    $fecha_exp = trim($_POST['fecha_exp'] ?? "");
    $cvv = preg_replace('/\D/', '', $_POST['cvv'] ?? "");
    $direccion = trim($_POST['direccion'] ?? "");

    if ($id && $id > 0 && !empty($numero_tarjeta) && !empty($nombre_tarjeta) && !empty($fecha_exp) && !empty($cvv) && !empty($direccion)) {
        
        if (!preg_match('/^\d{13,19}$/', $numero_tarjeta) || !preg_match('/^\d{3,4}$/', $cvv) || !preg_match('/^(0[1-9]|1[0-2])\/\d{2,4}$/', $fecha_exp) || mb_strlen($direccion, 'UTF-8') > 255 || mb_strlen($nombre_tarjeta, 'UTF-8') > 100) {
            header("Location: ../controllers/detalle_producto.php?id=" . urlencode((string)$id) . "&error=" . urlencode("Datos de pago o dirección inválidos"));
            exit();
        }

        $tarjeta_enmascarada = str_repeat('*', max(0, strlen($numero_tarjeta) - 4)) . substr($numero_tarjeta, -4);
        $cvv_enmascarado = '***';

        try {
            $sql = "SELECT * FROM ropa WHERE id = ?";
            $params = [$id];
            $stmt = sqlsrv_prepare($conn, $sql, $params);

            if (!$stmt || !sqlsrv_execute($stmt)) {
                throw new Exception("Error al consultar el producto");
            }

            $ropa = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            sqlsrv_free_stmt($stmt);

            if ($ropa) {
                $compra_id = bin2hex(random_bytes(8));

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
                    $tarjeta_enmascarada,
                    $fecha_exp,
                    $cvv_enmascarado,
                    $direccion
                ];

                $stmtInsert = sqlsrv_prepare($conn, $sqlInsert, $paramsInsert);

                if ($stmtInsert && sqlsrv_execute($stmtInsert)) {
                    sqlsrv_free_stmt($stmtInsert);
                    sqlsrv_close($conn);

                    $_SESSION['ticket'] = [
                        'id' => $compra_id,
                        'marca' => $ropa['marca'],
                        'modelo' => $ropa['modelo'],
                        'talla' => $ropa['talla'],
                        'precio' => $ropa['precio'],
                        'descripcion' => $ropa['descripcion'],
                        'imagen' => $ropa['imagen'],
                        'nombre_tarjeta' => $nombre_tarjeta,
                        'numero_tarjeta' => $tarjeta_enmascarada,
                        'fecha_exp' => $fecha_exp,
                        'cvv' => $cvv_enmascarado,
                        'direccion' => $direccion
                    ];

                    header("Location: ../imprimir_ticket.php?exito=" . urlencode("Compra realizada"));
                    exit();
                } else {
                    error_log(print_r(sqlsrv_errors(), true));
                    if ($stmtInsert) sqlsrv_free_stmt($stmtInsert);
                    sqlsrv_close($conn);
                    header("Location: ../controllers/detalle_producto.php?id=" . urlencode((string)$id) . "&error=" . urlencode("Error al guardar la compra"));
                    exit();
                }
            } else {
                sqlsrv_close($conn);
                header("Location: ../controllers/obtener_inventario.php?error=" . urlencode("Producto no encontrado"));
                exit();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (isset($conn) && $conn) sqlsrv_close($conn);
            header("Location: ../controllers/obtener_inventario.php?error=" . urlencode("Error al procesar la compra"));
            exit();
        }
    } else {
        $redirId = $id ? "?id=" . urlencode((string)$id) . "&" : "?";
        header("Location: ../controllers/detalle_producto.php" . $redirId . "error=" . urlencode("Datos incompletos"));
        exit();
    }
}
?>
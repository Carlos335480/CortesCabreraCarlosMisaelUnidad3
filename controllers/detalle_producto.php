<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (empty($_SESSION['usuario'])) {
    header("Location: ../index.php?error=" . urlencode("Debes iniciar sesión"));
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: ../controllers/obtener_inventario.php?error=" . urlencode("Producto no especificado"));
    exit();
}

$sql = "SELECT * FROM ropa WHERE id = ?";
$params = [$id];
$stmt = sqlsrv_prepare($conn, $sql, $params);

if ($stmt && sqlsrv_execute($stmt)) {
    $producto = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
} else {
    error_log(print_r(sqlsrv_errors(), true));
    if ($stmt) {
        sqlsrv_free_stmt($stmt);
    }
    sqlsrv_close($conn);
    $producto = null;
}

if ($producto) {
    require_once __DIR__ . "/../detalles.php";
} else {
    header("Location: ../controllers/obtener_inventario.php?error=" . urlencode("Producto no encontrado"));
    exit();
}
?>
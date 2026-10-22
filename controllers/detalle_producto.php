<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (empty($_SESSION['usuario'])) {
    header("Location: ../index.php?error=Debes iniciar sesión");
    exit();
}

$id = trim($_GET['id'] ?? "");

if (empty($id)) {
    header("Location: ../controllers/obtener_inventario.php?error=Producto no especificado");
    exit();
}

$sql = "SELECT * FROM ropa WHERE id = ?";
$params = [$id];
$stmt = sqlsrv_prepare($conn, $sql, $params);
sqlsrv_execute($stmt);

$producto = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($producto) {

    require_once __DIR__ . "/../detalles.php";
} else {
    header("Location: ../controllers/obtener_inventario.php?error=Producto no encontrado");
    exit();
}
?>

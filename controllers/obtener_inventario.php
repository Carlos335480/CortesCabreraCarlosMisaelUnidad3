<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (empty($_SESSION['usuario'])) {
    header("Location: ../index.php?error=Debes iniciar sesión");
    exit();
}

$marca = trim($_GET['marca'] ?? "");

$sqlBuscar = "SELECT * FROM ropa WHERE marca LIKE ?";
$paramsBuscar = ["%" . $marca . "%"];
$stmtBuscar = sqlsrv_prepare($conn, $sqlBuscar, $paramsBuscar);
sqlsrv_execute($stmtBuscar);

$result = [];
while ($row = sqlsrv_fetch_array($stmtBuscar, SQLSRV_FETCH_ASSOC)) {
    $result[] = $row;
}


require_once __DIR__ . "/../inventario.php";
?>

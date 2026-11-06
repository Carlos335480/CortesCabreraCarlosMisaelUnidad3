<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (empty($_SESSION['usuario'])) {
    header("Location: ../index.php?error=" . urlencode("Debes iniciar sesión"));
    exit();
}

$marca = filter_input(INPUT_GET,'marca', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$marca = trim($marca);
$result =[];

$sqlBuscar = "SELECT * FROM ropa WHERE marca LIKE ? " ;
$paramsBuscar = ["%" . $marca . "%"];

$stmtBuscar = sqlsrv_prepare($conn, $sqlBuscar, $paramsBuscar);

if ($stmtBuscar && sqlsrv_execute($stmtBuscar)) {
    while ($row = sqlsrv_fetch_array($stmtBuscar, SQLSRV_FETCH_ASSOC)) {
        $result[] = $row;
    }
    sqlsrv_free_stmt($stmtBuscar);
} else {
    error_log(print_r(sqlsrv_errors(), true));
}
if(isset($conn) && $conn){
    sqlsrv_close($conn);
}

require_once __DIR__ . "/../inventario.php";
?>

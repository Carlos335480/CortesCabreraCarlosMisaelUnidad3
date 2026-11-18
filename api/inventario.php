<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . "/../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'mensaje' => 'Método no permitido'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$marca = trim((string) (filter_input(INPUT_GET, 'marca', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''));

$sql  = "SELECT id, marca, modelo, talla, precio, descripcion, imagen FROM ropa WHERE marca LIKE ?";
$stmt = sqlsrv_prepare($conn, $sql, ["%" . $marca . "%"]);

if ($stmt && sqlsrv_execute($stmt)) {
    $productos = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $productos[] = $row;
    }
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'total' => count($productos),
        'data' => $productos
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    error_log(print_r(sqlsrv_errors(), true));
    if ($stmt) {
        sqlsrv_free_stmt($stmt);
    }
    if (isset($conn) && $conn) {
        sqlsrv_close($conn);
    }
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'mensaje' => 'Error al consultar el inventario'
    ], JSON_UNESCAPED_UNICODE);
}
?>
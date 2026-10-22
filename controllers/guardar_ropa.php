<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (empty($_SESSION['usuario'])) {
    header("Location: ../index.php?error=Debes iniciar sesión");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);
    $talla = trim($_POST['talla']);
    $precio = floatval($_POST['precio']);
    $descripcion = trim($_POST['descripcion']);
    $imagen = $_FILES['imagen']['name'] ?? null;

    if ($imagen) {
        $ruta = "../assets/img/" . basename($imagen);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
    } else {
        $ruta = null;
    }

    $sql = "INSERT INTO ropa (marca, modelo, talla, precio, descripcion, imagen) VALUES (?, ?, ?, ?, ?, ?)";
    $params = [$marca, $modelo, $talla, $precio, $descripcion, $ruta];

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if ($stmt && sqlsrv_execute($stmt)) {
        header("Location: ../controllers/Obtener_inventario.php?exito=Ropa guardada");
        exit();
    } else {
        header("Location: ../inventario.php?error=Error al guardar la ropa");
        exit();
    }
}
?>

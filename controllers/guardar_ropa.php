<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (empty($_SESSION['usuario'])) {
    header("Location: ../index.php?error=" . urlencode("Debes iniciar sesión"));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $talla = trim($_POST['talla'] ?? '');
    $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $descripcion = trim($_POST['descripcion'] ?? '');

    if (empty($marca) || empty($modelo) || empty($talla) || $precio === false || $precio < 0) {
        header("Location: ../inventario.php?error=" . urlencode("Datos del producto inválidos o incompletos"));
        exit();
    }

    $ruta = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            header("Location: ../inventario.php?error=" . urlencode("Error al subir la imagen"));
            exit();
        }

        if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
            header("Location: ../inventario.php?error=" . urlencode("La imagen supera el tamaño máximo permitido (5MB)"));
            exit();
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeReal = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
        finfo_close($finfo);

        $mimesPermitidos = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        if (!array_key_exists($mimeReal, $mimesPermitidos)) {
            header("Location: ../inventario.php?error=" . urlencode("Formato de imagen no permitido. Usa JPG, PNG o WEBP"));
            exit();
        }

        $nombreSeguro = bin2hex(random_bytes(16)) . '.' . $mimesPermitidos[$mimeReal];
        $ruta = "../assets/img/" . $nombreSeguro;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
            header("Location: ../inventario.php?error=" . urlencode("No se pudo guardar la imagen en el servidor"));
            exit();
        }
    }

    $sql = "INSERT INTO ropa (marca, modelo, talla, precio, descripcion, imagen) VALUES (?, ?, ?, ?, ?, ?)";
    $params = [$marca, $modelo, $talla, $precio, $descripcion, $ruta];
    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if ($stmt && sqlsrv_execute($stmt)) {
        sqlsrv_free_stmt($stmt);
        sqlsrv_close($conn);
        header("Location: ../controllers/Obtener_inventario.php?exito=" . urlencode("Ropa guardada"));
        exit();
    } else {
        if ($ruta && file_exists($ruta)) {
            unlink($ruta);
        }
        error_log(print_r(sqlsrv_errors(), true));
        if ($stmt) {
            sqlsrv_free_stmt($stmt);
        }
        sqlsrv_close($conn);
        header("Location: ../inventario.php?error=" . urlencode("Error al guardar la ropa"));
        exit();
    }
}
?>
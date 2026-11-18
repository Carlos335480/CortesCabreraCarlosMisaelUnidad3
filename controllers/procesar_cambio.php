<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/conexion.php";

function obtenerEnvCambio(string $clave, string $default = ''): string {
    $val = getenv($clave);
    if ($val !== false && $val !== '') {
        return trim((string) $val);
    }
    if (!empty($_ENV[$clave])) {
        return trim((string) $_ENV[$clave]);
    }
    if (!empty($_SERVER[$clave])) {
        return trim((string) $_SERVER[$clave]);
    }
    $rutaEnv = __DIR__ . '/../.env';
    if (file_exists($rutaEnv)) {
        foreach (file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
            $linea = trim($linea);
            if ($linea === '' || str_starts_with($linea, '#')) {
                continue;
            }
            if (str_starts_with($linea, $clave . '=')) {
                return trim(substr($linea, strlen($clave) + 1), " \t\n\r\0\x0B\"'");
            }
        }
    }
    return $default;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token     = trim($_POST['token'] ?? '');
    $actual    = trim($_POST['actual'] ?? '');
    $nueva     = trim($_POST['nueva'] ?? '');
    $confirmar = trim($_POST['confirmar'] ?? '');
    $captcha   = trim($_POST['g-recaptcha-response'] ?? '');

    $redirError = ($token !== '')
        ? "../cambiar_contraseña.php?token=" . urlencode($token) . "&error="
        : "../cambiar_contraseña.php?error=";

    // 1. Validar Google reCAPTCHA v2
    if (empty($captcha)) {
        if (isset($conn) && $conn) sqlsrv_close($conn);
        header("Location: " . $redirError . urlencode("Por favor, completa el reCAPTCHA."));
        exit();
    }

    $secretKey = obtenerEnvCambio('RECAPTCHA_SECRET_KEY');
    if ($secretKey !== '') {
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query([
                    'secret'   => $secretKey,
                    'response' => $captcha,
                    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
                ]),
                'timeout' => 10
            ]
        ];
        $context  = stream_context_create($options);
        $response = @file_get_contents("https://www.google.com/recaptcha/api/siteverify", false, $context);
        $result   = $response ? json_decode($response, true) : null;

        if (!$result || empty($result['success'])) {
            if (isset($conn) && $conn) sqlsrv_close($conn);
            header("Location: " . $redirError . urlencode("Captcha incorrecto. Inténtalo de nuevo."));
            exit();
        }
    }

    // 2. Validar nueva contraseña (mínimo 8 caracteres como ya lo tenías)
    if (empty($nueva) || empty($confirmar)) {
        if (isset($conn) && $conn) sqlsrv_close($conn);
        header("Location: " . $redirError . urlencode("Por favor, llena todos los campos"));
        exit();
    }

    if ($nueva !== $confirmar) {
        if (isset($conn) && $conn) sqlsrv_close($conn);
        header("Location: " . $redirError . urlencode("Las contraseñas nuevas no coinciden"));
        exit();
    }

    if (strlen($nueva) < 8) {
        if (isset($conn) && $conn) sqlsrv_close($conn);
        header("Location: " . $redirError . urlencode("La nueva contraseña debe tener al menos 8 caracteres"));
        exit();
    }

    if ($token !== '') {
        $tokenHash = hash('sha256', $token);

        $sqlToken  = "SELECT correo, expira FROM tokens_recuperacion WHERE token = ?";
        $stmtToken = sqlsrv_prepare($conn, $sqlToken, [$tokenHash]);

        if (!$stmtToken || !sqlsrv_execute($stmtToken)) {
            error_log(print_r(sqlsrv_errors(), true));
            if (isset($conn) && $conn) sqlsrv_close($conn);
            header("Location: ../recuperar.php?error=" . urlencode("Error de conexión con el servidor"));
            exit();
        }

        $rowToken = sqlsrv_fetch_array($stmtToken, SQLSRV_FETCH_ASSOC);
        sqlsrv_free_stmt($stmtToken);

        if (!$rowToken) {
            if (isset($conn) && $conn) sqlsrv_close($conn);
            header("Location: ../recuperar.php?error=" . urlencode("El enlace de recuperación no es válido o ya fue usado."));
            exit();
        }

        $expira = ($rowToken['expira'] instanceof DateTime)
            ? $rowToken['expira']
            : new DateTime((string) $rowToken['expira']);

        if ($expira < new DateTime()) {
            $stmtDel = sqlsrv_prepare($conn, "DELETE FROM tokens_recuperacion WHERE token = ?", [$tokenHash]);
            if ($stmtDel) {
                sqlsrv_execute($stmtDel);
                sqlsrv_free_stmt($stmtDel);
            }
            if (isset($conn) && $conn) sqlsrv_close($conn);
            header("Location: ../recuperar.php?error=" . urlencode("El enlace ha expirado. Solicita uno nuevo."));
            exit();
        }

        $hash       = password_hash($nueva, PASSWORD_DEFAULT);
        $sqlUpdate  = "UPDATE usuarios SET password = ? WHERE LOWER(LTRIM(RTRIM(email))) = LOWER(LTRIM(RTRIM(?)))";
        $stmtUpdate = sqlsrv_prepare($conn, $sqlUpdate, [$hash, $rowToken['correo']]);

        if ($stmtUpdate && sqlsrv_execute($stmtUpdate)) {
            sqlsrv_free_stmt($stmtUpdate);

            // Borrar el token usado para que no pueda reutilizarse
            $stmtDel = sqlsrv_prepare($conn, "DELETE FROM tokens_recuperacion WHERE token = ?", [$tokenHash]);
            if ($stmtDel) {
                sqlsrv_execute($stmtDel);
                sqlsrv_free_stmt($stmtDel);
            }

            sqlsrv_close($conn);
            session_regenerate_id(true);

            header("Location: ../index.php?exito=" . urlencode("Contraseña restablecida correctamente. Ya puedes iniciar sesión."));
            exit();
        } else {
            error_log(print_r(sqlsrv_errors(), true));
            if ($stmtUpdate) sqlsrv_free_stmt($stmtUpdate);
            if (isset($conn) && $conn) sqlsrv_close($conn);

            header("Location: " . $redirError . urlencode("Error al actualizar la contraseña en el servidor"));
            exit();
        }
    }

    if (empty($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
        if (isset($conn) && $conn) sqlsrv_close($conn);
        header("Location: ../index.php?error=" . urlencode("Debes iniciar sesión para realizar esta acción"));
        exit();
    }

    if (empty($actual)) {
        if (isset($conn) && $conn) sqlsrv_close($conn);
        header("Location: " . $redirError . urlencode("Por favor, llena todos los campos"));
        exit();
    }

    $id_usuario = (int) $_SESSION['usuario']['id'];

    $sql  = "SELECT password FROM usuarios WHERE id = ?";
    $stmt = sqlsrv_prepare($conn, $sql, [$id_usuario]);

    if ($stmt && sqlsrv_execute($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        if ($row && password_verify($actual, $row['password'])) {
            sqlsrv_free_stmt($stmt);

            $hash       = password_hash($nueva, PASSWORD_DEFAULT);
            $sqlUpdate  = "UPDATE usuarios SET password = ? WHERE id = ?";
            $stmtUpdate = sqlsrv_prepare($conn, $sqlUpdate, [$hash, $id_usuario]);

            if ($stmtUpdate && sqlsrv_execute($stmtUpdate)) {
                sqlsrv_free_stmt($stmtUpdate);
                sqlsrv_close($conn);

                session_regenerate_id(true);

                header("Location: ../cambiar_contraseña.php?exito=" . urlencode("Contraseña actualizada correctamente"));
                exit();
            } else {
                error_log(print_r(sqlsrv_errors(), true));
                if (isset($conn) && $conn) sqlsrv_close($conn);

                header("Location: ../cambiar_contraseña.php?error=" . urlencode("Error al actualizar la contraseña en el servidor"));
                exit();
            }

        } else {
            sqlsrv_free_stmt($stmt);
            if (isset($conn) && $conn) sqlsrv_close($conn);

            header("Location: ../cambiar_contraseña.php?error=" . urlencode("La contraseña actual es incorrecta"));
            exit();
        }

    } else {
        error_log(print_r(sqlsrv_errors(), true));
        if (isset($conn) && $conn) sqlsrv_close($conn);

        header("Location: ../cambiar_contraseña.php?error=" . urlencode("Error de conexión con el servidor"));
        exit();
    }

} else {
    header("Location: ../index.php");
    exit();
}
?>
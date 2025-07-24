<?php
session_start();
require_once __DIR__ . '/config.php';

// Configuración básica
define('CSV_USERS_PATH', __DIR__ . '/data/usuarios.csv');
define('BASE_URL', 'https://aldeanoglobal.org/sirena/app/');

// Verificar permisos de archivo
if (!is_readable(CSV_USERS_PATH)) {
    die("Error: No se puede leer el archivo de usuarios. Verifique permisos.");
}

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Ambos campos son requeridos";
        header('Location: access.php');
        exit;
    }

    // Leer CSV de usuarios
    $file = fopen(CSV_USERS_PATH, 'r');
    $headers = fgetcsv($file);
    $authenticated = false;
    
    while (($user = fgetcsv($file)) !== false) {
        $userData = array_combine($headers, $user);
        
        if ($userData['Nombre de usuario'] === $email && 
            $userData['Elija una Contraseña'] === $password) {
            
            $_SESSION['user'] = [
                'nombre' => $userData['Nombres y Apeliidos Completos'],
                'email' => $userData['Nombre de usuario'],
                'telefono' => $userData['Telefono WhatsApp']
            ];
            $authenticated = true;
            break;
        }
    }
    fclose($file);

    if ($authenticated) {
        header('Location: dashboard.php');
        exit;
    } else {
        $_SESSION['error'] = "Credenciales incorrectas";
        header('Location: access.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - SIRENA</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="img/logosirena.ico">
</head>
<body>
    <div class="login-container">
        <img src="img/logosirena.jpeg" alt="Logo SIRENA" class="logo">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Acceder</button>
        </form>
    </div>
</body>
</html>
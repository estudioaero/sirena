<?php
session_start();
require_once __DIR__ . '/config.php';

// Verificar autenticación
if (!isset($_SESSION['user'])) {
    header('Location: access.php');
    exit;
}

// Configuración
define('DESTINATARIOS_CSV', __DIR__ . '/data/destinatarios.csv');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - SIRENA</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="img/logosirena.ico">
</head>
<body>
    <header>
        <img src="img/logosirena.jpeg" alt="Logo SIRENA">
        <h1>Bienvenido, <?= htmlspecialchars($_SESSION['user']['nombre']) ?></h1>
    </header>
    
    <div class="dashboard">
        <div class="emergency-buttons">
            <a href="alert_form.php?tipo=seguridad" class="btn btn-blue">
                SIRENA SEGURIDAD
            </a>
            <a href="alert_form.php?tipo=bomberos" class="btn btn-red">
                SIRENA BOMBEROS
            </a>
            <a href="alert_form.php?tipo=salud" class="btn btn-green">
                SIRENA SALUD
            </a>
            <a href="alert_form.php?tipo=servicios" class="btn btn-orange">
                SIRENA SERVICIOS PÚBLICOS
            </a>
        </div>
    </div>
</body>
</html>
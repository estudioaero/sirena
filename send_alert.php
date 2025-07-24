<?php
// Configuración básica
define('BASE_URL', 'https://aldeanoglobal.org/sirena/app/');
define('GMAPS_API_KEY', 'IzaSyBj_nGxkrw8V-cBP8MRbLqaUcRiQrLmKnY&libraries=places&callback=initMap');

// Configuración de archivos
define('CSV_USERS_PATH', __DIR__ . '/data/usuarios.csv');
define('DESTINATARIOS_CSV', __DIR__ . '/data/destinatarios.csv');
define('ALERTS_LOG', __DIR__ . '/data/alertas.log');
define('UPLOADS_DIR', __DIR__ . '/alerts/');

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

// Verificar y crear directorios si no existen
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755);
}

if (!file_exists(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0755);
}

// Establecer permisos
chmod(__DIR__ . '/data', 0755);
chmod(UPLOADS_DIR, 0755);
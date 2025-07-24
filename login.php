<?php
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Expires: 0');
header('Pragma: no-cache');

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'locucion_sirena_db_2';
$username = 'locucion_sirena_user';
$password = 'Q1a2z3581321+-';

// Obtener datos del POST
$documento = $_POST['documento_identidad'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

// Validar datos de entrada
if (empty($documento) || empty($contrasena)) {
    echo json_encode(['estado' => 'error', 'mensaje' => 'Documento y contraseña son requeridos']);
    exit;
}

try {
    // Conexión a la base de datos
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consulta para verificar el usuario
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE documento_identidad = :documento LIMIT 1");
    $stmt->bindParam(':documento', $documento);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'Usuario no encontrado']);
        exit;
    }
    
    // Verificar contraseña
    if (password_verify($contrasena, $usuario['contrasena'])) {
        // Generar token de sesión seguro
        $token = bin2hex(random_bytes(32));
        
        // Devolver datos del usuario
        echo json_encode([
            'estado' => 'ok',
            'nombre' => $usuario['nombres_apellidos'] ?? 'Usuario',
            'documento' => $usuario['documento_identidad'],
            'telefono' => $usuario['telefono_whatsapp'] ?? '',
            'token' => $token,
            'datos_completos' => $usuario
        ]);
    } else {
        echo json_encode(['estado' => 'error', 'mensaje' => 'Contraseña incorrecta']);
    }
} catch (PDOException $e) {
    echo json_encode(['estado' => 'error', 'mensaje' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
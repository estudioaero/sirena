<?php

// Configuración de la base de datos
$dbConfig = [
    "host"     => "65.181.111.148",
    "port"     => "3306",
    "database" => "locucion_sirena_db_2",
    "username" => "locucion_sirena_user",
    "password" => "Q1a2z3581321+-"
];

// Conexión a la base de datos
try {
    $dsn = "mysql:host={$dbConfig["host"]};port={$dbConfig["port"]};dbname={$dbConfig["database"]};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig["username"], $dbConfig["password"], [
        PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Procesar datos del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $marca_temporal = $_POST["marca_temporal"] ?? '';
    $nombre_usuario = $_POST["nombre_usuario"] ?? '';
    $telefono_whatsapp = $_POST["telefono_whatsapp"] ?? '';
    $telefono_whatsapp_2 = $_POST["telefono_whatsapp_2"] ?? '';
    $nombres_apellidos = $_POST["nombres_apellidos"] ?? '';
    $documento_identidad = $_POST["documento_identidad"] ?? '';
    $contrasena = password_hash($_POST["contrasena"] ?? '', PASSWORD_DEFAULT); // Hashear la contraseña
    $domicilio = $_POST["domicilio"] ?? '';
    $tipo_residencia = $_POST["tipo_residencia"] ?? '';
    $entre_calle = $_POST["entre_calle"] ?? '';
    $y_calle = $_POST["y_calle"] ?? '';
    $datos_ubicacion = $_POST["datos_ubicacion"] ?? '';
    $barrio = $_POST["barrio"] ?? '';
    $fecha_caducidad_alquiler = $_POST["fecha_caducidad_alquiler"] ?? null;
    $edad_2025 = $_POST["edad_2025"] ?? null;
    $enfermedades_preexistentes = $_POST["enfermedades_preexistentes"] ?? '';
    $detalle_enfermedades = $_POST["detalle_enfermedades"] ?? '';
    $alergico_medicamentos = $_POST["alergico_medicamentos"] ?? '';
    $detalle_alergias = $_POST["detalle_alergias"] ?? '';
    $socio_emergencia = $_POST["socio_emergencia"] ?? '';
    $detalle_emergencia = $_POST["detalle_emergencia"] ?? ";
    $clave_intrusos = $_POST["clave_intrusos"] ?? ";
    $clave_salud = $_POST["clave_salud"] ?? ";
    $clave_incendio = $_POST["clave_incendio"] ?? ";
    $clave_violencia = $_POST["clave_violencia"] ?? ";
    $clave_contacto = $_POST["clave_contacto"] ?? ";
    $referido_2 = $_POST["referido_2"] ?? '';
    $aceptacion_terminos = isset($_POST["aceptacion_terminos"]) ? 1 : 0;

    // Preparar la consulta SQL
    $sql = "INSERT INTO usuarios (
                marca_temporal, nombre_usuario, telefono_whatsapp, telefono_whatsapp_2, nombres_apellidos,
                documento_identidad, contrasena, domicilio, tipo_residencia, entre_calle, y_calle,
                datos_ubicacion, barrio, fecha_caducidad_alquiler, edad_2025, enfermedades_preexistentes,
                detalle_enfermedades, alergico_medicamentos, detalle_alergias, socio_emergencia,
                detalle_emergencia, clave_intrusos, clave_salud, clave_incendio, clave_violencia,
                clave_contacto, referido_1, referido_2, aceptacion_terminos
            ) VALUES (
                :marca_temporal, :nombre_usuario, :telefono_whatsapp, :telefono_whatsapp_2, :nombres_apellidos,
                :documento_identidad, :contrasena, :domicilio, :tipo_residencia, :entre_calle, :y_calle,
                :datos_ubicacion, :barrio, :fecha_caducidad_alquiler, :edad_2025, :enfermedades_preexistentes,
                :detalle_enfermedades, :alergico_medicamentos, :detalle_alergias, :socio_emergencia,
                :detalle_emergencia, :clave_intrusos, :clave_salud, :clave_incendio, :clave_violencia,
                :clave_contacto, :referido_1, :referido_2, :aceptacion_terminos
            )";

    $stmt = $pdo->prepare($sql);

    // Bind de parámetros
    $stmt->bindParam(':marca_temporal', $marca_temporal);
    $stmt->bindParam(':nombre_usuario', $nombre_usuario);
    $stmt->bindParam(':telefono_whatsapp', $telefono_whatsapp);
    $stmt->bindParam(':telefono_whatsapp_2', $telefono_whatsapp_2);
    $stmt->bindParam(':nombres_apellidos', $nombres_apellidos);
    $stmt->bindParam(':documento_identidad', $documento_identidad);
    $stmt->bindParam(':contrasena', $contrasena);
    $stmt->bindParam(':domicilio', $domicilio);
    $stmt->bindParam(':tipo_residencia', $tipo_residencia);
    $stmt->bindParam(':entre_calle', $entre_calle);
    $stmt->bindParam(':y_calle', $y_calle);
    $stmt->bindParam(':datos_ubicacion', $datos_ubicacion);
    $stmt->bindParam(':barrio', $barrio);
    $stmt->bindParam(':fecha_caducidad_alquiler', $fecha_caducidad_alquiler);
    $stmt->bindParam(':edad_2025', $edad_2025);
    $stmt->bindParam(':enfermedades_preexistentes', $enfermedades_preexistentes);
    $stmt->bindParam(':detalle_enfermedades', $detalle_enfermedades);
    $stmt->bindParam(':alergico_medicamentos', $alergico_medicamentos);
    $stmt->bindParam(':detalle_alergias', $detalle_alergias);
    $stmt->bindParam(':socio_emergencia', $socio_emergencia);
    $stmt->bindParam(':detalle_emergencia', $detalle_emergencia);
    $stmt->bindParam(':clave_intrusos', $clave_intrusos);
    $stmt->bindParam(':clave_salud', $clave_salud);
    $stmt->bindParam(':clave_incendio', $clave_incendio);
    $stmt->bindParam(':clave_violencia', $clave_violencia);
    $stmt->bindParam(':clave_contacto', $clave_contacto);
    $stmt->bindParam(':referido_1', $referido_1);
    $stmt->bindParam(':referido_2', $referido_2);
    $stmt->bindParam(':aceptacion_terminos', $aceptacion_terminos);

    // Ejecutar la consulta
    try {
        $stmt->execute();
        echo "<p style=\"color: green;\">Datos de usuario cargados exitosamente.</p>";
    } catch (PDOException $e) {
        echo "<p style=\"color: red;\">Error al cargar datos de usuario: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style=\"color: orange;\">Acceso no permitido. Por favor, envía el formulario.</p>";
}

?>


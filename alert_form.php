<?php
session_start();
require_once __DIR__ . '/config.php';

// Verificar autenticación y tipo de alerta
$validTypes = ['seguridad', 'bomberos', 'salud', 'servicios'];
if (!isset($_SESSION['user']) || !isset($_GET['tipo']) || !in_array($_GET['tipo'], $validTypes)) {
    header('Location: access.php');
    exit;
}

$tipoAlerta = $_GET['tipo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta - SIRENA</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="img/logosirena.ico">
    <script>
        // Obtener ubicación GPS
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    showPosition, 
                    showError,
                    {enableHighAccuracy: true, timeout: 10000}
                );
            } else {
                document.getElementById('ubicacion').value = "Geolocalización no soportada";
            }
        }
        
        function showPosition(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            document.getElementById('ubicacion').value = `${lat}, ${lng}`;
            
            // Opcional: Mostrar mapa estático
            document.getElementById('mapa').innerHTML = 
                `<img src="https://maps.googleapis.com/maps/api/staticmap?center=${lat},${lng}&zoom=15&size=400x200&markers=color:red%7C${lat},${lng}&key=<?= GMAPS_API_KEY ?>" alt="Ubicación">`;
        }
        
        function showError(error) {
            let message = "";
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    message = "Usuario denegó la solicitud de geolocalización";
                    break;
                case error.POSITION_UNAVAILABLE:
                    message = "Información de ubicación no disponible";
                    break;
                case error.TIMEOUT:
                    message = "Tiempo de espera agotado";
                    break;
                case error.UNKNOWN_ERROR:
                    message = "Error desconocido";
                    break;
            }
            document.getElementById('ubicacion').value = message;
        }
        
        // Iniciar grabación de audio
        let mediaRecorder;
        let audioChunks = [];
        
        async function startRecording() {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            
            mediaRecorder.ondataavailable = event => {
                audioChunks.push(event.data);
            };
            
            mediaRecorder.onstop = () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/mp3' });
                const audioUrl = URL.createObjectURL(audioBlob);
                
                // Crear input file oculto con el audio
                const audioInput = document.createElement('input');
                audioInput.type = 'file';
                audioInput.name = 'audio';
                audioInput.style.display = 'none';
                
                // Convertir Blob a File
                const audioFile = new File([audioBlob], 'audio-alerta.mp3', { type: 'audio/mp3' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(audioFile);
                audioInput.files = dataTransfer.files;
                
                document.getElementById('audio-container').appendChild(audioInput);
                document.getElementById('audio-preview').src = audioUrl;
                document.getElementById('audio-status').textContent = "Audio grabado";
            };
            
            audioChunks = [];
            mediaRecorder.start();
            document.getElementById('audio-status').textContent = "Grabando...";
            document.getElementById('stop-recording').disabled = false;
        }
        
        function stopRecording() {
            mediaRecorder.stop();
            document.getElementById('stop-recording').disabled = true;
        }
        
        // Capturar foto desde la cámara
        async function capturePhoto() {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            const video = document.createElement('video');
            video.srcObject = stream;
            await video.play();
            
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            
            stream.getTracks().forEach(track => track.stop());
            
            canvas.toBlob(blob => {
                const photoUrl = URL.createObjectURL(blob);
                
                // Crear input file oculto con la foto
                const photoInput = document.createElement('input');
                photoInput.type = 'file';
                photoInput.name = 'foto';
                photoInput.style.display = 'none';
                
                // Convertir Blob a File
                const photoFile = new File([blob], 'foto-alerta.jpg', { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(photoFile);
                photoInput.files = dataTransfer.files;
                
                document.getElementById('photo-container').appendChild(photoInput);
                document.getElementById('photo-preview').src = photoUrl;
                document.getElementById('photo-status').textContent = "Foto capturada";
            }, 'image/jpeg', 0.9);
        }
    </script>
</head>
<body onload="getLocation()">
    <header>
        <img src="img/logosirena.jpeg" alt="Logo SIRENA">
        <h1>Alerta <?= strtoupper($tipoAlerta) ?></h1>
    </header>
    
    <div class="alert-container">
        <form action="send_alert.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="tipo" value="<?= $tipoAlerta ?>">
            
            <div class="form-group">
                <label>Usuario: <?= htmlspecialchars($_SESSION['user']['nombre']) ?></label>
            </div>
            
            <div class="form-group">
                <label for="ubicacion">Ubicación GPS:</label>
                <input type="text" id="ubicacion" name="ubicacion" required>
                <small>Verifique que la ubicación es correcta o ingrésela manualmente</small>
                <div id="mapa"></div>
            </div>
            
            <div class="form-group">
                <label for="mensaje">Mensaje:</label>
                <textarea id="mensaje" name="mensaje" rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Audio:</label>
                <button type="button" onclick="startRecording()">Grabar Audio</button>
                <button type="button" id="stop-recording" onclick="stopRecording()" disabled>Detener</button>
                <span id="audio-status">No grabado</span>
                <audio id="audio-preview" controls style="display: none;"></audio>
                <div id="audio-container"></div>
            </div>
            
            <div class="form-group">
                <label>Foto:</label>
                <button type="button" onclick="capturePhoto()">Tomar Foto</button>
                <span id="photo-status">No capturada</span>
                <img id="photo-preview" style="display: none; max-width: 200px;">
                <div id="photo-container"></div>
            </div>
            
            <div class="form-group">
                <label for="video">Video (opcional):</label>
                <input type="file" id="video" name="video" accept="video/*">
            </div>
            
            <button type="submit" class="btn btn-send">Enviar Alerta</button>
        </form>
    </div>
</body>
</html>
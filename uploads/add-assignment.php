<?php
session_start();
include("config/db.php");
include("include/utils.php");
//Establecer la zona horaria
date_default_timezone_set('America/Guayaquil');
// Obtener la fecha y hora actual
$current_date = date("Y-m-d"); // Fecha actual en formato YYYY-MM-DD
$current_time = date("H:i:s");  // Hora actual en formato HH:MM:SS

// Imprimir la fecha y hora
echo "Fecha y hora actual: $current_date $current_time <br>";

// Verificar si el usuario está logueado
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'Teacher') {
    header('Location: login.php');
    exit();
}

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $due_time = $_POST['due_time'];  // Hora de entrega

    // Convertir las fechas a formato de fecha en PHP para la comparación
    $current_date = date("Y-m-d"); // Fecha actual en formato YYYY-MM-DD
    $due_date_php = date("Y-m-d", strtotime($due_date)); // Aseguramos que la fecha ingresada tenga el mismo formato
    $current_time = date("H:i");  // Hora actual en formato HH:MM
    $due_datetime = $due_date_php . ' ' . $due_time; // Concatenar la fecha y la hora

    // Depuración: Verifica cómo se ven los datos combinados
    echo "Fecha: $due_date, Hora: $due_time, Fecha y hora combinada: $due_datetime <br>";

    // Validar si la fecha y hora son posteriores a la actual
    if ($due_datetime < $current_date . ' ' . $current_time) {
        $error_message = "No puedes asignar una tarea con una fecha y hora anterior a la actual.";
    } else {
        // Manejo del archivo subido
        $file_path = "";  // Valor vacío por defecto

        if (isset($_FILES['file_path']) && $_FILES['file_path']['error'] === UPLOAD_ERR_OK) {
            // Especificar la carpeta de destino donde se guardarán los archivos
            $upload_dir = 'uploads/';  // Asegúrate de tener esta carpeta en tu servidor
            $file_name = basename($_FILES['file_path']['name']);
            $file_path = $upload_dir . $file_name;

            // Mover el archivo desde el directorio temporal a la carpeta de destino
            if (!move_uploaded_file($_FILES['file_path']['tmp_name'], $file_path)) {
                $error_message = "Error al subir el archivo.";
            }
        }

        // Si la fecha y hora son válidas, procesamos la tarea
        $teacher_id = $_SESSION['teacher_id']; // Asegúrate de tener este ID disponible en sesión
        $sql = "INSERT INTO assignments (teacher_id, title, description, due_date, file_path, due_datetime) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        // Preparar la consulta
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo "Error en la preparación de la consulta: " . $conn->error;
            exit();
        }

        // Vincular los parámetros
        $stmt->bind_param("ssssss", $teacher_id, $title, $description, $due_date, $file_path, $due_datetime);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Redirigir al listado de asignaciones o mostrar mensaje de éxito
            header('Location: view-assignment.php');
            exit();
        } else {
            echo "Error al insertar la asignación: " . $stmt->error;
        }
    }
    // Obtener la hora actual
    $current_time = date("H:i:s");  // Hora actual en formato HH:MM:SS

    // Imprimir la hora
    echo "Hora de ejecución: $current_time <br>";

    // Convertir las fechas a formato de fecha en PHP para la comparación
    $current_date = date("Y-m-d"); // Fecha actual en formato YYYY-MM-DD
    $due_date_php = date("Y-m-d", strtotime($due_date)); // Aseguramos que la fecha ingresada tenga el mismo formato
    $due_datetime = $due_date_php . ' ' . $due_time; // Concatenar la fecha y la hora

    // Depuración: Verifica cómo se ven los datos combinados
    echo "Fecha: $due_date, Hora: $due_time, Fecha y hora combinada: $due_datetime <br>";

}

include("include/header.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Nueva Tarea</title>
    <style>
        /* Estilo para el contenedor del formulario */
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        .container h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        /* Estilo específico para el campo de archivo */
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        /* Input de archivo oculto */
        .file-input-wrapper input[type="file"] {
            font-size: 16px;
            position: absolute;
            opacity: 0;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            cursor: pointer;
        }
        /* Botón personalizado de seleccionar archivo */
        .file-input-button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;  /* Aseguramos que el tamaño sea más pequeño */
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            width: 100%; /* Ajustar al tamaño de la pantalla */
        }
        .file-input-button:hover {
            background-color: #0056b3;
        }
        .file-name {
            margin-top: 10px;
            font-size: 14px;
            color: #555;
        }
        /* Estilo para el botón principal */
        .btn-primary {
            background-color: #28a745;  /* Verde */
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-block;
            text-align: center;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Añadir Nueva Tarea</h1>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Título de la tarea:</label>
            <input type="text" id="title" name="title" required class="form-control">
        </div>
        <div class="form-group">
            <label for="description">Descripción:</label>
            <textarea id="description" name="description" required class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="due_date">Fecha de entrega:</label>
            <input type="date" id="due_date" name="due_date" required class="form-control">
        </div>
        <div class="form-group">
            <label for="due_time">Hora de entrega:</label>
            <input type="time" id="due_time" name="due_time" required class="form-control">
        </div>
        <div class="form-group">
            <label for="file_path">Archivo adjunto (opcional):</label>
            <div class="file-input-wrapper">
                <input type="file" id="file_path" name="file_path" onchange="updateFileName()">
                <button type="button" class="file-input-button">Seleccionar archivo</button>
            </div>
            <p id="file-name" class="file-name"></p>
        </div>
        <button type="submit" class="btn-primary">Añadir tarea</button>
    </form>
</div>

<script>
    function updateFileName() {
        const fileInput = document.getElementById("file_path");
        const fileNameDisplay = document.getElementById("file-name");
        if (fileInput.files.length > 0) {
            fileNameDisplay.textContent = "Archivo seleccionado: " + fileInput.files[0].name;
        } else {
            fileNameDisplay.textContent = "";
        }
    }
</script>

</body>
</html>
<?php include("include/footer.php"); ?>


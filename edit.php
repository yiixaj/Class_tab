<?php 
session_start();
include("config/db.php");
// Establecer la zona horaria de tu ubicación
date_default_timezone_set('America/Guayaquil');  // Cambia esta zona horaria según tu ubicación


// Verificar si el usuario está logueado y es profesor
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'Teacher') {
    header('Location: login.php');
    exit();
}

// Verificar que el ID existe en la URL
if (!isset($_GET['id'])) {
    header('Location: view-assignment.php');
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Recuperar los datos actuales de la tarea
$sql = "SELECT * FROM assignments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $title = $row['title'];
    $duedate = $row['due_date']; // La fecha sin hora
    $due_time = date('H:i', strtotime($row['due_datetime'])); // Extraemos la hora de due_datetime
    $description = $row['description'];
    $file_path = $row['file_path'];
} else {
    header('Location: view-assignment.php');
    exit();
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $duedate = mysqli_real_escape_string($conn, $_POST['duedate']);
    $duetime = mysqli_real_escape_string($conn, $_POST['duetime']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $delete_file = isset($_POST['delete_file']); // Verificar si se marcó eliminar archivo

    // Combina la fecha y la hora para el campo due_datetime
    $due_datetime = $duedate . ' ' . $duetime;

    // Manejo del archivo subido
    $new_file_path = $file_path; // Mantener el archivo existente si no se sube uno nuevo
    if (isset($_FILES['file_path']) && $_FILES['file_path']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $file_name = basename($_FILES['file_path']['name']);
        $new_file_path = $upload_dir . $file_name;

        if (!move_uploaded_file($_FILES['file_path']['tmp_name'], $new_file_path)) {
            $error = "Error al subir el archivo.";
        } else {
            // Eliminar el archivo anterior si se sube uno nuevo
            if (!empty($file_path) && file_exists($file_path)) {
                unlink($file_path);
            }
        }
    } elseif ($delete_file) {
        // Si el usuario marcó eliminar el archivo
        if (!empty($file_path) && file_exists($file_path)) {
            unlink($file_path); // Eliminar el archivo del servidor
        }
        $new_file_path = null; // Establecer el campo file_path como NULL en la base de datos
    }

    // Validar campos y actualizar en la base de datos
    if ($title !== '' && $duedate !== '' && $duetime !== '' && $description !== '') {
        $update_sql = "UPDATE assignments SET title = ?, due_date = ?, due_datetime = ?, description = ?, file_path = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssi", $title, $duedate, $due_datetime, $description, $new_file_path, $id);
        
        if ($stmt->execute()) {
            header('Location: view-assignment.php');
            exit();
        } else {
            $error = 'Error al actualizar la tarea: ' . $conn->error;
        }
    } else {
        $error = 'Por favor, completa todos los campos.';
    }
}

include("include/header.php");
?>

<style>
    .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        border-radius: 10px;
        background-color: #f9f9f9;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        font-weight: bold;
        color: #555;
    }

    input[type="text"], input[type="date"], input[type="time"], textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    input[type="file"] {
        display: none;
    }

    .custom-file-upload {
        display: inline-block;
        padding: 10px 15px;
        color: #fff;
        background-color: #007bff;
        border: 1px solid #007bff;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        text-align: center;
        transition: background-color 0.3s ease;
    }

    .custom-file-upload:hover {
        background-color: #0056b3;
    }

    .file-name {
        margin-left: 10px;
        font-size: 14px;
        color: #555;
        font-style: italic;
    }

    button {
        width: 100%;
        padding: 10px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #218838;
    }

    .alert {
        margin-bottom: 20px;
        padding: 10px;
        border-radius: 5px;
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
    }

    a {
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    input[type="checkbox"] {
        margin-right: 5px;
    }
</style>

<div class="container">
    <h1>Editar Tarea</h1>
    <?php if (isset($error)): ?>
        <div class="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form action="edit.php?id=<?php echo urlencode($id); ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
        <div class="form-group">
            <label for="title">Título de la tarea:</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" required>
        </div>
        <div class="form-group">
            <label for="duedate">Fecha de entrega:</label>
            <input type="date" name="duedate" id="duedate" value="<?php echo htmlspecialchars($duedate); ?>" required>
        </div>
        <div class="form-group">
            <label for="duetime">Hora de entrega:</label>
            <input type="time" name="duetime" id="duetime" value="<?php echo htmlspecialchars($due_time); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Descripción:</label>
            <textarea name="description" id="description" required><?php echo htmlspecialchars($description); ?></textarea>
        </div>
        <div class="form-group">
            <label>Archivo actual:</label>
            <?php if (!empty($file_path)): ?>
                <p><a href="<?php echo htmlspecialchars($file_path); ?>" target="_blank">Ver archivo subido</a></p>
                <input type="checkbox" name="delete_file" value="1"> Eliminar archivo existente
            <?php else: ?>
                <p>No hay archivo subido.</p>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="file_path">Subir nuevo archivo (opcional):</label>
            <label class="custom-file-upload">
                <input type="file" name="file_path" id="file_path">
                Seleccionar archivo
            </label>
            <span id="file-name" class="file-name">No se ha seleccionado ningún archivo</span>
        </div>
        <button type="submit">Actualizar Tarea</button>
    </form>
</div>

<script>
    // Script para mostrar el nombre del archivo seleccionado
    document.getElementById('file_path').addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : "No se ha seleccionado ningún archivo";
        document.getElementById('file-name').textContent = fileName;
    });
</script>

<?php include("include/footer.php"); ?>

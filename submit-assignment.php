<?php
session_start();
include("config/db.php");

// Verificar si el usuario está logueado y es estudiante
if (!isset($_SESSION['id']) || $_SESSION['user_role'] !== 'Student') {
    header("Location: login.php");
    exit();
}

// Verificar que se haya proporcionado el ID de la asignación
if (!isset($_GET['assignment_id'])) {
    header("Location: student_dashboard.php?error=missing_assignment_id");
    exit();
}

$assignment_id = intval($_GET['assignment_id']);
$user_id = $_SESSION['id']; // ID del usuario de la tabla 'users'

// Verificar si el usuario está registrado como estudiante
$sql = "SELECT id FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Estudiante no encontrado en la base de datos.");
}

// Obtener el ID del estudiante desde la tabla 'students'
$student = $result->fetch_assoc();
$student_id = $student['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);
    
    // Configuración de subida de archivos
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/submissions/";
    
    // Crear directorio si no existe
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filePath = null;
    $fileName = null;

    // Manejo de subida de archivos
    if (!empty($_FILES['submission_file']['name'])) {
        $fileName = $_FILES['submission_file']['name'];
        $fileTmpName = $_FILES['submission_file']['tmp_name'];
        
        // Generar nombre de archivo único
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $uniqueFileName = uniqid() . '_' . $student_id . '_' . $assignment_id . '.' . $fileExt;
        $filePath = $uploadDir . $uniqueFileName;

        // Validaciones de archivo
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        $allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];

        if ($_FILES['submission_file']['size'] > $maxFileSize) {
            $error = "El archivo es demasiado grande. Máximo 5 MB.";
        } elseif (!in_array($fileExt, $allowedExtensions)) {
            $error = "Tipo de archivo no permitido. Solo se permiten: " . implode(', ', $allowedExtensions);
        } else {
            // Intentar mover el archivo
            if (!move_uploaded_file($fileTmpName, $filePath)) {
                $error = "Error al subir el archivo.";
            } else {
                // Usar ruta relativa para guardar en base de datos
                $filePath = "/uploads/submissions/" . $uniqueFileName;
            }
        }
    }

    // Validar contenido
    if (empty($content) && empty($filePath)) {
        $error = "Debes escribir tu respuesta o subir un archivo.";
    }

    // Si no hay errores, guardar en base de datos
    if (!isset($error)) {
        // Insertar la entrega en la base de datos
        $sql = "INSERT INTO submissions (student_id, assignment_id, submission_date, content, file_path) 
                VALUES (?, ?, NOW(), ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $student_id, $assignment_id, $content, $filePath);

        if ($stmt->execute()) {
            header("Location: student_dashboard.php?success=submitted");
            exit();
        } else {
            $error = "Error al enviar la tarea. Inténtalo de nuevo.";
        }
    }
}

// Obtener información de la tarea
$sql = "SELECT * FROM assignments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$result = $stmt->get_result();
$assignment = $result->fetch_assoc();

if (!$assignment) {
    header("Location: student_dashboard.php?error=invalid_assignment");
    exit();
}

// Obtener la entrega del estudiante (si existe)
$sql = "SELECT * FROM submissions WHERE student_id = ? AND assignment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $student_id, $assignment_id);
$stmt->execute();
$submissionResult = $stmt->get_result();
$submission = $submissionResult->fetch_assoc();
?>

<?php include("include/header.php"); ?>

<div class="container">
    <h1>Resolver tarea: <?php echo htmlspecialchars($assignment['title']); ?></h1>
    <p><strong>Fecha límite:</strong> <?php echo htmlspecialchars($assignment['due_datetime']); ?></p>
    <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>

    <?php if (!empty($assignment['file_path'])): ?>
        <p><strong>Archivo de la tarea:</strong> 
            <a href="<?php echo htmlspecialchars($assignment['file_path']); ?>" download>Descargar archivo</a>
        </p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($submission): ?>
        <h2>Tu entrega</h2>
        <p><strong>Fecha de envío:</strong> <?php echo htmlspecialchars($submission['submission_date']); ?></p>
        <p><strong>Respuesta:</strong> <?php echo nl2br(htmlspecialchars($submission['content'])); ?></p>
        
        <?php if (!empty($submission['file_path'])): ?>
            <p><strong>Archivo enviado:</strong> 
                <a href="download.php?file=<?php echo urlencode($submission['file_path']); ?>" download>Descargar archivo</a>
            </p>
        <?php endif; ?>
    <?php else: ?>
        <h2>Realiza tu entrega</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="content">Respuesta:</label>
                <textarea name="content" id="content" class="form-control" rows="10" placeholder="Escribe tu respuesta aquí..."></textarea>
            </div>
            <div class="form-group">
                <label for="submission_file">Subir archivo:</label>
                <input type="file" name="submission_file" id="submission_file" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary">Enviar tarea</button>
        </form>
    <?php endif; ?>
</div>

<?php include("include/footer.php"); ?>
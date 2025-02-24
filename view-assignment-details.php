<?php
session_start();
include("config/db.php");
include("include/utils.php");
// Verificar si el usuario está logueado
if (!isset($_SESSION['username']) || !isset($_SESSION['user_role']) || !isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['id'];

// Verificar si se recibió el ID de la tarea
if (!isset($_GET['assignment_id'])) {
    die('ID de la tarea no proporcionado.');
}
$assignment_id = $_GET['assignment_id'];

// Primero obtener el student_id correspondiente al user_id
$stmt = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student_result = $stmt->get_result();
$student = $student_result->fetch_assoc();
$student_id = $student ? $student['id'] : null;

// Obtener los detalles de la tarea y la entrega (si existe)
$stmt = $conn->prepare("
    SELECT 
        a.title, 
        a.description, 
        a.due_datetime,  /* Cambié 'due_date' a 'due_datetime' */
        a.file_path AS assignment_file, 
        COALESCE(s.content, '') AS submission, 
        COALESCE(s.file_path, '') AS submission_file,
        COALESCE(s.grade, NULL) AS grade,
        COALESCE(s.feedback, '') AS feedback
    FROM assignments a
    LEFT JOIN submissions s 
        ON s.assignment_id = a.id AND s.student_id = ? 
    WHERE a.id = ?
");

$stmt->bind_param("ii", $student_id, $assignment_id);
$stmt->execute();
$result = $stmt->get_result();
$assignment = $result->fetch_assoc();

// Verificar si la tarea existe
if (!$assignment) {
    echo "La tarea no existe.";
    exit();
}

include("include/header.php");
?>

<div class="container">
    <h1>Detalles de la Tarea</h1>
    <h3>Información de la Tarea:</h3>
    <p><strong>Título:</strong> <?php echo htmlspecialchars($assignment['title']); ?></p>
    
    <div class="assignment-description">
        <p><strong>Descripción:</strong></p>
        <div class="formatted-content">
            <?php echo formatContent($assignment['description']); ?>
        </div>
    </div>
    
    <p><strong>Fecha Límite:</strong> <?php echo date("Y-m-d H:i:s", strtotime($assignment['due_datetime'])); ?></p>  <!-- Aquí mostramos la fecha y hora límite -->
    
    <?php if (!empty($assignment['assignment_file'])): ?>
        <p><a href="<?php echo htmlspecialchars($assignment['assignment_file']); ?>" download>Descargar Archivo de la Tarea</a></p>
    <?php endif; ?>

    <hr>

    <h3>Tu Entrega:</h3>
    <?php if (!empty($assignment['submission']) || !empty($assignment['submission_file'])): ?>
        <div class="submission-content">
            <p><strong>Contenido:</strong></p>
            <div class="formatted-content">
                <?php echo formatContent($assignment['submission']); ?>
            </div>
        </div>
        <?php if (!empty($assignment['submission_file'])): ?>
            <p><a href="<?php echo htmlspecialchars($assignment['submission_file']); ?>" download>Descargar Archivo Enviado</a></p>
        <?php endif; ?>
    <?php else: ?>
        <p>No has entregado esta tarea.</p>
    <?php endif; ?>

    <hr>

    <h3>Calificación:</h3>
    <?php if (!is_null($assignment['grade'])): ?>
        <p><strong>Nota:</strong> <?php echo htmlspecialchars($assignment['grade']); ?>/10</p>
        <div class="feedback-content">
            <p><strong>Comentarios del Maestro:</strong></p>
            <div class="formatted-content">
                <?php echo formatContent($assignment['feedback']); ?>
            </div>
        </div>
    <?php else: ?>
        <p>Aún no se ha calificado esta tarea.</p>
    <?php endif; ?>
</div>


<style>
.formatted-content {
    margin: 10px 0;
    line-height: 1.6;
}

.formatted-content table {
    border-collapse: collapse;
    width: 100%;
    margin: 10px 0;
}

.formatted-content td, 
.formatted-content th {
    border: 1px solid #ddd;
    padding: 8px;
}

.formatted-content ul,
.formatted-content ol {
    margin-left: 20px;
}

.formatted-content p {
    margin: 8px 0;
}
</style>

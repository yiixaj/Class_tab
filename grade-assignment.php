<?php
require_once 'config/db.php';
session_start();

// Validar sesión y rol
if (!isset($_SESSION['id']) || $_SESSION['user_role'] !== 'Teacher') {
    header('Location: teacher_dashboard.php');
    exit();
}

// Validar ID de la entrega
$submission_id = filter_input(INPUT_GET, 'submission_id', FILTER_VALIDATE_INT);
if (!$submission_id) {
    die("ID de entrega inválido.");
}

// Obtener detalles de la entrega y calificación
$stmt = $conn->prepare("
    SELECT s.id, s.assignment_id, s.student_id, s.submission_date, s.content, s.file_path, s.grade, s.feedback, 
           a.title, u.username AS student_name, g.grade AS grade_value, g.comments
    FROM submissions s
    JOIN assignments a ON s.assignment_id = a.id
    JOIN students st ON s.student_id = st.id
    JOIN users u ON st.user_id = u.id
    LEFT JOIN grades g ON g.submission_id = s.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $submission_id);
$stmt->execute();
$submission = $stmt->get_result()->fetch_assoc();

if (!$submission) {
    die("No se encontró la entrega.");
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade = filter_input(INPUT_POST, 'grade', FILTER_VALIDATE_INT);
    $comments = filter_input(INPUT_POST, 'comments', FILTER_SANITIZE_STRING);

    // Validar la calificación
    if ($grade !== false && $grade >= 0 && $grade <= 10) {
        // Iniciar transacción para asegurar integridad de datos
        $conn->begin_transaction();

        try {
            // Verificar si ya existe una calificación
            $stmt = $conn->prepare("SELECT id FROM grades WHERE submission_id = ?");
            $stmt->bind_param("i", $submission_id);
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();

            if ($existing) {
                // Actualizar calificación existente
                $stmt = $conn->prepare("UPDATE grades SET grade = ?, comments = ? WHERE submission_id = ?");
                $stmt->bind_param("isi", $grade, $comments, $submission_id);
            } else {
                // Insertar nueva calificación
                $stmt = $conn->prepare("INSERT INTO grades (submission_id, grade, comments) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $submission_id, $grade, $comments);
            }

            // Ejecutar la actualización de la calificación
            $stmt->execute();

            // Actualizar la tabla submissions
            $update_submission = $conn->prepare("UPDATE submissions SET grade = ?, feedback = ? WHERE id = ?");
            $update_submission->bind_param("isi", $grade, $comments, $submission_id);
            $update_submission->execute();

            // Confirmar la transacción
            $conn->commit();

            // Redirigir al usuario después de guardar
            header("Location: view-submissions.php?assignment_id=" . $submission['assignment_id']);
            exit();

        } catch (Exception $e) {
            // Si hay un error, deshacer los cambios
            $conn->rollback();
            $error = "Error al guardar la calificación. Inténtelo nuevamente.";
        }
    } else {
        $error = "La calificación debe ser un número entre 0 y 10.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Calificar Tarea</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php include 'include/header.php'; ?>

    <div class="container">
        <h2>Calificar Tarea: <?php echo htmlspecialchars($submission['title']); ?></h2>

        <div class="submission-details">
            <p><strong>Estudiante:</strong> <?php echo htmlspecialchars($submission['student_name']); ?></p>
            <p><strong>Fecha de entrega:</strong> <?php echo htmlspecialchars($submission['submission_date']); ?></p>

            <div class="submission-content">
                <h3>Contenido de la entrega:</h3>
                <p><?php echo htmlspecialchars($submission['content']); ?></p>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="grading-form">
            <div class="form-group">
                <label for="grade">Calificación (0-10):</label>
                <input type="number" name="grade" id="grade" min="0" max="10" required 
                       value="<?php echo isset($submission['grade_value']) ? htmlspecialchars($submission['grade_value']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="comments">Comentarios:</label>
                <textarea name="comments" id="comments" rows="4"><?php echo isset($submission['comments']) ? htmlspecialchars($submission['comments']) : ''; ?></textarea>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-primary">Guardar Calificación</button>
            </div>
        </form>
    </div>
</body>
</html>

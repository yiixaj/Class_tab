<?php
session_start();
include("config/db.php");
include("include/utils.php");

// Establecer la zona horaria de tu ubicación
date_default_timezone_set('America/Guayaquil');

// Verificar autenticación
if (!isset($_SESSION['id']) || $_SESSION['user_role'] !== 'Teacher') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['assignment_id'])) {
    header("Location: teacher_dashboard.php?error=missing_assignment_id");
    exit();
}

// Función para verificar el estado de la entrega
function checkSubmissionStatus($submission_date, $due_date) {
    $submission_time = strtotime($submission_date);
    $due_time = strtotime($due_date);

    if ($submission_time <= $due_time) {
        return [
            'status' => 'A tiempo',
            'class' => 'bg-success'
        ];
    } else {
        return [
            'status' => 'Atrasado',
            'class' => 'bg-danger'
        ];
    }
}

$assignment_id = intval($_GET['assignment_id']);

// Obtener detalles de la tarea
$sql = "SELECT * FROM assignments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$result = $stmt->get_result();
$assignment = $result->fetch_assoc();

if (!$assignment) {
    header("Location: teacher_dashboard.php?error=invalid_assignment");
    exit();
}

// Procesar la calificación mediante AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id']) && isset($_POST['grade'])) {
    $submission_id = intval($_POST['submission_id']);
    $grade = floatval($_POST['grade']);
    $feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';

    $sql = "UPDATE submissions SET grade = ?, feedback = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dsi", $grade, $feedback, $submission_id);
    $response = [];

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Calificación guardada correctamente.";
    } else {
        $response['success'] = false;
        $response['message'] = "Error al guardar la calificación.";
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Obtener entregas de estudiantes
$sql = "
    SELECT submissions.*, users.username AS student_name, users.email AS student_email, students.id AS student_id
    FROM submissions
    INNER JOIN students ON submissions.student_id = students.id
    INNER JOIN users ON students.user_id = users.id
    WHERE submissions.assignment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$submissions = $stmt->get_result();

include("include/header.php");
?>

<!-- Estilos y scripts -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="container-fluid px-4 py-5">
    <h1 class="text-center text-primary mb-4">Entregas de la Tarea: <?= htmlspecialchars($assignment['title']) ?></h1>

    <div class="alert alert-primary text-center">
        <i class="fas fa-clock me-2"></i>
        <strong>Fecha y Hora Actual:</strong> <?= date("d-m-Y H:i:s") ?>
    </div>

    <div class="card border-0 shadow-lg mb-5">
        <div class="card-header bg-primary text-white py-3">
            <h3 class="card-title">Detalles de la Tarea</h3>
        </div>
        <div class="card-body">
            <p><?= htmlspecialchars($assignment['description']) ?></p>
            <div class="alert alert-info">
                <strong>Fecha Límite:</strong> <?= date("d-m-Y H:i", strtotime($assignment['due_datetime'])) ?>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-lg">
        <div class="card-header bg-primary text-white py-3">
            <h3 class="card-title">Entregas de los Estudiantes</h3>
        </div>
        <div class="card-body">
            <?php if ($submissions && $submissions->num_rows > 0): ?>
                <table class="table table-striped table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Estudiante</th>
                            <th>Fecha de Entrega</th>
                            <th>Contenido</th>
                            <th>Archivo</th>
                            <th>Calificación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($submission = $submissions->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($submission['student_name']) ?></td>
                                <td>
                                    <?= htmlspecialchars($submission['submission_date']) ?><br>
                                    <span class="badge <?= checkSubmissionStatus($submission['submission_date'], $assignment['due_datetime'])['class'] ?>">
                                        <?= checkSubmissionStatus($submission['submission_date'], $assignment['due_datetime'])['status'] ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($submission['content']) ?></td>
                                <td>
                                    <?php if (!empty($submission['file_path'])): ?>
                                        <a href="<?= htmlspecialchars($submission['file_path']) ?>" download class="btn btn-primary btn-sm">
                                            <i class="fas fa-download"></i> Descargar
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= !is_null($submission['grade']) ? 'bg-success' : 'bg-warning' ?>">
                                        <?= !is_null($submission['grade']) ? htmlspecialchars($submission['grade']) . '/10' : 'Sin calificar' ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#gradeModal-<?= $submission['id'] ?>">
                                        <i class="fas fa-edit"></i> Calificar
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div class="modal fade" id="gradeModal-<?= $submission['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Calificar a <?= htmlspecialchars($submission['student_name']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="gradeForm-<?= $submission['id'] ?>">
                                                <div class="mb-3">
                                                    <label for="grade-<?= $submission['id'] ?>" class="form-label">Calificación (0-10)</label>
                                                    <input type="number" id="grade-<?= $submission['id'] ?>" class="form-control" min="0" max="10" value="<?= $submission['grade'] ?? '' ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="feedback-<?= $submission['id'] ?>" class="form-label">Comentarios</label>
                                                    <textarea id="feedback-<?= $submission['id'] ?>" class="form-control"><?= htmlspecialchars($submission['feedback'] ?? '') ?></textarea>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="button" class="btn btn-primary" onclick="submitGrade(<?= $submission['id'] ?>)">Guardar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">No hay entregas aún.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function submitGrade(submission_id) {
    const grade = document.getElementById(`grade-${submission_id}`).value;
    const feedback = document.getElementById(`feedback-${submission_id}`).value;

    fetch("", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ submission_id, grade, feedback })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    });
}
</script>
<?php include("include/footer.php"); ?>

<?php 
session_start();
include("config/db.php");
include("include/header.php");

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

// Obtener el ID del usuario y su rol
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Obtener el ID de la tarea de forma segura
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    if ($role === 'Teacher') {
        header('Location: teacher-dashboard.php');
    } else {
        header('Location: student-dashboard.php');
    }
    exit();
}

// Redirigir según el rol si no hay permisos
if ($role === 'Teacher') {
    // Verificar si la tarea pertenece al profesor
    $stmt = $conn->prepare("SELECT * FROM assignments WHERE id = ? AND teacher_id = (SELECT id FROM teachers WHERE user_id = ?)");
    $stmt->bind_param("ii", $id, $user_id);
} else {
    // Para estudiantes, simplemente verificar si la tarea existe
    $stmt = $conn->prepare("SELECT * FROM assignments WHERE id = ?");
    $stmt->bind_param("i", $id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $assignment = $result->fetch_assoc();
    
    // Si es estudiante, obtener su ID de estudiante y verificar si ya envió la tarea
    $submission = null;
    if ($role === 'Student') {
        $stmt = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();
        
        if ($student) {
            $stmt = $conn->prepare("
                SELECT s.*, g.grade, g.comments 
                FROM submissions s 
                LEFT JOIN grades g ON s.id = g.submission_id 
                WHERE s.student_id = ? AND s.assignment_id = ?
            ");
            $stmt->bind_param("ii", $student['id'], $id);
            $stmt->execute();
            $submission = $stmt->get_result()->fetch_assoc();
        }
    }
    // Si es profesor, obtener todas las entregas
    elseif ($role === 'Teacher') {
        $stmt = $conn->prepare("
            SELECT s.*, u.username as student_name, g.grade, g.comments 
            FROM submissions s
            JOIN students st ON s.student_id = st.id
            JOIN users u ON st.user_id = u.id
            LEFT JOIN grades g ON s.id = g.submission_id
            WHERE s.assignment_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $submissions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
?>
    <div class="container">
        <div class="assignment-card" id="data">
            <h2><?php echo htmlspecialchars($assignment['title']); ?></h2>
            <h4>Fecha límite: <?php echo htmlspecialchars($assignment['due_date']); ?></h4>
            <hr>
            <div><?php echo $assignment['description']; ?></div>
        </div>

        <?php if ($role === 'Student'): ?>
            <div class="submission-section">
                <?php if ($submission): ?>
                    <div class="submission-status">
                        <h3>Tu entrega</h3>
                        <p>Fecha de entrega: <?php echo htmlspecialchars($submission['submission_date']); ?></p>
                        
                        <?php if (isset($submission['grade'])): ?>
                            <div class="grade-info">
                                <h4>Calificación: <?php echo htmlspecialchars($submission['grade']); ?>/100</h4>
                                <?php if ($submission['comments']): ?>
                                    <p><strong>Comentarios del profesor:</strong></p>
                                    <p><?php echo htmlspecialchars($submission['comments']); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="submission-content">
                            <h4>Tu respuesta:</h4>
                            <?php echo $submission['content']; ?>
                        </div>
                        
                        <form action="submit-assignment.php" method="GET">
                            <input type="hidden" name="assignment_id" value="<?php echo $id; ?>">
                            <button type="submit" class="btn-primary">Editar respuesta</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="submit-prompt">
                        <p>Aún no has enviado tu respuesta.</p>
                        <form action="submit-assignment.php" method="GET">
                            <input type="hidden" name="assignment_id" value="<?php echo $id; ?>">
                            <button type="submit" class="btn-primary">Responder tarea</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($role === 'Teacher'): ?>
            <div class="submissions-section">
                <h3>Entregas de estudiantes</h3>
                <?php if (!empty($submissions)): ?>
                    <div class="submissions-list">
                        <?php foreach ($submissions as $submission): ?>
                            <div class="submission-item">
                                <h4>Estudiante: <?php echo htmlspecialchars($submission['student_name']); ?></h4>
                                <p>Fecha de entrega: <?php echo htmlspecialchars($submission['submission_date']); ?></p>
                                
                                <?php if (isset($submission['grade'])): ?>
                                    <p>Calificación: <?php echo htmlspecialchars($submission['grade']); ?>/100</p>
                                <?php else: ?>
                                    <p>Sin calificar</p>
                                <?php endif; ?>
                                
                                <div class="submission-actions">
                                    <form action="grade-assignment.php" method="GET" style="display: inline;">
                                        <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                                        <input type="hidden" name="assignment_id" value="<?php echo $id; ?>">
                                        <button type="submit" class="btn-primary">
                                            <?php echo isset($submission['grade']) ? 'Editar calificación' : 'Calificar'; ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Aún no hay entregas para esta tarea.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?php 
} else {
    if ($role === 'Teacher') {
        header('Location: teacher-dashboard.php');
    } else {
        header('Location: student-dashboard.php');
    }
    exit();
}

include("include/footer.php");
?>
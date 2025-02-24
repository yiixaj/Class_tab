<?php 
session_start();
include("config/db.php"); // Incluir la configuración de la base de datos

// Verificar si el usuario está logueado y es un estudiante
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'Student') {
    header('Location: login.php'); // Redirigir al login si no está logueado o no es estudiante
    exit();
}

// Obtener el ID del estudiante desde la sesión
if (!isset($_SESSION['student_id'])) {
    echo "Error: No se pudo verificar la identidad del estudiante. Por favor, vuelve a iniciar sesión.";
    exit();
}
$student_id = $_SESSION['student_id'];

// Obtener los IDs de los maestros asociados al estudiante
$teachers_query = "SELECT teacher_id FROM teacher_students WHERE student_id = $student_id";
$teachers_result = $conn->query($teachers_query);

if ($teachers_result && $teachers_result->num_rows > 0) {
    $teacher_ids = [];
    while ($row = $teachers_result->fetch_assoc()) {
        $teacher_ids[] = $row['teacher_id'];
    }

    // Convertir los IDs a un formato para la consulta SQL (e.g., "1,2,3")
    $teacher_ids_str = implode(',', $teacher_ids);

    // Obtener las asignaturas de los maestros asociados
    $assignments_query = "SELECT * FROM assignments WHERE teacher_id IN ($teacher_ids_str)";
    $assignments_result = $conn->query($assignments_query);
} else {
    $assignments_result = null; // No hay maestros asociados
}

include("include/header.php");
?>

<div class="container" id="data">
    <div class="user">
        <h1>Asignaturas disponibles</h1>

        <?php if ($assignments_result && $assignments_result->num_rows > 0): ?>
            <ul>
                <?php while ($assignment = $assignments_result->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($assignment['title']); ?></strong><br>
                        <?php echo htmlspecialchars($assignment['description']); ?><br>
                        <span><em>Fecha de entrega: <?php echo htmlspecialchars($assignment['due_date']); ?></em></span>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No tienes asignaturas disponibles. Asegúrate de unirte a una clase con el código de un maestro.</p>
        <?php endif; ?>
    </div>
</div>

<?php 
include("include/footer.php");
?>

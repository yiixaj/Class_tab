<?php 
session_start();
include("config/db.php");
require_once 'config.php';
//Establecer la zona horaria
date_default_timezone_set('America/Guayaquil');

// Verificar si el usuario está logueado y es un estudiante
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'Student') {
    header('Location: login.php');
    exit();
}

// Verificar si el ID del maestro está en la URL
if (!isset($_GET['teacher_id']) || empty($_GET['teacher_id'])) {
    echo "Error: Maestro no especificado.";
    exit();
}

$teacher_id = intval($_GET['teacher_id']);
$student_id = intval($_SESSION['student_id']);

// Verificar conexión a la base de datos
if (!$conn) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Verificar si el estudiante está asociado con el maestro
$query = "
    SELECT * 
    FROM teacher_students 
    WHERE teacher_id = ? AND student_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $teacher_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    echo "No tienes acceso a las actividades de este maestro.";
    exit();
}

// Consultar las actividades asignadas por el maestro
$activities_query = "
    SELECT id, title, description, due_datetime, file_path
    FROM assignments
    WHERE teacher_id = ?
";
$activities_stmt = $conn->prepare($activities_query);
$activities_stmt->bind_param("i", $teacher_id);
$activities_stmt->execute();
$activities_result = $activities_stmt->get_result();

include("include/header.php");

?>

<div class="container">
    <h1>Actividades del Maestro</h1>

   

    <?php if ($activities_result && $activities_result->num_rows > 0): ?>
        <div class="activities-container">
            <?php while ($activity = $activities_result->fetch_assoc()): 
                 
                // Obtener la fecha y hora actuales
                $current_datetime = new DateTime(); 
                $current_date = $current_datetime->format('Y-m-d');
                $current_hour = intval($current_datetime->format('H'));
                $current_minute = intval($current_datetime->format('i'));
                
                // Obtener la fecha y hora de la actividad
                $due_datetime = new DateTime($activity['due_datetime']); 
                $due_date = $due_datetime->format('Y-m-d');
                $due_hour = intval($due_datetime->format('H'));
                $due_minute = intval($due_datetime->format('i'));
                
                // Lógica de revisión secuencial para determinar si está atrasada
                $is_late = false;
                
                if ($due_date < $current_date) {
                    // La fecha límite ya pasó
                    $is_late = true;
                } elseif ($due_date == $current_date) {
                    // La fecha coincide; revisar la hora
                    if ($due_hour < $current_hour) {
                        $is_late = true;
                    } elseif ($due_hour == $current_hour) {
                        // La hora coincide; revisar los minutos
                        if ($due_minute < $current_minute) {
                            $is_late = true;
                        }
                    }
                }
            ?>
                
                <div class="assignment-card">
                    <h2><?php echo htmlspecialchars($activity['title']); ?></h2>
                    <h4>Fecha límite: <?php echo date('d/m/Y H:i:s', strtotime($activity['due_datetime'])); ?></h4>
                    <div class="description formatted-content">
                        <?php echo htmlspecialchars($activity['description']); ?>
                    </div>

                    <?php if ($is_late): ?>
                        <div class="alert alert-danger">
                            <strong>¡Esta tarea está atrasada!</strong> La hora límite para entregarla ya pasó.
                        </div>
                    <?php endif; ?>

                    <div class="buttons">
                        <a class="blue" href="view-assignment-details.php?assignment_id=<?php echo urlencode($activity['id']); ?>">Ver tarea</a>
                        <a class="green" href="submit-assignment.php?assignment_id=<?php echo urlencode($activity['id']); ?>">Resolver tarea</a>
                    </div>

                    <?php if ($activity['file_path']): ?>
                        <div class="assignment-file">
                            <a href="<?php echo htmlspecialchars($activity['file_path']); ?>" target="_blank">Descargar documento</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No hay actividades asignadas por este maestro.</p>
    <?php endif; ?>
</div>

<style>
.formatted-content {
    margin: 10px 0;
    line-height: 1.6;
}

.alert-info {
    background-color: #e9f7fe;
    border-color: #b3e5fc;
    color: #31708f;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.assignment-card {
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.buttons a, .buttons button {
    display: inline-block;
    margin-right: 10px;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
}

.green {
    background-color: #28a745;
    color: #fff;
}

.blue {
    background-color: #007bff;
    color: #fff;
}
</style>

<?php 
include("include/footer.php");
?>

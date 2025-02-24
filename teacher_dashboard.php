<?php
session_start();
include("config/db.php");

// Verificar si el usuario está logueado y es maestro
if (!isset($_SESSION['username']) || $_SESSION['user_role'] != 'Teacher') {
    header('Location: login.php'); // Redirigir al login si no está logueado o no es maestro
    exit();
}

// Verificar si el teacher_id está disponible en la sesión
if (!isset($_SESSION['teacher_id'])) {
    $error = "Error: No se pudo obtener la información del maestro. Por favor, contacte al administrador.";
    die($error); // Detener la ejecución si falta el teacher_id
}

// Obtener datos de la sesión
$username = htmlspecialchars($_SESSION['username']); // Sanitizar el nombre de usuario
$teacher_id = $_SESSION['teacher_id']; // ID del maestro logueado

// Opcional: Obtener más información del maestro (si es necesario)
// $teacher_query = "SELECT * FROM teachers WHERE id = ?";
// $stmt = $conn->prepare($teacher_query);
// $stmt->bind_param("i", $teacher_id);
// $stmt->execute();
// $teacher_result = $stmt->get_result();
// if ($teacher_result->num_rows > 0) {
//     $teacher_data = $teacher_result->fetch_assoc();
//     // Puedes usar los datos del maestro si es necesario
// }
?>

<?php include("include/header.php"); ?>

<div class="container" id="data">
    <!-- Vista para el maestro -->
    <h1>Bienvenido, <?php echo $username; ?>.</h1>

    <div class="cards">
        <!-- Card para ver estudiantes -->
        <div class="card">
            <a href="view-students.php?teacher_id=<?php echo $teacher_id; ?>" style="color: #ff4200">
                <img src="./assets/05.svg" alt="View Students">
                Estudiantes
            </a>
        </div>
        <!-- Card para ver tareas -->
        <div class="card">
            <a href="view-assignment.php?teacher_id=<?php echo $teacher_id; ?>" style="color: #ff4200">
                <img src="./assets/06.svg" alt="View Assignments">
                Revisar actividades
            </a>
        </div>
        <!-- Card para agregar tareas -->
        <div class="card">
            <a href="add-assignment.php" style="color: #ff4200">
                <img src="./assets/07.svg" alt="Add Assignment">
                Añadir actividades
            </a>
        </div>
    </div>
</div>

<?php include("include/footer.php"); ?>

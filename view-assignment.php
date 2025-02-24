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
$user_id = $_SESSION['id']; // El ID del usuario logueado

// Obtener el teacher_id desde la tabla teachers
if ($user_role === 'Teacher') {
    $sql = "SELECT id FROM teachers WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // Usamos el user_id para obtener el teacher_id
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si el maestro existe y obtener el teacher_id
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $teacher_id = $row['id']; // teacher_id del maestro
    } else {
        echo "<p>Error: No se encontró al maestro.</p>";
        exit();
    }
}

include("include/header.php");
?>

<style>
    /* Estilos para contenido formateado */
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

    /* Estilos para tarjetas de asignación */
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

    .red {
        background-color: #dc3545;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    .assignment-file {
        margin-top: 10px;
    }

    .assignment-file a {
        color: #007bff;
        text-decoration: none;
    }

    .assignment-file a:hover {
        text-decoration: underline;
    }

    /* Nuevos estilos para botón de añadir tarea */
    .add-task-container {
    display: flex;
    justify-content: flex-start;
    margin-bottom: 20px;
    }

    .add-task-btn {
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: background-color 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .add-task-btn:hover {
        background-color: #0056b3;
    }

    .add-task-btn i {
        margin-right: 8px;
    }
</style>

<div class="container">
    <h1>Asignaciones</h1>

    <!-- Botón para añadir tarea si es maestro -->
    <?php if ($user_role === 'Teacher'): ?>
        <div class="add-task-container">
            <a href="add-assignment.php" class="btn btn-primary add-task-btn">
                <i class="fas fa-plus-circle"></i> Añadir nueva tarea
            </a>
        </div>
    <?php endif; ?>

    <div class="assignments-container">
        <?php
        // Cargar las asignaciones dependiendo del rol del usuario
        if ($user_role === 'Teacher') {
            // Consultar las asignaciones del maestro utilizando el teacher_id
            $sql = "SELECT * FROM assignments WHERE teacher_id = ? ORDER BY due_datetime DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $teacher_id); // Usar el teacher_id del maestro
            $stmt->execute();
            $result = $stmt->get_result();

            // Verificar si la consulta ha devuelto resultados
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $title = $row['title'];
                    $due_datetime = $row['due_datetime']; // Fecha y hora
                    $description = $row['description'];
                    $file_path = $row['file_path'];

                    // Verificamos si hay hora en due_datetime
                    if ($due_datetime) {
                        $formatted_due_datetime = date("d-m-Y H:i", strtotime($due_datetime));
                    } else {
                        $formatted_due_datetime = date("d-m-Y", strtotime($due_datetime)); // Si no hay hora, mostramos solo la fecha
                    }
                    ?>
                    <div class="assignment-card">
                        <h2><?php echo htmlspecialchars($title); ?></h2>
                        <h4>Fecha límite: <?php echo htmlspecialchars($formatted_due_datetime); ?></h4>
                        <div class="description formatted-content">
                            <?php echo formatContent($description); // Formatear el contenido ?>
                        </div>

                        <div class="buttons">
                            <a class="green" href="view-submissions.php?assignment_id=<?php echo urlencode($id); ?>">Revisar</a>
                            <a class="blue" href="edit.php?id=<?php echo urlencode($id); ?>">Editar</a>
                            <form action="delete.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo urlencode($id); ?>">
                                <button type="submit" class="red">Eliminar</button>
                            </form>
                        </div>

                        <?php if ($file_path): ?>
                            <div class="assignment-file">
                                <a href="<?php echo htmlspecialchars($file_path); ?>" target="_blank">Descargar documento</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No hay asignaciones disponibles.</p>";
            }
        } else {
            // Consultar las asignaciones activas para los estudiantes
            $sql = "SELECT * FROM assignments WHERE due_datetime >= NOW() ORDER BY due_datetime ASC";
            $result = $conn->query($sql);

            // Verificar si la consulta ha devuelto resultados
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $title = $row['title'];
                    $due_datetime = $row['due_datetime'];
                    $description = $row['description'];
                    $file_path = $row['file_path'];

                    // Formatear la fecha y hora
                    if ($due_datetime) {
                        $formatted_due_datetime = date("d-m-Y H:i", strtotime($due_datetime));
                    } else {
                        $formatted_due_datetime = date("d-m-Y", strtotime($due_datetime));
                    }
                    ?>
                    <div class="assignment-card">
                        <h2><?php echo htmlspecialchars($title); ?></h2>
                        <h4>Fecha límite: <?php echo htmlspecialchars($formatted_due_datetime); ?></h4>
                        <div class="description formatted-content">
                            <?php echo formatContent($description); // Formatear el contenido ?>
                        </div>

                        <div class="buttons">
                            <a class="green" href="view-assignment-details.php?assignment_id=<?php echo urlencode($id); ?>">Ver tarea</a>
                            <a class="blue" href="submit-assignment.php?assignment_id=<?php echo urlencode($id); ?>">Resolver tarea</a>
                        </div>

                        <?php if ($file_path): ?>
                            <div class="assignment-file">
                                <a href="<?php echo htmlspecialchars($file_path); ?>" target="_blank">Descargar documento</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No hay asignaciones disponibles.</p>";
            }
        }
        ?>
    </div>
</div>

<?php include("include/footer.php"); ?>
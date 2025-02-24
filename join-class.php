<?php
// Conexión a la base de datos
include("config/db.php");
session_start(); // Asegúrate de tener una sesión activa para identificar al estudiante

// Verificar si el método de solicitud es POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el código de la clase y limpiar espacios en blanco
    $access_code = trim($_POST['access_code']);
    
    // Validar si el código está vacío
    if (empty($access_code)) {
        echo "Por favor, ingresa un código de clase.";
        exit();
    }

    // Obtener el ID del estudiante desde la sesión
    $student_id = $_SESSION['student_id']; // Suponiendo que tienes la sesión del estudiante

    // Verificar si el código existe en la tabla teachers
    $stmt = $conn->prepare("SELECT id FROM teachers WHERE access_code = ?");
    $stmt->bind_param("s", $access_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El código es válido, obtener el ID del maestro
        $teacher = $result->fetch_assoc();
        $teacher_id = $teacher['id'];

        // Verificar si ya existe la asociación en teacher_students
        $check_stmt = $conn->prepare("SELECT * FROM teacher_students WHERE teacher_id = ? AND student_id = ?");
        $check_stmt->bind_param("ii", $teacher_id, $student_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            // Crear la asociación entre el maestro y el estudiante
            $insert_stmt = $conn->prepare("INSERT INTO teacher_students (teacher_id, student_id, joined_at) VALUES (?, ?, NOW())");
            $insert_stmt->bind_param("ii", $teacher_id, $student_id);

            if ($insert_stmt->execute()) {
                echo "¡Te has unido a la clase exitosamente!";
            } else {
                // Registrar el error en el log (opcional)
                error_log("Error al insertar en teacher_students: " . $insert_stmt->error);
                echo "Ocurrió un error al unirte a la clase. Intenta nuevamente.";
            }
        } else {
            echo "Ya estás inscrito en esta clase.";
        }
    } else {
        echo "Código de clase no válido.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unirse a una clase</title>
</head>
<body>
    <h1>Unirse a una clase</h1>
    <form method="POST" action="join-class.php">
        <label for="access_code">Código de la clase:</label>
        <input type="text" name="access_code" id="access_code" required>
        <button type="submit">Unirse</button>
    </form>
</body>
</html>

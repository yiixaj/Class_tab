<?php
session_start(); // Iniciar la sesión
include("config/db.php"); // Incluir la conexión a la base de datos

// Verificar que el docente esté logueado
if (!isset($_SESSION['teacher_id'])) {
    die("Acceso no autorizado.");
}

// Verificar si se ha enviado la materia
if (isset($_POST['subject']) && !empty($_POST['subject'])) {
    $subject_id = $_POST['subject']; // Materia seleccionada
    $teacher_id = $_SESSION['teacher_id']; // ID del docente (supongo que está en la sesión)

    // Actualizar la materia en la tabla teachers (suponiendo que tienes un campo llamado 'subject_id')
    $sql_update = "UPDATE teachers SET subject_id = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $subject_id, $teacher_id); // Vinculamos los parámetros
    $stmt_update->execute();

    if ($stmt_update->affected_rows > 0) {
        // Redirigir con un mensaje de éxito
        header("Location: view-students.php?message=Materia actualizada con éxito");
    } else {
        // Redirigir con un mensaje de error
        header("Location: view-students.php?message=Error al actualizar la materia");
    }
} else {
    // Redirigir con mensaje de error si no se seleccionó materia
    header("Location: view-students.php?message=Por favor, selecciona una materia.");
}
exit;
?>

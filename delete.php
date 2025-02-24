<?php 
session_start();
include("config/db.php");

// Verificar si el usuario está logueado y es profesor
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'Teacher') {
    header('Location: login.php');
    exit();
}

// Verificar si se recibió un ID para eliminar
if (isset($_POST['id'])) {
    // Sanitizar el ID
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    // Usar prepared statement para prevenir SQL injection
    $sql = "DELETE FROM assignments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Redirigir a view-assignment.php en lugar de dashboard
        header("Location: view-assignment.php");
        exit();
    } else {
        // Si hay error, redirigir con mensaje de error
        $_SESSION['error'] = "Error al eliminar la tarea.";
        header("Location: view-assignment.php");
        exit();
    }
} else {
    // Si no hay ID, redirigir
    header("Location: view-assignment.php");
    exit();
}
?>
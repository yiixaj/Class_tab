<?php
include("config/db.php");
include("include/utils.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submission_id = intval($_POST['submission_id']);
    $grade = floatval($_POST['grade']);
    $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';

    // Validar entrada
    if ($grade < 0 || $grade > 10) {
        echo json_encode(['success' => false, 'message' => 'La calificación debe estar entre 0 y 10.']);
        exit();
    }

    // Actualizar la base de datos
    $sql = "UPDATE submissions SET grade = ?, comments = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dsi", $grade, $comments, $submission_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la calificación.']);
    }
}

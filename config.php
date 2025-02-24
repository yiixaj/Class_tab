<?php
// Establecer la zona horaria para todo el proyecto
date_default_timezone_set('America/Guayaquil');

$host = 'localhost'; // Cambiado de 'db' a 'localhost' para XAMPP
$dbname = 'tutor';
$username = 'root';  // Usuario por defecto de XAMPP
$password = '';      // Contraseña vacía por defecto en XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to the database: " . $e->getMessage());
}
?>
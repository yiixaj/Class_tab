<?php
    $server = "localhost"; // Cambiado de "db" a "localhost"
    $user = "root";       // Usuario por defecto de XAMPP
    $password = "";       // Contraseña por defecto de XAMPP (vacía)
    $dbname = "tutor";

    $conn = mysqli_connect($server, $user, $password, $dbname);

    // Set UTF-8 encoding
    if ($conn) {
        mysqli_set_charset($conn, "utf8");
    } else {
        die("Connection Failed: " . mysqli_connect_error());
    }
?>
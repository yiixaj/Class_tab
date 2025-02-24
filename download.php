<?php
// download.php
$file = $_GET['file'];

// Ruta completa del archivo
$fullPath = $_SERVER['DOCUMENT_ROOT'] . $file;

// Verificar si el archivo existe
if (file_exists($fullPath)) {
    // Determinar el tipo MIME del archivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fullPath);
    finfo_close($finfo);

    // Preparar las cabeceras para la descarga
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fullPath));
    
    // Limpiar buffer de salida
    ob_clean();
    flush();
    
    // Leer y enviar archivo
    readfile($fullPath);
    exit;
} else {
    die("El archivo no existe.");
}
?>
<?php
/**
 * Función para formatear el contenido, maneja tanto HTML como texto plano
 */
function formatContent($content) {
    // Si el contenido parece contener HTML (detectamos algunas etiquetas comunes)
    if (preg_match('/<[^>]*(p|div|table|figure|ul|ol|li|h[1-6])[^>]*>/i', $content)) {
        // Para contenido HTML, solo limpiamos etiquetas peligrosas
        return strip_tags($content, '<p><br><div><span><table><thead><tbody><tr><td><th><ul><ol><li><strong><em><h1><h2><h3><h4><h5><h6><figure><img>');
    } else {
        // Para texto plano, convertimos saltos de línea y mantenemos el formato
        $content = htmlspecialchars($content);
        $content = str_replace(['\r\n', '\n', '\r'], "\n", $content);
        return nl2br($content);
    }
}
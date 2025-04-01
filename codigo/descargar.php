<?php
if (isset($_GET['file'])) {
    $filePath = __DIR__ . '/firmados/' . basename($_GET['file']);

    if (file_exists($filePath)) {
        // Configurar encabezados para la descarga
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        readfile($filePath);
        exit;
    } else {
        echo "El archivo no existe.";
    }
} else {
    echo "No se especificó ningún archivo.";
}
?>

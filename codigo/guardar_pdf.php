<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
    $uploadDir = __DIR__ . '/sinfirmar/';
    
    // Crear la carpeta si no existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filePath = $uploadDir . 'Contrato_' . time() . '.pdf'; // Nombre único

    if (move_uploaded_file($_FILES['pdf']['tmp_name'], $filePath)) {
        // Devolver la ruta completa del archivo guardado
        echo "sinfirmar/" . basename($filePath);
    } else {
        echo "Error al guardar el archivo.";
    }
} else {
    echo "No se recibió ningún archivo.";
}
?>

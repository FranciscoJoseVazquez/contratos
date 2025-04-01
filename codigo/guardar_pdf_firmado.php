<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
    $uploadDir = __DIR__ . '/firmados/';
    
    // Crear la carpeta si no existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filePath = $uploadDir . 'Contrato_firmado_' . time() . '.pdf'; // Nombre único

    if (move_uploaded_file($_FILES['pdf']['tmp_name'], $filePath)) {
        // Regresamos el nombre del archivo para poder usarlo para la descarga
        echo json_encode(["message" => "Archivo guardado", "fileName" => basename($filePath)]);
    } else {
        echo json_encode(["message" => "Error al guardar el archivo."]);
    }
} else {
    echo json_encode(["message" => "No se recibió ningún archivo."]);
}
?>

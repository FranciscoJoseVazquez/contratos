<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firmar PDF</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.16.0/pdf-lib.min.js"></script>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
        }
        #container {
            display: flex;
            align-items: flex-start;
        }
        #signaturePanel {
            margin-right: 20px;
        }
        iframe {
            width: 600px;
            height: 800px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <div id="container">
        <!-- Panel de firma -->
        <div id="signaturePanel">
            <canvas id="signatureCanvas" width="300" height="100" style="border:1px solid black;"></canvas>
            <br>
            <button onclick="clearSignature()">Borrar Firma</button>
            <button onclick="signPDF()">Firmar PDF</button>
        </div>

        <!-- Vista previa del PDF -->
        <iframe id="pdfPreview" src="" frameborder="0"></iframe>
    </div>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js";

        let signatureCanvas = document.getElementById("signatureCanvas");
        let ctx = signatureCanvas.getContext("2d");
        let drawing = false;

        // Cargar el PDF en el iframe para vista previa
        let pdfPath = localStorage.getItem("ruta");
        if (pdfPath) {
            document.getElementById('pdfPreview').src = pdfPath;
        }

        signatureCanvas.addEventListener("mousedown", () => drawing = true);
        signatureCanvas.addEventListener("mouseup", () => { drawing = false; ctx.beginPath(); });
        signatureCanvas.addEventListener("mousemove", draw);

        function draw(event) {
            if (!drawing) return;
            ctx.lineWidth = 2;
            ctx.lineCap = "round";
            ctx.strokeStyle = "black";
            ctx.lineTo(event.offsetX, event.offsetY);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(event.offsetX, event.offsetY);
        }

        function clearSignature() {
            ctx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
        }

        async function signPDF() {
            let pdfPath = localStorage.getItem("ruta");

            if (!pdfPath) {
                alert("No se ha guardado la ruta del PDF.");
                return;
            }

            try {
                let response = await fetch(pdfPath);
                let pdfBytes = await response.arrayBuffer();
                const pdfDoc = await PDFLib.PDFDocument.load(pdfBytes);
                const pages = pdfDoc.getPages();

                const signatureImage = signatureCanvas.toDataURL("image/png");
                const pngImage = await pdfDoc.embedPng(signatureImage);

                // Dimensiones de una hoja A4 en puntos PDF (595 × 842)
                pages.forEach(page => {
                    const { width, height } = page.getSize(); // Obtiene dimensiones de la página

                    page.drawImage(pngImage, {
                        x: width - 170, // Posición en la esquina inferior derecha
                        y: 20, // Un poco por encima del borde inferior
                        width: 150,
                        height: 50
                    });
                });

                const signedPdfBytes = await pdfDoc.save();
                const blob = new Blob([signedPdfBytes], { type: "application/pdf" });

                let formData = new FormData();
                formData.append("pdf", blob, "signed.pdf");

                fetch("guardar_pdf_firmado.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.fileName) {
                        window.location.href = `descargar.php?file=${encodeURIComponent(data.fileName)}`;
                    } else {
                        alert("Error al descargar el archivo.");
                    }
                })
                .catch(error => console.error("Error al guardar el PDF firmado:", error));
            } catch (error) {
                console.error("Error al procesar el PDF:", error);
                alert("Error al cargar o firmar el PDF.");
            }
        }
    </script>
</body>
</html>

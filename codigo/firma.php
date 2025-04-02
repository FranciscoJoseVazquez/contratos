<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firmar PDF</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.16.0/pdf-lib.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
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
            let fechaContrato = localStorage.getItem("fechaContrato") || "Fecha desconocida";
            let apoderado = localStorage.getItem("apoderado") || "Nombre desconocido";

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

                // Generar huella digital (hash SHA-256 del contenido del PDF)
                let hash = CryptoJS.SHA256(CryptoJS.lib.WordArray.create(pdfBytes)).toString();

                // Obtener la hora actual
                let now = new Date();
                let horaActual = now.toLocaleTimeString();

                let textoFirma = `Documento firmado digitalmente el ${fechaContrato} a las ${horaActual} :: Huella digital única: ${hash}`;
                let textoFirma2 = `Documento firmado digitalmente el ${fechaContrato} a las ${horaActual}\nHuella digital única: ${hash}`;

                pages.forEach(page => {
                    const { width, height } = page.getSize();

                    // Firma en la esquina inferior derecha
                    page.drawImage(pngImage, {
                        x: width - 170,
                        y: 50,
                        width: 150,
                        height: 50
                    });

                    // Texto de firma debajo de la imagen 
                    page.drawText(textoFirma, {
                        x: width - 575,
                        y: 30,
                        size: 8
                    });
                });

                // Crear una nueva página con la firma grande en el centro
                const newPage = pdfDoc.addPage([595, 842]);
                const { width, height } = newPage.getSize();
                
                // Encabezado en la página extra
                let textoEncabezado = `DOCUMENTO FIRMADO con fecha de ${fechaContrato}\nD./DÑA. ${apoderado} reconoce haber recibido el presente documento\n\nPara que así conste:`;

                newPage.drawText(textoEncabezado, {
                    x: 40,
                    y: height - 100,
                    size: 14
                });

                // Firma grande en el centro de la nueva página
                newPage.drawImage(pngImage, {
                    x: (width - 300) / 2, 
                    y: (height / 4) * 2.3,
                    width: 300,
                    height: 150
                });

                // Texto de firma en la nueva página (tamaño más grande)
                newPage.drawText(textoFirma2, {
                    x: 50,
                    y: 450,
                    size: 9
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

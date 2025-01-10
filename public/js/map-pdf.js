function downloadMap(selectedMapId, points) {
    $.ajax({
        url: `/api/map/${selectedMapId}`,
        method: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: async function (pdfBlob, status, xhr) {
            const fileHeader = xhr.getResponseHeader('X-Pages-Data');
            if (!fileHeader) {
                alert('Brak współczynników w odpowiedzi serwera.');
                return;
            }

            const fileData = JSON.parse(fileHeader);

            console.log(fileData);
            await modifyMap(pdfBlob, fileData, points);
        },
        error: function () {
            alert('Wystąpił błąd podczas pobierania mapy.');
        }
    });

};

 // Funkcja modyfikująca mapę PDF
 async function modifyMap(pdfBlob, data, points) {
    try {
        // Załaduj PDF z obiektu Blob
        const pdfBytes = await pdfBlob.arrayBuffer();
        const pdfDoc = await PDFLib.PDFDocument.load(pdfBytes);

        // Dodawanie współczynników na każdą stronę PDF
        const pages = pdfDoc.getPages();

        // const fontBytes = await fetch('/path/to/roboto-bold.ttf').then(res => res.arrayBuffer());
        // const boldFont = await pdfDoc.embedFont(fontBytes);

        points.sort((p1, p2) => {
            return p1.position - p2.position;
        });

        console.log("points");
        console.log(points);

        data.forEach(mapData => {
            const index = mapData.page - 1;
            const page = pages[index];

            const circleSize = 15;
            const thickness = 2;
            const circleColor = PDFLib.rgb(1, 0, 1);
            const textSize = 15;
            const textColor = PDFLib.rgb(1, 0, 0.3);

            // zaznacz pierwszy punkt
            const firstPointCoords = getPDFCoords(points[0], mapData);
            drawPoint(page, points[0], firstPointCoords, circleSize, thickness, circleColor, textSize, textColor)

            for(let j = 1; j < points.length; j++) {
            
                // współrzędne punktu początkowego odcinka trasy
                const start = getPDFCoords(points[j-1], mapData);

                // współrzędne punktu końcowego odcinka trasy
                const end = getPDFCoords(points[j], mapData);

                let deltaX = start.x-end.x;
                let deltaY = start.y-end.y;
                let distance = Math.sqrt(deltaX*deltaX+deltaY*deltaY);
                
                let XLineCorrection = deltaX*circleSize/distance;
                let YLineCorrection = deltaY*circleSize/distance;
        
                page.drawLine({
                    start:{x: start.x - XLineCorrection, y: start.y - YLineCorrection},
                    end: {x: end.x + XLineCorrection, y: end.y + YLineCorrection},
                    thickness: thickness,
                    color: circleColor
                })
        
                
                if(points[0].id !== points[j].id) {
                    drawPoint(page, points[j], end, circleSize, thickness, circleColor, textSize, textColor)
                    
                }
            }

        })

        // ==========================================================
        // const helveticaBoldFont = await pdfDoc.embedFont(StandardFonts.HelveticaBold);
        // Dodaj nową stronę do PDF
        const newPage = pdfDoc.addPage([595, 842]); // A4 w punktach (72 DPI)

        // Dodanie tekstu na nowej stronie

        const fontSize = 14;
        let yPosition = 800;

        newPage.drawText(`Liczba punktow: ${points.length}`, {
            x: 50,
            y: yPosition,
            size: fontSize,
            // font: font
        });

        yPosition -= 20;
        newPage.drawText('Dystans: ' + calculateRouteLength(points) + ' km', {
            x: 50,
            y: yPosition,
            size: fontSize,
            // font: font
        });

        yPosition -= 20;
        newPage.drawText('Karta startowa: ', {
            x: 50,
            y: yPosition,
            size: fontSize,
            // font: font
        });

        // Dodanie tabeli na nowej stronie
        const cellSize = 82; // Rozmiar komórki tabeli (kwadratowej)
        const startX = 50; // Początkowa pozycja X tabeli
        let currentX = startX;
        let currentY = yPosition - cellSize - 10;

        points.forEach((point, index) => {
            // Narysowanie obramowania komórki
            newPage.drawRectangle({
                x: currentX,
                y: currentY,
                width: cellSize,
                height: cellSize,
                borderColor: PDFLib.rgb(0, 0, 0),
                borderWidth: 1
            });

            // Dodanie kodu punktu w lewym górnym rogu komórki
            newPage.drawText(`${point.code}`, {
                x: currentX + 5,
                y: currentY + cellSize - 15,
                size: 12,
                // font: font,
                color: PDFLib.rgb(0, 0, 0)
            });

            // Przejście do kolejnej kolumny
            currentX += cellSize;

            // Sprawdzenie, czy trzeba przejść do nowego wiersza
            if ((index + 1) % 6 === 0) {
                currentX = startX; // Resetowanie pozycji X
                currentY -= cellSize; // Przejście do nowego wiersza
            }
        });
        // =============================================


        // Zapisz zmodyfikowany PDF jako plik Blob
        const modifiedPdfBytes = await pdfDoc.save();
        const modifiedPdfBlob = new Blob([modifiedPdfBytes], { type: 'application/pdf' });

        // Utworzenie linku do pobrania zmodyfikowanego PDF
        const downloadUrl = URL.createObjectURL(modifiedPdfBlob);
        const a = document.createElement('a');
        a.href = downloadUrl;
        a.download = 'mapa.pdf';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(downloadUrl);
    } catch (error) {
        alert('Wystąpił błąd podczas modyfikacji mapy.');
        console.log(error);
    }
}

function getPDFCoords(point, data) {
    let x = data.coeff_a * point.easting + data.coeff_b * point.northing + data.coeff_c;
    let y = data.coeff_d * point.easting + data.coeff_e * point.northing + data.coeff_f;
    return {x: x, y: y};
}

async function drawPoint(page, point, pdfCoords, circleSize, thickness, circleColor, textSize, textColor) {

    page.drawCircle({
        x: pdfCoords.x,
        y: pdfCoords.y,
        size: circleSize,
        borderWidth: thickness,
        borderColor: circleColor
    });
    
    page.drawText(
        point.position + " - " + point.code,
        {
            x: pdfCoords.x + circleSize + 3,
            y: pdfCoords.y,
            size: textSize,
            color: textColor,
        }
    )
    
}
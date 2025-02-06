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

        pdfDoc.registerFontkit(window.fontkit);
        const fontBytes = await fetch('/fonts/Lato-Bold.ttf').then(res => res.arrayBuffer());
        const font = await pdfDoc.embedFont(fontBytes);

        // Dodawanie współczynników na każdą stronę PDF
        const pages = pdfDoc.getPages();

        // console.log(pages[0]);

        // const fontBytes = await fetch('/path/to/roboto-bold.ttf').then(res => res.arrayBuffer());
        // const boldFont = await pdfDoc.embedFont(fontBytes);

        points.sort((p1, p2) => {
            return p1.position - p2.position;
        });

        console.log("points");
        console.log(points);

        for (const mapData of data) {
            const index = mapData.page - 1;
            const page = pages[index];
            const {width: pageWidth, height: pageHeight} = page.getSize();
            console.log("width: " + pageWidth + "; height: " + pageHeight);
            const circleSize = 15;
            const thickness = 2;
            const circleColor = PDFLib.rgb(1, 0, 1);
            const textSize = 15;
            const textColor = PDFLib.rgb(1, 0, 0.3);

            // najpierw rysujem ścieżkę
            const firsttPointPdfCoord = getPDFCoords(points[0], mapData);
            page.drawCircle({
                x: firsttPointPdfCoord.x,
                y: firsttPointPdfCoord.y,
                size: circleSize,
                borderWidth: thickness,
                borderColor: circleColor
            });
            
            console.log("first circle");
            
            for(let j = 1; j < points.length; j++) {
                console.log(points[j].code);
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

                if((end.x > 0 && end.x < pageWidth) && (end.y > 0 && end.y < pageHeight)){
                    page.drawCircle({
                        x: end.x,
                        y: end.y,
                        size: circleSize,
                        borderWidth: thickness,
                        borderColor: points[j].pointVirtual === 1 ? PDFLib.rgb(0, 0.5, 1) : circleColor
                    });
                }
                

                                    
                // if(points[0].id !== points[j].id) {
                //     drawPoint(page, points[j], end, circleSize, thickness, circleColor, textSize, textColor)
                    
                // }
            }

            console.log("path");

            // let iterations = points.length;

            // if(points[0].id === points[points.length - 1].id) {
            //     iterations -= 1;
            // }
            // const font = await pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica);
            let pointMap = new Map();
            for(let j = 0; j < points.length; j++) {
                pointMap = updatePointMap(pointMap, points[j].id);
                // współrzędne punktu trasy
                const pointPdfCoord = getPDFCoords(points[j], mapData);
                //  drawPoint(page, points[j], pointPdfCoord, circleSize, thickness, circleColor, textSize, textColor);
                
                // const text = points[j].position + " - " + points[j].code;
                // const textWidth = font.widthOfTextAtSize(text, textSize);
                // console.log("text width: " + textWidth);
                if((pointPdfCoord.x > 0 && pointPdfCoord.x < pageWidth) && (pointPdfCoord.y > 0 && pointPdfCoord.y < pageHeight)){
                    const text = points[j].position + " - " + points[j].code;
                    const textWidth = font.widthOfTextAtSize(text, textSize);
                    console.log("text width: " + textWidth);
                    
                    let xPosition = pointPdfCoord.x + circleSize + 3;
                    let yPosition = pointPdfCoord.y - (pointMap.get(points[j].id) - 1) * textSize;

                    // console.log("text end x: " + (xPosition + textWidth));

                    if((xPosition + textWidth) >= pageWidth) {
                        console.log("tekst poza marginesem: " + (pageWidth - textWidth - 3));
                        xPosition = Math.min(pointPdfCoord.x, pageWidth - textWidth - 3);
                        yPosition = yPosition - circleSize - textSize;

                        console.log(">>>>>>>>>> x: " + xPosition + "; y: " + yPosition);
                    }
                    console.log("x: " + xPosition + "; y: " + yPosition);

                    page.drawText(
                        text,
                        {
                            x: xPosition,
                            y: yPosition,
                            size: textSize,
                            color: textColor,
                            font: font
                        }
                    );
                }

            }

            console.log("descriptions");

        };

        // ==========================================================
        // const helveticaBoldFont = await pdfDoc.embedFont(StandardFonts.HelveticaBold);
        // Dodaj nową stronę do PDF
        let newPage = pdfDoc.addPage([595, 842]); // A4 w punktach (72 DPI)

        // Dodanie tekstu na nowej stronie

        const fontSize = 14;
        let yPosition = 800;
        // const font = await pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica);

        newPage.drawText(`Liczba punktów: ${points.length}`, {
            x: 50,
            y: yPosition,
            size: fontSize,
            font: font
        });

        yPosition -= 20;
        newPage.drawText('Dystans: ' + calculateRouteLength(points) + ' km', {
            x: 50,
            y: yPosition,
            size: fontSize,
            font: font
        });

        yPosition -= 20;
        newPage.drawText('Karta startowa: ', {
            x: 50,
            y: yPosition,
            size: fontSize,
            font: font
        });

        // Dodanie tabeli na nowej stronie
        const cellSize = 82; // Rozmiar komórki tabeli (kwadratowej)
        const startX = 50; // Początkowa pozycja X tabeli
        let currentX = startX;
        yPosition = yPosition - 10;

        points.forEach((point, index) => {
            // Sprawdzenie, czy trzeba przejść do nowego wiersza
            if ((index) % 6 === 0) {
                currentX = startX; // Resetowanie pozycji X
                yPosition -= cellSize; // Przejście do nowego wiersza

                if(yPosition < 30) {
                    newPage = pdfDoc.addPage([595, 842]);
                    yPosition = 800 - cellSize;
                }
            }

            // Narysowanie obramowania komórki
            newPage.drawRectangle({
                x: currentX,
                y: yPosition,
                width: cellSize,
                height: cellSize,
                borderColor: PDFLib.rgb(0, 0, 0),
                borderWidth: 1
            });

            // Dodanie kodu punktu w lewym górnym rogu komórki
            newPage.drawText(`${point.code}`, {
                x: currentX + 5,
                y: yPosition + cellSize - 15,
                size: 12,
                font: font,
                color: PDFLib.rgb(0, 0, 0)
            });

            if (point.pointVirtual === 1) {
                const text = "punkt wirtualny";
            
                // Obliczenie pozycji środka komórki
                const textWidth = font.widthOfTextAtSize(text, 10); // Rozmiar tekstu w bieżącym rozmiarze
                const textX = currentX + (cellSize - textWidth) / 2; // Pozycja X na środku
                const textY = yPosition + (cellSize / 2) - 5; // Pozycja Y na środku
            
                newPage.drawText(text, {
                    x: textX,
                    y: textY,
                    size: 10,
                    font: font,
                    color: PDFLib.rgb(0, 0.5, 1) // Niebieski kolor
                });
            }

            // Przejście do kolejnej kolumny
            currentX += cellSize;

            
        });
        // console.log("currentX: " + currentX + "; startX: " + startX);
        // if(currentX !== startX) {
            yPosition -= 20;
        // }
        // else {
        //     yPosition
        // }

        let listFontSize = 12

        newPage.drawText("Przebieg trasy:", {
            x: 50,
            y: yPosition,
            size: listFontSize,
            font: font 
        });

        yPosition -= 15;
        

        points.forEach((point, index) => {
            newPage.drawText(`${point.position} - ${point.code} - ${point.description}${point.pointVirtual === 1 ? " (punkt wirtualny) " : ""}`, {
                x: 50,
                y: yPosition,
                size: listFontSize,
                font: font 
            });

            yPosition -= 15;
            
            if(yPosition < 30) {
                newPage = pdfDoc.addPage([595, 842]);
                yPosition = 800;
            }
        })
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

function updatePointMap(map, key){
    if (map.has(key)) {
        // Jeśli klucz istnieje, zwiększ wartość o 1
        map.set(key, map.get(key) + 1);
      } else {
        // Jeśli klucz nie istnieje, ustaw wartość na 1
        map.set(key, 1);
      }

      return map;
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
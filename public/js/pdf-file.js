/**
 * Pobiera mapę w formacie PDF na podstawie wybranego ID mapy i punktów trasy.
 * Po pobraniu pliku modyfikuje mapę przy użyciu funkcji `modifyMap`.
 *
 * @param {string} selectedMapId - ID wybranej mapy do pobrania.
 * @param {Array} points - Tablica punktów trasy, zawierająca współrzędne i opisy.
 */
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

            // Modyfikacja pobranej mapy na podstawie danych i punktów
            await modifyMap(pdfBlob, fileData, points);
        },
        error: function () {
            alert('Wystąpił błąd podczas pobierania mapy.');
        }
    });

};

/**
 * Funkcja modyfikuje plik PDF mapy, dodając wizualizację trasy oraz punkty wraz z ich opisami
 * Na końcu generuje podsumowanie trasy i umożliwia pobranie zmodyfikowanego pliku PDF
 * 
 * @async
 * @param {Blob} pdfBlob - Obiekt blob zawierający oryginalny plik PDF mapy
 * @param {Array} data - Tablica z danymi dotyczącymi stron mapy (współczynniki potrzebne do georeferencji)
 * @param {Array} points - Lista punktów trasy, zawierająca współrzędne i opisy punktów
 * 
 * Działanie:
 * - Ładuje i inicjalizuje dokument PDF oraz wbudowuje czcionkę
 * - Sortuje punkty trasy według ich pozycji
 * - Dla każdej strony PDF:
 *   - Rysuje trasę pomiędzy punktami
 *   - Dodaje opisy punktów
 * - Tworzy stronę podsumowania, zawierającą liczbę punktów, dystans oraz kartę startową.
 * - Generuje zmodyfikowany plik PDF i umożliwia jego pobranie.
 */
async function modifyMap(pdfBlob, data, points) {
    try {
        
        const pdfBytes = await pdfBlob.arrayBuffer();
        const pdfDoc = await PDFLib.PDFDocument.load(pdfBytes);
        pdfDoc.registerFontkit(window.fontkit);
        const fontBytes = await fetch('/fonts/Lato-Bold.ttf').then(res => res.arrayBuffer());
        const font = await pdfDoc.embedFont(fontBytes);

        
        const pages = pdfDoc.getPages();


        points.sort((p1, p2) => {
            return p1.position - p2.position;
        });


        for (const mapData of data) {
            const index = mapData.page - 1;
            const page = pages[index];
            const {width: pageWidth, height: pageHeight} = page.getSize();
            const circleSize = 15;
            const thickness = 2;
            const circleColor = PDFLib.rgb(1, 0, 1);
            const textSize = 15;
            const textColor = PDFLib.rgb(1, 0, 0.3);

            drawPathInFile(page, pageWidth, pageHeight, mapData, points, circleSize, thickness, circleColor);

            drawPointDescriptionInFile(page, pageWidth, pageHeight, mapData, points, textSize, textColor, font, circleSize);

        };

        createSummaryPage(pdfDoc, points, font);

        const modifiedPdfBytes = await pdfDoc.save();
        const modifiedPdfBlob = new Blob([modifiedPdfBytes], { type: 'application/pdf' });
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

/**
 * Rysuje trasę na stronie PDF, łącząc punkty za pomocą okręgów i linii.
 *
 * @param {Object} page - Strona PDF, na której rysowana jest trasa.
 * @param {number} pageWidth - Szerokość strony PDF.
 * @param {number} pageHeight - Wysokość strony PDF.
 * @param {Object} mapData - Dane transformacji współrzędnych dla strony.
 * @param {Array} points - Tablica punktów trasy.
 * @param {number} circleSize - Rozmiar rysowanych okręgów.
 * @param {number} thickness - Grubość linii.
 * @param {Object} circleColor - Obiekt koloru (PDFLib.rgb) okręgów i linii.
 */
function drawPathInFile(page, pageWidth, pageHeight, mapData, points, circleSize, thickness, circleColor) {
    const firsttPointPdfCoord = getPDFCoords(points[0], mapData);
    page.drawCircle({
        x: firsttPointPdfCoord.x,
        y: firsttPointPdfCoord.y,
        size: circleSize,
        borderWidth: thickness,
        borderColor: circleColor
    });
    
    
    for(let j = 1; j < points.length; j++) {

        const start = getPDFCoords(points[j-1], mapData);
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
        });

        if((end.x > 0 && end.x < pageWidth) && (end.y > 0 && end.y < pageHeight)){
            page.drawCircle({
                x: end.x,
                y: end.y,
                size: circleSize,
                borderWidth: thickness,
                borderColor: points[j].pointVirtual === 1 ? PDFLib.rgb(0, 0.5, 1) : circleColor
            });
        }
        
    }
}

/**
 * Rysuje opisy punktów na stronie PDF w pobliżu ich współrzędnych.
 *
 * @param {Object} page - Strona PDF, na której rysowane są opisy.
 * @param {number} pageWidth - Szerokość strony PDF.
 * @param {number} pageHeight - Wysokość strony PDF.
 * @param {Object} mapData - Dane transformacji współrzędnych dla strony.
 * @param {Array} points - Tablica punktów trasy.
 * @param {number} textSize - Rozmiar czcionki opisu.
 * @param {Object} textColor - Obiekt koloru (PDFLib.rgb) tekstu.
 * @param {Object} font - Czcionka używana do rysowania opisu.
 * @param {number} circleSize - Rozmiar okręgu punktu (używany do pozycjonowania tekstu).
 */
function drawPointDescriptionInFile(page, pageWidth, pageHeight, mapData, points, textSize, textColor, font, circleSize) {
    let hashTable = new Map();
    for(let j = 0; j < points.length; j++) {
        hashTable = updatePointHashTable(hashTable, points[j].id);
        
        const pointPdfCoord = getPDFCoords(points[j], mapData);
        
        if((pointPdfCoord.x > 0 && pointPdfCoord.x < pageWidth) && (pointPdfCoord.y > 0 && pointPdfCoord.y < pageHeight)){
            const text = points[j].position + " - " + points[j].code;
            const textWidth = font.widthOfTextAtSize(text, textSize);
            
            let xPosition = pointPdfCoord.x + circleSize + 3;
            let yPosition = pointPdfCoord.y - (hashTable.get(points[j].id) - 1) * textSize;

            if((xPosition + textWidth) >= pageWidth) {

                xPosition = Math.min(pointPdfCoord.x, pageWidth - textWidth - 3);
                yPosition = yPosition - circleSize - textSize;

            }


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
}

/**
 * Tworzy stronę podsumowania trasy w pliku PDF, zawierającą szczegóły trasy i kartę startową.
 *
 * @param {Object} pdfDoc - Dokument PDF, do którego dodawana jest strona podsumowania.
 * @param {Array} points - Tablica punktów trasy.
 * @param {Object} font - Czcionka używana do rysowania tekstu.
 */
function createSummaryPage(pdfDoc, points, font) {
    let newPage = pdfDoc.addPage([595, 842]); // A4 w punktach (72 DPI)

        const fontSize = 14;
        let yPosition = 800;

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

        
        const cellSize = 82;
        const startX = 50;
        let currentX = startX;
        yPosition = yPosition - 10;

        points.forEach((point, index) => {
            if ((index) % 6 === 0) {
                currentX = startX;
                yPosition -= cellSize

                if(yPosition < 30) {
                    newPage = pdfDoc.addPage([595, 842]);
                    yPosition = 800 - cellSize;
                }
            }

            newPage.drawRectangle({
                x: currentX,
                y: yPosition,
                width: cellSize,
                height: cellSize,
                borderColor: PDFLib.rgb(0, 0, 0),
                borderWidth: 1
            });

            newPage.drawText(`${point.code}`, {
                x: currentX + 5,
                y: yPosition + cellSize - 15,
                size: 12,
                font: font,
                color: PDFLib.rgb(0, 0, 0)
            });

            if (point.pointVirtual === 1) {
                const text = "punkt wirtualny";

                const textWidth = font.widthOfTextAtSize(text, 10);
                const textX = currentX + (cellSize - textWidth) / 2;
                const textY = yPosition + (cellSize / 2) - 5; 

                newPage.drawText(text, {
                    x: textX,
                    y: textY,
                    size: 10,
                    font: font,
                    color: PDFLib.rgb(0, 0.5, 1)
                });
            }

            currentX += cellSize;

        });

        yPosition -= 20;

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
}

/**
 * Aktualizuje tablicę (hash table) punktów, zwiększając licznik wystąpień dla danego klucza.
 * Jeśli klucz nie istnieje w tablicy, dodaje go z wartością początkową 1.
 *
 * @param {Map} hashTable - Tablica (obiekt Map) przechowująca licznik wystąpień dla kluczy.
 * @param {any} key - Klucz, którego licznik należy zaktualizować.
 * @returns {Map} - Zaktualizowana tablica (obiekt Map).
 */
function updatePointHashTable(hashTable, key){
    if (hashTable.has(key)) {
        hashTable.set(key, hashTable.get(key) + 1);
      } else {
        hashTable.set(key, 1);
      }

      return hashTable;
}

/**
 * Przelicza współrzędne punktu na układ współrzędnych PDF na podstawie współczynników mapy.
 *
 * @param {Object} point - Punkt zawierający współrzędne `easting` i `northing`.
 * @param {Object} data - Obiekt zawierający współczynniki transformacji.
 * @returns {Object} - Współrzędne w układzie PDF `{x, y}`.
 */
function getPDFCoords(point, data) {
    let x = data.coeff_a * point.easting + data.coeff_b * point.northing + data.coeff_c;
    let y = data.coeff_d * point.easting + data.coeff_e * point.northing + data.coeff_f;
    return {x: x, y: y};
}

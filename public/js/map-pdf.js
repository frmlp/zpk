function downloadMap(points) {
    $.ajax({
        url: 'http://localhost:8000/api/map-download',
        method: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: function(data) {
            const reader = new FileReader();
            reader.onload = function() {
                const uint8Array = new Uint8Array(reader.result);
                modifyMap(uint8Array, points);
            };
            reader.readAsArrayBuffer(data);
        },
        error: function(error) {
            console.error('Error downloading map: ', error);
        }
    });

};

// function downloadMap(routeId) {
//     $.ajax({
//         url: 'http://localhost:8000/api/map-download',
//         method: 'GET',
//         xhrFields: {
//             responseType: 'blob'
//         }
//     })
//     .then(data => {
//         return new Promise((resolve) => {
//             const reader = new FileReader();
//             reader.onload = () => resolve(new Uint8Array(reader.result));
//             reader.readAsArrayBuffer(data);
//         });
//     })
//     .then(uint8Array => {
//         modifyMap(uint8Array, routeId);
//     })
//     .catch(error => {
//         console.error('Error downloading map: ', error);
//     });
// }

async function modifyMap(uint8Array, points) {
    // console.log(">>>>>>>>>>>>>>>>>>>>> jsonresponse:")
    // console.log(jsonResponse.data);
    
    // let points = jsonResponse.data.find(r => r.id == routeId).points;
    console.log(points);
    points.sort((p1, p2) => {
        return p1.position - p2.position;
    });
    // console.log("Punkty: ");
    // console.log(points);
    // console.log("ścieżka: ", routeId);

    const pdfDoc = await PDFLib.PDFDocument.load(uint8Array);
    const pages = pdfDoc.getPages();
    const firstPage = pages[0];

    const { width, height } = firstPage.getSize();
    console.log("height: ", height);
    console.log("width: ", width);

    let circleSize = 15;
    let thickness = 2;
    let color = PDFLib.rgb(1,0,1);
    

    for(let i = 1; i < points.length; i++) {

        // let circleSize = 15;
        // let thickness = 2;
        // let color = PDFLib.rgb(1,0,0);

        let start = getPDFCoords(points[i-1]);
        let end = getPDFCoords(points[i]);

        let deltaX = start.x-end.x;
        let deltaY = start.y-end.y;
        let distance = Math.sqrt(deltaX*deltaX+deltaY*deltaY);
        
        let XLineCorrection = deltaX*circleSize/distance;
        let YLineCorrection = deltaY*circleSize/distance;

        firstPage.drawCircle({
            x: start.x,
            y: start.y,
            size: circleSize,
            borderWidth: thickness,
            borderColor: color
        })

        firstPage.drawLine({
            start:{x: start.x - XLineCorrection, y: start.y - YLineCorrection},
            end: {x: end.x + XLineCorrection, y: end.y + YLineCorrection},
            thickness: thickness,
            color: color
        })

        firstPage.drawCircle({
            x: end.x,
            y: end.y,
            size: circleSize,
            borderWidth: thickness,
            borderColor: color
        })
    }

    const pdfBytes = await pdfDoc.save();
    const blob = new Blob([pdfBytes], { type: 'application/pdf' });
    const url = URL.createObjectURL(blob);

    // Tworzenie linku do pobrania
    const link = document.createElement('a');
    link.href = url;
    link.download = `mapa.pdf`;
    link.click();

}

function getPDFCoords(point) {
    let x = 0.26768 * point.easting - 0.00125 * point.northing - 123844.43;
    let y = 0.00299 * point.easting + 0.26643 * point.northing - 197788.31;
    return {x: x, y: y};
}
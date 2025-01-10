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

    console.log(firstPage);

    // const { width, height } = firstPage.getSize();
    // console.log("height: ", height);
    // console.log("width: ", width);

    let circleSize = 15;
    let thickness = 2;
    let color = PDFLib.rgb(1,0,1);

    
    
    for(let i = 0; i < pages.length; i++) {
        // const page = firstPage;
        const page = pages[i];

        const { width, height } = page.getSize();
        console.log("height: ", height);
        console.log("width: ", width);
        for(let j = 1; j < points.length; j++) {
            
            // let circleSize = 15;
            // let thickness = 2;
            // let color = PDFLib.rgb(1,0,0);
            let start;
            let end;
            if(i == 0) {
                start = getPDFCoords(points[j-1]);
                end = getPDFCoords(points[j]);
            }
            else {
                start = getPDFCoords2(points[j-1]);
                end = getPDFCoords2(points[j]);
            }
    
            
    
            let deltaX = start.x-end.x;
            let deltaY = start.y-end.y;
            let distance = Math.sqrt(deltaX*deltaX+deltaY*deltaY);
            
            let XLineCorrection = deltaX*circleSize/distance;
            let YLineCorrection = deltaY*circleSize/distance;
    
            page.drawCircle({
                x: start.x,
                y: start.y,
                size: circleSize,
                borderWidth: thickness,
                borderColor: color
            })
    
            page.drawLine({
                start:{x: start.x - XLineCorrection, y: start.y - YLineCorrection},
                end: {x: end.x + XLineCorrection, y: end.y + YLineCorrection},
                thickness: thickness,
                color: color
            })
    
            page.drawCircle({
                x: end.x,
                y: end.y,
                size: circleSize,
                borderWidth: thickness,
                borderColor: color
            })
        }
    }

    // const mypoint = {easting: 466765.501, northing: 741742.612};
    // const mypoint = {easting: 464746.877, northing: 738854.585};

    // console.log(getPDFCoords(mypoint));

    // page.drawCircle({
    //     x: getPDFCoords(mypoint).x,
    //     y: getPDFCoords(mypoint).y,
    //     size: 1,
    //     borderWidth: 1,
    //     borderColor: color
    // })

    // firstPage.drawLine({
    //     start:{x: getPDFCoords(mypoint).x, y: getPDFCoords(mypoint).y},
    //     end: {x: 456, y: 369.8},
    //     thickness: thickness,
    //     color: color
    // })
    

    const pdfBytes = await pdfDoc.save();
    const blob = new Blob([pdfBytes], { type: 'application/pdf' });
    const url = URL.createObjectURL(blob);

    // Tworzenie linku do pobrania
    const link = document.createElement('a');
    link.href = url;
    link.download = `mapa.pdf`;
    link.click();

}

// function getPDFCoords(point) {
//     let x = 0.2779121 * point.easting + 0.0 * point.northing - 129142.473;
//     let y = 0.0 * point.easting + 0.2802259 * point.northing - 207029.100;
//     return {x: x, y: y};
// }

function getPDFCoords(point) {
    let x = 0.2838567 * point.easting + 0.0 * point.northing - 131909.927;
    let y = 0.0 * point.easting + 0.2837924 * point.northing - 209670.087;
    return {x: x, y: y};
}

function getPDFCoords2(point) {
    let x = 0.2670136 * point.easting + 0.0 * point.northing - 124458.643;
    let y = 0.0 * point.easting + 0.2667911 * point.northing - 196654.847;

    return {x: x, y: y};
}

// function getPDFCoords(point) {
//     let x = 0.282746 * point.easting - 0.021378 * point.northing - 115546.575;
//     let y = 0.021679 * point.easting + 0.283039 * point.northing - 218790.281;
//     return {x: x, y: y};
// }

// function getPDFCoords(point) {
//     let x = 0.283608 * point.easting - 0.022023 * point.northing - 115945.611;
//     let y = 0.020491 * point.easting + 0.283159 * point.northing - 218322.298;
//     console.log(point.code + "; " + point.easting + " / " + point.northing + "; " + x + " / " + y);
//     return {x: x, y: y};
// }

// function getPDFCoords(point) {
//     let x = 0.282330 * point.easting - 0.021516 * point.northing - 115251.324;
//     let y = 0.021724* point.easting + 0.283076 * point.northing - 219265.828;
//     console.log(point.code + "; " + point.easting + " / " + point.northing + "; " + x + " / " + y);
//     return {x: x, y: y};
// }

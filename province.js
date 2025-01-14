//result là 1 JSON string của mảng
function createArrayJsonObj(result) {
    // Parse the result if it's a JSON string
    var geoArray = JSON.parse(result);
    // console.log(geoArray);

    // Create an array to hold GeoJSON features
    var features = geoArray.map(function (geoJson) {
        return {
            "type": "Feature",
            "geometry": geoJson // Each geoJson is already a valid GeoJSON object
        };
    });

    // Construct the GeoJSON FeatureCollection
    var geoJsonObject = {
        "type": "FeatureCollection",
        "crs": {
            "type": "name",
            "properties": {
                "name": "EPSG:4326"
            }
        },
        "features": features // Use the array of features
    };

    return geoJsonObject;
}

// Hàm vẽ GeoJSON lên bản đồ
function provinceGeoJsonObj(paObjJson, vectorBufferLayer) {
    var vectorSource = new ol.source.Vector({
        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
            dataProjection: 'EPSG:4326',
            featureProjection: 'EPSG:3857'
        })
    });
    // console.log("Setting source for vectorBufferLayer with features:", vectorSource.getFeatures()); // Log the features being set

    vectorBufferLayer.setSource(vectorSource);
}

function displayProvince(result, vectorBufferLayer) {
    console.log("Array of geometry:", result);

    var objJson = createArrayJsonObj(result);
    console.log("Provinces GeoJSON Object:", objJson);

    bufferGeoJsonObj(objJson, vectorBufferLayer);
}

function fetchGeoBuffer(vectorBufferLayer, dateFromFocus) {
    $.ajax({
        type: "POST",
        url: "VN_pgsqlAPI.php", // Adjust the path if necessary
        data: {
            functionname: 'getGeoBuferToAjax',
            date: dateFromFocus
        },
        success: function (result) {
            displayBuffer(result, vectorBufferLayer); // Call the function with the result
        },
        error: function (xhr, status, error) {
            console.error("Error fetching geo buffer: " + error);
        }
    });
}
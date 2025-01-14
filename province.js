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
function provinceGeoJsonObj(paObjJson, vectorProvinceLayer) {
    var vectorSource = new ol.source.Vector({
        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
            dataProjection: 'EPSG:4326',
            featureProjection: 'EPSG:3857'
        })
    });
    // console.log("Setting source for vectorProvinceLayer with features:", vectorSource.getFeatures()); // Log the features being set

    vectorProvinceLayer.setSource(vectorSource);
}

function displayProvince(result, vectorProvinceLayer) {
    console.log("Provinces geometry array:", result);

    var objJson = createArrayJsonObj(result);
    console.log("Provinces GeoJSON Object:", objJson);

    provinceGeoJsonObj(objJson, vectorProvinceLayer);
}

function fetchGeoProvince(vectorProvinceLayer) {
    $.ajax({
        type: "POST",
        url: "pgsqlAPI.php", // Adjust the path if necessary
        data: {
            functionname: 'getGeoProvinceToAjax',
        },
        success: function (result) {
            displayProvince(result, vectorProvinceLayer); // Call the function with the result
        },
        error: function (xhr, status, error) {
            alert("Error fetching geo province: " + error);
        }
    });
}
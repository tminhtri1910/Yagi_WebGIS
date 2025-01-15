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

function updateHighlight(attribute) {
    // Lấy các giá trị min và max từ cơ sở dữ liệu (thông qua API)
    $.ajax({
        type: "POST",
        url: 'test_api.php',
        data: {
            functionname: 'getMinMaxValues'
        }, // Hàm PHP để lấy min, max
        success: function (result) {
            try {
                var minMaxValues = JSON.parse(result); // Giải mã kết quả từ server

                // Lấy min và max cho thuộc tính đã chọn
                var minValue, maxValue;
                switch (attribute) {
                    case 'number_of_deaths':
                        minValue = minMaxValues.min_deaths;
                        maxValue = minMaxValues.max_deaths;
                        break;
                    case 'number_of_injured':
                        minValue = minMaxValues.min_injured;
                        maxValue = minMaxValues.max_injured;
                        break;
                    case 'number_of_damaged_houses':
                        minValue = minMaxValues.min_damaged_houses;
                        maxValue = minMaxValues.max_damaged_houses;
                        break;
                    case 'number_of_flooded_houses':
                        minValue = minMaxValues.min_flooded_houses;
                        maxValue = minMaxValues.max_flooded_houses;
                        break;
                    default:
                        return;
                }

                // Lấy dữ liệu GeoJSON và cập nhật highlight theo thuộc tính đã chọn
                $.ajax({
                    type: "POST",
                    url: 'test_api.php',
                    data: {
                        functionname: 'getGeoJsonByAttribute',
                        attribute: attribute
                    },
                    success: function (result) {

                        var geoJson = JSON.parse(result);

                        if (geoJson.error) {
                            alert("Lỗi: " + geoJson.error);
                        } else {
                            // Duyệt qua từng feature trong GeoJSON và tính màu sắc
                            geoJson.features.forEach(function (feature) {
                                var value = feature.properties[attribute];
                                var color = getColorByValue(value, minValue, maxValue);
                                // console.log(color)
                                // Cập nhật màu sắc cho feature
                                feature.style = new ol.style.Style({
                                    fill: new ol.style.Fill({
                                        color: color
                                    }),
                                    stroke: new ol.style.Stroke({
                                        color: 'black', // Viền đen cho các đối tượng
                                        width: 2
                                    })
                                });
                            });

                            // Cập nhật dữ liệu cho layer vector
                            var vectorSource = new ol.source.Vector({
                                features: (new ol.format.GeoJSON()).readFeatures(geoJson, {
                                    dataProjection: 'EPSG:4326',
                                    featureProjection: 'EPSG:3857'
                                })
                            });
                            vectorLayer.setSource(vectorSource);
                        }
                    },
                    error: function () {
                        alert("Không thể tải dữ liệu GeoJSON.");
                    }
                });
            } catch (e) {
                alert("Lỗi khi xử lý dữ liệu: " + e.message);
            }
        },
        error: function () {
            alert("Không thể lấy giá trị min/max từ server.");
        }
    });
}
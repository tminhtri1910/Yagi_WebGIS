function createJsonObj(result) {
    geoJson = JSON.parse(result)

    var geoJsonObject = {
        "type": "FeatureCollection",
        "crs": {
            "type": "name",
            "properties": {
                "name": "EPSG:4326"
            }
        },
        "features": [{
            "type": "Feature",
            "geometry": geoJson // geoJson is already a valid GeoJSON object
        }]
    };
    return geoJsonObject;
}

// Hàm vẽ GeoJSON lên bản đồ
function highLightGeoJsonObj(paObjJson, vectorHighLightLayer) {
    var vectorSource = new ol.source.Vector({
        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
            dataProjection: 'EPSG:4326',
            featureProjection: 'EPSG:3857'
        })
    });
    vectorHighLightLayer.setSource(vectorSource);
}

function highLightObj(result, vectorHighLightLayer) {
    console.log("Geometry: ", result);

    var objJson = createJsonObj(result); //This is a built-in JavaScript function that takes a JSON string as input and converts it into a JavaScript object
    console.log("Highlight GeoJSON Object:",objJson)

    highLightGeoJsonObj(objJson, vectorHighLightLayer);
}

function displayObjInfo(result) {
    $("#info").html(result);
}

function fetchHighLight(myPoint, vectorHighLightLayer){
    $.ajax({
        type: "POST",
        url: "VN_pgsqlAPI.php",
        data: {
            functionname: 'getGeoVNToAjax',
            paPoint: myPoint
        },
        success: function (result, status, error) {
            if (result == "null") {
                alert("Không tìm thấy vùng quanh điểm đã chọn");
                return;
            }
            highLightObj(result, vectorHighLightLayer);
        },

        error: function (req, status, error) {
            alert(req + " " + status + " " + error);
            // console.error('Ajax error:', error)
        }
    });

    $.ajax({
        type: "POST",
        url: "VN_pgsqlAPI.php",
        data: {
            functionname: 'getInfoVNToAjax',
            paPoint: myPoint
        },
        success: function (result, status, error) {
            console.log(result); // Kiểm tra kết quả trả về
            if (result == "null") {
                return;
            }
            displayObjInfo(result);
        },

        error: function (req, status, error) {
            alert(req + " " + status + " " + error);
            // console.error('Ajax error:', error)
        }
    });
}
var vectorLayers = [];  // Mảng để lưu trữ tất cả các lớp vector

// Hàm cập nhật highlight theo thuộc tính
function updateHighLightByAttribute(attribute, map) {
    var minValue, maxValue;
    // Lấy các giá trị min và max từ cơ sở dữ liệu (thông qua API)
    $.ajax({
        type: "POST",
        url: 'pgsqlAPI.php',
        data: { functionname: 'getMinMaxValues' },  // Hàm PHP để lấy min, max
        success: function (result) {
            try {
                var minMaxValues = JSON.parse(result);  // Giải mã kết quả từ server

                // Lấy min và max cho thuộc tính đã chọn
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
            }
            catch (e) {
                alert("Lỗi khi xử lý dữ liệu: " + e.message);
            }
        },
        error: function () {
            alert("Không thể lấy giá trị min/max từ server.");
        }
    });

    // Lấy dữ liệu GeoJSON và cập nhật highlight theo thuộc tính đã chọn
    $.ajax({
        type: "POST",
        url: 'pgsqlAPI.php',
        data: {
            functionname: 'getGeoJsonByAttribute',
            attribute: attribute
        },
        success: function (result) {
            console.log("Filter by attribute array:",result);

            var geoJson = JSON.parse(result);
            console.log("Filter by attribute GeoJSON Obj:", geoJson);
            
            if (geoJson.error) {
                alert("Lỗi: " + geoJson.error);
            } else {
                // Xóa các lớp vector cũ trước khi thêm lớp mới
                vectorLayers.forEach(function (layer) {
                    map.removeLayer(layer);
                });
                vectorLayers = [];  // Reset lại mảng các lớp

                // Duyệt qua từng feature trong GeoJSON và tạo lớp vector riêng biệt cho mỗi feature
                geoJson.features.forEach(function (feature) {
                    var value = feature.properties[attribute];
                    var color = getColorByValue(value, minValue, maxValue);

                    // Tạo một lớp vector riêng biệt cho mỗi feature
                    var featureLayer = new ol.layer.Vector({
                        source: new ol.source.Vector({
                            features: [new ol.format.GeoJSON().readFeature(feature, {
                                dataProjection: 'EPSG:4326',
                                featureProjection: 'EPSG:3857'
                            })]
                        }),
                        style: new ol.style.Style({
                            fill: new ol.style.Fill({
                                color: color
                            }),
                            stroke: new ol.style.Stroke({
                                color: 'black',  // Viền đen cho các đối tượng
                                width: 2
                            })
                        })
                    });

                    vectorLayers.push(featureLayer);  // Thêm lớp vào mảng
                    map.addLayer(featureLayer);  // Thêm lớp vào bản đồ
                });
            }
        },
        error: function () {
            alert("Không thể tải dữ liệu GeoJSON.");
        }
    });
}

// Hàm tính màu dựa trên giá trị
function getColorByValue(value, minValue, maxValue) {
    // Kiểm tra nếu giá trị không phải là null
    if (value === null) {
        return 'rgba(200, 200, 200, 0.5)'; // Màu xám cho giá trị null
    }

    // Tránh chia cho 0 nếu minValue và maxValue bằng nhau
    if (minValue === maxValue) {
        return 'rgba(200, 200, 200, 0.5)'; // Trả về màu xám nếu không có sự khác biệt giữa min và max
    }

    // Chuẩn hóa giá trị (scale từ 0 đến 1)
    let normalizedValue = (value - minValue) / (maxValue - minValue);

    // Đảm bảo giá trị chuẩn hóa nằm trong khoảng 0 đến 1
    normalizedValue = Math.max(0, Math.min(1, normalizedValue));

    // if (normalizedValue <= 0.5) {
    //     var red = Math.floor(normalizedValue * 2 * 255);  // Màu đỏ tăng dần
    //     var green = 255;
    //     var blue = 120;  // Giữ màu xanh dương bằng 0
    // }
    // // Sử dụng giá trị chuẩn hóa để quyết định màu sắc (gradient từ vàng đến đỏ)
    // else {
    //     var red = 255;  // Màu đỏ tăng dần
    //     var green = Math.floor((1-normalizedValue) * 2 * 255);  // Màu xanh lá giảm dần
    //     var blue = 0;  // Giữ màu xanh dương bằng 0
    // }

    var alpha = normalizedValue + 0.3;
    // Trả về màu sắc dưới dạng rgba
    return `rgba(255,0,0, ${alpha})`;
}
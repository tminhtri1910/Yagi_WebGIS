<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text-html" ; charset="UTF-8">
    <title>Bản đồ Yagi</title>

    <link rel="stylesheet" href="openlayers.css" type="text/css" />
    <link rel="stylesheet" href="timeline.css" type="text/css" />
    <!-- <link rel="stylesheet" href="bootstrap4.6.0.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="openlayers.js" type="text/javascript"></script>
    <script src="jquery3.7.1.js" type="text/javascript"></script>

    <script src="yagi.js" type="text/javascript"></script>
    <script src="province.js" type="text/javascript"></script>
    <script src="highlight.js" type="text/javascript"></script>
</head>

<body onload="initialize_map();">
    <div class="container p-3">
        <div class="row text-center justify-content-center mb-5">
            <div class="col-xl-10 col-lg-8">
                <h2 class="fw-bold">Đường đi và ảnh hưởng của bão Yagi</h2>
                <!-- <p class="text-muted">We’re very proud of the path we’ve taken. Explore the history that made us the company we are today.</p> -->
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="timeline-steps" data-aos="fade-up">
                    <div class="timeline-step">
                        <a href="#" class="timeline-content" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" title="" data-bs-content="And here's some amazing content. It's very engaging. Right?" data-bs-original-title="2004">
                            <div class="inner-circle"></div>
                            <p class="h6 mt-3 mb-1">2024-08-30</p>
                        </a>
                    </div>

                    <div class="timeline-step">
                        <a href="#" class="timeline-content" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" title="" data-bs-content="And here's some amazing content. It's very engaging. Right?" data-bs-original-title="2004">
                            <div class="inner-circle"></div>
                            <p class="h6 mt-3 mb-1">2024-08-31</p>
                        </a>
                    </div>

                    <div class="timeline-step">
                        <a href="#" class="timeline-content" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" title="" data-bs-content="And here's some amazing content. It's very engaging. Right?" data-bs-original-title="2004">
                            <div class="inner-circle"></div>
                            <p class="h6 mt-3 mb-1">2024-09-01</p>
                        </a>
                    </div>

                    <div class="timeline-step">
                        <a href="#" class="timeline-content" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" title="" data-bs-content="And here's some amazing content. It's very engaging. Right?" data-bs-original-title="2004">
                            <div class="inner-circle"></div>
                            <p class="h6 mt-3 mb-1">2024-09-02</p>
                        </a>
                    </div>

                    <div class="timeline-step">
                        <a href="#" class="timeline-content" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" title="" data-bs-content="And here's some amazing content. It's very engaging. Right?" data-bs-original-title="2004">
                            <div class="inner-circle"></div>
                            <p class="h6 mt-3 mb-1">2024-09-03</p>
                        </a>
                    </div>

                    <div class="timeline-step">
                        <a href="#" class="timeline-content" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" title="" data-bs-content="And here's some amazing content. It's very engaging. Right?" data-bs-original-title="2004">
                            <div class="inner-circle"></div>
                            <p class="h6 mt-3 mb-1">2024-09-04</p>
                        </a>
                    </div>

                    <div class="timeline-step">
                        <a href="#" class="timeline-content" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" title="" data-bs-content="And here's some amazing content. It's very engaging. Right?" data-bs-original-title="2004">
                            <div class="inner-circle"></div>
                            <p class="h6 mt-3 mb-1">2024-09-05</p>
                        </a>
                    </div>

                    <div class="timeline-step">
                        <a href="#" class="timeline-content" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" title="" data-bs-content="And here's some amazing content. It's very engaging. Right?" data-bs-original-title="2004">
                            <div class="inner-circle"></div>
                            <p class="h6 mt-3 mb-1">2024-09-06</p>
                        </a>
                    </div>

                    <div class="timeline-step mb-0">
                        <a href="#" class="timeline-content" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" title="" data-bs-content="And here's some amazing content. It's very engaging. Right?" data-bs-original-title="2004">
                            <div class="inner-circle"></div>
                            <p class="h6 mt-3 mb-1">2024-09-07</p>
                            <!-- <p class="h6 text-muted mb-0 mb-lg-0">In Fortune 500</p> -->
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table>
        <tr>
            <td>
                <div id="info"></div>
                <div id="map" class="px-3" style="width: 85vw; height: 80vh;"></div>
            </td>
            <td>
                <button id="myButton" type="button" class="btn btn-primary fw-bold">Ảnh hưởng của bão</button>
            </td>
        </tr>
    </table>

    <script>
        var format = 'image/png';
        var map;

        //tọa độ khu vực 
        var minX = 108.19999694824219;
        var minY = 4.199999809265137;
        var maxX = 132.8000030517578;
        var maxY = 20.30000114440918;
        var cenX = (minX + maxX) / 2;
        var cenY = (minY + maxY) / 2;
        var mapLat = cenY;
        var mapLng = cenX;
        var mapDefaultZoom = 5;

        function initialize_map() {
            <?php #region Khởi tạo các layer nền
            ?>
            var layerBG = new ol.layer.Tile({
                source: new ol.source.OSM({})
            });

            //layer các tỉnh VN
            var layerVNM_1 = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/example/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'gadm41_vnm_1',
                    },
                    serverType: 'geoserver'
                })
            });

            //Layer các tỉnh Philip
            var layerPHL_1 = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/example/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'gadm41_phl_1',
                    },
                    serverType: 'geoserver'
                })
            });

            var layerYagi = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/example/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'yagistorm',
                    },
                    serverType: 'geoserver'
                })
            });

            var viewMap = new ol.View({
                center: ol.proj.fromLonLat([mapLng, mapLat]),
                zoom: mapDefaultZoom
            });

            map = new ol.Map({
                target: 'map',
                layers: [layerBG, layerVNM_1, layerPHL_1],
                view: viewMap
            });
            <?php #endregion
            ?>

            <?php #region style cho các vector layer được vẽ thêm vào 
            ?>
            // Định nghĩa style cho đối tượng
            var styles = {
                'MultiPolygon': new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: 'rgba(19, 128, 237, 0.5)'
                    }),
                    stroke: new ol.style.Stroke({
                        color: 'navy',
                        width: 2
                    })
                }),

                'Polygon': new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: 'rgba(60, 179, 113, 0.5)'
                    }),
                    stroke: new ol.style.Stroke({
                        color: 'darkgreen',
                        width: 1
                    })
                }),

                'Point': new ol.style.Style({
                    image: new ol.style.Icon({
                        anchor: [0.5, 1.0],
                        anchorXUnits: "fraction",
                        anchorYUnits: "fraction",
                        src: "hurricane-2-svgrepo-com.svg"
                    })
                })
            };

            var styleHighLight = new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: 'rgba(186, 0, 0, 0.8)'
                    }),
                    stroke: new ol.style.Stroke({
                        color: 'darkred',
                        width: 3
                    })
                });

            var styleFunction = function(feature) {
                return styles[feature.getGeometry().getType()];
            };

            // Tạo lớp vector để vẽ các đối tượng lên bản đồ
            var vectorBufferLayer = new ol.layer.Vector({
                style: styleFunction
            });
            var vectorProvinceLayer = new ol.layer.Vector({
                style: styleFunction
            });
            var vectorHighLightLayer = new ol.layer.Vector({
                style: styleHighLight
            });
            map.addLayer(vectorBufferLayer);
            map.addLayer(vectorProvinceLayer);
            map.addLayer(vectorHighLightLayer);
            <?php #endregion
            ?>

            // Use the PHP variable in JavaScript
            var dateFromURL = "<?php
                                // Retrieve the date parameter from the GET request
                                $date = isset($_GET['date']) ? $_GET['date'] : "";
                                echo $date;
                                ?>";

            var currentlyFocusedElement = null; // Variable to store the currently focused element
            var loop = 0;
            var singleClickListenerAdded = false; // Flag to track if the listener has been added

            $(document).ready(function() { //ensures that the Document Object Model(DOM) is fully loaded before attaching event handlers.
                // Attach focus event handler to elements with class 'timeline-content'
                $('.timeline-content').on('focus', function() {
                    if (currentlyFocusedElement)
                        // Remove the 'blurred' class of previously focused element when focused new element
                        currentlyFocusedElement.classList.remove('blurred');
                    currentlyFocusedElement = this; // Update the currently focused element

                    // // Check if the source exists and clear it if it does
                    // const source = vectorHighLightLayer.getSource();
                    // if (source) {
                    //     source.clear(); // Clear existing features if the source is set
                    // }
                    // $("#info").html(''); // Clear existing content of the #info element

                    console.log('focused');

                    //update dateFromFocus
                    takeDate(this);
                    // Call the function to fetch the geo buffer
                    fetchGeoBuffer(vectorBufferLayer, dateFromFocus);

                    // Add click listener only if it hasn't been added before
                    // if (!singleClickListenerAdded) {

                    //     singleClickListenerAdded = true; // Set the flag to true after adding the listener
                    // }
                });

                map.on('singleclick', function(evt) {
                    var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                    var lon = lonlat[0];
                    var lat = lonlat[1];
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';

                    fetchHighLight(myPoint, vectorHighLightLayer);
                    loop = loop + 1;
                    // console.log(loop);
                });
                
                // Attach blur event handler to elements with class 'timeline-content'
                $('.timeline-content').on('blur', function() {
                    // Add the 'blurred' class when focus is lost
                    this.classList.add('blurred');
                    console.log('blurred');
                });

                //Click event handler for button
                $('#myButton').on('click', function() {
                    // alert('Button');
                    fetchGeoProvince(vectorProvinceLayer);
                });
            });


        };
    </script>

    <script>
        var dateFromFocus = "";

        function takeDate(element) {
            dateFromFocus = element.querySelector('p').textContent;
            // console.log("Date from focus:", dateFromFocus); // Log the date for verification
        }

        function updateUrl(element) {
            // Get the text content of the <p> tag
            var content = element.querySelector('p').textContent;
            // return content;

            // Get the current URL
            var currentUrl = window.location.href;

            // Create a new URL object
            var url = new URL(currentUrl);

            // Remove any existing fragment
            url.hash = ''; // This removes the fragment identifier

            // Append the content as a query parameter
            url.searchParams.set('date', content); // Use 'set' to update or add the parameter

            // Redirect to the new URL
            window.location.href = url.toString(); // Redirect to the updated URL
        }
    </script>


</body>

</html>
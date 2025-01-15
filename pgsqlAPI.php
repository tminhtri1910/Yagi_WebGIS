<?php
    if (isset($_POST['functionname']))
    {
        $paPDO = initDB();
        $paSRID = '4326';

        if (isset($_POST['paPoint'])) ;
        $functionname = $_POST['functionname'];

        switch($functionname){
            case 'getGeoHighLightToAjax':
                $paPoint = $_POST['paPoint'];
                $aResult = getGeoHighLightToAjax($paPDO, $paSRID, $paPoint);
                break;
            case 'getInfoToAjax':
                $paPoint = $_POST['paPoint'];
                $aResult = getInfoToAjax($paPDO, $paSRID, $paPoint);
                break;
            case 'getGeoBuferToAjax':
                $date = $_POST['date'];
                $aResult = getGeoBuferToAjax($paPDO, $date);
                break;
            case 'getGeoProvinceToAjax':
                $aResult = getGeoProvinceToAjax($paPDO);
                break;
            case 'getGeoJsonByAttribute':
                $attribute = $_POST['attribute'];
                $aResult = getGeoJsonByAttribute($paPDO, $attribute);
                break;
            case 'getMinMaxValues':
                $aResult = getMinMaxValues($paPDO); // Gọi hàm getMinMaxValues
                break;
            default:
                $aResult = "default null";
        } 

        // var_dump( $aResult); 
        echo $aResult;//trả về string
        
        closeDB($paPDO);
    }

    function initDB()
    {
        //Ket noi CSDL
        try{ 
            $paPDO = new PDO('pgsql:host=localhost;dbname=CSDLKhongGian;port=5432', 'postgres', 'Tmt@2003');
        }

        catch(PDOException $e){
            die("Loi ket noi CSDL: ".$e->getMessage());
        }
        return $paPDO;
    }

    function query ($paPDO, $paSQLStr)
    {
        try
        {
            //Khai bao exception
            $paPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Su dung Prepare
            $stmt = $paPDO->prepare($paSQLStr);
            //Thuc thi cau truy van
            $stmt->execute();

            //Khai bao fetch kieu mang ket hop
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            //Lay danh sach ket qua
            $paResult = $stmt->fetchAll();
            return $paResult;
        }

        catch(PDOException $e){
            echo "Truy vấn thất bại: ".$e->getMessage();
            return null;
        }
    }

    function closeDB($paPDO){
        //Ngat ket noi
        $paPDO = null;
    }

    function getGeoHighLightToAjax ($paPDO, $paSRID, $paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);

        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo 
                        FROM gadm41_vnm_1 JOIN vndamage ON vndamage.province = gadm41_vnm_1.name_1 
                        WHERE ST_Within(ST_GeomFromText('" . $paPoint . "', " . $paSRID . "), geom)
                    UNION ALL
                    SELECT ST_AsGeoJson(geom) as geo 
                        FROM gadm41_phl_1 JOIN pldamage ON pldamage.province = gadm41_phl_1.name_1
                        WHERE ST_Within(ST_GeomFromText('" . $paPoint . "', " . $paSRID . "), geom)";
        $result = query ($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            return $result[0]['geo'];
        }
        else
            return "null";
    }

    function getInfoToAjax($paPDO, $paSRID,$paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);

        $mySQLStr = "SELECT name_1, number_of_deaths, number_of_injured, number_of_damaged_houses, number_of_flooded_houses, ST_Area(geom::geography)/1000000 as shape_area
                        FROM gadm41_vnm_1 JOIN vndamage ON vndamage.province = gadm41_vnm_1.name_1 
                        WHERE ST_Within(ST_GeomFromText('" . $paPoint . "', " . $paSRID . "), geom)
                    UNION ALL 
                    SELECT name_1, number_of_deaths, number_of_injured, number_of_damaged_houses, number_of_flooded_houses, ST_Area(geom::geography)/1000000 as shape_area
                        FROM gadm41_phl_1 JOIN pldamage ON pldamage.province = gadm41_phl_1.name_1
                        WHERE ST_Within(ST_GeomFromText('" . $paPoint . "', " . $paSRID . "), geom)";
        $result = query ($paPDO, $mySQLStr);

        if ($result != null)
        {
            $resFin = '<h4 style="color:darkred; font-weight:bold;">Thiệt hại của từng tỉnh</h4>';
            $resFin = $resFin.'<table>';
            // Lặp kết quả
            foreach ($result as $item) {
                // $resFin = $resFin.'<tr><td>GID_1: '.$item['gid_1'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Tỉnh: '.$item['name_1'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Số người chết: '.($item['number_of_deaths'] ?? 'Không có dữ liệu').'</td></tr>';
                $resFin = $resFin.'<tr><td>Số người bị thương: '.($item['number_of_injured'] ?? 'Không có dữ liệu').'</td></tr>';
                $resFin = $resFin.'<tr><td>Số nhà bị phá hủy: '.($item['number_of_damaged_houses'] ?? 'Không có dữ liệu').'</td></tr>';
                $resFin = $resFin. '<tr><td>Số nhà bị ngập: '.($item['number_of_flooded_houses'] ?? 'Không có dữ liệu').'</td></tr>';
                // $resFin = $resFin.'<tr><td>Chu vi: '.$item['shape_leng'] . '&deg' . '</td></tr>';
                $resFin = $resFin.'<tr><td>Diện tích: '.$item['shape_area'] . ' km&sup2;' . '</td></tr>';
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        else
            return "null";
    }

    function getGeoBuferToAjax($paPDO, $date)
    {
        //echo $paPoint;
        //echo "<br>";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as point, ST_AsGeoJson(ST_Buffer(geom::geography,3000*intensity)) as buffer,
                        ST_AsGeoJson(ST_Makeline(ARRAY(SELECT geom FROM yagistorm WHERE dtg <= '" . $date . "' ORDER BY gid ))) AS line
                     FROM yagistorm
                     WHERE dtg <= '".$date."'";
        $result = query($paPDO, $mySQLStr);
        // echo $result;
        if ($result != null) {
            $geoArray = []; // Initialize an array to hold all geo results
            // Lặp kết quả
            foreach ($result as $item) {
                $geoArray[] = json_decode($item['point']); // Decode each JSON string to GeoJSON object then add to the array
                $geoArray[] = json_decode($item['buffer']); // Decode each JSON string to GeoJSON object then add to the array
                $geoArray[] = json_decode($item['line']); // Decode each JSON string to GeoJSON object then add to the array
            }
            return json_encode($geoArray);  // Return the array as a JSON string
        } else
            return "null";
    }   

    function getGeoProvinceToAjax($paPDO)
    {
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as province from vndamage 
                        JOIN gadm41_vnm_1 ON vndamage.province = gadm41_vnm_1.name_1 
                    UNION ALL
                    SELECT ST_AsGeoJson(geom) as province from pldamage
                        JOIN gadm41_phl_1 ON pldamage.province = gadm41_phl_1.name_1";
        $result = query($paPDO, $mySQLStr);
        // return $result;
        if ($result != null) {
            $geoArray = []; // Initialize an array to hold all geo results
            // Lặp kết quả
            foreach ($result as $item) {
                $geoArray[] = json_decode($item['province']); // Decode each JSON string to GeoJSON object then add to the array
            }
            return json_encode($geoArray);  // Return the array as a JSON string
        } else
            return "null";
    } 
    
    function getGeoJsonByAttribute($paPDO, $attribute)
    {
        // Câu lệnh SQL JOIN giữa VNDamage và GADM41_VNM_1 để lấy dữ liệu không gian và thuộc tính
        $mySQLStr = "SELECT ST_AsGeoJson(g.geom) as geo, d.province, d.\"$attribute\" 
                        FROM \"gadm41_vnm_1\" g
                        JOIN \"vndamage\" d ON g.name_1 = d.province
                        WHERE d.\"$attribute\" IS NOT NULL
                    UNION ALL
                    SELECT ST_AsGeoJson(g.geom) as geo, d.province, d.\"$attribute\" 
                        FROM \"gadm41_phl_1\" g
                        JOIN \"pldamage\" d ON g.name_1 = d.province
                        WHERE d.\"$attribute\" IS NOT NULL";  // Kiểm tra nếu thuộc tính không null

        $result = query($paPDO, $mySQLStr);

        $features = [];
        if ($result != null) {
            foreach ($result as $item) {
                $geoJson = json_decode($item['geo']);
                if ($geoJson) {
                    // Thêm thuộc tính vào đối tượng GeoJSON
                    $geoJson->properties = [
                        'province' => $item['province'],
                        $attribute => $item[$attribute]  // Thêm giá trị thuộc tính vào phần properties
                    ];
                    $features[] = $geoJson;
                }
            }
        }
        return json_encode([
            "type" => "FeatureCollection",
            "features" => $features
        ]);
    }

    function getMinMaxValues($paPDO)
    {
        // Truy vấn để lấy min, max của từng thuộc tính
        $sql = "SELECT 
                    MIN(number_of_deaths) AS min_deaths, MAX(number_of_deaths) AS max_deaths,
                    MIN(number_of_injured) AS min_injured, MAX(number_of_injured) AS max_injured,
                    MIN(number_of_damaged_houses) AS min_damaged_houses, MAX(number_of_damaged_houses) AS max_damaged_houses,
                    MIN(number_of_flooded_houses) AS min_flooded_houses, MAX(number_of_flooded_houses) AS max_flooded_houses
                FROM (
                    SELECT number_of_deaths, number_of_injured, number_of_damaged_houses, number_of_flooded_houses
                        FROM vndamage
                    UNION ALL
                    SELECT number_of_deaths, number_of_injured, number_of_damaged_houses, number_of_flooded_houses
                        FROM pldamage
                    )";
        $result = query($paPDO, $sql);

        if ($result != null) {
            return json_encode($result[0]);  // Trả về kết quả min, max dưới dạng JSON
        } else {
            return json_encode(["error" => "Không tìm thấy dữ liệu"]);
        }
    }
?>
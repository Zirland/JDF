<?php
ini_set('memory_limit', '-1');
set_time_limit(0);

require_once 'dbconnect.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$link) {
    echo "Error: Unable to connect to database." . PHP_EOL;
    echo "Reason: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$file    = 'linemap.csv';
$current = "id,lon,lat,seq\n";
file_put_contents($file, $current);

$query16 = "SELECT DISTINCT shape_id FROM shape WHERE shape_id IN (SELECT shape_id FROM shapecheck) ORDER BY shape_id;";
if ($result16 = mysqli_query($link, $query16)) {
    while ($row16 = mysqli_fetch_row($result16)) {
        $shape_id = $row16[0];

        $query21 = "SELECT DISTINCT route_id FROM trip WHERE trip_id IN (SELECT trip_id FROM shapecheck WHERE shape_id = '$shape_id');";
        if ($result21 = mysqli_query($link, $query21)) {
            while ($row21 = mysqli_fetch_row($result21)) {
                $route_short_name = $row21[0];

                $query26 = "SELECT tvartrasy FROM shapetvary WHERE shape_id = '$shape_id';";
                if ($result26 = mysqli_query($link, $query26)) {
                    while ($row26 = mysqli_fetch_row($result26)) {
                        $tvartrasy = $row26[0];

                        $i        = 0;
                        $prevstop = "";

                        $output = explode('|', $tvartrasy);
                        $output = array_filter($output);

                        foreach ($output as $prujstop) {
                            $query38 = "SELECT `path` FROM du WHERE (stop1 = '$prevstop') AND (stop2 = '$prujstop');";
                            if ($result38 = mysqli_query($link, $query38)) {
                                while ($row38 = mysqli_fetch_row($result38)) {
                                    $linie = $row38[0];
                                    $body = explode(';', $linie);

                                    foreach ($body as $point) {
                                        $sourad = explode(',', $point);
                                        $lon    = $sourad[0];
                                        $lat    = $sourad[1];

                                        if ($lon != "" && $lat != "") {
                                            $i       = $i + 1;
                                            $current = "$route_short_name" . "_$shape_id, $lon, $lat, $i\n";
                                            file_put_contents($file, $current, FILE_APPEND);
                                        }
                                    }
                                }
                            }
                            $prevstop = $prujstop;
                        }
                    }
                }
            }
        }
    }
}

mysqli_close($link);

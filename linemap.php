<?php
$link = mysqli_connect('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

$file    = 'linemap.csv';
$current = "id,lon,lat,seq\n";
file_put_contents($file, $current);

$query13 = "SELECT DISTINCT shape_id FROM shape WHERE shape_id IN (SELECT shape_id FROM shapecheck) ORDER BY shape_id;";
if ($result13 = mysqli_query($link, $query13)) {
    while ($row13 = mysqli_fetch_row($result13)) {
        $shape_id = $row13[0];

        $query19 = "SELECT DISTINCT route_short_name FROM trip LEFT JOIN route ON route.route_id = trip.route_id WHERE trip_id IN (SELECT trip_id FROM shapecheck WHERE shape_id = '$shape_id');";
        if ($result19 = mysqli_query($link, $query19)) {
            while ($row19 = mysqli_fetch_row($result19)) {
                $route_short_name = $row19[0];

                $query25 = "SELECT tvartrasy FROM shapetvary WHERE shape_id = '$shape_id';";
                if ($result25 = mysqli_query($link, $query25)) {
                    while ($row25 = mysqli_fetch_row($result25)) {
                        $tvartrasy = $row25[0];

                        $i        = 0;
                        $prevstop = "";

                        $output = explode('|', $tvartrasy);

                        foreach ($output as $prujstop) {
                            $query107  = "SELECT path FROM du WHERE (stop1 = '$prevstop') AND (stop2 = '$prujstop');";
                            $result235 = mysqli_query($link, $query107);

                            $pom235 = mysqli_fetch_row($result235);
                            $linie  = $pom235[0];

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
                            $prevstop = $prujstop;
                        }
                    }
                }
            }
        }
    }
}

mysqli_close($link);

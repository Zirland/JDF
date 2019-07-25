<?php
$link = mysqli_connect('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

$file    = 'linemap.csv';
$current = "route_short_name,trip_id,lat,lon,seq\n";
file_put_contents($file, $current);

$akt_route = "SELECT route_id,route_short_name FROM route WHERE active='1';";
if ($result69 = mysqli_query($link, $akt_route)) {
    while ($row69 = mysqli_fetch_row($result69)) {
        $route_id         = $row69[0];
        $route_short_name = $row69[1];

        $akt_trip = "SELECT route_id,trip_id,shape_id FROM trip WHERE ((route_id = '$route_id') AND (active='1'));";
        if ($result85 = mysqli_query($link, $akt_trip)) {
            while ($row85 = mysqli_fetch_row($result85)) {
                $route_id  = $row85[0];
                $trip_id   = $row85[1];
                $tvartrasy = $row85[2];

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

                        $i       = $i + 1;
                        $current = "$route_short_name, $trip_id, $lat, $lon, $i\n";
                        file_put_contents($file, $current, FILE_APPEND);
                    }
                    $prevstop = $prujstop;
                }
            }
        }
    }
}

mysqli_close($link);

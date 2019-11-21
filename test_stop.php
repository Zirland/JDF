<?php
include 'header.php';

$route = $_GET['route'];

if (substr($route, 0, 1) == "F") {
    $clnroute = substr($route, 1);
}

$query55 = "SELECT trip_id FROM trip WHERE route_id IN (SELECT route_id FROM route WHERE agency_id = '25332473');";
if ($result55 = mysqli_query($link, $query55)) {
    while ($row55 = mysqli_fetch_row($result55)) {
        $trip_id = $row55[0];

        $query221 = "SELECT stoptime.stop_id, stoptime.stop_sequence, stop.stop_name FROM stoptime LEFT JOIN stop ON stoptime.stop_id = stop.stop_id WHERE trip_id = '$trip_id' ORDER BY stop_sequence;";
        if ($result221 = mysqli_query($link, $query221)) {
            while ($row221 = mysqli_fetch_row($result221)) {
                $stop_id       = $row221[0];
                $stop_sequence = $row221[1];
                $stop_name     = $row221[2];

                $previous = $stop_sequence - 1;

                $query228 = "UPDATE stoptime SET stop_headsign = '$stop_name' WHERE trip_id = '$trip_id' AND stop_sequence = '$previous';";
                echo "$query228<br/>";
                $prikaz228 = mysqli_query($link, $query228);
            }
        }
    }
}

mysqli_close($link);

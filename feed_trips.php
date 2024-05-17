<?php
ini_set('memory_limit', '-1');
set_time_limit(0);
date_default_timezone_set('Europe/Prague');

require_once 'dbconnect.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$link) {
    echo "Error: Unable to connect to database." . PHP_EOL;
    echo "Reason: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$oblast = $_GET["oblast"];

$fixdate = date_create();
$dnes = date_format($fixdate, 'Y-m-d');

$prirustek = "63 days";
date_add($fixdate, date_interval_create_from_date_string($prirustek));
$konec = date_format($fixdate, 'Y-m-d');

$current = "";

$akt_trip = "SELECT route_id, trip_id, trip_headsign, direction_id, shape_id,wheelchair_accessible, bikes_allowed FROM trip WHERE active='1' AND trip_id LIKE '$oblast%' AND route_id IN (SELECT route_id FROM `route` WHERE active='1') AND trip_id IN (SELECT trip_id FROM jizdy WHERE datum>='$dnes' AND datum<'$konec');";
if ($result85 = mysqli_query($link, $akt_trip)) {
    while ($row85 = mysqli_fetch_row($result85)) {
        $route_id = $row85[0];
        $trip_id = $row85[1];
        $trip_headsign = $row85[2];
        $direction_id = $row85[3];
        $shape_tvar = $row85[4];
        $wheelchair_accessible = $row85[5];
        $bikes_allowed = $row85[6];

        $matice = "";

        for ($i = 0; $i < 63; $i++) {
            $matice[$i] = 0;
        }

        $dnes_date = date_create_from_format('Y-m-d', $dnes);

        $query64 = "SELECT datum FROM jizdy WHERE (trip_id='$trip_id' AND datum>='$dnes' AND datum<'$konec');";
        if ($result64 = mysqli_query($link, $query64)) {
            while ($row64 = mysqli_fetch_row($result64)) {
                $datum = $row64[0];

                $day_date = date_create_from_format('Y-m-d', $datum);
                $daydiff = date_diff($dnes_date, $day_date);
                $dnu = $daydiff->days;

                $matice[$dnu] = 1;
            }
        }

        $vtydnu = date_format($dnes_date, "w");

        $weekmatrix = "";
        $vyjimky_0 = [];
        $vyjimky_1 = [];

        for ($k = 0; $k < 7; $k++) {
            $linecount = 0;
            $except_0 = [];
            $except_1 = [];
            for ($j = $k; $j < strlen($matice); $j += 7) {
                $hodnota = (int) $matice[$j];
                $linecount += $hodnota;
                if ($hodnota == 0) {
                    array_push($except_0, $j);
                } else {
                    array_push($except_1, $j);
                }
            }
            $matrix_value = ($linecount >= 4) ? 1 : 0;
            $weekmatrix .= $matrix_value;
            if ($matrix_value == 0) {
                foreach ($except_1 as $vyjimka) {
                    array_push($vyjimky_1, $vyjimka);
                }
            } else {
                foreach ($except_0 as $vyjimka) {
                    array_push($vyjimky_0, $vyjimka);
                }
            }
        }

        sort($vyjimky_0);
        sort($vyjimky_1);

        $adjust = substr($weekmatrix, -$vtydnu + 1) . substr($weekmatrix, 0, -$vtydnu + 1);
        $dec = bindec($adjust) + 1;

        $service_id = $dec;

        $except = "";
        foreach ($vyjimky_1 as $den) {
            $except1 = date_create();
            $posun1 = "$den days";
            date_add($except1, date_interval_create_from_date_string($posun1));
            $day_id = date_format($except1, "z");
            $except .= "_" . $day_id . "(1)";
        }

        foreach ($vyjimky_0 as $den) {
            $except0 = date_create();
            $posun0 = "$den days";
            date_add($except0, date_interval_create_from_date_string($posun0));
            $day_id = date_format($except0, "z");
            $except .= "_" . $day_id . "(2)";
        }

        $query119 = "SELECT id FROM calendar_except WHERE vyjimky='$except';";
        if ($result119 = mysqli_query($link, $query119)) {
            $radku = mysqli_num_rows($result119);
            if ($radku == 0) {
                $vyjimka_query = "INSERT INTO calendar_except (vyjimky) VALUES ('$except');";
                $vlozvyjimku = mysqli_query($link, $vyjimka_query);
                $except_id = mysqli_insert_id($link);
            } else {
                while ($row119 = mysqli_fetch_row($result119)) {
                    $except_id = $row119[0];
                }
            }
            mysqli_free_result($result119);
        }

        if ($except_id != "") {
            $service_id .= "J_" . $except_id;
        }

        $cal_query = "INSERT INTO cal_use (trip_id, kalendar) VALUES ('$trip_id', '$service_id');";
        $mark_cal = mysqli_query($link, $cal_query);

        $query152 = "SELECT shape_id FROM shapetvary WHERE tvartrasy = '$shape_tvar';";
        if ($result152 = mysqli_query($link, $query152)) {
            $radku = mysqli_num_rows($result152);
            if ($radku == 0) {
                $vloztrasu = mysqli_query($link, "INSERT INTO shapetvary (tvartrasy, complete) VALUES ('$shape_tvar', '0');");
                $shape_id = mysqli_insert_id($link);
            } else {
                while ($row152 = mysqli_fetch_row($result152)) {
                    $shape_id = $row152[0];
                }
            }
            mysqli_free_result($result152);
        }

        $trip_short = '';

        $query162 = "SELECT stop_name FROM `stop` WHERE stop_id = '$trip_headsign';";
        if ($result162 = mysqli_query($link, $query162)) {
            while ($row162 = mysqli_fetch_row($result162)) {
                $head_name = $row162[0];
            }
        }

        $current .= "$route_id,$service_id,$trip_id,\"$head_name\",\"$trip_short\",$direction_id,J$shape_id,$wheelchair_accessible,$bikes_allowed\n";

        $query171 = "INSERT INTO shapecheck (trip_id, shape_id) VALUES ('$trip_id', '$shape_id');";
        $zapistrasy = mysqli_query($link, $query171);
    }

    $file = 'trips.txt';
    file_put_contents($file, $current, FILE_APPEND);
    mysqli_free_result($result85);
}

$current = "";
$i = 0;

$tripstops = "SELECT trip_id,arrival_time,departure_time,stop_id,stop_sequence,stop_headsign,pickup_type,drop_off_type FROM stoptime WHERE trip_id IN (SELECT trip_id FROM trip WHERE active='1' AND trip_id LIKE '$oblast%' AND route_id IN (SELECT route_id FROM `route` WHERE active='1') AND trip_id IN (SELECT trip_id FROM jizdy WHERE datum>='$dnes' AND datum<'$konec'));";
if ($result166 = mysqli_query($link, $tripstops)) {
    while ($row166 = mysqli_fetch_row($result166)) {
        $trip_id = $row166[0];
        $arrival_time = $row166[1];
        $departure_time = $row166[2];
        $stop_id = $row166[3];
        $stop_sequence = $row166[4];
        $stop_headsign = $row166[5];
        $pickup_type = $row166[6];
        $drop_off_type = $row166[7];
        $precise = 1;

        $query162 = "SELECT stop_name FROM `stop` WHERE stop_id = '$stop_headsign';";
        if ($result162 = mysqli_query($link, $query162)) {
            while ($row162 = mysqli_fetch_row($result162)) {
                $stop_head_name = $row162[0];
            }
        }

        $current .= "$trip_id,$arrival_time,$departure_time,$stop_id,$stop_sequence,\"$stop_head_name\",$pickup_type,$drop_off_type,$precise\n";

        $query187 = "INSERT INTO stop_use (trip_id, stop_id) VALUES ('$trip_id', '$stop_id');";
        $mark_stop = mysqli_query($link, $query187);
        if (!$mark_stop) {
            echo mysqli_error($link) . "\n";
        }
    }
    $file = 'stop_times.txt';
    file_put_contents($file, $current, FILE_APPEND);
    mysqli_free_result($result166);
}

mysqli_close($link);
<?php
ini_set('memory_limit', '-1');
set_time_limit(0);
include 'header.php';

$route = $_GET['route'];

if (substr($route, 0, 1) == "F") {
    $clnroute = substr($route, 1);
}

$query10  = "DELETE FROM stoptime WHERE trip_id IN (SELECT trip_id FROM trip WHERE route_id = '$route');";
$prikaz10 = mysqli_query($link, $query10);

$prevtrip = 0;
$query16  = "SELECT triptimesDB.zastav_id, triptimesDB.trip_id, triptimesDB.trip_pk, triptimesDB.prijezd, triptimesDB.odjezd, triptimesDB.km, linestopsDB.stop_poradi FROM triptimesDB LEFT JOIN linestopsDB ON triptimesDB.zastav_id=linestopsDB.stop_id WHERE trip_id LIKE '$clnroute%' AND linestopsDB.stop_smer = '0' GROUP BY triptimesDB.trip_id,triptimesDB.zastav_id,triptimesDB.trip_pk,triptimesDB.km,triptimesDB.prijezd,triptimesDB.odjezd ORDER BY triptimesDB.trip_id,linestopsDB.stop_poradi,triptimesDB.prijezd,triptimesDB.odjezd;";
$result16 = mysqli_query($link, $query16);
if ($result16) {
    while ($row16 = mysqli_fetch_row($result16)) {
        $zastav_id = $row16[0];
        $trip_id   = $row16[1];
        $trip_pk   = $row16[2];
        $prijezd   = $row16[3];
        $odjezd    = $row16[4];
        $km        = $row16[5];

        if ($prevtrip != $trip_id) {
            $s = 1;
        }
        $znam    = 0;
        $query29 = "SELECT stop_pk, stop_vazba FROM linestopsDB WHERE stop_id = '$zastav_id';";
        $pomoc29 = mysqli_fetch_row(mysqli_query($link, $query29));
        $stop_pk = $pomoc29[0];
        if (strpos($stop_pk, '-18-') !== false) {
            // na znamení
            $znam = 1;
        }

        $pickup  = 0;
        $dropoff = 0;
        if (strpos($trip_pk, '-22-') !== false) {
            // jen pro nástup
            $dropoff = 1;
        }
        if (strpos($trip_pk, '-21-') !== false) {
            // jen pro výstup
            $pickup = 1;
        }

        if ($znam == 1) {
            $pickup  = 3;
            $dropoff = 3;
        }
        if ($prijezd == '') {
            $prijezd = $odjezd;
        }
        if ($odjezd == '') {
            $odjezd = $prijezd;
        }

        $arrival   = substr($prijezd, 0, 2) . ":" . substr($prijezd, -2) . ":00";
        $departure = substr($odjezd, 0, 2) . ":" . substr($odjezd, -2) . ":00";

        $stop_id    = $pomoc29[1];

        if ($stop_id != '') {
            $query487  = "INSERT INTO stoptime (trip_id, arrival_time, departure_time, stop_id, stop_sequence, pickup_type, drop_off_type, shape_dist_traveled, zastav_id) VALUES ('$trip_id', '$arrival', '$departure', '$stop_id', '$s', '$pickup', '$dropoff', '$km', '$zastav_id');";
            $prikaz487 = mysqli_query($link, $query487);
            $s         = $s + 1;
        }
        $prevtrip = $trip_id;
    }
}

$prevtrip = 0;
$query66  = "SELECT triptimesDB.zastav_id, triptimesDB.trip_id, triptimesDB.trip_pk, triptimesDB.prijezd, triptimesDB.odjezd, triptimesDB.km, linestopsDB.stop_poradi FROM triptimesDB LEFT JOIN linestopsDB ON triptimesDB.zastav_id=linestopsDB.stop_id WHERE trip_id LIKE '$clnroute%' AND linestopsDB.stop_smer = '1' GROUP BY triptimesDB.trip_id,triptimesDB.zastav_id,triptimesDB.trip_pk,triptimesDB.km,triptimesDB.prijezd,triptimesDB.odjezd ORDER BY triptimesDB.trip_id,linestopsDB.stop_poradi DESC,triptimesDB.prijezd,triptimesDB.odjezd;";
$result66 = mysqli_query($link, $query66);
if ($result66) {
    while ($row66 = mysqli_fetch_row($result66)) {
        $zastav_id = $row66[0];
        $trip_id   = $row66[1];
        $trip_pk   = $row66[2];
        $prijezd   = $row66[3];
        $odjezd    = $row66[4];
        $km        = $row66[5];

        if ($prevtrip != $trip_id) {
            $s = 1;
        }
        $znam    = 0;
        $query29 = "SELECT stop_pk, stop_vazba FROM linestopsDB WHERE stop_id = '$zastav_id';";
        $pomoc29 = mysqli_fetch_row(mysqli_query($link, $query29));
        $stop_pk = $pomoc29[0];
        if (strpos($stop_pk, '-18-') !== false) {
            // na znamení
            $znam = 1;
        }

        $pickup  = 0;
        $dropoff = 0;
        if (strpos($trip_pk, '-22-') !== false) {
            // jen pro nástup
            $dropoff = 1;
        }
        if (strpos($trip_pk, '-21-') !== false) {
            // jen pro výstup
            $pickup = 1;
        }

        if ($znam == 1) {
            $pickup  = 3;
            $dropoff = 3;
        }
        if ($prijezd == '') {
            $prijezd = $odjezd;
        }
        if ($odjezd == '') {
            $odjezd = $prijezd;
        }

        $arrival   = substr($prijezd, 0, 2) . ":" . substr($prijezd, -2) . ":00";
        $departure = substr($odjezd, 0, 2) . ":" . substr($odjezd, -2) . ":00";

        $stop_id    = $pomoc29[1];

        if ($stop_id != '') {
            $query115  = "INSERT INTO stoptime (trip_id, arrival_time, departure_time, stop_id, stop_sequence, pickup_type, drop_off_type, shape_dist_traveled, zastav_id) VALUES ('$trip_id', '$arrival', '$departure', '$stop_id', '$s', '$pickup', '$dropoff', '$km', '$zastav_id');";
            $prikaz115 = mysqli_query($link, $query115);
            $s         = $s + 1;
        }
        $prevtrip = $trip_id;
    }
}

$query55 = "SELECT trip_id FROM trip WHERE route_id = '$route';";
if ($result55 = mysqli_query($link, $query55)) {
    while ($row55 = mysqli_fetch_row($result55)) {
        $trip_id = $row55[0];

        $query90 = "SELECT MIN(arrival_time),MAX(arrival_time) FROM stoptime WHERE (trip_id = '$trip_id');";
        if ($result90 = mysqli_query($link, $query90)) {
            while ($row90 = mysqli_fetch_row($result90)) {
                $start  = $row90[0];
                $finish = $row90[1];

                $start_hour  = substr($start, 0, 2);
                $finish_hour = substr($finish, 0, 2);

                if ($start_hour == "00" && $finish_hour == "23") {
                    $query6 = "SELECT arrival_time,departure_time,stop_sequence FROM stoptime WHERE (trip_id = '$trip_id');";
                    if ($result6 = mysqli_query($link, $query6)) {
                        while ($row6 = mysqli_fetch_row($result6)) {
                            $arrival_time   = $row6[0];
                            $departure_time = $row6[1];
                            $stop_sequence  = $row6[2];

                            $arr_hour = substr($arrival_time, 0, 2);
                            $arr_rest = substr($arrival_time, 2);

                            $arr_hour = (int)$arr_hour;
                            if ($arr_hour == 0) {
                                $arr_hour = 24;
                            }
                            $arrival_time = $arr_hour . $arr_rest;

                            $dep_hour = substr($departure_time, 0, 2);
                            $dep_rest = substr($departure_time, 2);

                            $dep_hour = (int)$dep_hour;
                            if ($dep_hour == 0) {
                                $dep_hour = 24;
                            }
                            $departure_time = $dep_hour . $dep_rest;

                            $query26  = "UPDATE stoptime SET arrival_time = '$arrival_time', departure_time = '$departure_time' WHERE (trip_id = '$trip_id' AND stop_sequence = '$stop_sequence');";
                            $prikaz27 = mysqli_query($link, $query26);
                        }
                    }
                    $r       = 1;
                    $query31 = "SELECT trip_id,stop_id,arrival_time FROM stoptime WHERE trip_id='$trip_id' ORDER BY arrival_time;";
                    if ($result31 = mysqli_query($link, $query31)) {
                        while ($row31 = mysqli_fetch_row($result31)) {
                            $trip_id = $row31[0];
                            $stop_id = $row31[1];
                            $arrival = $row31[2];

                            $query38  = "UPDATE stoptime SET stop_sequence=$r WHERE trip_id='$trip_id' AND stop_id='$stop_id' AND arrival_time='$arrival';";
                            $prikaz38 = mysqli_query($link, $query38);
                            $r        = $r + 1;
                        }
                    }
                }
            }
        }

        $query546  = "SELECT stop_id FROM stoptime WHERE trip_id = '$trip_id' AND stop_sequence = (SELECT MAX(stop_sequence) FROM stoptime WHERE trip_id='$trip_id');";
        $row546    = mysqli_fetch_row(mysqli_query($link, $query546));
        $headsign  = $row546[0];
        $query549  = "UPDATE trip SET trip_headsign='$headsign' WHERE trip_id='$trip_id';";
        $prikaz549 = mysqli_query($link, $query549);

        $shape     = "";
        $query1156 = "SELECT stop_id FROM stoptime WHERE trip_id='$trip_id' ORDER BY stop_sequence;";
        if ($result1156 = mysqli_query($link, $query1156)) {
            while ($row1156 = mysqli_fetch_row($result1156)) {
                $stop_id = $row1156[0];
                $shape .= $stop_id . "|";
            }
        }
        $query1163  = "UPDATE trip SET shape_id='$shape', active = '1' WHERE trip_id='$trip_id';";
        $prikaz1163 = mysqli_query($link, $query1163);

        $query221 = "SELECT stop_id, stop_sequence FROM stoptime WHERE trip_id = '$trip_id' ORDER BY stop_sequence;";
        if ($result221 = mysqli_query($link, $query221)) {
            while ($row221 = mysqli_fetch_row($result221)) {
                $stop_id       = $row221[0];
                $stop_sequence = $row221[1];

                $previous = $stop_sequence - 1;

                $query228  = "UPDATE stoptime SET stop_headsign = '$stop_id' WHERE trip_id = '$trip_id' AND stop_sequence = '$previous';";
                $prikaz228 = mysqli_query($link, $query228);
            }
        }
    }
}

mysqli_close($link);

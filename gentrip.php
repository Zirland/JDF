<?php
ini_set('memory_limit', '-1');
set_time_limit(0);
include 'header.php';

$route = $_GET['route'];

if (substr($route, 0, 1) == "F") {
    $clnroute = substr($route, 1);
}

$query12 = "DELETE FROM stoptime WHERE trip_id IN (SELECT trip_id FROM trip WHERE route_id = '$route');";
$prikaz12 = mysqli_query($link, $query12);

$prevtrip = 0;
$query16 = "SELECT triptimesDB.zastav_id, triptimesDB.trip_id, triptimesDB.trip_pk, triptimesDB.prijezd, triptimesDB.odjezd, triptimesDB.km, linestopsDB.stop_poradi FROM triptimesDB LEFT JOIN linestopsDB ON triptimesDB.zastav_id=linestopsDB.stop_id WHERE trip_id LIKE '$clnroute%' AND linestopsDB.stop_smer = '0' GROUP BY triptimesDB.trip_id,triptimesDB.zastav_id,triptimesDB.trip_pk,triptimesDB.km,triptimesDB.prijezd,triptimesDB.odjezd ORDER BY triptimesDB.trip_id,linestopsDB.stop_poradi,triptimesDB.prijezd,triptimesDB.odjezd;";
$result16 = mysqli_query($link, $query16);
if ($result16) {
    while ($row16 = mysqli_fetch_row($result16)) {
        $zastav_id = $row16[0];
        $trip_id = $row16[1];
        $trip_pk = $row16[2];
        $prijezd = $row16[3];
        $odjezd = $row16[4];
        $km = $row16[5];

        if ($prevtrip != $trip_id) {
            $s = 1;
        }
        $znam = 0;
        $query31 = "SELECT stop_pk, stop_vazba FROM linestopsDB WHERE stop_id = '$zastav_id';";
        $pomoc31 = mysqli_fetch_row(mysqli_query($link, $query31));
        $stop_pk = $pomoc31[0];
        if (strpos($stop_pk, '-18-') !== false) {
            // na znamení
            $znam = 1;
        }

        $pickup = 0;
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
            $pickup = 3;
            $dropoff = 3;
        }
        if ($prijezd == '') {
            $prijezd = $odjezd;
        }
        if ($odjezd == '') {
            $odjezd = $prijezd;
        }

        $arrival = substr($prijezd, 0, 2) . ":" . substr($prijezd, -2) . ":00";
        $departure = substr($odjezd, 0, 2) . ":" . substr($odjezd, -2) . ":00";

        $stop_id = $pomoc31[1];

        if ($stop_id != '') {
            $query67 = "INSERT INTO stoptime (trip_id, arrival_time, departure_time, stop_id, stop_sequence, pickup_type, drop_off_type, shape_dist_traveled, zastav_id) VALUES ('$trip_id', '$arrival', '$departure', '$stop_id', '$s', '$pickup', '$dropoff', '$km', '$zastav_id');";
            $prikaz67 = mysqli_query($link, $query67);
            $s++;
        }
        $prevtrip = $trip_id;
    }
}

$prevtrip = 0;
$query76 = "SELECT triptimesDB.zastav_id, triptimesDB.trip_id, triptimesDB.trip_pk, triptimesDB.prijezd, triptimesDB.odjezd, triptimesDB.km, linestopsDB.stop_poradi FROM triptimesDB LEFT JOIN linestopsDB ON triptimesDB.zastav_id=linestopsDB.stop_id WHERE trip_id LIKE '$clnroute%' AND linestopsDB.stop_smer = '1' GROUP BY triptimesDB.trip_id,triptimesDB.zastav_id,triptimesDB.trip_pk,triptimesDB.km,triptimesDB.prijezd,triptimesDB.odjezd ORDER BY triptimesDB.trip_id,linestopsDB.stop_poradi DESC,triptimesDB.prijezd,triptimesDB.odjezd;";
$result76 = mysqli_query($link, $query76);
if ($result76) {
    while ($row76 = mysqli_fetch_row($result76)) {
        $zastav_id = $row76[0];
        $trip_id = $row76[1];
        $trip_pk = $row76[2];
        $prijezd = $row76[3];
        $odjezd = $row76[4];
        $km = $row76[5];

        if ($prevtrip != $trip_id) {
            $s = 1;
        }
        $znam = 0;
        $query91 = "SELECT stop_pk, stop_vazba FROM linestopsDB WHERE stop_id = '$zastav_id';";
        $pomoc91 = mysqli_fetch_row(mysqli_query($link, $query91));
        $stop_pk = $pomoc91[0];
        if (strpos($stop_pk, '-18-') !== false) {
            // na znamení
            $znam = 1;
        }

        $pickup = 0;
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
            $pickup = 3;
            $dropoff = 3;
        }
        if ($prijezd == '') {
            $prijezd = $odjezd;
        }
        if ($odjezd == '') {
            $odjezd = $prijezd;
        }

        $arrival = substr($prijezd, 0, 2) . ":" . substr($prijezd, -2) . ":00";
        $departure = substr($odjezd, 0, 2) . ":" . substr($odjezd, -2) . ":00";

        $stop_id = $pomoc91[1];

        if ($stop_id != '') {
            $query127 = "INSERT INTO stoptime (trip_id, arrival_time, departure_time, stop_id, stop_sequence, pickup_type, drop_off_type, shape_dist_traveled, zastav_id) VALUES ('$trip_id', '$arrival', '$departure', '$stop_id', '$s', '$pickup', '$dropoff', '$km', '$zastav_id');";
            $prikaz127 = mysqli_query($link, $query127);
            $s++;
        }
        $prevtrip = $trip_id;
    }
}

$query135 = "SELECT trip_id FROM trip WHERE route_id = '$route';";
if ($result135 = mysqli_query($link, $query135)) {
    while ($row135 = mysqli_fetch_row($result135)) {
        $trip_id = $row135[0];

        $query140 = "SELECT MIN(arrival_time),MAX(arrival_time) FROM stoptime WHERE (trip_id = '$trip_id');";
        if ($result140 = mysqli_query($link, $query140)) {
            while ($row140 = mysqli_fetch_row($result140)) {
                $start = $row140[0];
                $finish = $row140[1];

                $start_hour = substr($start, 0, 2);
                $finish_hour = substr($finish, 0, 2);

                if ($start_hour == "00" && $finish_hour == "23") {
                    $query150 = "SELECT arrival_time,departure_time,stop_sequence FROM stoptime WHERE (trip_id = '$trip_id');";
                    if ($result150 = mysqli_query($link, $query150)) {
                        while ($row150 = mysqli_fetch_row($result150)) {
                            $arrival_time = $row150[0];
                            $departure_time = $row150[1];
                            $stop_sequence = $row150[2];

                            $arr_hour = substr($arrival_time, 0, 2);
                            $arr_rest = substr($arrival_time, 2);

                            $arr_hour = (int) $arr_hour;
                            if ($arr_hour == 0) {
                                $arr_hour = 24;
                            }
                            $arrival_time = "$arr_hour$arr_rest";

                            $dep_hour = substr($departure_time, 0, 2);
                            $dep_rest = substr($departure_time, 2);

                            $dep_hour = (int) $dep_hour;
                            if ($dep_hour == 0) {
                                $dep_hour = 24;
                            }
                            $departure_time = "$dep_hour$dep_rest";

                            $query175 = "UPDATE stoptime SET arrival_time = '$arrival_time', departure_time = '$departure_time' WHERE (trip_id = '$trip_id' AND stop_sequence = '$stop_sequence');";
                            $prikaz175 = mysqli_query($link, $query175);
                        }
                    }
                    $r = 1;
                    $query180 = "SELECT trip_id,stop_id,arrival_time FROM stoptime WHERE trip_id='$trip_id' ORDER BY arrival_time;";
                    if ($result180 = mysqli_query($link, $query180)) {
                        while ($row180 = mysqli_fetch_row($result180)) {
                            $trip_id = $row180[0];
                            $stop_id = $row180[1];
                            $arrival = $row180[2];

                            $query187 = "UPDATE stoptime SET stop_sequence=$r WHERE trip_id='$trip_id' AND stop_id='$stop_id' AND arrival_time='$arrival';";
                            $prikaz187 = mysqli_query($link, $query187);
                            $r++;
                        }
                    }
                }
            }
        }

        $query196 = "SELECT stop_id FROM stoptime WHERE trip_id = '$trip_id' AND stop_sequence = (SELECT MAX(stop_sequence) FROM stoptime WHERE trip_id='$trip_id');";
        $row196 = mysqli_fetch_row(mysqli_query($link, $query196));
        $headsign = $row196[0];
        $query199 = "UPDATE trip SET trip_headsign='$headsign' WHERE trip_id='$trip_id';";
        $prikaz199 = mysqli_query($link, $query199);

        $shape = "";
        $query203 = "SELECT stop_id FROM stoptime WHERE trip_id='$trip_id' ORDER BY stop_sequence;";
        if ($result203 = mysqli_query($link, $query203)) {
            while ($row203 = mysqli_fetch_row($result203)) {
                $stop_id = $row203[0];
                $shape .= "$stop_id|";
            }
        }
        $query210 = "UPDATE trip SET shape_id='$shape', active = '1' WHERE trip_id='$trip_id';";
        $prikaz210 = mysqli_query($link, $query210);

        $query213 = "SELECT stop_id, stop_sequence FROM stoptime WHERE trip_id = '$trip_id' ORDER BY stop_sequence;";
        if ($result213 = mysqli_query($link, $query213)) {
            while ($row213 = mysqli_fetch_row($result213)) {
                $stop_id = $row213[0];
                $stop_sequence = $row213[1];

                $previous = $stop_sequence - 1;

                $query221 = "UPDATE stoptime SET stop_headsign = '$stop_id' WHERE trip_id = '$trip_id' AND stop_sequence = '$previous';";
                $prikaz221 = mysqli_query($link, $query221);
            }
        }
    }
}

mysqli_close($link);

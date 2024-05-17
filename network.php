<?php
ini_set('memory_limit', '-1');
set_time_limit(0);

include 'header.php';

$oldtrip = 0;
$oldstop = 0;
$old_lat = 0;
$old_lon = 0;

$query12 = "SELECT stop_id, trip_id FROM stoptime WHERE trip_id IN (SELECT trip_id FROM trip WHERE route_id IN (SELECT route_id FROM `route` WHERE (route_type = '3') OR (route_type = '11'))) ORDER BY trip_id, stop_sequence;";
if ($result12 = mysqli_query($link, $query12)) {
    while ($row12 = mysqli_fetch_row($result12)) {
        $stop_id = $row12[0];
        $trip_id = $row12[1];

        $du_id = '';
        $query19 = "SELECT du_id FROM du WHERE stop1 = '$oldstop' AND stop2 = '$stop_id';";
        if ($result19 = mysqli_query($link, $query19)) {
            $hit = mysqli_num_rows($result19);
            while ($row19 = mysqli_fetch_row($result19)) {
                $du_id = $row19[0];
            }
        }

        if ($hit == 0) {
            $query28 = "SELECT stop_lat, stop_lon FROM `stop` WHERE stop_id = '$stop_id';";
            $result28 = mysqli_query($link, $query28);
            while ($row28 = mysqli_fetch_row($result28)) {
                $stop_lat = $row28[0];
                $stop_lon = $row28[1];
            }

            $prujezdy = $old_lon . "," . $old_lat . ";" . $stop_lon . "," . $stop_lat;
            if ($trip_id == $oldtrip) {
                $insert_query = "INSERT INTO du (stop1, stop2, `path`, final) VALUES ('$oldstop', '$stop_id', '$prujezdy', '0');";
                $insert_action = mysqli_query($link, $insert_query);
                $du_id = mysqli_insert_id($link);
            }
        }

        if ($du_id != '' && $oldstop != $stop_id) {
            $query44 = "INSERT INTO du_use (du_id, trip_id) VALUES ('$du_id', '$trip_id');";
            $prikaz44 = mysqli_query($link, $query44);
        }

        $oldtrip = $trip_id;
        $oldstop = $stop_id;
        $old_lat = $stop_lat;
        $old_lon = $stop_lon;
    }
}

$oldtrip = 0;
$oldstop = 0;
$old_lat = 0;
$old_lon = 0;

$query60 = "SELECT stop_id, trip_id FROM stoptime WHERE trip_id IN (SELECT trip_id FROM trip WHERE route_id IN (SELECT route_id FROM `route` WHERE (route_type != '3') AND (route_type != '11'))) ORDER BY trip_id, stop_sequence;";
if ($result60 = mysqli_query($link, $query60)) {
    while ($row60 = mysqli_fetch_row($result60)) {
        $stop_id = $row60[0];
        $trip_id = $row60[1];

        $du_id = '';
        $query67 = "SELECT du_id FROM du WHERE stop1 = '$oldstop' AND stop2 = '$stop_id';";
        if ($result67 = mysqli_query($link, $query67)) {
            $hit = mysqli_num_rows($result67);
            while ($row67 = mysqli_fetch_row($result67)) {
                $du_id = $row67[0];
            }
        }

        if ($hit == 0) {
            $query76 = "SELECT stop_lat, stop_lon FROM `stop` WHERE stop_id = '$stop_id';";
            $result76 = mysqli_query($link, $query76);
            while ($row76 = mysqli_fetch_row($result76)) {
                $stop_lat = $row76[0];
                $stop_lon = $row76[1];
            }

            $prujezdy = $old_lon . "," . $old_lat . ";" . $stop_lon . "," . $stop_lat;
            if ($trip_id == $oldtrip) {
                $insert_query = "INSERT INTO du (stop1, stop2, `path`, final) VALUES ('$oldstop', '$stop_id', '$prujezdy', '2');";
                $insert_action = mysqli_query($link, $insert_query);
                $du_id = mysqli_insert_id($link);
            }
        }

        if ($du_id != '' && $oldstop != $stop_id) {
            $query92 = "INSERT INTO du_use (du_id, trip_id) VALUES ('$du_id', '$trip_id');";
            $prikaz92 = mysqli_query($link, $query92);
        }

        $oldtrip = $trip_id;
        $oldstop = $stop_id;
        $old_lat = $stop_lat;
        $old_lon = $stop_lon;
    }
}

$query111 = "DELETE FROM du WHERE (stop1 = '0') OR (stop1 = stop2);";
echo "112: $query111<br/>";
$prikaz111 = mysqli_query($link, $query111);

include 'footer.php';

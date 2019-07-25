<?php
include 'header.php';

$trip_id = @$_GET['id'];

$query6 = "SELECT arrival_time,departure_time,stop_sequence FROM stoptime WHERE (trip_id = '$trip_id');";
if ($result6 = mysqli_query($link, $query6)) {
    while ($row6 = mysqli_fetch_row($result6)) {
        $arrival_time   = $row6[0];
        $departure_time = $row6[1];
        $stop_sequence  = $row6[2];

        $arr_hour = substr($arrival_time, 0, 2);
        $arr_rest = substr($arrival_time, 2);

        $arr_hour = (int)$arr_hour;
        if ($arr_hour == 0) {$arr_hour = 24;}
        $arrival_time = $arr_hour . $arr_rest;

        $dep_hour = substr($departure_time, 0, 2);
        $dep_rest = substr($departure_time, 2);

        $dep_hour = (int)$dep_hour;
        if ($dep_hour == 0) {$dep_hour = 24;}
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

        $r = $r + 1;
    }
}

$query45 = "SELECT MAX(stop_sequence) FROM stoptime WHERE trip_id='$trip_id';";
$row45   = mysqli_fetch_row(mysqli_query($link, $query45));
$max     = $row45[0];

$query49  = "SELECT stop_name FROM stop WHERE stop_id IN (SELECT stop_id FROM stoptime WHERE trip_id = '$trip_id' AND stop_sequence = $max);";
$row49    = mysqli_fetch_row(mysqli_query($link, $query49));
$headsign = $row49[0];

$query53  = "UPDATE trip SET trip_headsign='$headsign' WHERE trip_id='$trip_id';";
$prikaz53 = mysqli_query($link, $query53);

$shape   = "";
$query57 = "SELECT stop_id FROM stoptime WHERE trip_id='$trip_id' ORDER BY stop_sequence;";
if ($result57 = mysqli_query($link, $query57)) {
    while ($row57 = mysqli_fetch_row($result57)) {
        $stop_id = $row57[0];
        $shape .= $stop_id . "|";
    }
}

$query65  = "UPDATE trip SET shape_id='$shape', active = '1' WHERE trip_id='$trip_id';";
$prikaz65 = mysqli_query($link, $query65);

include 'footer.php';

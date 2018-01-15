<?php
include 'header.php';

$trip_id = $_GET['id'];
$odd = $trip_id % 2;

$r = 1;

if ($odd == 1) {$query7 = "SELECT trip_id,stop_id,arrival_time FROM stoptime WHERE trip_id='$trip_id' ORDER BY arrival_time,zastav_id;";}
if ($odd == 0) {$query7 = "SELECT trip_id,stop_id,arrival_time FROM stoptime WHERE trip_id='$trip_id' ORDER BY arrival_time,zastav_id DESC;";}

if ($result7 = mysqli_query($link, $query7)) {
	while ($row7 = mysqli_fetch_row($result7)) {
		$trip_id = $row7[0];
		$stop_id = $row7[1];
		$arrival = $row7[2];
	
		$query14 = "UPDATE stoptime SET stop_sequence=$r WHERE trip_id='$trip_id' AND stop_id='$stop_id' AND arrival_time='$arrival';";
		$prikaz14 = mysqli_query($link, $query14);
			
		$r = $r+1;
	}
}

$query21 = "SELECT MAX(stop_sequence) FROM stoptime WHERE trip_id='$trip_id';";
$row21 = mysqli_fetch_row(mysqli_query($link, $query21));
$max = $row21[0];
$query24 = "SELECT stop_name FROM stop WHERE stop_id IN (SELECT stop_id FROM stoptime WHERE trip_id = '$trip_id' AND stop_sequence = $max);";
$row24 = mysqli_fetch_row(mysqli_query($link, $query24));
$headsign = $row24[0];
$query27 = "UPDATE trip SET trip_headsign='$headsign' WHERE trip_id='$trip_id';";
$prikaz27 = mysqli_query($link, $query27);

$shape="";
$query31 = "SELECT stop_id FROM stoptime WHERE trip_id='$trip_id' ORDER BY stop_sequence;";
if ($result31 = mysqli_query($link, $query31)) {
	while ($row31 = mysqli_fetch_row($result31)) {
		$stop_id = $row31[0];
		$shape.=$stop_id."|";
	}
}
$query38 = "UPDATE trip SET shape_id='$shape' WHERE trip_id='$trip_id';";
$prikaz38 = mysqli_query($link, $query38);

mysqli_close ($link);
?>

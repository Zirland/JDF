<?php
include 'header.php';

$query4 = "SELECT trip_id FROM trip;";
if ($result4 = mysqli_query($link, $query4)) {
	while ($row4 = mysqli_fetch_row($result4)) {
		$trip_id = $row4[0];

		$pomendstop = mysqli_fetch_row(mysqli_query($link, "SELECT MAX(stop_sequence) FROM stoptime WHERE (trip_id = '$trip_id');"));
		$endstopno = $pomendstop[0];

		$pomfinstop=mysqli_fetch_row(mysqli_query($link, "SELECT stop_id FROM stoptime WHERE (trip_id='$trip_id' AND stop_sequence='$endstopno');"));
		$finstopid=$pomfinstop[0];

		$query15 = "SELECT stop_name FROM stop WHERE stop_id='$finstopid';";
		$result15 = mysqli_query($link, $query15);
		$pomhead = mysqli_fetch_row($result15);
		$headsign = $pomhead[0];

		$query20="UPDATE trip SET trip_headsign='$headsign' WHERE trip_id='$trip_id';";
		$prikaz20=mysqli_query($link, $query20);
	}
}

include 'footer.php';
?>

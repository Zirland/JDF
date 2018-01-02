<?php
include 'header.php';

$query = "SELECT trip_id FROM trip;";
if ($result = mysqli_query($link, $query)) {
	while ($row = mysqli_fetch_row($result)) {
		$trip_id = $row[0];

		$pomendstop = mysqli_fetch_row(mysqli_query($link, "SELECT MAX(stop_sequence) FROM stoptime WHERE (trip_id = '$trip_id');"));
        	$endstopno = $pomendstop[0];

		$pomfinstop=mysqli_fetch_row(mysqli_query($link, "SELECT stop_id FROM stoptime WHERE (trip_id='$trip_id' AND stop_sequence='$endstopno');"));
		$finstop=$pomfinstop[0];

		$query180 = "SELECT stop_name FROM stop WHERE stop_id='$finstopid';";
		$result180 = mysqli_query($link, $query180);
	        $pomhead = mysqli_fetch_row($result180);
	        $headsign = $pomhead[0];

        	$query1701="UPDATE trip SET trip_headsign='$headsign' WHERE trip_id='$trip_id';";
		$prikaz1701=mysqli_query($link, $query1701);
	}
}

include 'footer.php';
?>

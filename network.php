<?php
$link = mysqli_connect ('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
	echo "Error: Unable to connect to MySQL.".PHP_EOL;
	echo "Debugging errno: ".mysqli_connect_errno ().PHP_EOL;
	exit;
}

$oldtrip = 0;

$query9 = "SELECT stop_id, trip_id FROM stoptime ORDER BY trip_id, stop_sequence;";
if ($result9 = mysqli_query ($link, $query9)) {
	while ($row9 = mysqli_fetch_row ($result9)) {
		$stop_id = $row9[0];
		$trip_id = $row9[1];
		
		if ($trip_id != $oldtrip) {
		    $oldstop = 0;
		}
		
		$insert = mysqli_query ($link, "INSERT INTO du (stop1, stop2) VALUES ('$oldstop', '$stop_id');");
		
		$oldtrip = $trip_id;
		$oldstop = $stop_id;
	}
}

$clean1 = mysqli_query ($link, "DELETE FROM du WHERE stop1=0;");

$clean2 = mysqli_query ($link, "DELETE FROM du WHERE du_id NOT IN (SELECT MAX(du_id) FROM du GROUP BY stop1, stop2 HAVING MAX(du_id) IS NOT NULL);");

mysqli_close ($link);
?>

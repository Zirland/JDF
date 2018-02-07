<?php
$link = mysqli_connect ('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
	echo "Error: Unable to connect to MySQL.".PHP_EOL;
	echo "Debugging errno: ".mysqli_connect_errno ().PHP_EOL;
	exit;
}

$oldtrip = 0;

$query9 = "SELECT stop_id, trip_id FROM stoptime ORDER BY trip_id, stop_sequence LIMIT 5;";
if ($result9 = mysqli_query ($link, $query9)) {
	while ($row9 = mysqli_fetch_row ($result9)) {
		$stop_id = $row9[0];
		$trip_id = $row9[1];
		
		if ($trip_id != $oldtrip) {
		    $oldstop = 0;
		}
		
		echo "$oldtrip | $trip_id<br/>";
		
		$oldtrip = $trip_id;
	}
}

mysqli_close ($link);
?>
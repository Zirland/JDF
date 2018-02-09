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
			$oldlat = "";
			$oldlon = "";
		}

		$coord = mysqli_fetch_row (mysqli_query ($link, "SELECT stop_lat, stop_lon FROM stop WHERE stop_id = '$stop_id';"));
		$stop_lat = $coord[0];
		$stop_lon = $coord[1];

		$prujezdy = $oldlon.",".$oldlat.";".$stop_lon.",".$stoplat;

		$insert_query = "INSERT INTO du (stop1, stop2, via, path, final) VALUES ('$oldstop', '$stop_id', '', '$prujezdy', '1');";
		$insert_action = mysqli_query ($link, $insert_query);

		$oldtrip = $trip_id;
		$oldstop = $stop_id;
		$oldlat = $stop_lat;
		$oldlon = $stop_lon;
	}
}

$clean1 = mysqli_query ($link, "DELETE FROM du WHERE stop1=0;");

$clean2 = mysqli_query ($link, "DELETE FROM du WHERE du_id NOT IN (SELECT MAX(du_id) FROM du GROUP BY stop1, stop2 HAVING MAX(du_id) IS NOT NULL);");

mysqli_close ($link);
?>

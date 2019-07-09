<?php
$link = mysqli_connect ('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
	echo "Error: Unable to connect to MySQL.".PHP_EOL;
	echo "Debugging errno: ".mysqli_connect_errno ().PHP_EOL;
	exit;
}

$oldtrip = 0;
$oldstop = 0;

$query12 = "SELECT stop_id, trip_id FROM stoptime WHERE trip_id IN (SELECT trip_id FROM trip WHERE route_id IN (SELECT route_id FROM route WHERE (route_type = 3) OR (route_type = 5))) ORDER BY trip_id, stop_sequence;";
if ($result12 = mysqli_query ($link, $query12)) {
	while ($row12 = mysqli_fetch_row ($result12)) {
		$stop_id = $row12[0];
		$trip_id = $row12[1];
		$stop_lat = 0;
		$stop_lon = 0;

		$query19 = "SELECT final FROM du WHERE stop1 = '$oldstop' AND stop2 = '$stop_id';";
		if ($result19 = mysqli_query ($link, $query19)) {
			$hit = mysqli_num_rows ($result19);
		}
		$coord = mysqli_fetch_row (mysqli_query ($link, "SELECT stop_lat, stop_lon FROM stop WHERE stop_id = '$stop_id';"));
		$stop_lat = $coord[0];
		$stop_lon = $coord[1];

		if ($hit == 0) {
			$prujezdy = $oldlon.",".$oldlat.";".$stop_lon.",".$stop_lat;
			if ($trip_id == $oldtrip) {
				$insert_query = "INSERT INTO du (stop1, stop2, path, final) VALUES ('$oldstop', '$stop_id', '$prujezdy', '0');";
				$insert_action = mysqli_query ($link, $insert_query);
			}
		}
		$oldtrip = $trip_id;
		$oldstop = $stop_id;
		$oldlat = $stop_lat;
		$oldlon = $stop_lon;
	}
}

$oldtrip = 0;
$oldstop = 0;

$query44 = "SELECT stop_id, trip_id FROM stoptime WHERE trip_id IN (SELECT trip_id FROM trip WHERE route_id IN (SELECT route_id FROM route WHERE (route_type != 3) AND (route_type != 5))) ORDER BY trip_id, stop_sequence;";
if ($result44 = mysqli_query ($link, $query44)) {
	while ($row44 = mysqli_fetch_row ($result44)) {
		$stop_id = $row44[0];
		$trip_id = $row44[1];
		$stop_lat = 0;
		$stop_lon = 0;

		$query50 = "SELECT final FROM du WHERE stop1 = '$oldstop' AND stop2 = '$stop_id';";
		if ($result50 = mysqli_query ($link, $query50)) {
			$hit = mysqli_num_rows ($result50);
		}
		$coord = mysqli_fetch_row (mysqli_query ($link, "SELECT stop_lat, stop_lon FROM stop WHERE stop_id = '$stop_id';"));
		$stop_lat = $coord[0];
		$stop_lon = $coord[1];

		if ($hit == 0) {
			$prujezdy = $oldlon.",".$oldlat.";".$stop_lon.",".$stop_lat;
			if ($trip_id == $oldtrip) {
				$insert_query = "INSERT INTO du (stop1, stop2, path, final) VALUES ('$oldstop', '$stop_id', '$prujezdy', '2');";
				$insert_action = mysqli_query ($link, $insert_query);
			}
		}
		$oldtrip = $trip_id;
		$oldstop = $stop_id;
		$oldlat = $stop_lat;
		$oldlon = $stop_lon;
	}
}

$query79 = "DELETE FROM du WHERE (stop1 = '0') OR (stop1 = stop2);";
echo "$query79<br/>";
$prikaz79 = mysqli_query($link, $query79);

mysqli_close ($link);
?>

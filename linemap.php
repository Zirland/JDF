<?php
$link = mysqli_connect ('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
	echo "Error: Unable to connect to MySQL.".PHP_EOL;
	echo "Debugging errno: ".mysqli_connect_errno().PHP_EOL;
	exit;
}

$file = 'linemap.csv';
$current = "route_short_name,trip_id,lat,lon,seq\n";
file_put_contents ($file, $current);

$akt_route = "SELECT route_id,route_short_name FROM route WHERE active='1';";
if ($result69 = mysqli_query ($link, $akt_route)) {
    while ($row69 = mysqli_fetch_row ($result69)) {
		$route_id = $row69[0];
		$route_short_name = $row69[1];

		$akt_trip = "SELECT route_id,trip_id,shape_id FROM trip WHERE ((route_id = '$route_id') AND (active='1'));";
		if ($result85 = mysqli_query ($link, $akt_trip)) {
			while ($row85 = mysqli_fetch_row ($result85)) {
				$route_id = $row85[0];
				$trip_id = $row85[1];
				$shape_tvar = $row85[2];

				$query152 = "SELECT shape_id FROM shapetvary WHERE tvartrasy = '$shape_tvar';";
				if ($result152 = mysqli_query ($link, $query152)) {
					$radku = mysqli_num_rows ($result152);
					if ($radku == 0) {
						$vloztrasu = mysqli_query ($link, "INSERT INTO shapetvary (tvartrasy, complete) VALUES ('$shape_tvar', '0');");
						$shape_id = mysqli_insert_id ($link);
					} else
						while ($row152 = mysqli_fetch_row ($result152)) {
						$shape_id = $row152[0];
						}
				}

				$query162 = "SELECT tvartrasy, complete FROM shapetvary WHERE shape_id = '$shape_id';";
				if ($result162 = mysqli_query ($link, $query162)) {
					while ($row162 = mysqli_fetch_row ($result162)) {
						$tvartrasy = $row162[0];

						$i = 0;
						$prevstop = "";
						$vzdal = 0;
						$komplet = 1;

						$output = explode ('|', $tvartrasy);

						foreach ($output as $prujbod) {
							$pom139 = mysqli_fetch_row(mysqli_query ($link, "SELECT stop_name,stop_lat,stop_lon FROM stop WHERE (stop_id='$prujbod');"));
							$name = $pom139[0];
							$lat = $pom139[1];
							$lon = $pom139[2];
							$i = $i + 1;

							if ($lat != '' && $lon != '') {
								$current = "$route_short_name, $trip_id, $lat, $lon, $i\n";
								file_put_contents ($file, $current, FILE_APPEND);
							} 
						}
					}
				}
			}
		}
	}
}

mysqli_close ($link);
?>
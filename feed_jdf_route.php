<?php
$oblast = $_GET["oblast"];

function soubor($text) {
	$file = 'log.log';
	$text = $text."\n";
	file_put_contents($file, $text, FILE_APPEND);
}

$link = mysqli_connect ('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
	echo "Error: Unable to connect to MySQL.".PHP_EOL;
	echo "Debugging errno: ".mysqli_connect_errno ().PHP_EOL;
	exit;
}

$dnes = date("Y-m-d", time());
$tyden = date("Y-m-d", strtotime("+ 1 week"));

$akt_trip = "SELECT route_id, matice, trip_id, trip_headsign, direction_id, shape_id, wheelchair_accessible, bikes_allowed FROM trip WHERE active='1' AND trip_id LIKE '$oblast%' AND route_id IN (SELECT route_id FROM route WHERE active='1') AND (trip_id IN (SELECT trip_id FROM jizdy WHERE datum>='$dnes' AND datum<'$tyden') OR matice != '');";

$current = "";

if ($result85 = mysqli_query ($link, $akt_trip)) {
	while ($row85 = mysqli_fetch_row ($result85)) {
		$route_id = $row85[0];
		$matrix = $row85[1];
		$trip_id = $row85[2];
		$trip_headsign = $row85[3];
		$direction_id = $row85[4];
		$shape_tvar = $row85[5];
		$wheelchair_accessible = $row85[6];
		$bikes_allowed = $row85[7];

		$matice = "0000000";

		$dnesden = substr($dnes,8,2);
		$dnesmesic = substr($dnes,5,2);
		$dnesrok = substr($dnes,0,4);
		$dnestime = mktime(0,0,0,$dnesmesic,$dnesden,$dnesrok);

		$tydenden = substr($tyden,8,2);
		$tydenmesic = substr($tyden,5,2);
		$tydenrok = substr($tyden,0,4);
		$tydentime = mktime(0,0,0,$tydenmesic,$tydenden,$tydenrok);

		$query64 = "SELECT * FROM jizdy WHERE trip_id = '$trip_id' AND (datum>='$dnes' AND datum<'$tyden');";
		if ($result64 = mysqli_query($link, $query64)) {
			while ($row64 = mysqli_fetch_row($result64)) {
				$datum = $row64[3];

				$datumden = substr($datum,8,2);
				$datummesic = substr($datum,5,2);
				$datumrok = substr($datum,0,4);
				$datumtime = mktime(0,0,0,$datummesic,$datumden,$datumrok);

				$dnu = round (($datumtime - $dnestime) / 86400); 
				$matice[$dnu] = 1;
			}
		}

		$vtydnu = date ('w',$dnestime);

		$adjust = substr($matice,-$vtydnu + 1).substr ($matice,0,-$vtydnu + 1);
		$dec= bindec($adjust) + 1;

		if ($dec == 1) {
			$matice_start = mktime(0,0,0,12,3,2017);
			$dnes_den = date("j", time ());
			$dnes_mesic = date("n", time ());
			$dnes_rok = date("Y", time ());
			$calendar_start = mktime(0,0,0,$dnes_mesic,$dnes_den,$dnes_rok);
			$calendar_start_format = date("Ymd", $calendar_start);
			$calendar_stop_format = date("Ymd", $calendar_start + 6 * 86400);
			$vtydnu = date('w',$calendar_start);
			$sek = $calendar_start - $matice_start;
			$min = floor($sek / 60);
			$sek = $sek % 60;
			$hod = floor($min / 60);
			$min = $min % 60;
			$dni = floor($hod / 24);
			$hod = $hod % 24;
			$aktual = substr($matrix,$dni + 1,7);
			$adjust = substr($aktual,-$vtydnu + 1).substr($aktual,0,-$vtydnu + 1);
			$dec = bindec($adjust) + 1;
		}

		$service_id = $dec;

		$mark_cal = mysqli_query ($link, "INSERT INTO SZDC.cal_use (trip_id, kalendar) VALUES ('$trip_id', '$service_id');");

		$query152 = "SELECT shape_id FROM shapetvary WHERE tvartrasy = '$shape_tvar';";
		if ($result152 = mysqli_query ($link, $query152)) {
			$radku = mysqli_num_rows ($result152);
			if ($radku == 0) {
				$vloztrasu = mysqli_query ($link, "INSERT INTO shapetvary (tvartrasy, complete) VALUES ('$shape_tvar', '0');");
				$shape_id = mysqli_insert_id ($link);
			} else {
				while ($row152 = mysqli_fetch_row ($result152)) {
					$shape_id = $row152[0];
				}
			}
			mysqli_free_result($result152);
		}

		$current .= "$route_id,$service_id,$trip_id,\"$trip_headsign\",$direction_id,J$shape_id,$wheelchair_accessible,$bikes_allowed\n";

		$query171 = "INSERT INTO shapecheck (trip_id, shape_id) VALUES ('$trip_id', '$shape_id');";
		$zapistrasy = mysqli_query ($link, $query171);
	}
	$file = 'trips.txt';
	file_put_contents ($file, $current, FILE_APPEND);
	mysqli_free_result($result85);
}

$tripstops = "SELECT DISTINCT * FROM stoptime WHERE trip_id IN (SELECT trip_id FROM trip WHERE active='1' AND trip_id LIKE '$oblast%' AND route_id IN (SELECT route_id FROM route WHERE active = '1') AND (trip_id IN (SELECT trip_id FROM jizdy WHERE datum>='$dnes' AND datum<'$tyden') OR matice != ''));";

$current = "";

if ($result166 = mysqli_query ($link, $tripstops)) {
	while ($row166 = mysqli_fetch_row ($result166)) {
		$trip_id = $row166[0];
		$arrival_time = $row166[1];
		$departure_time = $row166[2];
		$stop_id = $row166[3];
		$stop_sequence = $row166[4];
		$pickup_type = $row166[6];
		$drop_off_type = $row166[7];

		$current .= "$trip_id,$arrival_time,$departure_time,$stop_id,$stop_sequence,$pickup_type,$drop_off_type\n";

		$mark_stop = mysqli_query ($link, "INSERT INTO stop_use (trip_id, stop_id) VALUES ('$trip_id', '$stop_id');");
	}
	$file = 'stop_times.txt';
	file_put_contents ($file, $current, FILE_APPEND);
	mysqli_free_result($result166);
}

echo "Oblast: $oblast\n";

mysqli_close ($link);
?>

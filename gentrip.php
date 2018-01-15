<?php
include 'header.php';

$route = $_GET['route'];

$query6 = "DELETE FROM stoptime WHERE trip_id LIKE '$route%';";
$prikaz6 = mysqli_query($link, $query6);
$query8 = "UPDATE trip SET active=0 WHERE route_id = '$route';";
$prikaz8 = mysqli_query($link, $query8);

$query16 = "SELECT DISTINCT * FROM (SELECT * FROM triptimesDB WHERE trip_id LIKE '$route%' GROUP BY trip_id,zastav_id,prijezd,odjezd) AS pomoc GROUP BY trip_id, zastav_id ORDER BY trip_id,prijezd,odjezd,km;";
$result16 = mysqli_query($link, $query16);
if ($result16) {
	while ($row16 = mysqli_fetch_row($result16)) {
		$zastav_id = $row16[0];
		$trip_id = $row16[1];
		$trip_pk = $row16[2];
		$prijezd = $row16[3];
		$odjezd = $row16[4];
		$km = $row16[5];

		$znam = 0;
		$query29 = "SELECT stop_pk, stop_vazba FROM linestopsDB WHERE stop_id = '$zastav_id';";
		$pomoc29=mysqli_fetch_row(mysqli_query($link,$query29));
		$stop_pk = $pomoc29[0];
		if (strpos($stop_pk, '-18-') !== false) {
		// na znamení
			$znam = 1;
		}

		$pickup = 0; $dropoff = 0;
		if (strpos($trip_pk, '-22-') !== false) {
		// jen pro nástup
			$dropoff = 1;
		}
		if (strpos($trip_pk, '-21-') !== false) {
		// jen pro výstup
			$pickup = 1;
		}

		if ($znam == 1) {$pickup = 3; $dropoff = 3;}
		if ($prijezd == '') {$prijezd = $odjezd;}
		if ($odjezd == '') {$odjezd = $prijezd;}

		$arrival = substr($prijezd,0,2).":".substr($prijezd,-2).":00";
		$departure = substr($odjezd,0,2).":".substr($odjezd,-2).":00";
	
		$stop_id = $pomoc29[1];
		$prikaz76 = mysqli_fetch_row(mysqli_query($link, "SELECT stop_vazba FROM tripvazba WHERE (zastav_id = '$zastav_id' AND trip_id='$trip_id');"));
		$stop_vazba = $prikaz76[0];
		if ($stop_vazba != '') {$stop_id = $stop_vazba;}

		if ($stop_id != '') {
		$query487 = "INSERT INTO stoptime (trip_id, arrival_time, departure_time, stop_id, stop_sequence, pickup_type, drop_off_type, shape_dist_traveled, zastav_id) VALUES ('$trip_id', '$arrival', '$departure', '$stop_id', '0', '$pickup', '$dropoff', '$km', '$zastav_id');";
		$prikaz487 = mysqli_query($link, $query487);
		}
	}
}

$query55 = "SELECT trip_id FROM trip WHERE route_id = '$route';";
if ($result55 = mysqli_query($link, $query55)) {
	while ($row55 = mysqli_fetch_row($result55)) {
		$trip_id = $row55[0];

		$odd = $trip_id % 2;

		$r = 1;


		if ($odd == 1) {$query511 = "SELECT trip_id,stop_id,arrival_time FROM stoptime WHERE trip_id='$trip_id' ORDER BY arrival_time, zastav_id;";}
		if ($odd == 0) {$query511 = "SELECT trip_id,stop_id,arrival_time FROM stoptime WHERE trip_id='$trip_id' ORDER BY arrival_time, zastav_id DESC;";}

		if ($result511 = mysqli_query($link, $query511)) {
			while ($row511 = mysqli_fetch_row($result511)) {
				$trip_id = $row511[0];
				$stop_id = $row511[1];
				$arrival = $row511[2];
			
				$query517 = "UPDATE stoptime SET stop_sequence=$r WHERE trip_id='$trip_id' AND stop_id='$stop_id' AND arrival_time='$arrival';";
				$prikaz517 = mysqli_query($link, $query517);
					
				$r = $r+1;
			}
		}

		$query543 = "SELECT MAX(stop_sequence) FROM stoptime WHERE trip_id='$trip_id';";
		$row543 = mysqli_fetch_row(mysqli_query($link, $query543));
		$max = $row543[0];
		$query546 = "SELECT stop_name FROM stop WHERE stop_id IN (SELECT stop_id FROM stoptime WHERE trip_id = '$trip_id' AND stop_sequence = $max);";
		$row546 = mysqli_fetch_row(mysqli_query($link, $query546));
		$headsign = $row546[0];
		$query549 = "UPDATE trip SET trip_headsign='$headsign' WHERE trip_id='$trip_id';";
		$prikaz549 = mysqli_query($link, $query549);

		$shape="";
		$query1156 = "SELECT stop_id FROM stoptime WHERE trip_id='$trip_id' ORDER BY stop_sequence;";
		if ($result1156 = mysqli_query($link, $query1156)) {
			while ($row1156 = mysqli_fetch_row($result1156)) {
				$stop_id = $row1156[0];
				$shape.=$stop_id."|";
			}
		}
		$query1163 = "UPDATE trip SET shape_id='$shape', active = '1' WHERE trip_id='$trip_id';";
		$prikaz1163 = mysqli_query($link, $query1163);

	}
}

mysqli_close ($link);
?>

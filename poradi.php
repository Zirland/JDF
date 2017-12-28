<?php
include 'header.php';

$trip_id = $_GET['id'];

		$r = 1;
		$query511 = "SELECT trip_id,stop_id,arrival_time FROM stoptime WHERE trip_id='$trip_id' ORDER BY arrival_time;";
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

mysqli_close ($link);
?>

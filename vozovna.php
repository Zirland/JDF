<?php
include 'header.php';

$query = "SELECT trip_id FROM stoptime WHERE (stop_id = '554821Z965' AND stop_sequence = '1');";
if ($result = mysqli_query($link, $query)) {
	while ($row = mysqli_fetch_row($result)) {
		$trip_id = $row[0];

		echo "<a href=\"tripedit.php?id=$trip_id\">$trip_id</a><br/>";
	}
}

$query = "SELECT trip_id FROM stoptime WHERE (stop_id = '554821Z967' AND stop_sequence = '1');";
if ($result = mysqli_query($link, $query)) {
	while ($row = mysqli_fetch_row($result)) {
		$trip_id = $row[0];

		echo "<a href=\"tripedit.php?id=$trip_id\">$trip_id</a><br/>";
	}
}

$query = "SELECT trip_id FROM stoptime WHERE (stop_id = '554821Z887' AND stop_sequence = '1');";
if ($result = mysqli_query($link, $query)) {
	while ($row = mysqli_fetch_row($result)) {
		$trip_id = $row[0];

		echo "<a href=\"tripedit.php?id=$trip_id\">$trip_id</a><br/>";
	}
}

$query = "SELECT trip_id FROM stoptime WHERE (stop_id = '554821Z410' AND stop_sequence = '1');";
if ($result = mysqli_query($link, $query)) {
	while ($row = mysqli_fetch_row($result)) {
		$trip_id = $row[0];

		echo "<a href=\"tripedit.php?id=$trip_id\">$trip_id</a><br/>";
	}
}

$query = "SELECT trip_id FROM stoptime WHERE (stop_id = '554821Z590' AND stop_sequence = '1');";
if ($result = mysqli_query($link, $query)) {
	while ($row = mysqli_fetch_row($result)) {
		$trip_id = $row[0];

		echo "<a href=\"tripedit.php?id=$trip_id\">$trip_id</a><br/>";
	}
}



echo "== Konec ==";
include 'footer.php';
?>

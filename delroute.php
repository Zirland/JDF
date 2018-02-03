<?php
include 'header.php';

$route_id = $_GET['route'];

$query = "SELECT trip_id FROM trip WHERE route_id = '$route_id';";
if ($result = mysqli_query ($link, $query)) {
	while ($row = mysqli_fetch_row ($result)) {
		$trip_id = $row[0];
		
		$prikaz = mysqli_query ($link, "DELETE FROM trip WHERE trip_id = '$trip_id';");
		$prikaz2 = mysqli_query ($link, "DELETE FROM stoptime WHERE trip_id = '$trip_id';");
			
	}
}

$prikaz3 = mysqli_query ($link, "DELETE FROM route WHERE route_id = '$route_id';");

echo "== Konec ==";
include 'footer.php';
?>
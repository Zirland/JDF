<?php
include 'header.php';

$route = $_GET['route'];

$newroute = substr($route, 1);

$query4 = "DELETE FROM route WHERE route_id = '$newroute';";
$command4 = mysqli_query ($link, $query4);

$query7 = "DELETE FROM trip WHERE route_id = '$newroute';";
$command7 = mysqli_query ($link, $query7);

$query14 = "DELETE FROM stoptime WHERE trip_id LIKE  '$newroute%';";
$command14 = mysqli_query ($link, $query14);

$query17 = "UPDATE route SET route_id = '$newroute' WHERE route_id = '$route';";
$command17 = mysqli_query ($link, $query17);

$query20 = "SELECT trip_id FROM trip WHERE route_id = '$route';";
if ($result20 = mysqli_query ($link, $query20)) {
	while ($row20 = mysqli_fetch_row ($result20)) {
		$trip_id = $row20[0];

		$newtrip = substr ($trip_id, 1);

		$query27 = "UPDATE trip SET trip_id = '$newtrip', route_id = '$newroute' WHERE trip_id = '$trip_id';";
		$prikaz27 = mysqli_query ($link, $query27);

		$query30 = "UPDATE stoptime SET trip_id = '$newtrip' WHERE trip_id = '$trip_id';";
		$prikaz30 = mysqli_query ($link, $query30);
	}
}

$query35 = "DELETE FROM exter WHERE linka = '$newroute';";
$command35 = mysqli_query ($link, $query35);

$query38 = "UPDATE exter SET linka = '$newroute' WHERE linka = '$route';";
$command38 = mysqli_query ($link, $query38);

echo "Dokončeno";

include 'footer.php';
?> 
<?php
include 'header.php';

$route = $_GET['route'];

$newroute = substr($route, 1);

$query5 = mysqli_query($link, "DELETE FROM route WHERE route_id = '$newroute';");

$query8  = "UPDATE route SET route_id = '$newroute' WHERE route_id = '$route';";
$prikaz8 = mysqli_query($link, $query8);

$query20 = "SELECT trip_id FROM trip WHERE route_id = '$route';";
if ($result20 = mysqli_query($link, $query20)) {
    while ($row20 = mysqli_fetch_row($result20)) {
        $trip_id = $row20[0];

        $query27  = "UPDATE trip SET route_id = '$newroute' WHERE trip_id = '$trip_id';";
        $prikaz27 = mysqli_query($link, $query27);
    }
}

$query35   = "DELETE FROM exter WHERE linka = '$newroute';";
$command35 = mysqli_query($link, $query35);

$query38   = "UPDATE exter SET linka = '$newroute' WHERE linka = '$route';";
$command38 = mysqli_query($link, $query38);

echo "Dokončeno";

include 'footer.php';

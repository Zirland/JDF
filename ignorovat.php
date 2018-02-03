<?php
include 'header.php';

$route_id = $_GET['route'];

$query4 = "INSERT INTO ignorace (route_id) VALUES ('$route_id');";
$prikaz3 = mysqli_query ($link, $query4);

echo "== Konec ==";
include 'footer.php';
?>
<?php
include 'header.php';

$route_id = $_GET['route'];

$query4 = "INSERT INTO ignorace VALUES ('$route_id');";
echo "$query4<br/>";
$prikaz3 = mysqli_query($link, $query4);

echo "== Konec ==";
include 'footer.php';
?>

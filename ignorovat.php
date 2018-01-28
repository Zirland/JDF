<?php
include 'header.php';

$route_id = $_GET['route'];

$prikaz3 = mysqli_query($link, "INSERT INTO ignorace VALUES ('$route_id');");

echo "== Konec ==";
include 'footer.php';
?>

<?php
$link = mysqli_connect('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

$action9 = mysqli_query($link,"TRUNCATE TABLE trip;");
$action10 = mysqli_query($link,"TRUNCATE TABLE stoptime;");
$action11 = mysqli_query($link,"TRUNCATE TABLE triptimesDB;");

mysqli_close($link);
?>
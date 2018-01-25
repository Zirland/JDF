<?php
$link = mysqli_connect ('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
	echo "Error: Unable to connect to MySQL.".PHP_EOL;
	echo "Debugging errno: ".mysqli_connect_errno ().PHP_EOL;
	exit;
}

$cistianal = mysqli_query ($link, "TRUNCATE TABLE analyza;");
$cistitrip = mysqli_query ($link, "TRUNCATE TABLE triptimesDB;");
$cistiline = mysqli_query ($link, "TRUNCATE TABLE linestopsDB;");

mysqli_close ($link);
?>
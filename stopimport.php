<?php
$link = mysqli_connect('localhost', 'root', 'root', 'JDF');
if (!$link) {
	echo "Error: Unable to connect to MySQL.".PHP_EOL;
	echo "Debugging errno: ".mysqli_connect_errno ().PHP_EOL;
	exit;
}

$gpx = simplexml_load_file("vlastni_body.gpx");

foreach ($gpx->wpt as $pt) {
	$lat = (string) $pt['lat'];
	$lon = (string) $pt['lon'];
	$name = (string) $pt->name;

	$name = explode (",", $name);
	$obec = $name[0];
	$castobce = $name[1];
	$misto = substr ($name[2],0,-2);
	$pomcode = substr ($name[2],-1);

	echo "$lat - $lon - $obec, $castobce, $misto $pomcode<br/>";
}


unset($gpx);
mysqli_close ($link);
?>
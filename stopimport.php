<?php
$link = mysqli_connect('localhost', 'root', 'root', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

$file = $_GET['file'];
$gpx = simplexml_load_file($file);

foreach ($gpx->wpt as $pt) {
    $lat = (string) $pt['lat'];
    $lon = (string) $pt['lon'];
    $name = (string) $pt->name;

    $name = explode(",", $name);
    $obec = trim($name[0]);
    $castobce = trim($name[1]);
    $misto = trim($name[2]);
    $pomcode = trim($name[3]);

    $query23 = "INSERT INTO importstop (lat,lon,obec,castobce,misto,pomcode) VALUES ('$lat', '$lon', '$obec', '$castobce', '$misto', '$pomcode');";
    $prikaz23 = mysqli_query($link, $query23);
}

unset($gpx);
mysqli_close($link);

<?php
date_default_timezone_set('Europe/Prague');

$link = mysqli_connect('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

$svatek[] = "";
$query27  = "SELECT datum FROM svatky ORDER BY datum;";
if ($result27 = mysqli_query($link, $query27)) {
    while ($row27 = mysqli_fetch_row($result27)) {
        $svatek[] = $row[0];
    }
}

print_r($svatek);

mysqli_close($link);

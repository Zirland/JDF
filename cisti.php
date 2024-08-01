<?php
include 'header.php';

$dnes = date("Y-m-d", time());
$tyden = date("Y-m-d", strtotime("+ 1 week"));

$query7 = "DELETE FROM jizdy WHERE datum < '$dnes';";
$prikaz7 = mysqli_query($link, $query7);

$query10 = "DELETE FROM `log` WHERE trip_id NOT IN (SELECT trip_id FROM trip);";
$prikaz10 = mysqli_query($link, $query10);

$query13 = "SELECT DISTINCT SUBSTR(trip_id,1,6) FROM (SELECT trip_id, stop_sequence, count(*) as pocet FROM stoptime GROUP BY trip_id, stop_sequence) as duplicity WHERE pocet > 1;";
if ($result13 = mysqli_query($link, $query13)) {
    while ($row13 = mysqli_fetch_row($result13)) {
        $dupl_route = $row13[0];

        echo "Duplicita: $dupl_route<br/>";
    }
    echo "------------<br/>";
}

$query23 = "SELECT trip_id FROM trip WHERE trip_id NOT IN (SELECT DISTINCT trip_id FROM jizdy);";
if ($result23 = mysqli_query($link, $query23)) {
    while ($row23 = mysqli_fetch_row($result23)) {
        $trip_id = $row23[0];

        echo "<a href=\"tripedit.php?id=$trip_id\">$trip_id</a><br/>";
        $prikaz = mysqli_query($link, "DELETE FROM trip WHERE trip_id = '$trip_id';");
    }
}

$query33 = "SELECT route_id FROM `route` WHERE route_id NOT IN (SELECT DISTINCT route_id FROM trip WHERE active='1');";
if ($result33 = mysqli_query($link, $query33)) {
    while ($row33 = mysqli_fetch_row($result33)) {
        $route_id = $row33[0];

        echo "Route $route_id<br/>";
        $prikaz43 = mysqli_query($link, "UPDATE `route` SET active='0' WHERE route_id = '$route_id';");
    }
}

$query43 = "DELETE FROM du WHERE stop1 = '0' OR stop1 = stop2 OR stop1 NOT IN (SELECT stop_id FROM stop) OR stop2 NOT IN (SELECT stop_id FROM stop);";
$prikaz43 = mysqli_query($link, $query43);

$query46 = "DELETE FROM stoptime WHERE trip_id NOT IN (SELECT trip_id FROM trip);";
$prikaz46 = mysqli_query($link, $query46);

$query49 = "DELETE FROM trip WHERE trip_id NOT IN (SELECT DISTINCT trip_id FROM stoptime);";
$prikaz49 = mysqli_query($link, $query49);

$query52 = "DELETE FROM jizdy WHERE trip_id NOT IN (SELECT DISTINCT trip_id FROM trip);";
$prikaz52 = mysqli_query($link, $query52);

$query55 = "DELETE FROM du_use WHERE trip_id NOT IN (SELECT DISTINCT trip_id FROM trip) OR du_id NOT IN (SELECT du_id FROM du);";
$prikaz55 = mysqli_query($link, $query55);

echo "== Konec ==";
include 'footer.php';

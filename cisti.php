<?php
include 'header.php';

$dnes  = date("Y-m-d", time());
$tyden = date("Y-m-d", strtotime("+ 1 week"));

$query7  = "DELETE FROM jizdy WHERE datum < '$dnes';";
$prikaz7 = mysqli_query($link, $query7);

$query11  = "DELETE FROM `log` WHERE trip_id NOT IN (SELECT trip_id FROM trip);";
$prikaz11 = mysqli_query($link, $query11);

$prepare13 = mysqli_query($link, "CREATE TABLE tyden AS (SELECT * FROM jizdy WHERE datum<'$tyden');");

$query15 = "SELECT id FROM tyden LEFT OUTER JOIN (SELECT MAX(id) as RowId, spoj, datum FROM tyden GROUP BY spoj, datum) as KeepRows ON tyden.id = KeepRows.RowId WHERE KeepRows.RowId IS NULL;";
if ($result15 = mysqli_query($link, $query15)) {
    while ($row15 = mysqli_fetch_row($result15)) {
        $id = $row15[0];

        $query20  = "DELETE FROM jizdy WHERE id = '$id';";
        $prikaz20 = mysqli_query($link, $query20);
    }
}

$prepare25 = mysqli_query($link, "DROP TABLE tyden;");

$query27 = "SELECT trip_id FROM trip WHERE trip_id NOT IN (SELECT DISTINCT trip_id FROM jizdy);";
if ($result27 = mysqli_query($link, $query27)) {
    while ($row27 = mysqli_fetch_row($result27)) {
        $trip_id = $row27[0];

        echo "<a href=\"tripedit.php?id=$trip_id\">$trip_id</a><br/>";
        $prikaz = mysqli_query($link, "DELETE FROM trip WHERE trip_id = '$trip_id';");
    }
}

$query37 = "SELECT route_id FROM `route` WHERE route_id NOT IN (SELECT DISTINCT route_id FROM trip WHERE active='1');";
if ($result37 = mysqli_query($link, $query37)) {
    while ($row37 = mysqli_fetch_row($result37)) {
        $route_id = $row37[0];

        echo "Route $route_id<br/>";
        $prikaz43 = mysqli_query($link, "UPDATE `route` SET active='0' WHERE route_id = '$route_id';");
    }
}

$query47  = "DELETE FROM du WHERE stop1 = '0';";
$prikaz47 = mysqli_query($link, $query47);

$query50  = "DELETE FROM du WHERE stop1 = stop2;";
$prikaz50 = mysqli_query($link, $query50);

$query53  = "DELETE FROM stoptime WHERE trip_id NOT IN (SELECT trip_id FROM trip);";
$prikaz53 = mysqli_query($link, $query53);

$query56  = "DELETE FROM trip WHERE trip_id NOT IN (SELECT DISTINCT trip_id FROM stoptime);";
$prikaz56 = mysqli_query($link, $query56);

$query59  = "DELETE FROM jizdy WHERE trip_id NOT IN (SELECT DISTINCT trip_id FROM trip);";
$prikaz59 = mysqli_query($link, $query59);

$query62  = "DELETE FROM du_use WHERE trip_id NOT IN (SELECT DISTINCT trip_id FROM trip);";
$prikaz62 = mysqli_query($link, $query62);

$query65  = "DELETE FROM du_use WHERE du_id NOT IN (SELECT du_id FROM du);";
$prikaz65 = mysqli_query($link, $query65);


echo "== Konec ==";
include 'footer.php';

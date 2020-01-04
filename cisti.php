<?php
include 'header.php';

$dnes  = date("Y-m-d", time());
$tyden = date("Y-m-d", strtotime("+ 1 week"));

$query11  = "DELETE FROM jizdy WHERE datum < '$dnes';";
$prikaz11 = mysqli_query($link, $query11);

$query11  = "DELETE FROM log WHERE trip_id NOT IN (SELECT trip_id FROM trip);";
$prikaz11 = mysqli_query($link, $query11);

$prepare32 = mysqli_query($link, "CREATE TABLE tyden AS (SELECT * FROM jizdy WHERE datum<'$tyden');");

$query40 = "SELECT id FROM tyden LEFT OUTER JOIN (SELECT MAX(id) as RowId, spoj, datum FROM tyden GROUP BY spoj, datum) as KeepRows ON tyden.id = KeepRows.RowId WHERE KeepRows.RowId IS NULL;";
if ($result40 = mysqli_query($link, $query40)) {
    while ($row40 = mysqli_fetch_row($result40)) {
        $id = $row40[0];

        $query45  = "DELETE FROM jizdy WHERE id = '$id';";
        $prikaz45 = mysqli_query($link, $query45);
    }
}

$prepare45 = mysqli_query($link, "DROP TABLE tyden;");

$query22 = "SELECT trip_id FROM trip WHERE trip_id NOT IN (SELECT DISTINCT trip_id FROM jizdy);";
if ($result22 = mysqli_query($link, $query22)) {
    while ($row22 = mysqli_fetch_row($result22)) {
        $trip_id = $row22[0];

        echo "<a href=\"tripedit.php?id=$trip_id\">$trip_id</a> = $soucet<br/>";
        $prikaz = mysqli_query($link, "DELETE FROM trip WHERE trip_id = '$trip_id';");
    }
}

$query1 = "SELECT route_id FROM route WHERE route_id NOT IN (SELECT DISTINCT route_id FROM trip WHERE active=1);";
if ($result1 = mysqli_query($link, $query1)) {
    while ($row1 = mysqli_fetch_row($result1)) {
        $route_id = $row1[0];

        echo "Route $route_id<br/>";
        $prikaz3 = mysqli_query($link, "UPDATE route SET active=0 WHERE route_id = '$route_id';");
    }
}

$query66  = "DELETE FROM du WHERE stop1 = '0';";
$prikaz66 = mysqli_query($link, $query66);

$query67  = "DELETE FROM du WHERE stop1 = stop2;";
$prikaz67 = mysqli_query($link, $query67);

$query160  = "DELETE FROM stoptime WHERE trip_id NOT IN (SELECT trip_id FROM trip);";
$prikaz160 = mysqli_query($link, $query160);

$query68 = "SELECT du_id, stop1, stop2 FROM du WHERE final = 1;";
$query68 = "";
if ($result68 = mysqli_query($link, $query68)) {
    while ($row68 = mysqli_fetch_row($result68)) {
        $du_id = $row68[0];
        $stop1 = $row68[1];
        $stop2 = $row68[2];

        $query75 = "SELECT trip_id FROM trip WHERE shape_id LIKE '%$stop1|$stop2|%';";
        $hits    = mysqli_num_rows(mysqli_query($link, $query75));
        if ($hits == 0) {
            $purge_du = mysqli_query($link, "DELETE FROM du WHERE du_id = $du_id;");
        }
    }
}

echo "== Konec ==";
include 'footer.php';

<?php
include 'header.php';

$dnes = date("Y-m-d", time());
$dnessrt = date("Ymd", time());
$tyden = date("Y-m-d", strtotime("+ 1 week"));

$query11 = "DELETE FROM jizdy WHERE datum < '$dnes';";
$prikaz11 = mysqli_query($link, $query11);

$prepare31 = mysqli_query($link, "DROP TABLE tyden;");
$prepare32 = mysqli_query($link, "CREATE TABLE tyden AS (SELECT * FROM jizdy WHERE datum<'$tyden');");

$query40 = "SELECT id FROM tyden LEFT OUTER JOIN (SELECT MAX(id) as RowId, spoj, datum FROM tyden GROUP BY spoj, datum) as KeepRows ON tyden.id = KeepRows.RowId WHERE KeepRows.RowId IS NULL;";
if ($result40 = mysqli_query($link, $query40)) {
	while ($row40 = mysqli_fetch_row($result40)) {
		$id = $row40[0];

		$query45 = "DELETE FROM jizdy WHERE id = '$id';";
		$prikaz45 = mysqli_query($link, $query45);
	}
}

$prepare45 = mysqli_query($link, "DROP TABLE tyden;");

$query22 = "SELECT trip_id FROM trip WHERE trip_id NOT IN (SELECT DISTINCT trip_id FROM jizdy);";
if ($result22 = mysqli_query($link, $query22)) {
	while ($row22 = mysqli_fetch_row($result22)) {
		$trip_id = $row22[0];

		$query = "SELECT matice,trip_id FROM trip WHERE trip_id = '$trip_id';";
		if ($result = mysqli_query ($link, $query)) {
			while ($row = mysqli_fetch_row ($result)) {
				$matice = $row[0];
				$trip_id = $row[1];

				$matice_start = mktime (0,0,0,12,3,2017);
				$matice_end = mktime (0,0,0,1,12,2019);

				$dnes_den = date ("j", time ());
				$dnes_mesic = date ("n", time ());
				$dnes_rok = date ("Y", time ());

				$calendar_start = mktime (0,0,0,$dnes_mesic,$dnes_den,$dnes_rok);

				$sek = $calendar_start - $matice_start;
				$min = floor ($sek / 60);
				$sek = $sek % 60;
				$hod = floor ($min / 60);
				$min = $min % 60;
				$dni = floor ($hod / 24);
				$hod = $hod % 24;

				$sek2 = $matice_end - $matice_start;
				$min2 = floor ($sek2 / 60);
				$sek2 = $sek2 % 60;
				$hod2 = floor ($min2 / 60);
				$min2 = $min2 % 60;
				$dni2 = floor ($hod2 / 24);
				$hod2 = $hod2 % 24;

				$zbyva = $dni2 - $dni;
				$aktual = substr ($matice,$dni + 1,$zbyva);

				$soucet = 0;
				$rozklad = str_split ($aktual);
				foreach ($rozklad as $den) {
					$soucet = $soucet + $den;
				}

				if ($soucet == 0) {
					echo "<a href=\"tripedit.php?id=$trip_id\">$trip_id</a> = $soucet<br/>";

					$prikaz = mysqli_query ($link, "DELETE FROM trip WHERE trip_id = '$trip_id';");
//					$prikaz = mysqli_query ($link, "UPDATE trip SET active=0 WHERE trip_id = '$trip_id';");
				}
			}
		}
	}
}

$query1 = "SELECT route_id FROM route WHERE route_id NOT IN (SELECT DISTINCT route_id FROM trip WHERE active=1);";
if ($result1 = mysqli_query ($link, $query1)) {
	while ($row1 = mysqli_fetch_row ($result1)) {
		$route_id = $row1[0];

		echo "Route $route_id<br/>";
		$prikaz3 = mysqli_query ($link, "UPDATE route SET active=0 WHERE route_id = '$route_id';");
	}
}


$query66 = "DELETE FROM du WHERE stop1 = '0';";
$prikaz66 = mysqli_query($link, $query66);

$query67 = "DELETE FROM du WHERE stop1 = stop2;";
$prikaz67 = mysqli_query($link, $query67);

$query160 = "DELETE FROM stoptime WHERE trip_id NOT IN (SELECT trip_id FROM trip);";
$prikaz160 = mysqli_query($link, $query160);

echo "== Konec ==";
include 'footer.php';
?>

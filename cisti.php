<?php
include 'header.php';

$query = "SELECT matice,trip_id FROM trip;";
if ($result = mysqli_query ($link, $query)) {
	while ($row = mysqli_fetch_row ($result)) {
		$matice = $row[0];
		$trip_id = $row[1];

		$matice_start = mktime (0,0,0,12,3,2017);
		$matice_end = mktime (0,0,0,1,12,2019);

		$dnes_den = date ("d", time ());
		$dnes_mesic = date ("m", time ());
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

//			$prikaz = mysqli_query ($link, "UPDATE trip SET active=0 WHERE trip_id = '$trip_id';");
			$prikaz = mysqli_query ($link, "DELETE FROM trip WHERE trip_id = '$trip_id';");
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


// $query68 = "SELECT du_id, stop1, stop2 FROM du;";
if ($result68 = mysqli_query($link, $query68)) {
	while ($row68 = mysqli_fetch_row($result68)) {
		$du_id = $row68[0];
		$stop1 = $row68[1];
		$stop2 = $row68[2];

		$query75 = "SELECT trip_id FROM trip WHERE shape_id LIKE '%$stop1|$stop2|%';";
		echo "$query75<br/>";
		$hits = mysqli_num_rows(mysqli_query($link, $query75));
		echo "$du_id = $hits<br/>";
		if ($hits == 0) {
			echo "$du_id<br/>";
//			$purge_du = mysqli_query($link, "DELETE FROM du WHERE du_id = $du_id;");
		}
	}
}

// $query86 = "SELECT du_id, stop1, stop2 FROM du WHERE (final = 2);";
if ($result86 = mysqli_query($link, $query86)) {
	while ($row86 = mysqli_fetch_row($result86)) {
		$du_id = $row86[0];
		$stop1 = $row86[1];
		$stop2 = $row86[2];

		$query93 = "SELECT stop_lat, stop_lon FROM stop WHERE (stop_id = '$stop1');";
		echo "$query93<br/>";
		if ($result93 = mysqli_query($link, $query93)) {
			while ($row93 = mysqli_fetch_row($result93)) {
				$begin_lat = $row93[0];
				$begin_lon = $row93[1];
			}
		}

		$query102 = "SELECT stop_lat, stop_lon FROM stop WHERE (stop_id = '$stop2');";
		echo "$query102<br/>";
		if ($result102 = mysqli_query($link, $query102)) {
			while ($row102 = mysqli_fetch_row($result102)) {
				$end_lat = $row102[0];
				$end_lon = $row102[1];
			}
		}

		$cesta = "$begin_lon,$begin_lat;$end_lon,$end_lat";
		echo "$cesta<br/>";

		$rovnej_du = mysqli_query($link, "UPDATE du SET path = '$cesta' WHERE du_id = $du_id;");
	}
}

echo "== Konec ==";
include 'footer.php';
?>

<?php
include 'header.php';

$query = "SELECT matice,trip_id FROM trip;";
if ($result = mysqli_query($link, $query)) {
	while ($row = mysqli_fetch_row($result)) {
		$matice = "0".$row[0];
		$trip_id = $row[1];
		
		$matice_start = mktime(0,0,0,12,3,2017);
		$matice_end = mktime(0,0,0,12,8,2018);
		
		$dnes_den = date("d", time());
		$dnes_mesic = date("m", time());
		$dnes_rok = date("Y", time());

		$calendar_start = mktime(0,0,0,$dnes_mesic,$dnes_den,$dnes_rok);
		
		$sek=$calendar_start-$matice_start;
		$min=floor($sek/60);
		$sek=$sek%60;
		$hod=floor($min/60);
		$min=$min%60;
		$dni=floor($hod/24);
		$hod=$hod%24;
		
		$sek2=$matice_end-$matice_start;
		$min2=floor($sek2/60);
		$sek2=$sek2%60;
		$hod2=floor($min2/60);
		$min2=$min2%60;
		$dni2=floor($hod2/24);
		$hod2=$hod2%24;
		
		$zbyva = $dni2 - $dni;
		$aktual = substr($matice,$dni+1,$zbyva);
		
		$soucet = 0;
		$rozklad = str_split($aktual);
		foreach ($rozklad as $den) {
			$soucet = $soucet + $den;
		}
		
		if ($soucet == 0) {
			echo "<a href=\"tripedit.php?id=$trip_id\">$trip_id</a> = $soucet<br/>";
			
			$prikaz = mysqli_query($link, "DELETE FROM trip WHERE trip_id = '$trip_id';");
			$prikaz2 = mysqli_query($link, "DELETE FROM stoptime WHERE trip_id = '$trip_id';");
			
		}
	}
}

$query1 = "SELECT route_id FROM route WHERE route_id NOT IN (SELECT DISTINCT route_id FROM trip);";
if ($result1 = mysqli_query($link, $query1)) {
	while ($row1 = mysqli_fetch_row($result1)) {
		$route_id = $row1[0];

		echo "Route $route_id<br/>";
		$prikaz3 = mysqli_query($link, "DELETE FROM route WHERE route_id = '$route_id';");
	}
}

echo "== Konec ==";
include 'footer.php';
?>

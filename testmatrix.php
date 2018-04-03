<?php
$barva=@$_GET["color"];

$link = mysqli_connect('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

$akt_trip = "SELECT route_id,matice,trip_id,trip_headsign,direction_id,shape_id,wheelchair_accessible,bikes_allowed FROM trip WHERE ((route_id = 'F9150211') AND (active='1'));";
if ($result85 = mysqli_query($link, $akt_trip)) {
	while ($row85 = mysqli_fetch_row($result85)) {
		$route_id = $row85[0];
		$matice = $row85[1];
		$trip_id = $row85[2];
		$trip_headsign = $row85[3];
		$direction_id = $row85[4];
		$shape_tvar = $row85[5];
		$wheelchair_accessible = $row85[6];
		$bikes_allowed = $row85[7];

		$matice_start = mktime(0,0,0,12,3,2017);
		$dnes_den = date("d", time());
		$dnes_mesic = date("m", time());
		$dnes_rok = date("Y", time());

		$calendar_start = mktime(0,0,0,$dnes_mesic,$dnes_den,$dnes_rok);
		$calendar_start_format = date("Ymd", $calendar_start);
		$calendar_stop_format = date("Ymd", $calendar_start+6*86400);
		$vtydnu = date('w',$calendar_start);

echo "$calendar_start_format - $calendar_stop_format - $vtydnu<br />";

		$sek=$calendar_start-$matice_start;
		$min=floor($sek/60);
		$sek=$sek%60;
		$hod=floor($min/60);
		$min=$min%60;
		$dni=floor($hod/24);
		$hod=$hod%24;
		$aktual = substr($matice,$dni+1,7);

		$adjust = substr($aktual,-$vtydnu+1).substr($aktual,0,-$vtydnu+1);
		$dec=bindec($adjust)+1;

echo "$trip_id - $adjust - $dec<br />";
	}
}
mysqli_close($link);
?>

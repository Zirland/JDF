<?php
$link = mysqli_connect('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

$routenums = 0;
$tripnums = 0;

$now = microtime(true);
$timestart = $now;
echo "Start: $now\n";
$prevnow = $now;

$akt_route = "SELECT route_id,agency_id,route_short_name,route_long_name,route_type,route_color,route_text_color FROM route WHERE (active='1');";

if ($result69 = mysqli_query($link, $akt_route)) {
    while ($row69 = mysqli_fetch_row($result69)) {
		$route_id = $row69[0];
		$agency_id = $row69[1];
		$route_short_name = $row69[2];
		$route_long_name = $row69[3];
		$route_type = $row69[4];
		$route_color = $row69[5];
		$route_text_color = $row69[6];
		$routenums = mysqli_num_rows($result69);
	
		$current = "$route_id,$agency_id,\"$route_short_name\",\"$route_long_name\",$route_type,$route_color,$route_text_color\n";

		$file = 'routes.txt';
		file_put_contents($file, $current, FILE_APPEND);
// zapsána aktivní linka

$now = microtime(true);
$dlouho = $now-$prevnow;
// echo "Route: $dlouho\n";
$prevnow = $now;


		$akt_trip = "SELECT route_id,matice,trip_id,trip_headsign,direction_id,shape_id,wheelchair_accessible,bikes_allowed FROM trip WHERE ((route_id = '$route_id') AND (active='1'));";
		if ($result85 = mysqli_query($link, $akt_trip)) {
			while ($row85 = mysqli_fetch_row($result85)) {
				$route_id = $row85[0];
				$matice = "0".$row85[1];
				$trip_id = $row85[2];
				$trip_headsign = $row85[3];
				$direction_id = $row85[4];
				$shape_tvar = $row85[5];
				$wheelchair_accessible = $row85[6];
				$bikes_allowed = $row85[7];

				$matice_start = mktime(0,0,0,12,3,2017);
				$dnes_den = date("j", time());
				$dnes_mesic = date("n", time());
				$dnes_rok = date("Y", time());

				$calendar_start = mktime(0,0,0,$dnes_mesic,$dnes_den,$dnes_rok);
				$calendar_start_format = date("Ymd", $calendar_start);
				$calendar_stop_format = date("Ymd", $calendar_start+6*86400);
				$vtydnu = date('w',$calendar_start);
		
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

$now = microtime(true);
$dlouho = $now-$prevnow;
// echo "Calendar $trip_id: $dlouho\n";
$prevnow = $now;


				if ($dec != 1)  {
					$service_id = $dec;
				
					$mark_cal = mysqli_query($link, "INSERT INTO cal_use (trip_id, kalendar) VALUES ('$trip_id', '$service_id');");
// zápis kalendáře spoje pro tento týden do databáze

					$query152 = "SELECT shape_id FROM shapetvary WHERE tvartrasy = '$shape_tvar';";
					if ($result152 = mysqli_query($link, $query152)) {
						$radku = mysqli_num_rows($result152);
							if ($radku == 0) {
								$vloztrasu = mysqli_query($link, "INSERT INTO shapetvary (tvartrasy, complete) VALUES ('$shape_tvar', '0');");
								$shape_id = mysqli_insert_id($link);
							} else
							while ($row152 = mysqli_fetch_row($result152)) {
								$shape_id = $row152[0];
							}
					}

					$current = "$route_id,$service_id,$trip_id,\"$trip_headsign\",$direction_id,J$shape_id,$wheelchair_accessible,$bikes_allowed\n";
					$file = 'trips.txt';
					file_put_contents($file, $current, FILE_APPEND);
					$tripnums = $tripnums + 1;
// zapsán aktivní spoj

					$query171 = "INSERT INTO shapecheck (trip_id, shape_id) VALUES ('$trip_id', '$shape_id');";
					$zapistrasy = mysqli_query($link, $query171);

$now = microtime(true);
$dlouho = $now-$prevnow;
// echo "Trip $trip_id: $dlouho\n";
$prevnow = $now;

					$query162 = "SELECT tvartrasy, complete FROM shapetvary WHERE shape_id = '$shape_id';";
					if ($result162 = mysqli_query($link, $query162)) {
						while ($row162 = mysqli_fetch_row($result162)) {
						$tvartrasy = $row162[0];
						$kompltrasa = $row162[1];
						if ($kompltrasa != 1) {
							$smaz182 = "DELETE FROM shape WHERE shape_id = '$shape_id';";
							$smazanitrasy = mysqli_query($link,$smaz182);
				
							$i = 0;
							$prevstop = "";
							$vzdal = 0;
							$komplet = 1;

							$output = explode('|', $tvartrasy);

							foreach ($output as $prujbod) {
								$pom139 = mysqli_fetch_row(mysqli_query($link, "SELECT stop_name,stop_lat,stop_lon FROM stop WHERE (stop_id='$prujbod');"));
								$name = $pom139[0];
								$lat = $pom139[1];
								$lon = $pom139[2];
								$i = $i + 1;
						
								$result235 = mysqli_query($link, "SELECT DELKA FROM DU_pom WHERE (STOP1 = '$prevstop') AND (STOP2 = '$prujbod');");
								$pom235 = mysqli_fetch_row($result235);
								$ujeto = $pom235[0];
								$radky = mysqli_num_rows($result235);
								$vzdal = $vzdal + $ujeto;
								$prevstop = $prujbod;
						
								if ($lat != '' && $lon != '') {
									if ($i == 1) {$vzdal = 0;} 
									$query144 = "INSERT INTO shape VALUES (
										'$shape_id',
										'$lat',
										'$lon',
										'$i',
										'$vzdal'
									);";
									$command = mysqli_query($link, $query144);
								} 
//								else {$komplet = 0;}
// zápis nové trasy do databáze
							}
						}				
						$query217 = "UPDATE shapetvary SET complete = '$komplet' WHERE shape_id = '$shape_id';";
						$command217 = mysqli_query($link, $query217);
						}
					}
	
$now = microtime(true);
$dlouho = $now-$prevnow;
// echo "Trasa $trip_id: $dlouho ~ Kompletní $komplet\n";
$prevnow = $now;

				
					$tripstops = "SELECT trip_id,arrival_time,departure_time,stop_id,stop_sequence,pickup_type,drop_off_type FROM stoptime WHERE (trip_id = '$trip_id');";
					if ($result166 = mysqli_query($link, $tripstops)) {
						while ($row166 = mysqli_fetch_row($result166))  {
							$trip_id = $row166[0];
							$arrival_time = $row166[1];
							$departure_time = $row166[2];
							$stop_id = $row166[3];
							$stop_sequence = $row166[4];
							$pickup_type = $row166[5];
							$drop_off_type = $row166[6];
				
							$current = "$trip_id,$arrival_time,$departure_time,$stop_id,$stop_sequence,$pickup_type,$drop_off_type\n";
							$file = 'stop_times.txt';
							file_put_contents($file, $current, FILE_APPEND);
			
							$mark_stop = mysqli_query($link, "INSERT INTO stop_use (trip_id, stop_id) VALUES ('$trip_id', '$stop_id');");
// zapsán jízdní řád trasy a stanice do pomocné databáze
						}					
					}

$now = microtime(true);
$dlouho = $now-$prevnow;
// echo "Schedule $trip_id: $dlouho\n";
$prevnow = $now;

				}
			}
		}
	}
}

echo "Exported lines: $routenums\n";
echo "Exported trips: $tripnums\n";

$timecelkem = $now - $timestart;
echo "Celkem zpracování: $timecelkem";

mysqli_close($link);
?>

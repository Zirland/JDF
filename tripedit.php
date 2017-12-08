<?php
include 'header.php';

$trip = @$_GET['id'];
$action = @$_POST['action'];

switch ($action) {
	case "hlava" :
	
	$trip = $_POST['trip_id'];
	$linka = $_POST['route_id'];
	$trip_headsign = $_POST['headsign'];
	$smer = $_POST['smer'];
	$blok = $_POST['block_id'];
	$invalida = $_POST['invalida'];
	$cyklo = $_POST['cyklo'];
	$aktif = $_POST['aktif'];

	$ready0 = "UPDATE trip SET route_id='$linka', trip_headsign='$trip_headsign', direction_id='$smer', block_id='$blok', wheelchair_accessible='$invalida', bikes_allowed='$cyklo', active='$aktif' WHERE (trip_id = '$trip');";

	$aktualz0 = mysqli_query($link, $ready0);
	break;
	
	case "zastavky" :
	$trip = $_POST['trip_id'];
	
	for ($y = 0; $y < 40; $y++) {
		$$ind = $y;
		$arrindex = "arrive".${$ind};
		$arrival_time = $_POST[$arrindex];
		$depindex = "leave".${$ind};
		$departure_time = $_POST[$depindex];
		$rzmindex = "rezim".${$ind};
		$rzm = $_POST[$rzmindex];
		$pickup_type = substr($rzm,0,1);
		$drop_off_type = substr($rzm,1,1);
		$seqindex = "poradi".${$ind};
		$stop_sequence = $_POST[$seqindex];
		$nameindex = "stopname".${$ind};
		$stop_name = $_POST[$nameindex];
		$stpidindex = "stop_id".${$ind};
		$stop_id = $_POST[$stpidindex];
		$stp2idindex = "stop2_id".${$ind};
		$stop2_id = $_POST[$stp2idindex];
		$rertindex = "reroute".${$ind};
		$reroute = $_POST[$rertindex];
		$zstidindex = "zastav_id".${$ind};
		$zastav_id = $_POST[$zstidindex];
		
		$delindex = "delete".${$ind};
		$delete = $_POST[$delindex];
		
		if ($reroute == 1) {
			$query54 = "UPDATE stoptime SET stop_id = '$stop2_id' WHERE ((trip_id = '$trip') AND (stop_sequence = '$stop_sequence'));";
			$prikaz54 = mysqli_query($link, $query54);

			$query1156 = "SELECT stop_id FROM stoptime WHERE trip_id='$trip' ORDER BY stop_sequence;";
			if ($result1156 = mysqli_query($link, $query1156)) {
				$shape="";
				while ($row1156 = mysqli_fetch_row($result1156)) {
					$stop_id = $row1156[0];
					$shape.=$stop_id."|";
				}
			}

			$query67 = "UPDATE trip SET shape_id = '$shape' WHERE trip_id='$trip';";
			$prikaz67 = mysqli_query($link, $query67);	

			$query72 = "UPDATE triptimesDB SET stop_vazba = '$stop2_id' WHERE ((trip_id = '$trip') AND (zastav_id = '$zastav_id'));";
			$prikaz72 = mysqli_query($link, $query72);

			$query75 = "INSERT INTO tripvazba (zastav_id, trip_id, stop_vazba) VALUES ('$zastav_id','$trip','$stop2_id');";
			$prikaz75 = mysqli_query($link, $query75);
		}
			
		switch ($delete) {
			case 1 : 
				$query58 = "DELETE FROM stoptime WHERE ((trip_id = '$trip') AND (stop_sequence = '$stop_sequence'));";
				$prikaz58 = mysqli_query($link, $query58);
			break;
				
			default : 
				$ready1 = "UPDATE stoptime SET arrival_time='$arrival_time', departure_time='$departure_time', pickup_type='$pickup_type', drop_off_type='$drop_off_type' WHERE ((trip_id ='$trip') AND (stop_sequence = '$stop_sequence'));";
				$aktualz1 = mysqli_query($link, $ready1);

				$ready2 = "UPDATE stop SET stop_name='$stop_name' WHERE (stop_id ='$stop_id');";
				$aktualz2 = mysqli_query($link, $ready2);
			break;
    	}
   	}
	
	$vlak = substr($trip,0,-2);
	$lomeni = substr($vlak,-1);
	$cislo7 = $vlak."/".$lomeni;

	$pom163 = mysqli_fetch_row(mysqli_query($link, "SELECT max(stop_sequence) FROM stoptime WHERE (trip_id = '$trip');"));
	$max_trip = $pom163[0];

	$pom129 = mysqli_fetch_row(mysqli_query($link, "SELECT min(stop_sequence) FROM stoptime WHERE (trip_id = '$trip');"));
	$min_trip = $pom129[0];
//vymezení výchozího a konečného bodu

	$tvartrasy = "";
	$i = 0;
	
	$query131 = "SELECT * FROM kango.DTV WHERE (CISLO7='$cislo7');";
	if ($result131 = mysqli_query($link, $query131)) {
		while ($row131 = mysqli_fetch_row($result131))  {
			$stopstat = $row131[1];
			$stopzst = $row131[2];
			$stopob = $row131[3];
			$ZST = substr($stopstat,-2).$stopzst.substr($stopob,-1);
			$i = $i + 1;
	
			if ($i <= $max_trip && $i >= $min_trip) {
				$tvartrasy .= $ZST;
			}
		}
	}
	
	break;
    
	case "grafikon" :
	    $trip = $_POST['trip_id'];
	    $grafi = "";
	    $invert = $_POST['invert'];
	    $altern = $_POST['altern'];
	    $proti = @$_POST['proti'];
	
   		switch ($invert) {
	   	case 1 :
	   		for ($v = 0; $v < 406; $v++) {
			$$ind = $v;
			$index = "grafikon".${$ind};
			$mtrx = $_POST[$index];
			
			switch ($mtrx) {
				case 1 : $grafi.="0";break;
				case 0 : $grafi.="1";break;			
	    		}
	   	}
	   		break;
	   		
	    default :
	    	for ($v = 0; $v < 406; $v++) {
			$$ind = $v;
			$index = "grafikon".${$ind};
			$mtrx = $_POST[$index];
			$grafi.=$mtrx;
		}
		}
		
		$denne = $_POST['denne'];
		if ($denne == 1) {
			for ($i = 0; $i < 406; $i++) {
				$grafi.="1";
			}
		}
		
		if ($altern == "1") {
			$pom84 = mysqli_fetch_row(mysqli_query($link, "SELECT matice FROM trip WHERE (trip_id = '$proti');"));
		 	$matice = $pom84[0];

			$grafi = "";

			$grafikon = str_split($matice);
			for ($w = 0; $w < 406; $w++) {
				switch ($grafikon[$w]) {
				case 0 : $grafi.="1"; break;
				case 1 : $grafi.="0"; break;
				}			
			}
		}
		
	    $operace = "UPDATE trip SET matice='$grafi' WHERE (trip_id = '$trip');";
	    $vykonej = mysqli_query($link, $operace) or die(mysqli_error());
	break;
}

echo "<table><tr><td>";
echo "<table>";
echo "<tr>";

$hlavicka = mysqli_fetch_row(mysqli_query($link, "SELECT * FROM trip WHERE (trip_id='$trip');"));
	$trip_id = $hlavicka[2];
	$linka = $hlavicka[0];
	$matice = $hlavicka[1];
	$trip_headsign = $hlavicka[3];
	$smer = $hlavicka[5];
	$blok = $hlavicka[6];
	$shape = $hlavicka[7];
	$invalida = $hlavicka[8];
	$cyklo = $hlavicka[9];
	$aktif = $hlavicka[10];

echo "<td><a href = \"routeedit.php?id=$linka\">Zpět na linku</a><td>";
echo "<td><form method=\"get\" action=\"tripedit.php\" name=\"id\"><input type=\"text\" name=\"id\" value=\"\"><input type=\"submit\"></form><td>";
echo "<td><a href=\"zajebal.php?err=$trip_id\" target=\"_blank\">Zajebal</a></td>";
echo "<td><a href=\"tripdelete.php?trip=$trip_id\" target=\"_blank\">Smazat trip</a></td>";
echo "<td><td>";
echo "</tr><tr>";


echo "<form method=\"post\" action=\"tripedit.php\" name=\"hlava\">
		<input name=\"action\" value=\"hlava\" type=\"hidden\">
		<input name=\"trip_id\" value=\"$trip_id\" type=\"hidden\">";
echo "<td>$trip_id</td><td>Linka: <select name=\"route_id\">";

$query45 = "SELECT route_id, route_short_name, route_long_name FROM route ORDER BY route_short_name;";
if ($result45 = mysqli_query($link, $query45)) {
	while ($row45 = mysqli_fetch_row($result45)) {
		$roid = $row45[0];
		$roshname = $row45[1];
		$rolgname = $row45[2];

		echo "<option value=\"$roid\"";
		if ($roid == $linka) {echo " SELECTED";}
		echo ">$roshname - $rolgname</option>";
	}
}
echo "</select></td><td>Směr: <input type=\"text\" name=\"headsign\" value=\"$trip_headsign\"><br />";
echo "<select name=\"smer\"><option value=\"0\"";
if ($smer=='0') {echo " SELECTED";}
echo ">Odchozí</option><option value=\"1\"";
if ($smer=='1') {echo " SELECTED";}
echo ">Příchozí</option></select></td>";
echo "<td>Blok <input type=\"text\" name=\"block_id\" value=\"$blok\"><br/>";
echo "Invalida: <select name=\"invalida\"><option value=\"0\"";
if ($invalida == '0') {echo " SELECTED";}
echo "></option><option value=\"1\"";
if ($invalida == '1') {echo " SELECTED";}
echo ">Vlak vhodný pro přepravu</option><option value=\"2\"";
if ($invalida == '2') {echo " SELECTED";}
echo ">Vlak neumožňuje přepravu</option></select><br />";
echo "Cyklo: <select name=\"cyklo\"><option value=\"0\"";
if ($cyklo == '0') {echo " SELECTED";}
echo "></option><option value=\"1\"";
if ($cyklo == '1') {echo " SELECTED";}
echo ">Vlak vhodný pro přepravu</option><option value=\"2\"";
if ($cyklo == '2') {echo " SELECTED";}
echo ">Vlak neumožňuje přepravu</option></select>";
echo "</td>";
echo "<td>Aktivní <input type=\"checkbox\" name=\"aktif\" value=\"1\"";
if ($aktif == '1') {echo " CHECKED";}
echo "></td><td><input type=\"submit\"></td></tr></form>";
echo "<tr><td colspan=\"5\">";

$vlak = substr($trip_id,0,-2);
$lomeni = substr($vlak,-1);
$cislo7 = $vlak."/".$lomeni;

$query86 = "SELECT POZNAM FROM kango.OBP WHERE ((CISLO7='$cislo7'));";
if ($result86 = mysqli_query($link, $query86)) {
    while ($row86 = mysqli_fetch_row($result86)) {
	$poznamka = $row86[0];
				
	echo "$poznamka<br />";
	}
}

echo "TRASA <a href=\"tripedit.php?id=$trip&trasa=1\">VYNUŤ</a><br />";
echo "$shape <br />";
echo "</td></tr>";
echo "</table>";

echo "<table>";
echo "<tr><td>";
echo "<table>";
echo "<tr><th>Stanice</th><th>Příjezd</th><th><Odjezd</th><th>Režim</th><th></th></tr>";

echo "<form method=\"post\" action=\"tripedit.php\" name=\"zastavky\">
		<input name=\"action\" value=\"zastavky\" type=\"hidden\">
		<input name=\"trip_id\" value=\"$trip_id\" type=\"hidden\">";
$z = 0;

$query108 = "SELECT stoptime.stop_id,stoptime.arrival_time,stoptime.departure_time,stoptime.pickup_type,stoptime.drop_off_type,stoptime.stop_sequence, stop.stop_name, stop.pomcode, stoptime.zastav_id FROM stoptime LEFT JOIN stop ON stoptime.stop_id = stop.stop_id WHERE (stoptime.trip_id = '$trip_id') ORDER BY stoptime.stop_sequence;";

if ($result108 = mysqli_query($link, $query108)) {
    while ($row108 = mysqli_fetch_row($result108)) {
	$stop_id = $row108[0];
	$arrival_time = $row108[1];
	$departure_time = $row108[2];
	$pickup_type = $row108[3];
	$drop_off_type = $row108[4];
	$stop_sequence = $row108[5];
	$nazev_stanice = $row108[6];
	$kod_stanice = $row108[7];
	$zastav_id = $row108[8];

	echo "<tr><td><input name=\"stop_id$z\" value=\"$stop_id\" type=\"hidden\">
	<input name=\"zastav_id$z\" value=\"$zastav_id\" type=\"hidden\">
	<input name=\"poradi$z\" value=\"$stop_sequence\" type=\"hidden\">
	<input type=\"checkbox\" name=\"reroute$z\" value=\"1\">
	<select name=\"stop2_id$z\">";
	$query194 = "SELECT stop_id, fullname, pomcode FROM stop WHERE active=1 ORDER BY stop_name;";
	if ($result194 = mysqli_query($link, $query194)) {
		while ($row194 = mysqli_fetch_row($result194)) {
			$stopid = $row194[0];
			$stopname = $row194[1];
			$stopcode = $row194[2];

			echo "<option value=\"$stopid\"";
			if ($stopid == $stop_id) {echo " SELECTED";}
			echo ">$stopname $stopcode</option>";
		}
	}
	echo "</select>";
	echo "<input type=\"text\" name=\"stopname$z\" value=\"$nazev_stanice\"> $kod_stanice</td>";
	echo "<td><input type=\"text\" name=\"arrive$z\" value=\"$arrival_time\"></td>";
	echo "<td><input type=\"text\" name=\"leave$z\" value=\"$departure_time\"></td>";
	echo "<td><select name=\"rezim$z\"><option value=\"00\"></option>";
	echo "<option value=\"01\"";
	if ($drop_off_type == 1) {echo " SELECTED";}
	echo ">Pouze výstup</option>";
	echo "<option value=\"10\"";
	if ($pickup_type == 1) {echo " SELECTED";}
	echo ">Pouze nástup</option>";
	echo "<option value=\"33\"";
	if ($drop_off_type == 3) {echo " SELECTED";}
	echo ">Zastavuje na znamení</option>";
	echo "<select></td>";
	echo "<td><input type=\"checkbox\" name=\"delete$z\" value=\"1\"></td></tr>";
	$z = $z+1;
    }
}
echo "<input type=\"submit\"></form>";
echo "</table></td></tr>";
echo "</table>";

echo "</td></tr></table>";

echo "<form method=\"post\" action=\"tripedit.php\" name=\"grafikon\">
		<input name=\"action\" value=\"grafikon\" type=\"hidden\">
		<input name=\"trip_id\" value=\"$trip_id\" type=\"hidden\">";

echo "<input type=\"checkbox\" name=\"denne\" value=\"1\"> Jede denně";
echo "<input type=\"checkbox\" name=\"invert\" value=\"1\"> Invertuj";
echo "<input type=\"checkbox\" name=\"altern\" value=\"1\"> Alternace <input type=\"text\" name=\"proti\" value=\"\">";

// Matice začíná 3.12.2017 
$matice_start = mktime(0,0,0,12,3,2017);
$grafikon = str_split($matice);
echo "<table border=\"1\"><tr><td>";
// 3.12.2017 je 0;
for ($u = 0; $u < 406; $u++) {
    
    $datum=$matice_start+($u*86400);
    $datum_format = date("d.m.", $datum);
    $denvtydnu = date('w',$datum);
    if ($grafikon[$u] == "1") {echo "<span style=\"background-color:green;\">";}
    echo "$datum_format<br /><input type=\"radio\" name=\"grafikon$u\" value=\"0\"";
    if ($grafikon[$u] == "0") {echo " CHECKED";}
    echo "><input type=\"radio\" name=\"grafikon$u\" value=\"1\"";
    if ($grafikon[$u] == "1") {echo " CHECKED";}
    echo "><br />";
    if ($grafikon[$u] == "1") {echo "</span>";}
	
    if ($denvtydnu == "0") {echo "</td><td>";}
}
echo "</td></tr></table>";
echo "<input type=\"submit\"></form>";


include 'footer.php';
?>

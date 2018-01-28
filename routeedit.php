<?php
include 'header.php';

function getContrastYIQ($hexcolor){
	$r = hexdec(substr($hexcolor,0,2));
	$g = hexdec(substr($hexcolor,2,2));
	$b = hexdec(substr($hexcolor,4,2));
	$yiq = (($r*299)+($g*587)+($b*114))/1000;
	return ($yiq >= 128) ? '000000' : 'FFFFFF';
}

$route = "XYZ";
$route = $_GET['id'];
$action = $_POST['action'];

switch ($action) {
	case "oprav" :
		$route = $_POST['route_id'];
		$dopravce = $_POST['dopravce'];
		$shortname = $_POST['shortname'];
		$longname = $_POST['longname'];
		$routetype = $_POST['routetype'];
		$pozadi = $_POST['route_pozadi'];
		$foreground = getContrastYIQ($pozadi);
		$aktif = $_POST['aktif'];

		$ready0 = "UPDATE route SET agency_id='$dopravce', route_short_name='$shortname', route_long_name='$longname', route_type='$routetype', route_color='$pozadi', route_text_color='$foreground', active='$aktif' WHERE (route_id = '$route');";
		$aktualz0 = mysqli_query($link, $ready0);
	break;

	case "zastavky" :
		$route = $_POST['route_id'];
		$pocet = $_POST['pocet'];

		for ($y = 0; $y < $pocet; $y++) {
			$$ind = $y;
			$stpidindex = "stop_id".${$ind};
			$stop_id = $_POST[$stpidindex];
			$stpvazbaindex = "stop_vazba".${$ind};
			$stop2_id = $_POST[$stpvazbaindex];

			$query30 = "UPDATE linestopsDB SET stop_vazba='$stop2_id' WHERE (stop_id ='$stop_id');";
			$aktual30 = mysqli_query($link, $query30);
			
			$queryvazba = "DELETE FROM linevazba WHERE stop_id = '$stop_id';";
			$cistivazba = mysqli_query($link, $queryvazba);

			$ready34 = "INSERT INTO linevazba (stop_id, stop_vazba) VALUES ('$stop_id', '$stop2_id');";
			$aktual34 = mysqli_query($link, $ready34);
		}
	break;
}

echo "<table><tr><td>";
echo "<table>";
echo "<tr>";

$query50 = "SELECT * FROM route WHERE (route_id='$route');";
if ($result50 = mysqli_query($link, $query50)) {
	while ($row50 = mysqli_fetch_row($result50)) {
		$route_id = $row50[0];
		$agency_id = $row50[1];
		$route_short_name = $row50[2];
		$route_long_name = $row50[3];
		$route_type = $row50[5];
		$route_color = $row50[7];
		$route_active = $row50[9];
	}
}

echo "<form method=\"post\" action=\"routeedit.php\" name=\"oprav\">
	<input name=\"action\" value=\"oprav\" type=\"hidden\">
	<input name=\"route_id\" value=\"$route_id\" type=\"hidden\">";
echo "<td>Dopravce: <select name=\"dopravce\">";

$query24 = "SELECT agency_id, agency_name FROM agency ORDER BY agency_id;";
if ($result24 = mysqli_query($link, $query24)) {
	while ($row24 = mysqli_fetch_row($result24)) {
		$agid = $row24[0];
		$agname = $row24[1];

		echo "<option value=\"$agid\"";
		if ($agid == $agency_id) {echo " SELECTED";}
		echo ">$agname</option>";
	}
}

echo "</select><br>Typ linky: <select name=\"routetype\">";
echo "<option value=\"0\""; if ($route_type == "0") {echo " SELECTED";} echo ">tramvaj</option>";
echo "<option value=\"1\""; if ($route_type == "1") {echo " SELECTED";} echo ">metro</option>";
echo "<option value=\"2\""; if ($route_type == "2") {echo " SELECTED";} echo ">vlak</option>";
echo "<option value=\"3\""; if ($route_type == "3") {echo " SELECTED";} echo ">autobus</option>";
echo "<option value=\"4\""; if ($route_type == "4") {echo " SELECTED";} echo ">přívoz</option>";
echo "<option value=\"5\""; if ($route_type == "5") {echo " SELECTED";} echo ">trolejbus</option>";
echo "<option value=\"6\""; if ($route_type == "6") {echo " SELECTED";} echo ">visutá lanovka</option>";
echo "<option value=\"7\""; if ($route_type == "7") {echo " SELECTED";} echo ">kolejová lanovka</option>";
echo "</select>";

echo "</td><td style=\"background-color : #$route_color;\">Linka: <input type=\"text\" name=\"shortname\" value=\"$route_short_name\"><br />";

echo "<input type=\"text\" name=\"longname\" value=\"$route_long_name\"></td>";

echo "<td>Pozadí: <input type=\"text\" name=\"route_pozadi\" value=\"$route_color\"></td>";

echo "<td>Aktivní <input type=\"checkbox\" name=\"aktif\" value=\"1\"";
if ($route_active == '1') {echo " CHECKED";}
echo "></td><td><input type=\"submit\"></td></tr></form></table>";

$query79 = "SELECT DISTINCT route_id, route_name, route_type FROM analyza WHERE route_id = '$route_id';";
if ($result79 = mysqli_query($link, $query79)) {
	while ($row79 = mysqli_fetch_row($result79)) {
		$routeid = $row79[0];
		$routename = $row79[1];

		echo "$routename<br/>";
	}
}


echo "<a href=\"gentrip.php?route=$route_id\" target=\"_blank\">Generovat trasy</a>";

echo "<form method=\"post\" action=\"ukonci.php\" name=\"konec\">
	<input name=\"route_id\" value=\"$route_id\" type=\"hidden\">";
echo "Naposledy jede <input type=\"text\" name=\"datumod\" value=\"31122019\">";
echo "<input type=\"submit\"></form><br/>";
echo "<table>";
echo "<tr><td>";

echo "<form method=\"post\" action=\"routeedit.php\" name=\"zastavky\">
	<input name=\"action\" value=\"zastavky\" type=\"hidden\">
	<input name=\"route_id\" value=\"$route_id\" type=\"hidden\">";
$z = 0;

echo "<table><tr><th>Zastávka</th><th>Vazba</th></tr>";

$query63 = "SELECT * FROM linestopsDB WHERE (stop_linka = '$route_id') AND (stop_smer = '0') ORDER BY stop_poradi;";
if ($result63 = mysqli_query($link, $query63)) {
	while ($row63 = mysqli_fetch_row($result63)) {
		$stop_id = $row63[0];
		$stop_name = $row63[1];
		$stop_vazba = $row63[4];

		echo "<tr><td>";
		echo "<input type=\"hidden\" name=\"stop_id$z\" value=\"$stop_id\">";
		echo "$stop_name</td><td>";

		echo "<select name=\"stop_vazba$z\">";
		echo "<option value=\"\">---</option>";
		$query82 = "SELECT stop_id, sortname, pomcode FROM stop WHERE active=1 AND obec='Česká Lípa' ORDER BY sortname;";
		if ($result82 = mysqli_query($link, $query82)) {
			while ($row82 = mysqli_fetch_row($result82)) {
				$stopid = $row82[0];
				$sortname = $row82[1];
				$stopcode = $row82[2];

				echo "<option value=\"$stopid\"";
				if ($stopid == $stop_vazba) {echo " SELECTED";}
				echo ">$sortname $stopcode</option>";
			}
		}
		$z = $z+1;
		echo "</select>";
		echo "</td></tr>";
	}
}
echo "</table></td><td>";

echo "<table><tr><th>Zastávka</th><th>Vazba</th></tr>";
$query63 = "SELECT * FROM linestopsDB WHERE (stop_linka = '$route_id') AND (stop_smer = '1') ORDER BY stop_poradi DESC;";
if ($result63 = mysqli_query($link, $query63)) {
	while ($row63 = mysqli_fetch_row($result63)) {
		$stop_id = $row63[0];
		$stop_name = $row63[1];
		$stop_vazba = $row63[4];

		echo "<tr><td>";
		echo "<input type=\"hidden\" name=\"stop_id$z\" value=\"$stop_id\">";
		echo "$stop_name</td><td>";

		echo "<select name=\"stop_vazba$z\">";
		echo "<option value=\"\">---</option>";
		$query82 = "SELECT stop_id, sortname, pomcode FROM stop WHERE active=1 AND obec='Česká Lípa' ORDER BY sortname;";
		if ($result82 = mysqli_query($link, $query82)) {
			while ($row82 = mysqli_fetch_row($result82)) {
				$stopid = $row82[0];
				$sortname = $row82[1];
				$stopcode = $row82[2];

				echo "<option value=\"$stopid\"";
				if ($stopid == $stop_vazba) {echo " SELECTED";}
				echo ">$sortname $stopcode</option>";
			}
		}
		$z = $z+1;
		echo "</select>";
		echo "</td></tr>";
	}
}
echo "</table><input type=\"hidden\" name=\"pocet\" value=\"$z-1\"><input type=\"submit\"></form></td></tr></table>";

echo "<table>";
echo "<tr><th>Linky odchozí</th><th>Linky příchozí</th></tr>";
echo "<tr><td>";
		
$query80 = "SELECT * FROM trip WHERE ((route_id = '$route_id') AND (direction_id='0')) ORDER BY trip_id;";
if ($result80 = mysqli_query($link, $query80)) {
	while ($row80 = mysqli_fetch_row($result80)) {
		$trip_id = $row80[2];
		$trip_headsign = $row80[3];
		$trip_aktif = $row80[10];

/*		$pomstartstop = mysqli_fetch_row(mysqli_query($link, "SELECT MIN(stop_sequence) FROM stoptime WHERE (trip_id = '$trip_id');"));
		$startstopno = $pomstartstop[0];

		$pomfinstop=mysqli_fetch_row(mysqli_query($link, "SELECT stop_id FROM stoptime WHERE (trip_id='$trip_id' AND stop_sequence='$startstopno');"));
		$finstopid=$pomfinstop[0];

		$query15 = "SELECT stop_name FROM stop WHERE stop_id='$finstopid';";
		$result15 = mysqli_query($link, $query15);
		$pomhead = mysqli_fetch_row($result15);
		$from = $pomhead[0];
*/
		if ($trip_aktif == '1') {echo "<span style=\"background-color:green;\">";}
		echo "$from - $trip_id - $trip_headsign - <a href=\"tripedit.php?id=$trip_id\">Upravit</a><br />";
		if ($trip_aktif == '1') {echo "</span>";}
	}
}
echo "</td><td>";
		
$query96 = "SELECT * FROM trip WHERE ((route_id = '$route_id') AND (direction_id = '1')) ORDER BY trip_id;";
if ($result96 = mysqli_query($link, $query96)) {
	while ($row96 = mysqli_fetch_row($result96)) {
		$trip_id = $row96[2];
		$trip_headsign = $row96[3];
		$trip_aktif = $row96[10];
				
/*		$pomstartstop = mysqli_fetch_row(mysqli_query($link, "SELECT MIN(stop_sequence) FROM stoptime WHERE (trip_id = '$trip_id');"));
		$startstopno = $pomstartstop[0];

		$pomfinstop=mysqli_fetch_row(mysqli_query($link, "SELECT stop_id FROM stoptime WHERE (trip_id='$trip_id' AND stop_sequence='$startstopno');"));
		$finstopid=$pomfinstop[0];

		$query15 = "SELECT stop_name FROM stop WHERE stop_id='$finstopid';";
		$result15 = mysqli_query($link, $query15);
		$pomhead = mysqli_fetch_row($result15);
		$from = $pomhead[0];
*/
		if ($trip_aktif == '1') {echo "<span style=\"background-color:green;\">";}				
		echo "$from - $trip_id - $trip_headsign - <a href=\"tripedit.php?id=$trip_id\">Upravit</a><br />";
		if ($trip_aktif == '1') {echo "</span>";}
	}
}	
echo "</td></tr></table>";

include 'footer.php';
?>

<?php
include 'header.php';

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
		$foreground = $_POST['foreground'];
		$aktif = $_POST['aktif'];

		$ready0 = "UPDATE route SET agency_id='$dopravce', route_short_name='$shortname', route_long_name='$longname', route_type='$routetype', route_color='$pozadi', route_text_color='$foreground', active='$aktif' WHERE (route_id = '$route');";
		$aktualz0 = mysqli_query($link, $ready0);
	break;

	case "zastavky" :
		$route = $_POST['route_id'];
	
		for ($y = 0; $y < 60; $y++) {
				$$ind = $y;
				$stpidindex = "stop_id".${$ind};
				$stop_id = $_POST[$stpidindex];
				$stpvazbaindex = "stop_vazba".${$ind};
				$stop2_id = $_POST[$stpvazbaindex];

				$query30 = "UPDATE linestopsDB SET stop_vazba='$stop2_id' WHERE (stop_id ='$stop_id');";
				$aktual30 = mysqli_query($link, $query30);
				$ready34 = "INSERT INTO linevazba (stop_id, stop_vazba) VALUES ('$stop_id', '$stop2_id');";
				$aktual34 = mysqli_query($link, $ready34);
   		}
}

echo "<table><tr><td>";
echo "<table>";
echo "<tr>";

$hlavicka = mysqli_fetch_row(mysqli_query($link, "SELECT * FROM route WHERE (route_id='$route');"));
$route_id = $hlavicka[0];
$agency_id = $hlavicka[1];
$route_short_name = $hlavicka[2];
$route_long_name = $hlavicka[3];
$route_type = $hlavicka[5];
$route_color = $hlavicka[7];
$route_text_color = $hlavicka[8];
$route_active = $hlavicka[9];

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

echo "<td>Pozadí: <input type=\"text\" name=\"route_pozadi\" value=\"$route_color\"><br />";
echo "Popředí: <input type=\"text\" name=\"foreground\" value=\"$route_text_color\"></td>";

echo "<td>Aktivní <input type=\"checkbox\" name=\"aktif\" value=\"1\"";
if ($route_active == '1') {echo " CHECKED";}
echo "></td><td><input type=\"submit\"></td></tr></form></table>";

$query79 = "SELECT DISTINCT route_id, route_name, route_type FROM analyza WHERE route_id = '$route_id';";
if ($result79 = mysqli_query($link, $query79)) {
	while ($row79 = mysqli_fetch_row($result79)) {
		$routeid = $row79[0];
		$routename = $row79[1];

		echo "$routename<BR/>";
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

$query63 = "SELECT * FROM linestopsDB WHERE (stop_linka LIKE '$route%') AND (stop_smer = '0') ORDER BY stop_poradi;";
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
		$query82 = "SELECT stop_id, obec, castobce, misto, pomcode FROM stop WHERE active=1 ORDER BY obec, castobce, misto;";
		if ($result82 = mysqli_query($link, $query82)) {
			while ($row82 = mysqli_fetch_row($result82)) {
				$stopid = $row82[0];
				$obec = $row82[1];
				$castobce = $row82[2];
				$misto = $row82[3];
				$stopcode = $row82[4];

				echo "<option value=\"$stopid\"";
				if ($stopid == $stop_vazba) {echo " SELECTED";}
				echo ">";
				if ($misto != "") {echo "$misto ";}
				if ($castobce != "") {echo "$castobce ";}
				echo "$obec $stopcode</option>";
			}
		}
		$z = $z+1;
		echo "</select>";
		echo "</td></tr>";
	}
}
echo "</table><input type=\"submit\"></form></td><td>";

echo "<form method=\"post\" action=\"routeedit.php\" name=\"zastavky\">
		<input name=\"action\" value=\"zastavky\" type=\"hidden\">
		<input name=\"route_id\" value=\"$route_id\" type=\"hidden\">";
$z = 0;

echo "<table><tr><th>Zastávka</th><th>Vazba</th></tr>";

$query63 = "SELECT * FROM linestopsDB WHERE (stop_linka LIKE '$route%') AND (stop_smer = '1') ORDER BY stop_poradi DESC;";
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
		$query82 = "SELECT stop_id, obec, castobce, misto, pomcode FROM stop WHERE active=1 ORDER BY obec, castobce, misto;";
		if ($result82 = mysqli_query($link, $query82)) {
			while ($row82 = mysqli_fetch_row($result82)) {
				$stopid = $row82[0];
				$obec = $row82[1];
				$castobce = $row82[2];
				$misto = $row82[3];
				$stopcode = $row82[4];

				echo "<option value=\"$stopid\"";
				if ($stopid == $stop_vazba) {echo " SELECTED";}
				echo ">";
				if ($misto != "") {echo "$misto ";}
				if ($castobce != "") {echo "$castobce ";}
				echo "$obec $stopcode</option>";
			}
		}
		$z = $z+1;
		echo "</select>";
		echo "</td></tr>";
	}
}
echo "</table><input type=\"submit\"></form></td></tr></table>";

echo "<table>";
echo "<tr><th>Linky odchozí</th><th>Linky příchozí</th></tr>";
echo "<tr><td>";
		
$query80 = "SELECT * FROM trip WHERE ((route_id = '$route_id') AND (direction_id='0')) ORDER BY trip_id;";
if ($result80 = mysqli_query($link, $query80)) {
	while ($row80 = mysqli_fetch_row($result80)) {
		$trip_id = $row80[2];
		$trip_headsign = $row80[3];
		$trip_aktif = $row80[10];
				
		$vlak = $trip_id;

		if ($trip_aktif == '1') {echo "<span style=\"background-color:green;\">";}
		echo "$vlak - $trip_headsign - <a href=\"tripedit.php?id=$trip_id\">Upravit</a><br />";
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
				
		$vlak = $trip_id;
		if ($trip_aktif == '1') {echo "<span style=\"background-color:green;\">";}				
		echo "$vlak - $trip_headsign - <a href=\"tripedit.php?id=$trip_id\">Upravit</a><br />";
		if ($trip_aktif == '1') {echo "</span>";}
	}
}	
echo "</td></tr></table>";

include 'footer.php';
?>

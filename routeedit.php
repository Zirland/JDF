<?php
include 'header.php';

function getContrastYIQ ($hexcolor){
	$r = hexdec (substr ($hexcolor,0,2));
	$g = hexdec (substr ($hexcolor,2,2));
	$b = hexdec (substr ($hexcolor,4,2));
	$yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
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
		$pozadi = substr($pozadi, 1);
		$foreground = getContrastYIQ ($pozadi);
		$aktif = $_POST['aktif'];

		$ready0 = "UPDATE route SET agency_id='$dopravce', route_short_name='$shortname', route_long_name='$longname', route_type='$routetype', route_color='$pozadi', route_text_color='$foreground', active='$aktif' WHERE (route_id = '$route');";
		$aktualz0 = mysqli_query ($link, $ready0);

		$ready1 = "DELETE FROM barvy WHERE route_id = '$route';";
		$del1 = mysqli_query ($link, $ready1);
		$ready2 = "INSERT INTO barvy VALUES ('$route', '$pozadi');";
		$aktualz2 = mysqli_query ($link, $ready2);

		$oldtrip = 0;
		$oldstop = 0;

		switch ($routetype) {
			case '3':
			case '5':
				$activity = 0;
			break;

			default:
				$activity = 2;
			break;
		}

		$query12 = "SELECT stop_id, trip_id FROM stoptime WHERE trip_id IN (SELECT trip_id FROM trip WHERE route_id = '$route') ORDER BY trip_id, stop_sequence;";
		if ($result12 = mysqli_query ($link, $query12)) {
			while ($row12 = mysqli_fetch_row ($result12)) {
				$stop_id = $row12[0];
				$trip_id = $row12[1];

				$query19 = "SELECT final FROM du WHERE stop1 = '$oldstop' AND stop2 = '$stop_id';";
				if ($result19 = mysqli_query ($link, $query19)) {
					$hit = mysqli_num_rows ($result19);
				}

				if ($hit == 0) {
					$coord = mysqli_fetch_row (mysqli_query ($link, "SELECT stop_lat, stop_lon FROM stop WHERE stop_id = '$stop_id';"));
					$stop_lat = $coord[0];
					$stop_lon = $coord[1];

					$prujezdy = $oldlon.",".$oldlat.";".$stop_lon.",".$stop_lat;

					if ($trip_id == $oldtrip) {
						$insert_query = "INSERT INTO du (stop1, stop2, via, path, final) VALUES ('$oldstop', '$stop_id', '', '$prujezdy', '$activity');";
						echo "$insert_query<br/>";
						$insert_action = mysqli_query ($link, $insert_query);
					}
				}
				$oldtrip = $trip_id;
				$oldstop = $stop_id;
				$oldlat = $stop_lat;
				$oldlon = $stop_lon;
			}
		}
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
			$aktual30 = mysqli_query ($link, $query30);
			
			$queryvazba = "DELETE FROM linevazba WHERE stop_id = '$stop_id';";
			$cistivazba = mysqli_query ($link, $queryvazba);

			$ready34 = "INSERT INTO linevazba (stop_id, stop_vazba) VALUES ('$stop_id', '$stop2_id');";
			$aktual34 = mysqli_query ($link, $ready34);
		}
	break;
}

echo "<table><tr><td>";
echo "<table>";
echo "<tr>";

$query50 = "SELECT * FROM route WHERE (route_id='$route');";
if ($result50 = mysqli_query ($link, $query50)) {
	while ($row50 = mysqli_fetch_row ($result50)) {
		$route_id = $row50[0];
		$agency_id = $row50[1];
		$route_short_name = $row50[2];
		$route_long_name = $row50[3];
		$route_type = $row50[5];
		$route_color = $row50[7];
		$route_active = $row50[9];
	}
}

echo "<form method=\"post\" action=\"routeedit.php\" name=\"oprav\"><input name=\"action\" value=\"oprav\" type=\"hidden\"><input name=\"route_id\" value=\"$route_id\" type=\"hidden\">";
echo "<td>Dopravce: <select name=\"dopravce\">";

$query24 = "SELECT agency_id, agency_name FROM agency ORDER BY agency_id;";
if ($result24 = mysqli_query ($link, $query24)) {
	while ($row24 = mysqli_fetch_row ($result24)) {
		$agid = $row24[0];
		$agname = $row24[1];

		echo "<option value=\"$agid\"";
		if ($agid == $agency_id) {
			echo " SELECTED";
		}
		echo ">$agname</option>";
	}
}

echo "</select><br>Typ linky: <select name=\"routetype\">";
echo "<option value=\"0\"";
if ($route_type == "0") {
	echo " SELECTED";
}
echo ">tramvaj</option>";
echo "<option value=\"1\"";
if ($route_type == "1") {
	echo " SELECTED";
}
echo ">metro</option>";
echo "<option value=\"2\"";
if ($route_type == "2") {
	echo " SELECTED";
}
echo ">vlak</option>";
echo "<option value=\"3\"";
if ($route_type == "3") {
	echo " SELECTED";
}
echo ">autobus</option>";
echo "<option value=\"4\"";
if ($route_type == "4") {
	echo " SELECTED";
}
echo ">přívoz</option>";
echo "<option value=\"5\"";
if ($route_type == "5") {
	echo " SELECTED";
}
echo ">trolejbus</option>";
echo "<option value=\"6\"";
if ($route_type == "6") {
	echo " SELECTED";
}
echo ">visutá lanovka</option>";
echo "<option value=\"7\"";
if ($route_type == "7") {
	echo " SELECTED";
}
echo ">kolejová lanovka</option>";
echo "</select>";

echo "</td><td style=\"background-color : #$route_color;\">Linka: <input type=\"text\" name=\"shortname\" value=\"$route_short_name\"><br />";

echo "<input type=\"text\" name=\"longname\" value=\"$route_long_name\"></td>";

echo "<td>Pozadí: <input type=\"color\" name=\"route_pozadi\" value=\"#$route_color\"></td>";

echo "<td>Aktivní <input type=\"checkbox\" name=\"aktif\" value=\"1\"";
if ($route_active == '1') {
	echo " CHECKED";
}
echo "></td><td><input type=\"submit\"></td></tr></form></table>";

$query79 = "SELECT DISTINCT route_id, route_name, route_type FROM analyza WHERE route_id = '$route_id';";
if ($result79 = mysqli_query ($link, $query79)) {
	while ($row79 = mysqli_fetch_row ($result79)) {
		$routeid = $row79[0];
		$routename = $row79[1];

		echo "$routename<br/>";
	}
}


echo "<a href=\"gentrip.php?route=$route_id\" target=\"_blank\">Generovat trasy</a><br/>";

echo "<form method=\"post\" action=\"routeedit.php\" name=\"zastavky\"><input name=\"action\" value=\"zastavky\" type=\"hidden\"><input name=\"route_id\" value=\"$route_id\" type=\"hidden\">";
$z = 0;
echo "<table>";
echo "<tr><td>";
echo "<table><tr><th>Zastávka</th><th>Vazba</th></tr>";

$query63 = "SELECT * FROM linestopsDB WHERE (stop_linka = '$route_id') AND (stop_smer = '0') ORDER BY stop_poradi;";
if ($result63 = mysqli_query ($link, $query63)) {
	while ($row63 = mysqli_fetch_row ($result63)) {
		$stop_id = $row63[0];
		$stop_name = $row63[1];
		$stop_vazba = $row63[4];

		echo "<tr><td>";
		echo "<input type=\"hidden\" name=\"stop_id$z\" value=\"$stop_id\">";
		echo "$stop_name</td><td>";
		echo "<select id=\"stop_vazba$z\" name=\"stop_vazba$z\">";
		echo "<option value=\"\">---</option>";
		$query82 = "SELECT stop_id, sortname, pomcode FROM stop WHERE active=1 AND obec LIKE '%' ORDER BY sortname;";
		if ($result82 = mysqli_query ($link, $query82)) {
			while ($row82 = mysqli_fetch_row ($result82)) {
				$stopid = $row82[0];
				$sortname = $row82[1];
				$stopcode = $row82[2];

				echo "<option value=\"$stopid\"";
				if ($stopid == $stop_vazba) {
					echo " SELECTED";
				}
				echo ">$sortname $stopcode</option>";
			}
		}
		$z = $z + 1;
		echo "</select>";
		echo "</td></tr>";
	}
}
echo "</table></td><td>";

echo "<table><tr><th>Zastávka</th><th>Vazba</th></tr>";
$query63 = "SELECT * FROM linestopsDB WHERE (stop_linka = '$route_id') AND (stop_smer = '1') ORDER BY stop_poradi DESC;";
if ($result63 = mysqli_query ($link, $query63)) {
	while ($row63 = mysqli_fetch_row ($result63)) {
		$stop_id = $row63[0];
		$stop_name = $row63[1];
		$stop_vazba = $row63[4];

		echo "<tr><td>";
		echo "<input type=\"hidden\" name=\"stop_id$z\" value=\"$stop_id\">";
		echo "$stop_name</td><td>";

		echo "<select id=\"stop_vazba$z\" name=\"stop_vazba$z\">";
		echo "<option value=\"\">---</option>";
		$query82 = "SELECT stop_id, sortname, pomcode FROM stop WHERE active=1 AND obec LIKE '%' ORDER BY sortname;";
		if ($result82 = mysqli_query ($link, $query82)) {
			while ($row82 = mysqli_fetch_row ($result82)) {
				$stopid = $row82[0];
				$sortname = $row82[1];
				$stopcode = $row82[2];

				echo "<option value=\"$stopid\"";
				if ($stopid == $stop_vazba) {
					echo " SELECTED";
				}
				echo ">$sortname $stopcode</option>";
			}
		}
		$z = $z + 1;
		echo "</select>";
		echo "</td></tr>";
	}
}

echo "</table><input type=\"hidden\" name=\"pocet\" value=\"$z-1\"><input type=\"submit\"></form>";
echo "</td><td><div id=\"mapa\" style=\"width:1280px; height:960px;\"></div><br/><div id=\"text\"></div>";
echo "</td></tr></table>";

echo "<table>";
echo "<tr><th>Linky odchozí</th><th>Linky příchozí</th></tr>";
echo "<tr><td>";
		
$query80 = "SELECT * FROM trip WHERE ((route_id = '$route_id') AND (direction_id='0')) ORDER BY trip_id;";
if ($result80 = mysqli_query ($link, $query80)) {
	while ($row80 = mysqli_fetch_row ($result80)) {
		$trip_id = $row80[2];
		$trip_headsign = $row80[3];
		$trip_aktif = $row80[10];

		if ($trip_aktif == '1') {
			echo "<span style=\"background-color:green;\">";
		}

		echo "$trip_id - $trip_headsign - <a href=\"tripedit.php?id=$trip_id\">Upravit</a><br />";
		if ($trip_aktif == '1') {
			echo "</span>";
		}
	}
}
echo "</td><td>";

$query96 = "SELECT * FROM trip WHERE ((route_id = '$route_id') AND (direction_id = '1')) ORDER BY trip_id;";
if ($result96 = mysqli_query ($link, $query96)) {
	while ($row96 = mysqli_fetch_row ($result96)) {
		$trip_id = $row96[2];
		$trip_headsign = $row96[3];
		$trip_aktif = $row96[10];

		if ($trip_aktif == '1') {
			echo "<span style=\"background-color:green;\">";
		}

		echo "$trip_id - $trip_headsign - <a href=\"tripedit.php?id=$trip_id\">Upravit</a><br />";
		if ($trip_aktif == '1') {
			echo "</span>";
		}
	}
}
echo "</td></tr></table>";

?>


<script type="text/javascript">
	function SelectElement(id, valueToSelect)
	{
		var element = document.getElementById(id);
		element.value = valueToSelect;
	}

	function addMarker(nazev, id, x, y) {
		var options = {
			title: nazev
		};

		var pozice = SMap.Coords.fromWGS84(Number(x), Number(y));
		var marker = new SMap.Marker(pozice, id, options);
		layer.addMarker(marker);
		markers.push(pozice);
	}

	function select(e) {
		var marker = e.target;
		var id = marker.getId();

		var focused = document.activeElement;
		if (!focused || focused == document.body)
			focused = null;
		else if (document.querySelector)
			focused = document.querySelector(":focus");

		if (poradi == "")
			oznaceno = focused.name;
		else
			oznaceno = "stop_vazba" + poradi;
		if (oldfocus != focused.name) {
			oznaceno = focused.name;
			oldfocus = focused.name;
		}
		SelectElement(oznaceno, id);
		document.getElementById(oznaceno).style = "background-color:green;";
		poradi = oznaceno.replace(/stop_vazba/g,"");
		poradi = Number(poradi) + 1;
	}

	var poradi = "";
	var oldfocus = "";
	var stred = SMap.Coords.fromWGS84(14.41, 50.08);
	var mapa = new SMap(JAK.gel("mapa"));
	mapa.addDefaultLayer(SMap.DEF_BASE).enable();

	mapa.addControl(new SMap.Control.Sync());
	var mouse = new SMap.Control.Mouse(SMap.MOUSE_PAN | SMap.MOUSE_WHEEL | SMap.MOUSE_ZOOM);
	mapa.addControl(mouse);

	var layer = new SMap.Layer.Marker();
	mapa.addLayer(layer);
	layer.enable();
	var markers = [];

<?php
$query30 = "SELECT stop_id, stop_name, stop_lon, stop_lat FROM stop WHERE obec = '$value' ORDER BY stop_id;";
if ($result30 = mysqli_query($link, $query30)) {
	while ($row30 = mysqli_fetch_row($result30)) {
		$stop_id = $row30[0];
		$stop_name = $row30[1];
		$longitude = $row30[2];
		$latitude = $row30[3];

		echo "addMarker('$stop_name', '$stop_id', $longitude, $latitude);\n";
	}
}

?>

	var cz = mapa.computeCenterZoom(markers);
	mapa.setCenterZoom(cz[0], cz[1]);

	var signals = mapa.getSignals();
	signals.addListener(window, "marker-click", select);

</script>


<?php
include 'footer.php';
?>
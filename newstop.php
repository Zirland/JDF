<?php
include 'header.php';

$action = @$_POST['action'];

$getlat = @$_GET['getlat'];
$getlon = @$_GET['getlon'];
$getobec = @$_GET['getobec'];
$getcastobce = @$_GET['getcastobce'];
$getmisto = @$_GET['getmisto'];
$getpomcode = @$_GET['getpomcode'];
$getid = @$_GET['getid'];

switch ($action) {
	case 'nova' :
		$stopcode = $_POST['stopcode'];
		$stoplat = $_POST['stoplat'];
		$stoplon = $_POST['stoplon'];
		$pomcode = $_POST['pomcode'];
		$kodobec = $_POST['kodobec'];
		$castobce = $_POST['castobce'];
		$misto = $_POST['misto'];
		$impid = $_POST['impid'];

		$pom18 = mysqli_fetch_row (mysqli_query ($link, "SELECT nazev_obce FROM obce WHERE lau2 = '$kodobec';"));
		$obec = $pom18[0];

		$query21 = "SELECT max FROM stop_count WHERE kodobce = '$kodobec';";
		if ($result21 = mysqli_query ($link, $query21)) {
			while ($row21 = mysqli_fetch_row ($result21)) {
				$max = $row21[0];
			}
		}

		$hit = mysqli_num_rows ($result21);
		if ($hit == 0) {
			$max = 0;
			$insert31 = mysqli_query ($link, "INSERT INTO stop_count (kodobce, max) VALUES ('$kodobec', '0');");
		}

		$newmax = $max + 1;
		$stopid = $kodobec."Z".$newmax;
		$update28 = mysqli_query ($link, "UPDATE stop_count SET max = '$newmax' WHERE kodobce = '$kodobec';");

		$stopname = $obec;
		if ($castobce != '') {
			$stopname .= ", ".$castobce;
		}
		if ($misto != '') {
			$stopname .= ", ".$misto;
		}

		if ($stopcode != '') {
			$stopname .= " (".$stopcode.")";
		}

		$sortname = "";
		if ($misto != '') {
			$sortname .= "$misto ";
		}
 		if ($castobce != '') {
 			$sortname .= "$castobce ";
 		}
 		$sortname .= $obec;
		if ($stopcode != '') {
			$sortname .= " $stopcode";
		}

		$query14 = "INSERT INTO stop (stop_id, stop_code, stop_name, stop_desc, stop_lat, stop_lon, zone_id, stop_url, location_type, stop_timezone, wheelchair_boarding, active, pomcode, obec, castobce, misto, sortname)  VALUES ('$stopid','$stopcode','$stopname','','$stoplat','$stoplon','','','0','','0','1', '$pomcode', '$obec', '$castobce', '$misto', '$sortname');";
		$prikaz14 = mysqli_query ($link, $query14);
		
		$deaktivace = "UPDATE shapetvary SET complete = '0' WHERE (tvartrasy LIKE '%$stopid|%'));";
		$prikaz19 = mysqli_query ($link, $deaktivace);

		$delimport = "DELETE FROM importstop WHERE id='$impid';";
		$prikazdel = mysqli_query ($link, $delimport);
	break;
}

echo "<table>";
echo "<tr><td colspan=\"4\">Insert new stop</td></tr>";

echo "<form method=\"post\" action=\"newstop.php\" name=\"nova\"><input name=\"action\" value=\"nova\" type=\"hidden\"><input name=\"impid\" value=\"$getid\" type=\"hidden\">";
		

echo "<tr><td>Obec</td><td>Část obce</td><td>Místo</td><td>Pomcode</td><td>Stop code</td><td>Latitude ~50.123456</td><td>Longitude ~16.987654</td></tr>";
echo "<tr><td><select name=\"kodobec\" autofocus>";
$query53 = "SELECT * FROM obce ORDER BY nazev_obce;";
if ($result53 = mysqli_query ($link, $query53)) {
	while ($row53 = mysqli_fetch_row ($result53)) {
		$kodokres = $row53[1];
		$kodobce = $row53[2];
		$nazevobce = $row53[3];

		echo "<option value=\"$kodobce\">$nazevobce $kodokres</option>";
	}
}
echo "</select>$getobec</td><td><input name=\"castobce\" value=\"$getcastobce\" type=\"text\"></td><td><input name=\"misto\" value=\"$getmisto\" type=\"text\"></td><td><input name=\"pomcode\" value=\"$getpomcode\" type=\"text\"></td><td><input name=\"stopcode\" value=\"\" type=\"text\"></td><td><input name=\"stoplat\" value=\"$getlat\" type=\"text\"></td><td><input name=\"stoplon\" value=\"$getlon\" type=\"text\"></td></tr>";
echo "<tr><td></td><td colspan=\"3\"><input type=\"submit\" value=\"Insert\"></form></td></tr>";
echo "</table>";

include 'footer.php';
?>

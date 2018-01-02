<?php
include 'header.php';

$stop_id = @$_GET['id'];
$action = @$_POST['action'];

switch ($action) {
	case 'edit' :
		$stop_id = $_POST['stopid'];
		$oldkodobce = $_POST['oldobec'];
		$stopkodobce = $_POST['kodobec'];
		$stopcastobce = $_POST['castobce'];
		$stopmisto = $_POST['misto'];
		$stoppomcode = $_POST['pomcode'];
		$stopstopcode = $_POST['stopcode'];
		$stoplat = $_POST['stoplat'];
		$stoplon = $_POST['stoplon'];
		
		if ($oldkodobce != $stopkodobce) {
			$query20 = "SELECT max FROM stop_count WHERE kodobce = '$stopkodobce';";
			if ($result20 = mysqli_query($link, $query20)) {
				while ($row20 = mysqli_fetch_row($result20)) {
					$max = $row20[0];
				}
			}
		
			$hit = mysqli_num_rows($result20);
			if ($hit == 0) {
				$max = 0;
				$insert31 = mysqli_query($link, "INSERT INTO stop_count (kodobce, max) VALUES ('$kodobec', '0');");
			}

			$newmax = $max + 1;
			$newstopid = $stopkodobce."Z".$newmax;
			$update28 = mysqli_query($link, "UPDATE stop_count SET max = '$newmax' WHERE kodobce = '$stopkodobce';");

			$update26 = mysqli_query($link, "UPDATE stoptime SET stop_id = '$newstopid' WHERE stop_id = '$stop_id';");
			$update27 = mysqli_query($link, "UPDATE linevazba SET stop_vazba = '$newstopid' WHERE stop_vazba = '$stop_id';");
			$update28 = mysqli_query($link, "UPDATE tripvazba SET stop_vazba = '$newstopid' WHERE stop_vazba = '$stop_id';");
						
		} else {$newstopid = $stop_id;}

		$pom18 = mysqli_fetch_row(mysqli_query($link, "SELECT nazev_obce FROM obce WHERE lau2 = '$stopkodobce';"));
		$obec = $pom18[0];

		$stopname = $obec;
		if ($stopcastobce != '') {$stopname .= ", ".$stopcastobce;}
		if ($stopmisto != '') {$stopname .= ", ".$stopmisto;}

		if ($stopstopcode != '') {$stopname .= " (".$stopstopcode.")";}

		$sortname = "";
		if ($stopmisto != '') {$sortname .= "$stopmisto ";}
 		if ($stopcastobce != '') {$sortname .= "$stopcastobce ";}
 		$sortname .= $obec;

		$query14 = "UPDATE stop SET stop_id = '$newstopid', obec = '$obec', castobce = '$stopcastobce', misto = '$stopmisto', stop_name = '$stopname', pomcode = '$stoppomcode', stop_code = '$stopstopcode', stop_lat = '$stoplat', stop_lon = '$stoplon', sortname = '$sortname' WHERE stop_id = '$stop_id';";
		// echo "$query14<br/>";
		$prikaz4 = mysqli_query($link, $query14);

		$deaktivace = "UPDATE shapetvary SET complete = '0' WHERE (tvartrasy LIKE '%$stop_id%'));";
		$prikaz19 = mysqli_query($link, $deaktivace);
		$stop_id = $newstopid;
	break;
}

echo "<table>";
echo "<tr><td colspan=\"4\">Edit stop</td></tr>";

echo "<form method=\"post\" action=\"stopedit.php\" name=\"edit\">
		<input name=\"action\" value=\"edit\" type=\"hidden\">
		<input name=\"stopid\" value=\"$stop_id\" type=\"hidden\">";
		
$query29 = "SELECT castobce, misto, pomcode, stop_code, stop_lat, stop_lon, obec, stop_name, stop_id FROM stop WHERE stop_id = '$stop_id';";
if ($result29 = mysqli_query ($link, $query29)) {
	while ($row29 = mysqli_fetch_row($result29)) {
		$kod_obec = substr($stop_id, 0, 6);
		$stop_cast = $row29[0];
		$stop_misto = $row29[1];
		$stop_pomcode = $row29[2];
		$stop_stopcode = $row29[3];
		$stop_lat = $row29[4];
		$stop_lon = $row29[5];
		$stop_obec = $row29[6];
		$stop_name = $row29[7];

echo "<tr><td>Obec</td><td>Část obce</td><td>Místo</td><td>Pomcode</td><td>Stop code</td><td>Latitude ~50.123456</td><td>Longitude ~16.987654</td></tr>";
echo "<tr><td><select name=\"kodobec\">";
$query53 = "SELECT * FROM obce ORDER BY nazev_obce;";
if ($result53 = mysqli_query($link, $query53)) {
	while ($row53 = mysqli_fetch_row($result53)) {
		$kodokres = $row53[1];
		$kodobce = $row53[2];
		$nazevobce = $row53[3];

		echo "<option value=\"$kodobce\"";
		if ($kodobce == $kod_obec) {echo " SELECTED";} 
		echo ">$nazevobce $kodokres</option>";
	}
}
echo "</select><input type=\"hidden\" name=\"oldobec\" value=\"$kod_obec\">$stop_obec</td><td><input name=\"castobce\" value=\"$stop_cast\" type=\"text\"></td><td><input name=\"misto\" value=\"$stop_misto\" type=\"text\"></td><td>$stop_name<input name=\"pomcode\" value=\"$stop_pomcode\" type=\"text\"></td><td><input name=\"stopcode\" value=\"$stop_stopcode\" type=\"text\"></td><td><input name=\"stoplat\" type=\"text\" value=\"$stop_lat\"></td><td><input name=\"stoplon\" type=\"text\" value=\"$stop_lon\"></td></tr>";

echo "<tr><td colspan=\"7\"><input type=\"submit\" value=\"Insert\"></td></tr>";
echo "</table>";
}
}
include 'footer.php';
?>

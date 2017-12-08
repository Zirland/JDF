<?php
include 'header.php';

$parent_id = @$_GET['id'];
$action = @$_POST['action'];

switch ($action) {
	case 'nova' :
		$stopid = $_POST['stopid'];
		$stopname = $_POST['stopname'];
		$stopcode = $_POST['stopcode'];
		$stoplat = $_POST['stoplat'];
		$stoplon = $_POST['stoplon'];
		$parent = $_POST['parent'];
//		$parent_id = $stopid;
		$fullname = $_POST['fullname'];
		$pomcode = $_POST['pomcode'];
		
		if ($stopname == '') {$stopname = $fullname;}
		
		$query14 = "INSERT INTO stop (stop_id, stop_code, stop_name, stop_desc, stop_lat, stop_lon, zone_id, stop_url, location_type, parent_station, stop_timezone, wheelchair_boarding, active, fullname, pomcode)  VALUES ('$stopid','$stopcode','$stopname','','$stoplat','$stoplon','','','$parent','','','0','1', '$fullname', '$pomcode');";
		echo $query14;
		$prikaz4 = mysqli_query($link, $query14);
		
		$deaktivace = "UPDATE shapetvary SET complete = '0' WHERE (tvartrasy LIKE '%$stop_id%'));";
		$prikaz19 = mysqli_query($link, $deaktivace);

		break;
		
	case 'sub' :
		$sub_id = $_POST['sub_id'];
		$parent_id = $_POST['parent_id'];

		$ready1 = "UPDATE stop SET parent_station = '$parent_id' WHERE stop_id = '$sub_id';";
		echo $ready1;
		$aktualz1 = mysqli_query($link, $ready1);
	break;

}

echo "<table>";
echo "<tr><td colspan=\"4\">Insert new stop</td></tr>";

echo "<form method=\"post\" action=\"newstop.php\" name=\"nova\">
		<input name=\"action\" value=\"nova\" type=\"hidden\">";
		

echo "<tr><td>Stop ID</td><td>Full name</td><td>Pomcode</td><td>Stop name</td><td>Stop code</td><td>Latitude ~50.123456</td><td>Longitude ~16.987654</td></tr>";
echo "<tr><td><input type=\"text\" name=\"stopid\"></td><td><input name=\"fullname\" value=\"\" type=\"text\"></td><td><input name=\"pomcode\" value=\"\" type=\"text\"></td><td><input name=\"stopname\" value=\"\" type=\"text\"></td><td><input name=\"stopcode\" value=\"\" type=\"text\"></td><td><input name=\"stoplat\" type=\"text\"></td><td><input name=\"stoplon\" type=\"text\"></td></tr>";
echo "<tr><td>0:<input type=\"radio\" name=\"parent\" value=\"0\"";
if ($parent == "0") {echo " CHECKED";}
echo ">1:<input type=\"radio\" name=\"parent\" value=\"1\"";
if ($parent == "1") {echo " CHECKED";}
echo "></td><td colspan=\"3\"><input type=\"submit\" value=\"Insert\"></form></td></tr>";
echo "</table>";

echo "<table>";
echo "<form method=\"post\" action=\"newstop.php\" name=\"sub\">
		<input name=\"action\" value=\"sub\" type=\"hidden\">
		<input name=\"parent_id\" value=\"$parent_id\" type=\"hidden\">";
$z = 1;

$query108 = "SELECT stop_id,fullname,pomcode FROM stop WHERE (parent_station = '$parent_id');";
if ($result108 = mysqli_query($link, $query108)) {
    while ($row108 = mysqli_fetch_row($result108)) {
	$stop_id = $row108[0];
	$nazev_stanice = $row108[1];
	$kod_stanice = $row108[2];
	
	echo "<tr><td>$stop_id - $nazev_stanice $kod_stanice</td>";
	echo "</tr>";
	$z = $z+1;
    }
	
	echo "<tr><td>";
	echo "<select name=\"sub_id\"><option value=\"\">-----</option>";
	$query194 = "SELECT stop_id, fullname, pomcode FROM stop WHERE location_type = '0' ORDER BY stop_name;";
	if ($result194 = mysqli_query($link, $query194)) {
		while ($row194 = mysqli_fetch_row($result194)) {
			$stopid = $row194[0];
			$stopname = $row194[1];
			$stopcode = $row194[2];

			echo "<option value=\"$stopid\">$stopid - $stopname $stopcode</option>";
		}
	}
	echo "</select></td>";
	echo "</tr>";
}
//echo "<input type=\"submit\" value=\"Parent\"></form>";
echo "</table>";

include 'footer.php';
?>

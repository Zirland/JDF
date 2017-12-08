<?php
include 'header.php';
echo "<form method=\"post\" action=\"_krok2.php\" name=\"linka\">";
echo "<table>";
echo "<tr><td colspan=\"2\">Vytvořit novou linku</td></tr>";
echo "<tr><td colspan=\"2\">Dopravce</td></tr>";
echo "<tr><td colspan=\"2\"><select name=\"agencyid\">";
$query8 = "SELECT agency_id, agency_name FROM agency;";
if ($result8 = mysqli_query($link, $query8)){
	while ($row8 = mysqli_fetch_row($result8)) {
		$agencykod = $row8[0];
		$agencypopis = $row8[1];

		echo "<option value=\"$agencykod\">$agencypopis</value>";
	}
}
echo "</select></td></tr>";
echo "<tr><td>Číslo linky</td><td>Název trasy</td></tr>";
echo "<tr><td><input type=\"text\" name=\"routeid\"></td><td><input name=\"fullname\" value=\"\" type=\"text\"></td></tr>";
echo "<tr><td>Jede od</td><td>Jede do</td></tr>";
echo "<tr><td><input name=\"jedeod\" value=\"01092017\" type=\"text\"></td><td><input name=\"jededo\" value=\"09122017\" type=\"text\"></td></tr>";
echo "<tr><td>Charakter dopravy</td><td>Dopravní prostředek</td></tr>";
echo "<tr><td><select name=\"areatype\">";
$query9 = "SELECT kod, popis FROM area_enum;";
if ($result9 = mysqli_query($link, $query9)){
	while ($row9 = mysqli_fetch_row($result9)) {
		$areakod = $row9[0];
		$areapopis = $row9[1];

		echo "<option value=\"$areakod\">$areapopis</value>";
	}
}
echo "</select></td><td><select name=\"routetype\">";
$query18 = "SELECT kod, popis FROM route_enum;";
if ($result18 = mysqli_query($link, $query18)){
	while ($row18 = mysqli_fetch_row($result18)) {
		$routekod = $row18[0];
		$routepopis = $row18[1];

		echo "<option value=\"$routekod\">$routepopis</value>";
	}
}
echo "</select></td></tr>";
echo "<tr><td>IDS</td><td>Linka IDS</td></tr>";
echo "<tr><td><select name=\"exter\">";
$query33 = "SELECT kod, nazev FROM exter_enum;";
if ($result33 = mysqli_query($link, $query33)){
	while ($row33 = mysqli_fetch_row($result33)) {
		$exterkod = $row33[0];
		$exterpopis = $row33[1];

		echo "<option value=\"$exterkod\">$exterpopis</value>";
	}
}
echo "</select></td><td><input name=\"shortname\" type=\"text\" value=\"\">";
echo "</td></tr>";
echo "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Zapsat\"></td></tr>";
echo "</table></form>";

include 'footer.php';
?>
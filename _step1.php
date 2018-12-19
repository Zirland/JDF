<?php
include 'header.php';

echo "<form method=\"post\" action=\"_step2.php\" name=\"linka\">";
echo "<input type=\"hidden\" name=\"action\" value=\"route\">";
echo "<table>";
echo "<tr><td colspan=\"2\">Vytvořit novou linku</td></tr>";
echo "<tr><td>Dopravce</td><td>Dopravní prostředek</td></tr>";
echo "<tr><td><select name=\"agencyid\">";
$query8 = "SELECT agency_id, agency_name FROM agency ORDER BY agency_name;";
if ($result8 = mysqli_query ($link, $query8)){
	while ($row8 = mysqli_fetch_row ($result8)) {
		$agencykod = $row8[0];
		$agencypopis = $row8[1];

		echo "<option value=\"$agencykod\">$agencypopis</value>";
	}
}
echo "</select></td><td><select name=\"routetype\">";
$query18 = "SELECT kod, popis FROM route_enum;";
if ($result18 = mysqli_query ($link, $query18)){
	while ($row18 = mysqli_fetch_row ($result18)) {
		$routekod = $row18[0];
		$routepopis = $row18[1];

		echo "<option value=\"$routekod\">$routepopis</value>";
	}
}
echo "</select></td></tr>";
echo "<tr><td>Číslo linky</td><td>Název trasy</td></tr>";
echo "<tr><td><input type=\"text\" name=\"routeid\"></td><td><input name=\"fullname\" value=\"\" type=\"text\"></td></tr>";
echo "<tr><td>Jede od</td><td>Jede do</td></tr>";

$jedeod = date ("Y-m-d", time ());
$jededo = date("Y-m-d", strtotime("+ 2 years"));


echo "<tr><td><input name=\"jedeod\" value=\"$jedeod\" type=\"text\"></td><td><input name=\"jededo\" value=\"$jededo\" type=\"text\"></td></tr>";
echo "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Zapsat\"></td></tr>";
echo "</table></form>";

include 'footer.php';
?>
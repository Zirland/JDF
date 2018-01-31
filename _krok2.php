<?php
include 'header.php';

$agencyid = $_POST['agencyid'];

$route_id = $_POST['routeid'];
$route_long_name = $_POST['fullname'];
$area_type = $_POST['areatype'];
$route_type = $_POST['routetype'];
$datumod = $_POST['jedeod'];
$datumdo = $_POST['jededo'];

$exter_id = $_POST['exter'];
$route_short_name = $_POST['shortname'];

mkdir ($route_id);

$today = date ("dmY", time());
$file = "$route_id/VerzeJDF.txt.txt";
$current = "\"1.11\",\"\",\"\",\"\",\"$today\",\"\";";
file_put_contents ($file, $current);

$query31 = "SELECT agency_name, agency_url FROM agency WHERE agency_id = '$agencyid';";
$pomoc31 = mysqli_fetch_row (mysqli_query ($link, $query31));
$agencyname = $pomoc31[0];
$agencyurl = $pomoc31[1];
$file = "$route_id/Dopravci.txt.txt";
$current = "\"$agencyid\",\"\",\"$agencyname\",\"1\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"$agencyurl\",\"1\";";
file_put_contents ($file, $current);


$file = "$route_id/Linky.txt.txt";
$current = "\"$route_id\",\"$route_long_name\",\"$agencyid\",\"$area_type\",\"$route_type\",\"0\",\"0\",\"0\",\"0\",\"\",\"\",\"\",\"\",\"$datumod\",\"$datumdo\",\"1\",\"1\";";
file_put_contents ($file, $current);

$file = "$route_id/Spoje.txt.txt";
$current = "";
file_put_contents ($file, $current);

$file = "$route_id/LinkyExt.txt.txt";
$current = "\"$route_id\",\"1\",\"$exter_id\",\"$route_short_name\",\"1\",\"\",\"1\";";
file_put_contents ($file, $current);

$file = "$route_id/Pevnykod.txt.txt";
$current = "\"1\",\"X\",\"\";\n";
$current .= "\"2\",\"+\",\"\";\n";
$current .= "\"8\",\"6\",\"\";\n";
$current .= "\"18\",\"x\",\"\";\n";
$current .= "\"21\",\"(\",\"\";\n";
$current .= "\"22\",\")\",\"\";\n";
file_put_contents ($file, $current);

echo "ZASTÁVKY<br />";
echo "<form action=\"_krok3.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"routeid\" value=\"$route_id\">";
echo "<textarea name=\"input\" cols=\"80\" rows=\"30\"></textarea>";
echo "<input type=\"submit\" value=\"Zapsat\">";
echo "</form>";

include 'footer.php';
?>
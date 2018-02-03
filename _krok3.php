<?php
include 'header.php';

$route_id = $_POST['routeid'];
$stanice = $_POST['input'];

$pole = preg_split ("/\r\n|\n|\r/", $stanice);

$x = 1;
$current0 = "";
$current1 = "";
foreach ($pole as $stanice) {
	$znam = "";
	if (strpos ($stanice, '[x]') !== false) {
		$znam="18";
		$stanice = str_replace ("[x]", "", $stanice);
	}

	$stanice = rtrim ($stanice);
	$stanice = ltrim ($stanice);

	$current0 .= "\"$x\",\"$stanice\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\";\n";
	$current1 .= "\"$route_id\",\"$x\",\"\",\"$x\",\"\",\"$znam\",\"\",\"\",\"1\";\n";
	
	$x = $x + 1;
}

$file = "$route_id/Zastavky.txt.txt";
file_put_contents ($file, $current0);

$file = "$route_id/Zaslinky.txt.txt";
file_put_contents ($file, $current1);

$file = "$route_id/Zasspoje.txt.txt";
$current2 = "";
file_put_contents ($file, $current2);

echo "SPOJE TABULKA<br/>";
echo "<form action=\"_krok4.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"routeid\" value=\"$route_id\">";
echo "<textarea name=\"input\" cols=\"80\" rows=\"30\"></textarea>";
echo "<input type=\"submit\" value=\"Zapsat\">";
echo "</form>";

include 'footer.php';
?>
<?php
include 'header.php';

$route_id = $_POST['routeid'];
$stanice = $_POST['input'];

$pole = preg_split("/\r\n|\n|\r/", $stanice);

$x=1;
$current0 = "";
$current1 = "";

foreach ($pole as $stanice) {
	$stanice = rtrim($stanice);
	if ($x == 1) {
		echo "<b>$stanice</b><br>";
		if ($stanice == 'Pracovní den') {$cal = "1\",\"";}
		if ($stanice == 'Sobota + Neděle') {$cal = "8\",\"2";}
	}
	if ($x == 2) {
		$spoje = preg_split("/\t/", $stanice);
		foreach ($spoje as $radek) {
			$current0 .= "\"$route_id\",\"$radek\",\"$cal\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\";\n";
			$DB24 = mysqli_query ($link, "INSERT INTO manspoje VALUES ($radek);");
		}
	}
	if ($x>3) {
		$jizda = preg_split("/\t/", $stanice);
		$index = 0;
		$zast = $x-3;
		foreach ($jizda as $prijezd) {
			$casprijezd = substr($prijezd,0,2) . substr($prijezd,-2);
			$current1 .= "\"$route_id\",\"$spoje[$index]\",\"$zast\",\"$zast\",\"\",\"\",\"\",\"\",\"\",\"$casprijezd\",\"$casprijezd\",\"1\";\n";
			$index = $index+1;
		}
	}
	$x=$x+1;
}

$file = $route_id."-Spoje.txt.txt";
echo "$file - $current0";
file_put_contents($file, $current0, FILE_APPEND);

$file = $route_id."-Zasspoje.txt.txt";
echo "$file - $current1";
file_put_contents($file, $current1, FILE_APPEND);

echo "SPOJE TABULKA<br/>";
echo "<form action=\"_krok4.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"routeid\" value=\"$route_id\">";
echo "<textarea name=\"input\" cols=\"80\" rows=\"30\"></textarea>";
echo "<input type=\"submit\" value=\"Zapsat\">";
echo "</form>";

include 'footer.php';
?>

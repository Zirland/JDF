<?php
include 'header.php';

$route_id = $_POST['routeid'];
$jizda = $_POST['input'];

$pole = preg_split ("/\r\n|\n|\r/", $jizda);

$x = 1;
$current0 = "";
$current1 = "";

foreach ($pole as $jizda) {
	$jizda = rtrim ($jizda);
	if ($x == 1) {
		echo "<b>$jizda</b><br>";
		if ($jizda == 'Pracovní den') {
			$cal = "1\",\"";
		}
		if ($jizda == 'Sobota + Neděle') {
			$cal = "8\",\"2";
		}
		if ($jizda == 'Jede denně') {
			$cal = "\",\"";
		}
	}
	if ($x == 2) {
		$spoje = preg_split ("/\t/", $jizda);
		foreach ($spoje as $radek) {
			$current0 .= "\"$route_id\",\"$radek\",\"$cal\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\";\n";
			$DB24 = mysqli_query ($link, "INSERT INTO manspoje VALUES ($radek,'');");
		}
	}
	if ($x == 3) {
		$kody = preg_split ("/\t/", $jizda);
		$index = 0;
		foreach ($kody as $kod) {
			$DB37 = mysqli_query ($link, "UPDATE manspoje SET kod=$kod WHERE spoj = $spoje[$index];");
			$index = $index + 1;
		}
	}
	if ($x>3) {
		$jizda = preg_split ("/\t/", $jizda);
		$index = 0;
		$zast = $x-3;
		
		foreach ($jizda as $prijezd) {
			$pk = "";
			if (strpos ($prijezd, '(') !== false) {
				$pk="21";
				$prijezd = str_replace ("(", "", $prijezd);
			}
			if (strpos ($prijezd, ')') !== false) {
				$pk="22";
				$prijezd = str_replace (")", "", $prijezd);
			}

			$casprijezd = substr ($prijezd,0,2) . substr ($prijezd,-2);
			$current1 .= "\"$route_id\",\"$spoje[$index]\",\"$zast\",\"$zast\",\"\",\"\",\"$pk\",\"\",\"\",\"$casprijezd\",\"$casprijezd\",\"1\";\n";
			$index = $index + 1;
		}
	}
	$x = $x + 1;
}

$file = "$route_id/Spoje.txt.txt";
file_put_contents ($file, $current0, FILE_APPEND);

$file = "$route_id/Zasspoje.txt.txt";
file_put_contents ($file, $current1, FILE_APPEND);

echo "SPOJE TABULKA<br/>";
echo "<form action=\"_krok4.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"routeid\" value=\"$route_id\">";
echo "<textarea name=\"input\" cols=\"80\" rows=\"30\"></textarea>";
echo "<input type=\"submit\" value=\"Zapsat\">";
echo "</form>";

include 'footer.php';
?>
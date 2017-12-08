<?php
include 'header.php';

$route_id = $_POST['routeid'];
$stanice = $_POST['input'];

$pole = preg_split("/\r\n|\n|\r/", $stanice);

$x=1;
$current0 = "";
$current1 = "";
foreach ($pole as $stanice) {
	$stanice = str_replace("[přestup na vlak]", "", $stanice);
	$stanice = str_replace("[zastávka je bezbariérově přístupná]", "", $stanice);
	$stanice = str_replace(" p", "", $stanice);
	$stanice = str_replace(" ,", "", $stanice);
	$znam = "";
	if (strpos($stanice, '[zastávka nebo spoj na znamení]') !== false) {
		$znam="18";
		$stanice = str_replace("[zastávka nebo spoj na znamení]", "", $stanice);
	}
		
	$stanice = rtrim($stanice);
	
	$current0 .= "\"$x\",\"$stanice\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\";\n";
	$current1 .= "\"$route_id\",\"$x\",\"\",\"$x\",\"\",\"$znam\",\"\",\"\",\"1\";\n";
	
	$x=$x+1;
}

$file = $route_id."-Zastavky.txt.txt";
file_put_contents($file, $current0);

$file = $route_id."-Zaslinky.txt.txt";
file_put_contents($file, $current1);

echo "<form action=\"_krok4.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"routeid\" value=\"$route_id\">";
echo "<textarea name=\"input\" cols=\"80\" rows=\"30\"></textarea>";
echo "<input type=\"submit\" value=\"Zapsat\">";
echo "</form>";

include 'footer.php';
?>

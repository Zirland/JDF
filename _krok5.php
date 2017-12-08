<?php
include 'header.php';

$route_id = $_GET['routeid'];
$action = $_POST['action'];

switch ($action) {
	case 'newcode' :
		$route_id = $_POST['routeid'];
		$znacka = $_POST['negative'];
		$typkodu = $_POST['typkodu'];
		$kodod = $_POST['kodod'];
		$koddo = $_POST['koddo'];

		if ($koddo == "") {$koddo = $kodod;}

		$query15 = "INSERT INTO man_ck VALUES (\"$znacka\", \"$typkodu\", \"$kodod\", \"$koddo\");";
		$prikaz15 = mysqli_query($link, $query15);
    break;
}

$query20 = "SELECT * FROM man_ck ORDER BY negative";
if ($result20 = mysqli_query($link, $query20)) {
	while ($row20 = mysqli_fetch_row($result20)) {
		$negative = $row20[0];
		$typ = $row20[1];
		$kodod = $row20[2];
		$koddo = $row20[3];

		echo "|$negative|$typ|$kodod|$koddo|<br />";
	}
}

echo "NOVÝ KÓD<br/>";
echo "<form action=\"_krok5.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"routeid\" value=\"$route_id\">";
echo "<input type=\"hidden\" name=\"action\" value=\"newcode\">";
echo "ZNAČKA: <input type=\"text\" name=\"negative\" value=\"\">";
echo "TYP: <select name=\"typkodu\">";

$query24 = "SELECT * FROM ck_enum ORDER BY kod";
if ($result24 = mysqli_query($link, $query24)) {
	while ($row24 = mysqli_fetch_row($result24)) {
		$kod = $row24[0];
		$popis = $row24[1];

		echo "<option value=\"$kod\">$popis</option>";
	}
}

echo "</select>";
echo "OD: <input type=\"text\" name=\"kodod\" value=\"\">";
echo "DO: <input type=\"text\" name=\"koddo\" value=\"\">";

echo "<input type=\"submit\" value=\"Zapsat\">";
echo "</form>";


echo "<hr>";


// "$linka","$spoj","$X","$NEG","$TYP","$OD","$DO","","1";


$file = $route_id."-Zasspoje.txt.txt";
echo "$file - $current1";
// file_put_contents($file, $current1, FILE_APPEND);

echo "SPOJE TABULKA<br/>";
echo "<form action=\"_krok4.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"routeid\" value=\"$route_id\">";
echo "<textarea name=\"input\" cols=\"80\" rows=\"30\"></textarea>";
echo "<input type=\"submit\" value=\"Zapsat\">";
echo "</form>";

include 'footer.php';
?>
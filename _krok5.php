<?php
include 'header.php';

$route_id = $_GET['routeid'];
$action = $_POST['action'];
$act2 = $_GET['action'];

if (!isset($action)) {
	$action = $act2;
}

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

	case 'export' :
		$current = "";
		$query38 = "SELECT * FROM manspoje WHERE kod != '0' ORDER BY spoj";
		if ($result38 = mysqli_query($link, $query38)) {
			while ($row38 = mysqli_fetch_row($result38)) {
				$spoj = $row38[0];
				$negative = $row38[1];

				$j = 1;
				$query44 = "SELECT * FROM man_ck WHERE negative=$negative;";
				if ($result44 = mysqli_query($link, $query44)) {
					while ($row44 = mysqli_fetch_row($result44)) {
						$negative = $row44[0];
						$typ = $row44[1];
						$kodod = $row44[2];
						$koddo = $row44[3];

						$current .= "\"$route_id\",\"$spoj\",\"$j\",\"$negative\",\"$typ\",\"$kodod\",\"$koddo\",\"\",\"1\";\n";
						$j = $j + 1;
			}
		}
		$file = "$route_id/Caskody.txt.txt";
		file_put_contents($file, $current);
	break;
}

$i = 1;
$query20 = "SELECT * FROM man_ck ORDER BY negative";
if ($result20 = mysqli_query($link, $query20)) {
	while ($row20 = mysqli_fetch_row($result20)) {
		$negative = $row20[0];
		$typ = $row20[1];
		$kodod = $row20[2];
		$koddo = $row20[3];

		echo "$negative ($i)\t$typ\t$kodod\t$koddo<br />";
		$i = i + 1;
	}
}

echo "<hr>";

$query38 = "SELECT * FROM manspoje WHERE kod != '0' ORDER BY spoj";
if ($result38 = mysqli_query($link, $query38)) {
	while ($row38 = mysqli_fetch_row($result38)) {
		$spoj = $row38[0];
		$negative = $row38[1];

		echo "$route_id\t$spoj\t$negative<br />";
	}
}

echo "<hr>";


$query38 = "SELECT * FROM manspoje WHERE kod != '0' ORDER BY spoj";
if ($result38 = mysqli_query($link, $query38)) {
	while ($row38 = mysqli_fetch_row($result38)) {
		$spoj = $row38[0];
		$negative = $row38[1];

$j = 1;
		$query44 = "SELECT * FROM man_ck WHERE negative=$negative;";
		if ($result44 = mysqli_query($link, $query44)) {
			while ($row44 = mysqli_fetch_row($result44)) {
				$negative = $row44[0];
				$typ = $row44[1];
				$kodod = $row44[2];
				$koddo = $row44[3];

				echo "\"$route_id\",\"$spoj\",\"$j\",\"$negative\",\"$typ\",\"$kodod\",\"$koddo\",\"\",\"1\";<br />";
				$j = $j + 1;
			}
		}
	}
}

echo "<hr>";

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


echo "Nepovolené kombinace: 5,6,7,8 | 5,6,7 | 5,6,8 | 5,6 | 5,7 | 5,8 | 5,7,8 | 6,7,8 | 6,7 | 6,8 | 7,8 | 1,7 | 1,8 | 1,7,8<br />";

echo "<hr>";

echo "<a href=\"_krok5.php?routeid=$route_id&action=export\">Exportovat kódy</a>";

include 'footer.php';
?>

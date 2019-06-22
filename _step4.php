<?php
include 'header.php';

$route_id = $_POST['routeid'];
if ($route_id == "") {
	$route_id = $_GET['routeid'];
}
$action = $_POST['action'];

switch ($action) {
	case 'newcode' :
		$route_id = $_POST['routeid'];
		$znacka = $_POST['negative'];
		$typkodu = $_POST['typkodu'];
		$kodod = $_POST['kodod'];
		$koddo = $_POST['koddo'];

		if ($koddo == "") {
			$koddo = $kodod;
		}

		$query22 = "INSERT INTO man_ck (negative, typ, kodod, koddo, route_id) VALUES (\"$znacka\", \"$typkodu\", \"$kodod\", \"$koddo\",\"$route_id\");";
		$prikaz22 = mysqli_query ($link, $query22);
	break;

	case 'oznac' :
		$negative = $_POST['negative'];
		$spoj = $_POST['spoj'];

		$query30 = "INSERT INTO manspoje (spoj, kod, route_id) VALUES ('$spoj', '$negative', '$route_id');";
		$prikaz30 = mysqli_query($link, $query30);
	break;
}

$i = 1;
$query36 = "SELECT * FROM man_ck ORDER BY negative";
if ($result36 = mysqli_query ($link, $query36)) {
	while ($row36 = mysqli_fetch_row ($result36)) {
		$negative = $row36[1];
		$typ = $row36[2];
		$kodod = $row36[3];
		$koddo = $row36[4];

		echo "$negative ($i)\t$typ\t$kodod\t$koddo<br />";
		$i = $i + 1;
	}
}

echo "<hr>";

$query51 = "SELECT * FROM manspoje WHERE route_id = '$route_id' ORDER BY spoj";
if ($result51 = mysqli_query ($link, $query51)) {
	while ($row51 = mysqli_fetch_row ($result51)) {
		$spoj = $row51[1];
		$negative = $row51[2];

		echo "$route_id\t$spoj\t$negative<br />";
	}
}

echo "<hr>";

echo "NOVÝ KÓD<br/>";
echo "<form action=\"_step4.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"routeid\" value=\"$route_id\">";
echo "<input type=\"hidden\" name=\"action\" value=\"newcode\">";
echo "ZNAČKA: <input type=\"text\" name=\"negative\" value=\"\">";
echo "TYP: <select name=\"typkodu\">";

$query70 = "SELECT * FROM ck_enum ORDER BY kod";
if ($result70 = mysqli_query ($link, $query70)) {
	while ($row70 = mysqli_fetch_row ($result70)) {
		$kod = $row70[0];
		$popis = $row70[1];

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

echo "OZNAČ SPOJ<br/>";
echo "<form action=\"_step4.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"routeid\" value=\"$route_id\">";
echo "<input type=\"hidden\" name=\"action\" value=\"oznac\">";
echo "ZNAČKA: <select name=\"negative\">";

$query98 = "SELECT DISTINCT negative FROM man_ck ORDER BY negative";
if ($result98 = mysqli_query ($link, $query98)) {
	while ($row98 = mysqli_fetch_row ($result98)) {
		$negative = $row98[0];

		echo "<option value=\"$negative\">$negative</option>";
	}
}

echo "</select>";
echo "SPOJ: <select name=\"spoj\">";

$query110 = "SELECT trip_no FROM mtrips WHERE route_id = '$route_id' ORDER BY trip_no";
if ($result110 = mysqli_query ($link, $query110)) {
	while ($row110 = mysqli_fetch_row ($result110)) {
		$trip_no = $row110[0];

		echo "<option value=\"$trip_no\">$trip_no</option>";
	}
}

echo "</select>";
echo "<input type=\"submit\" value=\"Zapsat\">";
echo "</form>";

include 'footer.php';
?>
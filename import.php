<?php
include 'header.php';

$action = @$_POST['action'];

echo "<form method=\"post\" action=\"import.php\" name=\"linka\">";
echo "<input type=\"hidden\" name=\"action\" value=\"import\">";
echo "<textarea name=\"routes\" cols=\"30\" rows=\"5\"></textarea>";
echo "<input type=\"submit\" value=\"Generovat\">";
echo "</form>";

switch ($action) {
	case 'import' :
		$vstup = $_POST['routes'];

		$pole = preg_split ("/\r\n|\n|\r/", $vstup);

		foreach ($pole as $route) {
			$DB19 = mysqli_query ($link, "DELETE FROM analyza WHERE dir=$route;");
			echo "Linka $route > <a href=\"sort.php?imp=$route\" target=\"_blank\">Generovat</a><br/>";
		}
	break;
}

mysqli_close ($link);
?>
<?php
include 'header.php';

$action = @$_POST['action'];
$from = @$_POST['from'];
$to = @$_POST['to'];
$du_id = @$_POST['du_id'];
$path = @$_POST['path'];

echo "<form method=\"post\" action=\"trasaedit.php\" name=\"odkud\"><input name=\"action\" value=\"odkud\" type=\"hidden\">";
echo "Odkud: <select name=\"from\">";
$query0 = "SELECT stop_id, sortname, pomcode FROM stop WHERE stop_id IN (SELECT stop1 FROM du) ORDER BY sortname;";
if ($result0 = mysqli_query ($link, $query0)) {
	while ($row0 = mysqli_fetch_row ($result0)) {
		$kodf = $row0[0];
		$nazevf = $row0[1];
		$codef = $row0[2];
		echo "<option value=\"$kodf\"";
		if ($kodf == $from) {
			echo " SELECTED";
		}
		echo ">$nazevf $codef $kodf</option>";
	}
	mysqli_free_result ($result0);
} else {
	echo "Error description: ".mysqli_error ($link);
}

echo "</select>";
echo "<input type=\"submit\"></form>";

switch ($action) {
	case "edit":
		$du_id = $_POST['du_id'];
		$pocet = $_POST['pocet'];
		$start = $_POST['start'];
		$end = $_POST['end'];
		$newpoint = $_POST['newpoint'];
		$coord = $_POST['coord'];

		$path = "";
		for ($y = 1; $y < $pocet; $y++) {
			$$ind = $y;
			$delindex = "delete".${$ind};
			$delete = $_POST[$delindex];
			$xindex = "x".${$ind};
			$ptx = $_POST[$xindex];
			$yindex = "y".${$ind};
			$pty = $_POST[$yindex];

			if ($delete != "1") {$path .= "$ptx,$pty;";}
		}
		$path = substr($path, 0, -1);

		if ($coord != "") {
			if ($start == "1") {
				$path = "$coord;".$path; 
			}
			if ($end == "1") {
				$path = $path.";$coord"; 
			}
		} else {
			if ($start == "1") {
				$query54 = "SELECT stop_lat, stop_lon FROM stop WHERE stop_id = '$newpoint';";
				if ($result54 = mysqli_query($link, $query54)) {
					$row54 = mysqli_fetch_row($result54);
					$lat = $row54[0];
					$lon = $row54[1];
				}
				$path = "$lon,$lat;".$path; 
			}
			if ($end == "1") {
				$query64 = "SELECT stop_lat, stop_lon FROM stop WHERE stop_id = '$newpoint';";
				if ($result64 = mysqli_query($link, $query64)) {
					$row64 = mysqli_fetch_row($result64);
					$lat = $row64[0];
					$lon = $row64[1];
				}
				$path = $path.";$lon,$lat"; 
			}
		}

		$body = explode (";", $path);

		echo "<form method=\"post\" action=\"trasaedit.php\" name=\"edit\"><input name=\"action\" value=\"edit\" type=\"hidden\"><input name=\"du_id\" value=\"$du_id\" type=\"hidden\">";

		$j = 0;
		$xmin = 20;
		$xmax = 0;
		$ymin = 60;
		$ymax = 0;
		foreach ($body as $point) {
			$j = $j + 1;
			$point = explode (",", $point);
			$pt_x = $point[0];
			$pt_y = $point[1];
			
			if ($pt_x < $xmin) {
				$xmin = $pt_x;
			}
			if ($pt_y < $ymin) {
				$ymin = $pt_y;
			}
			if ($pt_x > $xmax) {
				$xmax = $pt_x;
			}
			if ($pt_y > $ymax) {
				$ymax = $pt_y;
			}

			echo "$j | <input type=\"hidden\" name=\"x$j\" value=\"$pt_x\">$pt_x | <input type=\"hidden\" name=\"y$j\" value=\"$pt_y\">$pt_y | <input type=\"checkbox\" name=\"delete$j\" value=\"1\"><br/>";

		}
		echo "<input type=\"checkbox\" name=\"start\" value=\"1\"><select name=\"newpoint\">";
		$query229 = "SELECT stop_id, sortname, pomcode FROM stop ORDER BY sortname;";
		if ($result229 = mysqli_query ($link, $query229)) {
			while ($row229 = mysqli_fetch_row ($result229)) {
				$kodn = $row229[0];
				$nazevn = $row229[1];
				$coden = $row229[2];
				echo "<option value=\"$kodn\">$nazevn $coden $kodn</option>";
			}
			mysqli_free_result($result1);
		} else {
			echo "Error description: " . mysqli_error($link);
		}

		echo "</select><input type=\"text\" name=\"coord\"><input type=\"checkbox\" name=\"end\" value=\"1\">";
		$count = $j + 1;
		echo "<input type=\"hidden\" name=\"pocet\" value=\"$count\">";
		echo "<input type=\"submit\"></form>";

		$deltax = $xmax - $xmin;
		$deltay = $ymax - $ymin;

		echo "<canvas id=\"a\" width=\"800\" height=\"600\">";
		echo "This text is displayed if your browser does not support HTML5 Canvas.";
		echo "</canvas>";
		echo "<script type='text/javascript'>";
		echo "	var a_canvas = document.getElementById(\"a\");";
		echo "	var context = a_canvas.getContext(\"2d\");";

		$i = 0;
		foreach ($body as $point) {
			$point = explode (",", $point);
			$pt_x = $point[0];
			$pt_y = $point[1];
			
			$coorx = 784 * (($pt_x - $xmin) / $deltax) + 8;
			$coory = 600 - (584 * (($pt_y - $ymin) / $deltay) + 8);
			if ($i == 0) {
				echo "context.beginPath();";
				echo "context.moveTo($coorx,$coory);";
			}
			if ($i > 0) {
				echo "context.lineTo($coorx,$coory);";
			}
			$i = $i + 1;
		}
		echo "context.strokeStyle = \"#000\";";
		echo "context.stroke();";
		echo "</script>";

		echo "<form method=\"post\" action=\"trasaedit.php\" name=\"uloz\"><input name=\"action\" value=\"uloz\" type=\"hidden\"><input name=\"du_id\" value=\"$du_id\" type=\"hidden\"><input name=\"path\" value=\"$path\" type=\"hidden\"><input type=\"submit\" value=\"Zapsat\"></form>";
	break;


	case "uloz":
		$query159 = "UPDATE du SET path = '$path' WHERE du_id = '$du_id';";
		$zapis159 = mysqli_query ($link, $query159);
		$query162 = "UPDATE shapetvary SET complete = '0' WHERE tvartasy LIKE '%$from|$to|%';";
		$zapis162 = mysqli_query ($link, $query162);
		$action = "kam";

	case "odkud":
		echo "<form method=\"post\" action=\"trasaedit.php\" name=\"kam\"><input name=\"action\" value=\"kam\" type=\"hidden\"><input name=\"from\" value=\"$from\" type=\"hidden\">";
		echo "Kam: <select name=\"to\">";
		$query1 = "SELECT stop_id, sortname, pomcode FROM stop WHERE stop_id IN (SELECT stop2 FROM du WHERE stop1 = '$from') ORDER BY sortname;";
		echo $query1;
		if ($result1 = mysqli_query ($link, $query1)) {
			while ($row1 = mysqli_fetch_row ($result1)) {
				$kodt = $row1[0];
				$nazevt = $row1[1];
				$codet = $row1[2];
				echo "<option value=\"$kodt\"";
				if ($kodt == $to) {
					echo " SELECTED";
				}
				echo ">$nazevt $codet $kodt</option>";
			}
			mysqli_free_result($result1);
		} else {
			echo "Error description: " . mysqli_error($link);
		}

		echo "</select>";
		echo "<input type=\"submit\"></form>";
	break;

	case "kam" : 
		echo "<form method=\"post\" action=\"trasaedit.php\" name=\"kam\"><input name=\"action\" value=\"kam\" type=\"hidden\"><input name=\"from\" value=\"$from\" type=\"hidden\">";
		echo "Kam: <select name=\"to\">";
		$query1 = "SELECT stop_id, sortname, pomcode FROM stop WHERE stop_id IN (SELECT stop2 FROM du WHERE stop1 = '$from') ORDER BY sortname;";
		echo $query1;
		if ($result1 = mysqli_query ($link, $query1)) {
			while ($row1 = mysqli_fetch_row ($result1)) {
				$kodt = $row1[0];
				$nazevt = $row1[1];
				$codet = $row1[2];
				echo "<option value=\"$kodt\"";
				if ($kodt == $to) {
					echo " SELECTED";
				}
				echo ">$nazevt $codet $kodt</option>";
			}
			mysqli_free_result($result1);
		} else {
			echo "Error description: " . mysqli_error($link);
		}

		echo "</select>";

		echo "<input type=\"submit\"></form>";

		$query47 = "SELECT stop_lat, stop_lon FROM stop WHERE stop_id = '$from';";
		if ($result47 = mysqli_query($link, $query47)) {
			$row47 = mysqli_fetch_row($result47);
			$fromlat = $row47[0];
			$fromlon = $row47[1];
		}
		$query53 = "SELECT stop_lat, stop_lon FROM stop WHERE stop_id = '$to';";
		if ($result53 = mysqli_query($link, $query53)) {
			$row53 = mysqli_fetch_row($result53);
			$tolat = $row53[0];
			$tolon = $row53[1];
		}

		$query102 = "SELECT du_id, path FROM du WHERE stop1 = '$from' AND stop2 = '$to';";
		if ($result102 = mysqli_query($link, $query102)) {
			$row102 = mysqli_fetch_row($result102);
			$du_id = $row102[0];
			$path = $row102[1];
		}
		echo "$du_id<br/>";
		$body = explode (";", $path);

		echo "<form method=\"post\" action=\"trasaedit.php\" name=\"edit\"><input name=\"action\" value=\"edit\" type=\"hidden\"><input name=\"du_id\" value=\"$du_id\" type=\"hidden\">";

		$j = 0;
		$xmin = 20;
		$xmax = 0;
		$ymin = 60;
		$ymax = 0;
		foreach ($body as $point) {
			$j = $j + 1;
			$point = explode (",", $point);
			$pt_x = $point[0];
			$pt_y = $point[1];
			
			if ($pt_x < $xmin) {
				$xmin = $pt_x;
			}
			if ($pt_y < $ymin) {
				$ymin = $pt_y;
			}
			if ($pt_x > $xmax) {
				$xmax = $pt_x;
			}
			if ($pt_y > $ymax) {
				$ymax = $pt_y;
			}

			echo "$j | <input type=\"hidden\" name=\"x$j\" value=\"$pt_x\">$pt_x | <input type=\"hidden\" name=\"y$j\" value=\"$pt_y\">$pt_y | <input type=\"checkbox\" name=\"delete$j\" value=\"1\"><br/>";

		}
		echo "<input type=\"checkbox\" name=\"start\" value=\"1\"><select name=\"newpoint\">";
		$query229 = "SELECT stop_id, sortname, pomcode FROM stop ORDER BY sortname;";
		if ($result229 = mysqli_query ($link, $query229)) {
			while ($row229 = mysqli_fetch_row ($result229)) {
				$kodn = $row229[0];
				$nazevn = $row229[1];
				$coden = $row229[2];
				echo "<option value=\"$kodn\">$nazevn $coden $kodn</option>";
			}
			mysqli_free_result($result1);
		} else {
			echo "Error description: " . mysqli_error($link);
		}

		echo "</select><input type=\"text\" name=\"coord\"><input type=\"checkbox\" name=\"end\" value=\"1\">";
		$count = $j + 1;
		echo "<input type=\"hidden\" name=\"pocet\" value=\"$count\">";
		echo "<input type=\"submit\"></form>";

		$deltax = $xmax - $xmin;
		$deltay = $ymax - $ymin;

		echo "<canvas id=\"a\" width=\"800\" height=\"600\">";
		echo "This text is displayed if your browser does not support HTML5 Canvas.";
		echo "</canvas>";
		echo "<script type='text/javascript'>";
		echo "	var a_canvas = document.getElementById(\"a\");";
		echo "	var context = a_canvas.getContext(\"2d\");";

		$i = 0;
		foreach ($body as $point) {
			$point = explode (",", $point);
			$pt_x = $point[0];
			$pt_y = $point[1];
			
			$coorx = 784 * (($pt_x - $xmin) / $deltax) + 8;
			$coory = 600 - (584 * (($pt_y - $ymin) / $deltay) + 8);
			if ($i == 0) {
				echo "context.beginPath();";
				echo "context.moveTo($coorx,$coory);";
			}
			if ($i > 0) {
				echo "context.lineTo($coorx,$coory);";
			}
			$i = $i + 1;
		}
		echo "context.strokeStyle = \"#000\";";
		echo "context.stroke();";
		echo "</script>";
	break;
}

include 'footer.php';
?>

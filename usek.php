<?php
include 'header.php';


$action = $_POST['action'];
$delete = $_POST['delete'];
$usek_id = $_GET['du_id'];
if (!$usek_id) {
	$usek_id = $_POST['id_usek'];
}

echo "<form method=\"post\" action=\"usek.php\" name=\"odkud\"><input name=\"action\" value=\"search\" type=\"hidden\">";
echo "<input type=\"text\" name=\"id_usek\" value=\"$usek_id\"><input type=\"checkbox\" name=\"delete\" value=\"1\"><input type=\"submit\"></form>";

if ($action == "search") {

$query102 = "SELECT path, stop1, stop2 FROM du WHERE du_id = '$usek_id';";
echo $query102;
if ($result102 = mysqli_query($link, $query102)) {
	$row102 = mysqli_fetch_row($result102);
	$path = $row102[0];
	$from = $row102[1];
	$to = $row102[2];
}

$query2 = "SELECT stop_name, pomcode FROM stop WHERE stop_id = '$from';";
if ($result2 = mysqli_query ($link, $query2)) {
	while ($row2 = mysqli_fetch_row ($result2)) {
		$nazevv = $row2[0];
		$codev = $row2[1];
		echo "From: $nazevv $codev | ";
	}
	mysqli_free_result($result2);
}

$query2 = "SELECT stop_name, pomcode FROM stop WHERE stop_id = '$to';";
if ($result2 = mysqli_query ($link, $query2)) {
	while ($row2 = mysqli_fetch_row ($result2)) {
		$nazevv = $row2[0];
		$codev = $row2[1];
		echo "To: $nazevv $codev<br/>";
	}
	mysqli_free_result($result2);
}

$query146 = "SELECT trip_id FROM trip WHERE shape_id LIKE '%$from|$to|%';";
if ($result146 = mysqli_query($link, $query146)) {
	$count = mysqli_num_rows($result146);
	while ($row146 = mysqli_fetch_row($result146)) {
		$trip_id = $row146[0];

		echo "$trip_id > ";
	}
	echo "$count<br/>";
}

		$body = explode (";", $path);

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

			echo "$j | $pt_x | $pt_y<br/>";

		}

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

	if ($delete == "1") {
		$smazat = mysqli_query($link, "DELETE FROM du WHERE du_id = '$usek_id';");
		echo "<br/>Smazáno";
	}

}
include 'footer.php';
?>

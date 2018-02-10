<!DOCTYPE html>
<html>
	<head>
		<title>Trasa</title>
		<style>
			canvas {
				border: 1px dotted black;
			}	
		</style>
	</head>
	<body>

<?php
$link = mysqli_connect('localhost', 'root', 'root', 'JDF');
if (!$link) {
	echo "Error: Unable to connect to MySQL.".PHP_EOL;
	echo "Debugging errno: ".mysqli_connect_errno ().PHP_EOL;
	exit;
}

$action = @$_POST['action'];
$from = @$_POST['from'];
$to = @$_POST['to'];

echo "<form method=\"post\" action=\"testtras.php\" name=\"filtr\"><input name=\"action\" value=\"filtr\" type=\"hidden\">";
echo "Odkud: <select name=\"from\">";
$query0 = "SELECT stop_id, stop_name FROM stop ORDER BY stop_name;";
if ($result0 = mysqli_query ($link, $query0)) {
	while ($row0 = mysqli_fetch_row ($result0)) {
		$kodf = $row0[0];
		$nazevf = $row0[1];
		echo "<option value=\"$kodf\"";
		if ($kodf == $from) {
			echo " SELECTED";
		}
		echo ">$nazevf</option>";
	}
	mysqli_free_result ($result0);
} else {
	echo "Error description: ".mysqli_error ($link);
}

echo "</select>";
echo "Kam: <select name=\"to\">";
$query1 = "SELECT stop_id, stop_name FROM stop ORDER BY stop_name;";
if ($result1 = mysqli_query ($link, $query1)) {
	while ($row1 = mysqli_fetch_row ($result1)) {
		$kodt = $row1[0];
		$nazevt = $row1[1];
		echo "<option value=\"$kodt\"";
		if ($kodt == $to) {
			echo " SELECTED";
		}
		echo ">$nazevt</option>";
	}
	mysqli_free_result($result1);
} else {
	echo "Error description: " . mysqli_error($link);
}

echo "</select>";
echo "<input type=\"submit\"></form>";

switch ($action) {
	case "filtr" : 
		$query47 = "SELECT path from du WHERE stop1 = '$from' AND stop2 = '$to';";
		if ($result47 = mysqli_query($link, $query47)) {
			$row47 = mysqli_fetch_row($result47);
			$trasa = $row47[0];
		}

		echo $trasa;

	break;
}

/*		$query53 = "SELECT stop_lat, stop_lon FROM stop WHERE stop_id = '$to';";
		if ($result53 = mysqli_query($link, $query53)) {
			$row53 = mysqli_fetch_row($result53);
			$tolat = $row53[0];
			$tolon = $row53[1];
		}

		$prujezdy = $fromlon.",".$fromlat.";".$tolon.",".$tolat;

		$url = "https://router.project-osrm.org/route/v1/driving/$prujezdy?geometries=geojson&alternatives=false&steps=false&generate_hints=false&overview=full";

		echo distance ($fromlat,$fromlon,$tolat,$tolon)."<br/>";

		$contents = file_get_contents($url);
//		$contents = utf8_encode($contents);
		$results = json_decode($contents, TRUE);

		$souradnice = $results["routes"][0]["geometry"]["coordinates"];
		foreach ($souradnice as $bod) {
			$X = $bod[0];
			$Y = $bod[1];

			echo "$X,$Y|";
		}
		print_r($results); 

	break;
}



		<canvas id="a" width="800" height="600">
			This text is displayed if your browser does not support HTML5 Canvas.
		</canvas>
		<script type='text/javascript'>
			// Set up!
			var a_canvas = document.getElementById("a");
			var context = a_canvas.getContext("2d");

			context.beginPath();
			context.moveTo(14.5166326,50.0762931);
			context.lineTo(14.5399836,50.0746694);

			context.closePath();
			context.stroke();
		</script>
*/
mysqli_close($link);
?>

x = 784 * (lat - lat_min) + 8
y = 600 - (584 * (lon - lon_min) + 8)

	</body>
</html>


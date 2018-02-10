<?php
include 'header.php';

function distance ($lat1, $lon1, $lat2, $lon2) {
	$pi80 = M_PI / 180;
	$lat1 *= $pi80;
	$lon1 *= $pi80;
	$lat2 *= $pi80;
	$lon2 *= $pi80;

	$r = 6372.797; // mean radius of Earth in km
	$dlat = $lat2 - $lat1;
	$dlon = $lon2 - $lon1;
	$a = sin ($dlat / 2) * sin ($dlat / 2) + cos ($lat1) * cos ($lat2) * sin ($dlon / 2) * sin ($dlon / 2);
	$c = 2 * atan2 (sqrt ($a), sqrt (1 - $a));
	$km = $r * $c;

	return $km;
}

$action = @$_POST['action'];
$from = @$_POST['from'];
$to = @$_POST['to'];

echo "<form method=\"post\" action=\"trasa.php\" name=\"filtr\"><input name=\"action\" value=\"filtr\" type=\"hidden\">";
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

include 'footer.php';
?>
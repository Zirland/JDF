<?php
include 'header.php';

$route_id = $_POST['routeid'];
if ($route_id == "") {
	$route_id = $_GET['routeid'];
}
$action = $_POST['action'];

switch ($action) {
	case 'trip':
		$trip_no = $_POST['trip_no'];
		$trip_var = $_POST['trip_var'];
		$depart = $_POST['depart'];
		$week = $_POST['week'];

		$query17 = "INSERT INTO mtrips (route_id, trip_no, trip_var, depart, matrix) VALUES ('$route_id', '$trip_no', '$trip_var', '$depart', '$week')";
		$prikaz17 = mysqli_query($link, $query17);
	break;
}

$var = 0;
$query23 = "SELECT mvarianty.stop_seq, mvarianty.odstup, stop.stop_name, stop.pomcode, mvarianty.rezim, mvarianty.varianta FROM mvarianty LEFT JOIN stop ON mvarianty.stop_id = stop.stop_id WHERE route_id = '$route_id' ORDER BY varianta, stop_seq;";
if ($result23 = mysqli_query($link, $query23)) {
	while ($row23 = mysqli_fetch_row($result23)) {
		$stop_seq = $row23[0];
		$odstup = $row23[1];
		$stop_name = $row23[2];
		$pomcode = $row23[3];
		$rezim = $row23[4];
		$varianta = $row23[5];

		if ($var != $varianta) {
			echo "<br/>Varianta $varianta<br/>";
			$var = $varianta;
		}
		echo "$stop_seq | $stop_name $pomcode | $odstup | ";
		switch ($rezim) {
			case '01':
				echo "Pouze nástup";
			break;
			case '10':
				echo "Pouze výstup";
			break;
			case '33':
				echo "Zastavuje na znamení";
			break;
		}
		echo "<br/>";
	}
}

echo "<hr>";
echo "SPOJE TABULKA<br/>";

$query56 = "SELECT trip_no, trip_var, depart, matrix FROM mtrips WHERE route_id = '$route_id' ORDER BY depart, trip_no;";
if ($result56 = mysqli_query($link, $query56)) {
	while ($row56 = mysqli_fetch_row($result56)) {
		$trip_no = $row56[0];
		$trip_var = $row56[1];
		$depart = $row56[2];
		$week = $row56[3];

		echo "$trip_no | $trip_var | $depart | $week<br/>";
	}
}

echo "<hr>";
echo "<form action=\"_step3.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"routeid\" value=\"$route_id\">";
echo "<input type=\"hidden\" name=\"action\" value=\"trip\">";

echo "Číslo spoje: <input type=\"text\" name=\"trip_no\" autofocus><br/>";
echo "Varianta trasy: <input type=\"text\" name=\"trip_var\"><br/>";
echo "Čas odjezdu: <input type=\"text\" name=\"depart\"><br/>";

echo "Matrix týdne: <input type=\"text\" name=\"week\"><br/>";

echo "<input type=\"submit\" value=\"Zapsat\">";
echo "</form>";

echo "<a href=\"_step4.php?routeid=$route_id\">Přejít na časové kódy</a>";
include 'footer.php';
?>
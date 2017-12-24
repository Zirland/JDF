<?php
include 'header.php';

$cisti4 = mysqli_query($link, "DELETE FROM analyza WHERE datumdo < current_date();");

$dnes_den = date("j", time());
$dnes_mesic = date("n", time());
$dnes_rok = date("Y", time());
$today = mktime(0,0,0,$dnes_mesic,$dnes_den,$dnes_rok);

$query6 = "SELECT DISTINCT route_id, route_name, route_type FROM analyza WHERE route_id IN (SELECT DISTINCT route_id FROM anal_done) ORDER BY route_id;";
if ($result6 = mysqli_query($link, $query6)) {
	while ($row6 = mysqli_fetch_row($result6)) {
		$route_id = $row6[0];
		$route_name = $row6[1];
		$route_type = $row6[2];

		echo "$route_id - $route_name ($route_type)<br />";
		
		$halt = 0;
		$label = "";
		
		$query11 = "SELECT DISTINCT dir, verze, datumod, datumdo FROM analyza WHERE route_id = '$route_id' ORDER BY datumod DESC;";
		if ($result11 = mysqli_query($link, $query11)) {
			while ($row11 = mysqli_fetch_row($result11)) {
				$dir = $row11[0];
				$verze = $row11[1];
				$datumod = $row11[2];
				$datumdo = $row11[3];

				$query31 = mysqli_query($link, "SELECT * FROM anal_done WHERE route_id = '$route_id' AND datumod = '$datumod';");
				$platnost = mysqli_num_rows($query31);

				$od_den = substr($datumod,-2);
				$od_mesic = substr($datumod,5,2);
				$od_rok = substr($datumod,0,4);
				$od_time = mktime(0,0,0,$od_mesic,$od_den,$od_rok);

				if ($platnost > 0) {echo "<i>";}
				if ($od_time > $today) {$label = "F";}
				if ($od_time <= $today) {$label = "";}
				if ($halt == 0) {echo "$dir ($verze) $datumod > $datumdo $label > <a href=\"genroute.php?file=$dir&mode=$label&linkaod=$datumod\" target=\"_blank\">Generovat</a><br />";}
				if ($platnost > 0) {echo "</i>";}
				if ($od_time <= $today) {$halt = 1;}
			}
		}

	}
}

echo "<hr>";

$query6 = "SELECT DISTINCT route_id, route_name, route_type FROM analyza WHERE route_id NOT IN (SELECT DISTINCT route_id FROM anal_done) ORDER BY route_id;";
if ($result6 = mysqli_query($link, $query6)) {
	while ($row6 = mysqli_fetch_row($result6)) {
		$route_id = $row6[0];
		$route_name = $row6[1];
		$route_type = $row6[2];

		echo "$route_id - $route_name ($route_type)<br />";
		
		$halt = 0;
		$label = "";
		
		$query11 = "SELECT * FROM analyza WHERE route_id = '$route_id' ORDER BY datumod DESC;";
		if ($result11 = mysqli_query($link, $query11)) {
			while ($row11 = mysqli_fetch_row($result11)) {
				$dir = $row11[0];
				$verze = $row11[1];
				$datumod = $row11[5];
				$datumdo = $row11[6];

				$od_den = substr($datumod,-2);
				$od_mesic = substr($datumod,5,2);
				$od_rok = substr($datumod,0,4);
				$od_time = mktime(0,0,0,$od_mesic,$od_den,$od_rok);

				if ($od_time > $today) {$label = "F";}
				if ($od_time <= $today) {$label = "";}
				if ($halt == 0) {echo "$dir ($verze) $datumod > $datumdo $label > <a href=\"genroute.php?file=$dir&mode=$label\" target=\"_blank\">Generovat</a><br />";}
				if ($od_time <= $today) {$halt = 1;}
			}
		}

	}
}

include 'footer.php';
?>

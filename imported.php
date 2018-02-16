<?php
include 'header.php';

$query = "SELECT * FROM importstop;";
if ($result = mysqli_query ($link, $query)) {
	while ($row = mysqli_fetch_row ($result)) {
		$imp_id = $row[0];
		$lat = $row[1];
		$lon = $row[2];
		$obec = $row[3];
		$castobce = $row[4];
		$castobce = trim ($castobce);
		$misto = $row[5];
		$misto = trim ($misto);
		$pomcode = $row[6];

		echo "$lat - $lon - $obec, $castobce, $misto $pomcode - <a href=\"newstop.php?getlat=$lat&getlon=$lon&getobec=$obec&getcastobce=$castobce&getmisto=$misto&getpomcode=$pomcode&getid=$imp_id\" target=\"_blank\">Vytvořit</a><br />";
	}
}

include 'footer.php';
?>

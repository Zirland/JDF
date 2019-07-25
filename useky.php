<?php
include 'header.php';

$file    = 'useky.csv';
$current = "";
$i       = 0;

$current .= "usek,S1,S2,lat,lon,seq,final\n";

$query107 = "SELECT du_id, stop1, stop2, path, final FROM du;";
if ($result235 = mysqli_query($link, $query107)) {;
	while ($row235 = mysqli_fetch_row($result235)) {
		$du_id = $row235[0];
		$stop1 = $row235[1];
		$stop2 = $row235[2];
		$linie = $row235[3];
		$final = $row235[4];

		$body = explode(';', $linie);

		foreach ($body as $point) {
			$sourad = explode(',', $point);
			$lat    = $sourad[0];
			$lon    = $sourad[1];

			if ($lat != '' && $lon != '') {
				$i = $i + 1;
				$current .= "$du_id,$stop1,$stop2,$lat,$lon,$i,$final\n";
			}
		}
		$i = 0;
	}
}

file_put_contents($file, $current);
include 'footer.php';

<?php
include 'header.php';

$query4 = "SELECT stop_id, obec, castobce, misto FROM stop;";
if ($result4 = mysqli_query($link, $query4)) {
	while ($row4 = mysqli_fetch_row($result4)) {
		$stop_id = $row4[0];
		$obec = $row4[1]; 
		$castobce = $row4[2];
		$misto = $row4[3];

		$stopname = $obec;
		if ($castobce != '') {$stopname .= ", ".$stopcastobce;}
		if ($misto != '') {$stopname .= ", ".$stopmisto;}

		$sortname = "";
		if ($misto != '') {$sortname .= "$misto ";}
 		if ($castobce != '') {$sortname .= "$castobce ";}
 		$sortname .= $obec;

		$query14 = "UPDATE stop SET stop_name = '$stopname', sortname = '$sortname' WHERE stop_id = '$stop_id';";
		// echo "$query14<br/>";
		$prikaz4 = mysqli_query($link, $query14);
	}
}

include 'footer.php';
?>
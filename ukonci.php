<?php
include 'header.php';

$route = "XYZ";
$route = $_POST['route_id'];
$maticestart = mktime(0,0,0,12,3,2017);
$datumod = @$_POST['datumod'];
$datumdo = "31122019";

$query7 = "SELECT trip_id, matice FROM trip WHERE route_id = '$route';";
if ($result7 = mysqli_query($link, $query7)) {
	while ($row7 = mysqli_fetch_row($result7)) {
		$trip_id = $row7[0]; 
		$matice = $row7[1]; 

		$Dod = substr($datumod,0,2); $Mod = substr($datumod,2,2); $Yod = substr($datumod,-4); $timeod = mktime(0,0,0,$Mod, $Dod, $Yod);
		$zacdnu = round(($timeod - $maticestart) / 86400); 
		$Ddo = substr($datumdo,0,2); $Mdo = substr($datumdo,2,2); $Ydo = substr($datumdo,-4); $timedo = mktime(0,0,0,$Mdo, $Ddo, $Ydo); 
		$kondnu = round(($timedo - $maticestart) / 86400); 
				
		for ($g=0; $g<406; $g++) {
			if ($g>$zacdnu && $g <=$kondnu) {$matice[$g] = 0;}
		}
		
		$operace = "UPDATE trip SET matice='$matice' WHERE (trip_id = '$trip_id');";
		$vykonej = mysqli_query($link, $operace) or die(mysqli_error());
	}
}

include 'footer.php';
?>
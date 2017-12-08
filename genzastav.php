<?php
$link = mysqli_connect('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

$dir = $_GET['file'];

$version = fopen("$dir/VerzeJDF.txt.txt", 'r');
if ($version) {
	while (($buffer0 = fgets($version, 4096)) !== false) {
		$vrz = explode ('"', $buffer0);
		$verze = $vrz[1];
	}
	fclose($version);
}

$zastavky = fopen("$dir/Zastavky.txt.txt", 'r');
if ($zastavky) {
	while (($buffer6 = fgets($zastavky, 4096)) !== false) {
		$newbuffer6 = str_replace ('"','', $buffer6);
		$zastav = explode (',', $newbuffer6);
		$zastav_no = $zastav[0];
		$zast_name = $zastav[1].",".$zastav[2].",".$zastav[3];
		if ($zastav[3] == '') {$zast_name = $zastav[1].",".$zastav[2];}
		if ($zastav[2] == '' && $zastav[3] == '') {$zast_name = $zastav[1];}
		$lastPK = explode(';', $zastav[11]);
		$zastPK = "-".$zastav[6]."-".$zastav[7]."-".$zastav[8]."-".$zastav[9]."-".$zastav[10]."-".$lastPK[0]."-";        

		$query236 = "INSERT INTO pomstop (pom_cislo, stop_name, stop_PK) VALUES ('$zastav_no', '$zast_name', '$zastPK');";
		$prikaz236 = mysqli_query($link, $query236);     
	}
	fclose ($zastavky);
}

$zaslinky = fopen("$dir/Zaslinky.txt.txt", 'r');
if ($zaslinky) {
	while (($buffer7 = fgets($zaslinky, 4096)) !== false) {
		$zastavlin = explode ('"', $buffer7);

		if ($verze == '1.8' || $verze == '1.9') {
			$routeno = "1";
			$linka_id = $zastavlin[1].$routeno;
		}                

		if ($verze == '1.10' || $verze == '1.11') {
			$linka_id = $zastavlin[1].$zastavlin[17];
		}

		$zastporadi = $zastavlin[3];
		$zastcode = $zastavlin[7];
		$stop_id = $linka_id.$zastporadi."P".$zastcode;
		$hledejpom = "SELECT stop_name, stop_PK FROM pomstop WHERE pom_cislo = '$zastcode';";
		$najdipom = mysqli_fetch_row(mysqli_query($link, $hledejpom));
		$stop_name = $najdipom[0];
    		$zastPK = $najdipom[1];
		$stopPK = $zastPK.$zastavlin[11]."-".$zastavlin[13]."-".$zastavlin[15]."-";        
		
		$query467 = "INSERT INTO linestopsDB (stop_id, stop_name, stop_pk, stop_linka, stop_poradi, stop_smer, stop_vazba) VALUES ('$stop_id+', '$stop_name', '$stopPK', '$linka_id', '$zastporadi', '0', '');";
		$prikaz467 = mysqli_query($link, $query467);
		$query469 = "INSERT INTO linestopsDB (stop_id, stop_name, stop_pk, stop_linka, stop_poradi, stop_smer, stop_vazba) VALUES ('$stop_id-', '$stop_name', '$stopPK', '$linka_id', '$zastporadi', '1', '');";
		$prikaz469 = mysqli_query($link, $query469);
	} 
	fclose ($zaslinky);
}

mysqli_close ($link);
?>

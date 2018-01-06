<?php
$link = mysqli_connect('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
	echo "Error: Unable to connect to MySQL." . PHP_EOL;
	echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
	exit;
}

$log = "import.log";
$dir = $_GET['file'];
$label = $_GET['mode'];
$linkaod = $_GET['linkaod'];
$linkado = $_GET['linkado'];
$current = "";
$dir = "data/".$dir;

$svatek = array ( 
"24122017",
"25122017",
"26122017",
"01012018",
"30032018",
"02042018",
"01052018",
"08052018",
"05072018",
"06072018",
"28092018",
"28102018",
"17112018",
"24122018",
"25122018",
"26122018",
"01012019",
"19042019",
"22042019",
"01052019",
"08052019",
"05072019",
"06072019",
"28092019",
"28102019",
"17112019",
"24122019",
"25122019",
"26122019"
);

$cististop = mysqli_query($link, "TRUNCATE TABLE pomstop;");

$version = fopen("$dir/VerzeJDF.txt.txt", 'r');
if ($version) {
	while (($buffer0 = fgets($version, 4096)) !== false) {
		$vrz = explode ('"', $buffer0);
		$verze = $vrz[1];
	}
	fclose($version);
}

$dopravci = fopen("$dir/Dopravci.txt.txt", 'r');
if ($dopravci) {
	while (($buffer1 = fgets($dopravci, 4096)) !== false) {
		$dopr = explode ('"', $buffer1);
		$dopr_id = $dopr[1];
		$dopr_name = $dopr[5];
		$dopr_url = $dopr[23];
		if ($dopr_url == '') {$dopr_url = "andreas.zirland.org";}
		
		$cistiag = mysqli_query($link, "DELETE FROM agency WHERE agency_id = '$dopr_id';");
		$query21 = "INSERT INTO agency (agency_id, agency_name, agency_url, agency_timezone) VALUES ('$dopr_id', '$dopr_name', 'http://$dopr_url', 'Europe/Prague');";
		$prikaz21 = mysqli_query($link, $query21);	
	}
	fclose ($dopravci);
}
	
$linky = fopen("$dir/Linky.txt.txt", 'r');
if ($linky) {
	while (($buffer2 = fgets($linky, 4096)) !== false) {
		$line = explode ('"', $buffer2);
		$route_no = $line[1];
		$route_short_name = $route_no;
		$route_long_name = $line[3];
		$agency_id = $line[5];
		$route_color = "0000FF";
		$route_text_color = "FFFFFF";

		if ($verze == '1.8' || $verze == '1.9') {
			$platnostod = $line[17];
			$platnostdo = $line[19];
			$linkano = "1";
			$route_type = "3";
		}	

		if ($verze == '1.10' || $verze == '1.11') {
			$typ = $line[9];
			switch ($typ) {
				case "A" : $route_type = "3"; break;
				case "E" : $route_type = "0"; break;
				case "L" : $route_type = "6"; break;
				case "M" : $route_type = "1"; break;
				case "P" : $route_type = "4"; break;
				case "T" : $route_type = "5"; break;
			}
		}

		if ($verze == '1.10') {
			$linkano = $line[31];
			$platnostod = $line[25];
			$platnostdo = $line[27];
		}

		if ($verze == '1.11') {
			$linkano = $line[33];
			$platnostod = $line[27];
			$platnostdo = $line[29];
		}

		$route_id = $route_no.$linkano;
		$queryro = "DELETE FROM route WHERE route_id = '$label$route_id';";
		$cistiro = mysqli_query($link, $queryro);

		$querytr = "DELETE FROM trip WHERE route_id = '$label$route_id';";
		$cistitr = mysqli_query($link, $querytr);

		$query46 = "INSERT INTO route (route_id, agency_id, route_short_name, route_long_name, route_type, route_color, route_text_color, active) VALUES ('$label$route_id', '$agency_id', '$route_short_name', '$route_long_name', '$route_type', '$route_color', '$route_text_color', '0');";
		$prikaz46 = mysqli_query($link, $query46);
	}
	fclose ($linky);
}
	
$spoje = fopen("$dir/Spoje.txt.txt", 'r');
if ($spoje) {
	while (($buffer3 = fgets($spoje, 4096)) !== false) {
		$newbuffer3 = str_replace ('"','', $buffer3);
		$trip = explode (',', $newbuffer3);

		if ($verze == '1.8' || $verze == '1.9') {
			$routeno = "1";		    
			$lastPK = explode(';', $trip[11]);					
			$PK = "-".$trip[2]."-".$trip[3]."-".$trip[4]."-".$trip[5]."-".$trip[6]."-".$trip[7]."-".$trip[8]."-".$trip[9]."-".$trip[10]."-".$lastPK[0]."-";
		}			

		if ($verze == '1.10' || $verze == '1.11') {
			$routeno = explode(';', $trip[13]);
			$PK = "-".$trip[2]."-".$trip[3]."-".$trip[4]."-".$trip[5]."-".$trip[6]."-".$trip[7]."-".$trip[8]."-".$trip[9]."-".$trip[10]."-".$trip[11]."-";
		}
		
		$route_id = $trip[0].$routeno[0];
		$trip_no = $trip[1];
		$trip_id = $route_id.$trip_no;
		$smer = ($trip_no % 2)+1;
		if ($smer == 2) {$smer = 0;}
		$matrix = "";
		$startformat = "2017-12-03"; // vždy neděle!
		$Ystart = substr($startformat,0,4);
		$Mstart = substr($startformat,5,2);
		$Dstart = substr($startformat,-2);
		$maticestart = mktime(0,0,0,$Mstart,$Dstart,$Ystart);
		for ($i=0; $i<406; $i++) {
			$matrix.="0";
		}

		if (strpos($PK, '-1-') !== false) {
		// pracdny
			$dy = 1;
			for ($wk=0; $wk < 58; $wk++) {
				$index = $dy+($wk*7);
				$matrix[$index] = 1;
			}
			
			$dy = 2;
			for ($wk=0; $wk < 58; $wk++) {
				$index = $dy+($wk*7);
				$matrix[$index] = 1;
			}
			
			$dy = 3;
			for ($wk=0; $wk < 58; $wk++) {
				$index = $dy+($wk*7);
				$matrix[$index] = 1;
			}
			
			$dy = 4;
			for ($wk=0; $wk < 58; $wk++) {
				$index = $dy+($wk*7);
				$matrix[$index] = 1;
			}
			
			$dy = 5;
			for ($wk=0; $wk < 58; $wk++) {
				$index = $dy+($wk*7);
				$matrix[$index] = 1;
			}
		}

		if (strpos($PK, '-3-') !== false) {
		// pondělí
			$dy = 1;
			for ($wk=0; $wk < 58; $wk++) {
				$index = $dy+($wk*7);
				$matrix[$index] = 1;
			}
		}

		if (strpos($PK, '-4-') !== false) {
		// úterý
			$dy = 2;
			for ($wk=0; $wk < 58; $wk++) {
				$index = $dy+($wk*7);
				$matrix[$index] = 1;
			}
		}

		if (strpos($PK, '-5-') !== false) {
		// středa
			$dy = 3;
			for ($wk=0; $wk < 58; $wk++) {
				$index = $dy+($wk*7);
				$matrix[$index] = 1;
			}
		}

		if (strpos($PK, '-6-') !== false) {
		// čtvrtek
			$dy = 4;
			for ($wk=0; $wk < 58; $wk++) {
				$index = $dy+($wk*7);
				$matrix[$index] = 1;
			}
		}
	
		if (strpos($PK, '-7-') !== false) {
		// pátek
			$dy = 5;
			for ($wk=0; $wk < 58; $wk++) {
				$index = $dy+($wk*7);
				$matrix[$index] = 1;
			}
		}
			
		if (strpos($PK, '-8-') !== false) {
		// sobota
			$dy = 6;
			for ($wk=0; $wk < 58; $wk++) {
				$index = $dy+($wk*7);
				$matrix[$index] = 1;
			}
		}
		
		foreach ($svatek as $datumsvatek1) { 
			$Dods1 = substr($datumsvatek1,0,2); $Mods1 = substr($datumsvatek1,2,2); $Yods1 = substr($datumsvatek1,-4); $timesvatek1 = mktime(0,0,0,$Mods1, $Dods1, $Yods1); 
			$dnusvatek1 = round(($timesvatek1 - $maticestart) / 86400); 
			for ($h=0; $h<406; $h++) {
				if ($h==$dnusvatek1) {$matrix[$h] = 0;}
			}
		}

		if (strpos($PK, '-2-') !== false) {
		// neděle a svátky
			$dy = 0;
			for ($wk=0; $wk < 58; $wk++) {
			$index = $dy+($wk*7);
				$matrix[$index] = 1;
			}
	
			foreach ($svatek as $datumsvatek1) { 
				$Dods1 = substr($datumsvatek1,0,2); $Mods1 = substr($datumsvatek1,2,2); $Yods1 = substr($datumsvatek1,-4); $timesvatek1 = mktime(0,0,0,$Mods1, $Dods1, $Yods1); 
				$dnusvatek1 = round(($timesvatek1 - $maticestart) / 86400); 
				for ($h=0; $h<406; $h++) {
					if ($h==$dnusvatek1) {$matrix[$h] = 1;}
				}
			}
		}

		$caskody = fopen("$dir/Caskody.txt.txt", 'r');
		if ($caskody) {
			while (($buffer5 = fgets($caskody, 4096)) !== false) {
				$newbuffer5 = str_replace ('"','', $buffer5);
				$caskod = explode (',', $newbuffer5);

				if ($verze == '1.8' || $verze == '1.9') {$routeno = "1";}
				if ($verze == '1.10' || $verze == '1.11') {$routeno = explode(';', $caskod[8]);}

				$linka = $caskod[0].$routeno[0];
				$spoj = $caskod[1];
				$caskod_trip_id = $linka.$spoj;
				$typkodu = $caskod[4];
				$datumod = $caskod[5];
				$datumdo = $caskod[6];
				if ($datumdo == "") {$datumdo = $datumod;}

				if ($trip_id == $caskod_trip_id) {
					switch ($typkodu) {
						case "1" :
							$Dod = substr($datumod,0,2); $Mod = substr($datumod,2,2); $Yod = substr($datumod,-4); $timeod = mktime(0,0,0,$Mod, $Dod, $Yod); 
							$zacdnu = round(($timeod - $maticestart) / 86400); 
							$Ddo = substr($datumdo,0,2); $Mdo = substr($datumdo,2,2); $Ydo = substr($datumdo,-4); $timedo = mktime(0,0,0,$Mdo, $Ddo, $Ydo); 
							$kondnu = round(($timedo - $maticestart) / 86400); 
				
							for ($g=0; $g<406; $g++) {
								if ($g>=$zacdnu && $g <=$kondnu) {$matrix[$g] = 1;}
							}
						break;
		
						case "2" :
							$Dod = substr($datumod,0,2); $Mod = substr($datumod,2,2); $Yod = substr($datumod,-4); $timeod = mktime(0,0,0,$Mod, $Dod, $Yod); 
							$zacdnu = round(($timeod - $maticestart) / 86400); 
							for ($g=0; $g<406; $g++) {
								if ($g==$zacdnu) {$matrix[$g] = 1;}
							}
						break;
			
						case "3" :
							$Dod = substr($datumod,0,2); $Mod = substr($datumod,2,2); $Yod = substr($datumod,-4); $timeod = mktime(0,0,0,$Mod, $Dod, $Yod); 
							$zacdnu = round(($timeod - $maticestart) / 86400);
							$current .= "* Spoj $caskod_trip_id jede pouze dne $datumod\n"; 
							echo "* Spoj $caskod_trip_id jede pouze dne $datumod<br/>"; 
							for ($g=0; $g<406; $g++) {
								if ($g==$zacdnu) {$matrix[$g] = 1;} else {$matrix[$g] = 0;}
							} 
						break;
			
						case "4" :
							$Dod = substr($datumod,0,2); $Mod = substr($datumod,2,2); $Yod = substr($datumod,-4); $timeod = mktime(0,0,0,$Mod, $Dod, $Yod); 
							$zacdnu = round(($timeod - $maticestart) / 86400); 
							$Ddo = substr($datumdo,0,2); $Mdo = substr($datumdo,2,2); $Ydo = substr($datumdo,-4); $timedo = mktime(0,0,0,$Mdo, $Ddo, $Ydo); 
							$kondnu = round(($timedo - $maticestart) / 86400); 
				
							for ($g=0; $g<406; $g++) {
								if ($g>=$zacdnu && $g <=$kondnu) {$matrix[$g] = 0;}
							}

						break;
		
						case "5" : $current .= "* Spoj $caskod_trip_id jede jen v lichých týdnech\n"; break;
			
						case "6" : $current .= "* Spoj $caskod_trip_id jede jen v sudých týdnech\n"; break;
			
						case "7" : $current .= "* Spoj $caskod_trip_id jede jen v lichých týdnech od $datumod do $datumdo\n"; break;
		
						case "8" : $current .= "* Spoj $caskod_trip_id jede jen v sudých týdnech od $datumod do $datumdo\n"; break;
					}
				}
			}
			fclose ($caskody);
		}
	
		$Dplod = substr($platnostod,0,2); $Mplod = substr($platnostod,2,2); $Yplod = substr($platnostod,-4); $timeplod = mktime(0,0,0,$Mplod, $Dplod, $Yplod); 
		$zacplat = round(($timeplod - $maticestart) / 86400); 
		$Dpldo = substr($platnostdo,0,2); $Mpldo = substr($platnostdo,2,2); $Ypldo = substr($platnostdo,-4); $timepldo = mktime(0,0,0,$Mpldo, $Dpldo, $Ypldo); 
		$konplat = round(($timepldo - $maticestart) / 86400); 
				
		for ($g=0; $g<406; $g++) {
			if ($g<$zacplat || $g >$konplat) {$matrix[$g] = 0;}
		}

		$wheelchair = 0;
		if (strpos($PK, '-14-') !== false) {
		// invalida
			$wheelchair=1;
		}

		$query64 = "INSERT INTO trip (route_id, matice, trip_id, trip_headsign, direction_id, wheelchair_accessible, active) VALUES ('$label$route_id', '$matrix', '$label$trip_id', '', '$smer', '$wheelchair','0');";
		$prikaz64 = mysqli_query($link, $query64);

		$query368 = "INSERT INTO pomtrip (trip_id) VALUES ('$label$trip_id');";
		$prikaz368 = mysqli_query($link, $query368);
	}
	fclose ($spoje);
}

if ($verze == '1.10' || $verze == '1.11') {
	$oznacnik = fopen("$dir/Oznacniky.txt.txt", 'r');
	if ($oznacnik) {
		fclose ($oznacnik);
	}	

	$spojskup = fopen("$dir/SpojSkup.txt.txt", 'r');
	if ($spojskup) {
		fclose ($spojskup);
	}

	$extlinka = fopen("$dir/LinExt.txt.txt", 'r');
	if ($extlinka) {
		while (($buffer9 = fgets($extlinka, 4096)) !== false) {
			$newbuffer9 = str_replace ('"','', $buffer9);
			$linext = explode (',', $newbuffer9);
			$routeno = explode(';', $linext[6]);
			$linka = $linext[0].$routeno[0];

			$poradi = $linext[1];
			$koddopravy = $linext[2];
			$oznaclin = $linext[3];
			$prefer = $linext[4];
				
			$queryex = "DELETE FROM exter WHERE linka = '$label$linka';";
			$cistiex = mysqli_query($link, $queryex);

			$query1213 = "INSERT INTO exter (linka, poradi, kod_dopravy, kod_linky, prefer) VALUES ('$label$linka', '$poradi', '$koddopravy', '$oznaclin', '$prefer');";
			$prikaz1213 = mysqli_query($link, $query1213);
		}
		fclose ($extlinka);
	}	
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

$querystopDB = "DELETE FROM linestopsDB WHERE stop_id LIKE '$label$linka';";
$cististopDB = mysqli_query($link, $querystopDB);

$querytripDB = "DELETE FROM triptimesDB WHERE trip_id LIKE '$label$linka';";
$cistitripDB = mysqli_query($link, $querytripDB);

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
		
		$query467 = "INSERT INTO linestopsDB (stop_id, stop_name, stop_pk, stop_linka, stop_poradi, stop_smer, stop_vazba) VALUES ('$label$stop_id+', '$stop_name', '$stopPK', '$label$linka_id', '$zastporadi', '0', '');";
		$prikaz467 = mysqli_query($link, $query467);

		$query469 = "INSERT INTO linestopsDB (stop_id, stop_name, stop_pk, stop_linka, stop_poradi, stop_smer, stop_vazba) VALUES ('$label$stop_id-', '$stop_name', '$stopPK', '$label$linka_id', '$zastporadi', '1', '');";
		$prikaz469 = mysqli_query($link, $query469);

		$query464 = "SELECT * FROM linevazba WHERE stop_id = '$label$stop_id+';";
		if ($result464 = mysqli_query ($link, $query464)) {
			while ($row464 = mysqli_fetch_row($result464)) {
				$stopid = $row464[1];
				$stopvazba = $row464[2];

				$querymig = "UPDATE linestopsDB SET stop_vazba = '$stopvazba' WHERE stop_id = '$stopid';";
				$migrate = mysqli_query($link, $querymig);
			}
      	}

		$query474 = "SELECT * FROM linevazba WHERE stop_id = '$label$stop_id-';";
		if ($result474 = mysqli_query ($link, $query474)) {
			while ($row474 = mysqli_fetch_row($result474)) {
				$stopid = $row474[1];
				$stopvazba = $row474[2];

				$querymig = "UPDATE linestopsDB SET stop_vazba = '$stopvazba' WHERE stop_id = '$stopid';";
				$migrate = mysqli_query($link, $querymig);
			}
		}
	} 
	fclose ($zaslinky);
}

$zasspoje = fopen("$dir/Zasspoje.txt.txt", 'r');
if ($zasspoje) {
	while (($buffer8 = fgets($zasspoje, 4096)) !== false) {
		$newbuffer8 = str_replace ('"','', $buffer8);
		$zastspoj = explode (',', $newbuffer8);
		
		if ($verze == '1.8' || $verze == '1.9') {
			$routeno = "1";
		}

		if ($verze == '1.10') {
			$routeno = explode(';', $zastspoj[11]);
		}
		
		if ($verze == '1.11') {
			$routeno = explode(';', $zastspoj[14]);
		}

		$linka = $zastspoj[0].$routeno[0]; 
		$spoj = $zastspoj[1];
		$trip_id = $linka.$spoj;
		$zastav_poradi = $zastspoj[2];
		$zastav_code = $zastspoj[3];

		$smer = $spoj % 2;
		switch ($smer) {
			case 0 : $direct = "-"; break;
			case 1 : $direct = "+"; break;
		}

		$zastav_id = $linka.$zastav_poradi."P".$zastav_code.$direct;
		
		if ($verze == '1.8' || $verze == '1.9') {
			$km = $zastspoj[7];
			$prijezd = $zastspoj[8];
			$lastodj = explode(";",$zastspoj[9]);
			$odjezd = $lastodj[0];
			$tripstopPK = "-".$zastspoj[5]."-".$zastspoj[6]."-";
		}
	
		if ($verze == '1.10') {
			$km = $zastspoj[8];
			$prijezd = $zastspoj[9];
			$odjezd = $zastspoj[10];
			$tripstopPK = "-".$zastspoj[6]."-".$zastspoj[7]."-";
		}

		if ($verze == '1.11') {			
			$km = $zastspoj[9];
			$prijezd = $zastspoj[10];
			$odjezd = $zastspoj[11];
			$tripstopPK = "-".$zastspoj[6]."-".$zastspoj[7]."-".$zastspoj[8]."-";
		}

		if ($prijezd != '<' && $prijezd != '|' && $odjezd != '<' && $odjezd != '|') {
			$query537 = "INSERT INTO triptimesDB (zastav_id,trip_id,trip_pk,prijezd,odjezd,km) VALUES ('$label$zastav_id','$label$trip_id', '$tripstopPK', '$prijezd', '$odjezd', '$km');";
			$prikaz537 = mysqli_query($link, $query537);
		}
	}
	fclose ($zasspoje);
}

$query542 = "INSERT INTO anal_done (route_id, datumod, datumdo) VALUES ('$linka','$linkaod', '$linkado');";
$zapis542 = mysqli_query($link, $query542);

file_put_contents($log, $current, FILE_APPEND);

echo "<a href=\"routeedit.php?id=$route_id\">Editace linky $linka</a>";

mysqli_close ($link);
?>

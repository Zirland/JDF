<?php
ini_set('memory_limit', '-1');
set_time_limit(0);
date_default_timezone_set('Europe/Prague');

require_once 'dbconnect.php';
$link = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
if (!$link) {
    echo "Error: Unable to connect to database." . PHP_EOL;
    echo "Reason: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$log = "import.log";
$dir = $_GET['file'];
$label = "F";
$linkaod = $_GET['linkaod'];
$linkado = $_GET['linkado'];
$current = "";

$dir = "data/$dir";

unset($svatek);
$query24 = "SELECT datum FROM svatky ORDER BY datum;";
if ($result24 = mysqli_query($link, $query24)) {
    while ($row24 = mysqli_fetch_row($result24)) {
        $svatek[] = $row24[0];
    }
}

$version = fopen("$dir/VerzeJDF.txt.txt", 'r');
if ($version) {
    while (($buffer0 = fgets($version, 4096)) !== false) {
        $vrz = explode('"', $buffer0);
        $verze = $vrz[1];
    }
    fclose($version);
}

$dopravci = fopen("$dir/Dopravci.txt.txt", 'r');
if ($dopravci) {
    while (($buffer1 = fgets($dopravci, 4096)) !== false) {
        $dopr = explode('"', $buffer1);
        $dopr_id = $dopr[1];
        $dopr_name = $dopr[5];
        $dopr_url = $dopr[23];
        if ($dopr_url == '') {
            $dopr_url = "cascais.zirland.org";
        }

        //        $cistiag = mysqli_query($link, "DELETE FROM agency WHERE agency_id = '$dopr_id';");
        $query52 = "INSERT INTO agency (agency_id, agency_name, agency_url, agency_timezone) VALUES ('$dopr_id', '$dopr_name', 'http://$dopr_url', 'Europe/Prague');";
        //        echo "$query52<br/>";
        //        $prikaz52 = mysqli_query($link, $query52);
    }
    fclose($dopravci);
}

if ($verze == '1.10' || $verze == '1.11') {
    $extlinka = fopen("$dir/LinExt.txt.txt", 'r');
    if ($extlinka) {
        while (($buffer9 = fgets($extlinka, 4096)) !== false) {
            $newbuffer9 = str_replace('"', '', $buffer9);
            $linext = explode(',', $newbuffer9);
            $routeno = explode(';', $linext[6]);
            $linka = "$linext[0]$routeno[0]";

            $poradi = $linext[1];
            $koddopravy = $linext[2];
            $oznaclin = $linext[3];
            $prefer = $linext[4];

            $queryex = "DELETE FROM exter WHERE linka = '$linka';";
            echo "$queryex<br/>";
            //            $cistiex = mysqli_query($link, $queryex);

            $query77 = "INSERT INTO exter (linka, poradi, kod_dopravy, kod_linky, prefer) VALUES ('$linka', '$poradi', '$koddopravy', '$oznaclin', '$prefer');";
            echo "$query77<br/>";
            //            $prikaz77 = mysqli_query($link, $query77);
        }
        fclose($extlinka);
    }
}

$linky = fopen("$dir/Linky.txt.txt", 'r');
if ($linky) {
    while (($buffer2 = fgets($linky, 4096)) !== false) {
        $line = explode('"', $buffer2);
        $route_no = $line[1];
        $route_long_name = $line[3];
        $agency_id = $line[5];
        $route_text_color = "000000";

        if ($verze == '1.8' || $verze == '1.9') {
            $platnostod = $line[17];
            $platnostdo = $line[19];
            $linkano = "1";
            $route_type = "3";
        }

        if ($verze == '1.10' || $verze == '1.11') {
            $typ = $line[9];
            $route_type = match ($typ) {
                "A" => "3",
                "E" => "0",
                "L" => "6",
                "M" => "1",
                "P" => "4",
                "T" => "11",
            };
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

        $route_long_name = str_replace(",", ", ", $route_long_name);
        $route_long_name = str_replace(".", ". ", $route_long_name);
        $route_long_name = str_replace(". ,", ".,", $route_long_name);
        $route_long_name = str_replace("-", " – ", $route_long_name);
        $route_long_name = str_replace("/", " / ", $route_long_name);
        $route_long_name = str_replace("  ", " ", $route_long_name);
        $route_long_name = str_replace("  ", " ", $route_long_name);
        $route_long_name = str_replace("  ", " ", $route_long_name);

        $route_id = "$route_no$linkano";

        $query136 = "SELECT kod_linky FROM exter WHERE linka='$route_id';";
        if ($result136 = mysqli_query($link, $query136)) {
            while ($row136 = mysqli_fetch_row($result136)) {
                $route_short_name = $row136[0];
            }
        }
        if ($route_short_name == '') {
            $route_short_name = $route_no;
        }

        $query146 = "SELECT route_color FROM barvy WHERE route_id = '$label$route_id';";
        if ($result146 = mysqli_query($link, $query146)) {
            $row146 = mysqli_fetch_row($result146);
            $radku146 = mysqli_num_rows($result146);
        }
        $route_color = ($radku146 > 0) ? $row146[0] : "017DC2";
        $queryro = "DELETE FROM `route` WHERE route_id = '$label$route_id';";
        //        echo "$queryro<br/>";
        //        $cistiro = mysqli_query($link, $queryro);

        $querytr = "DELETE FROM trip WHERE route_id = '$label$route_id';";
        //        echo "$querytr<br/>";
        //        $cistitr = mysqli_query($link, $querytr);

        $query160 = "INSERT INTO `route` (route_id, agency_id, route_short_name, route_long_name, route_type, route_color, route_text_color, active) VALUES ('$label$route_id', '$agency_id', '$route_short_name', '$route_long_name', '$route_type', '$route_color', '$route_text_color', '0');";
        //        echo "$query160<br/>";
        //        $prikaz160 = mysqli_query($link, $query160);
    }
    fclose($linky);
}

$query_jizdy = "DELETE FROM jizdy WHERE spoj LIKE '$route_id%' AND (datum BETWEEN '$linkaod' AND '$linkado');";
//echo "$query_jizdy<br/>";
//$cisti_jizdy = mysqli_query($link, $query_jizdy);

$spoje = fopen("$dir/Spoje.txt.txt", 'r');
if ($spoje) {
    while (($buffer3 = fgets($spoje, 4096)) !== false) {
        $newbuffer3 = str_replace('"', '', $buffer3);
        $trip = explode(',', $newbuffer3);

        if ($verze == '1.8' || $verze == '1.9') {
            $routeno = "1";
            $lastPK = explode(';', $trip[11]);
            $PK = "-$trip[2]-$trip[3]-$trip[4]-$trip[5]-$trip[6]-$trip[7]-$trip[8]-$trip[9]-$trip[10]-$lastPK[0]-";
        }

        if ($verze == '1.10' || $verze == '1.11') {
            $routeno = explode(';', $trip[13]);
            $PK = "-$trip[2]-$trip[3]-$trip[4]-$trip[5]-$trip[6]-$trip[7]-$trip[8]-$trip[9]-$trip[10]-$trip[11]-";
        }

        $route_id = "$trip[0]$routeno[0]";
        $trip_no = $trip[1];
        $tripspoj = "$route_id$trip_no";

        $dnes_den = date("j", time());
        $dnes_mesic = date("n", time());
        $dnes_rok = date("Y", time());
        $dnes_datum = mktime(0, 0, 0, $dnes_mesic, $dnes_den, $dnes_rok);
        $dnes_format = date("Y-m-d", $dnes_datum);

        $query198 = "INSERT INTO log(trip_id, datum) VALUES ('$tripspoj','$dnes_format');";
        echo "$query198<br/>";
        //        $prikaz167 = mysqli_query($link, $query198);
        //        $logid     = mysqli_insert_id($link);
        $logid = 0;

        $vznik = $logid;
        if ($logid > 999999) {
            $vznik = substr($vznik, -6);
        }
        if ($logid < 100000) {
            $vznik = "0$vznik";
        }
        if ($logid < 10000) {
            $vznik = "0$vznik";
        }
        if ($logid < 1000) {
            $vznik = "0$vznik";
        }
        if ($logid < 100) {
            $vznik = "0$vznik";
        }
        if ($logid < 10) {
            $vznik = "0$vznik";
        }

        $trip_id = "$tripspoj$vznik";

        $smer = ($trip_no % 2) + 1;
        if ($smer == 2) {
            $smer = 0;
        }

        $matrix = "";

        $maticestart = date_create($linkaod);
        $start = date_format($maticestart, "N");
        $shift = -1 * $start;

        for ($i = 0; $i < 3650; $i++) {
            $matrix .= "0";
        }

        if (
            substr($PK, 0, 3) != '-1-' &&
            substr($PK, 0, 3) != '-2-' &&
            substr($PK, 0, 3) != '-3-' &&
            substr($PK, 0, 3) != '-4-' &&
            substr($PK, 0, 3) != '-5-' &&
            substr($PK, 0, 3) != '-6-' &&
            substr($PK, 0, 3) != '-7-' &&
            substr($PK, 0, 3) != '-8-' &&
            substr($PK, 0, 3) != '-9-'
        ) {
            $PK = "-1-2-8$PK";
        }

        if (strpos($PK, '-1-') !== false) {
            // pracdny
            $dy = 1;
            for ($wk = 0; $wk < 520; $wk++) {
                $index = $shift + $dy + $wk * 7;
                $matrix[$index] = 1;
            }

            $dy = 2;
            for ($wk = 0; $wk < 520; $wk++) {
                $index = $shift + $dy + $wk * 7;
                $matrix[$index] = 1;
            }

            $dy = 3;
            for ($wk = 0; $wk < 520; $wk++) {
                $index = $shift + $dy + $wk * 7;
                $matrix[$index] = 1;
            }

            $dy = 4;
            for ($wk = 0; $wk < 520; $wk++) {
                $index = $shift + $dy + $wk * 7;
                $matrix[$index] = 1;
            }

            $dy = 5;
            for ($wk = 0; $wk < 520; $wk++) {
                $index = $shift + $dy + $wk * 7;
                $matrix[$index] = 1;
            }

            foreach ($svatek as $datumsvatek1) {
                $svatek_date = date_create_from_format('Y-m-d', $datumsvatek1);
                $svatekdiff = date_diff($maticestart, $svatek_date);
                $dnusvatek1 = $svatekdiff->days;

                for ($h = 0; $h < 3650; $h++) {
                    if ($h == $dnusvatek1) {
                        $matrix[$h] = 0;
                    }
                }
            }
        }
        echo "313: <pre>$matrix</pre><br/>";
        if (strpos($PK, '-2-') !== false) {
            // neděle a svátky
            $dy = 0;
            for ($wk = 0; $wk < 520; $wk++) {
                $index = $shift + $dy + $wk * 7;
                $matrix[$index] = 1;
            }

            foreach ($svatek as $datumsvatek1) {
                $svatek_date = date_create_from_format('Y-m-d', $datumsvatek1);
                $svatekdiff = date_diff($maticestart, $svatek_date);
                $dnusvatek1 = $svatekdiff->days;

                for ($h = 0; $h < 3650; $h++) {
                    if ($h == $dnusvatek1) {
                        $matrix[$h] = 1;
                    }
                }
            }
        }
        echo "334: <pre>$matrix</pre><br/>";
        if (strpos($PK, '-3-') !== false) {
            // pondělí
            $dy = 1;
            for ($wk = 0; $wk < 520; $wk++) {
                $index = $shift + $dy + $wk * 7;
                $matrix[$index] = 1;
            }
        }
        echo "343: <pre>$matrix</pre><br/>";
        if (strpos($PK, '-4-') !== false) {
            // úterý
            $dy = 2;
            for ($wk = 0; $wk < 520; $wk++) {
                $index = $shift + $dy + $wk * 7;
                $matrix[$index] = 1;
            }
        }
        echo "352: <pre>$matrix</pre><br/>";
        if (strpos($PK, '-5-') !== false) {
            // středa
            $dy = 3;
            for ($wk = 0; $wk < 520; $wk++) {
                $index = $shift + $dy + $wk * 7;
                $matrix[$index] = 1;
            }
        }
        echo "361: <pre>$matrix</pre><br/>";
        if (strpos($PK, '-6-') !== false) {
            // čtvrtek
            $dy = 4;
            for ($wk = 0; $wk < 520; $wk++) {
                $index = $shift + $dy + $wk * 7;
                $matrix[$index] = 1;
            }
        }
        echo "370: <pre>$matrix</pre><br/>";
        if (strpos($PK, '-7-') !== false) {
            // pátek
            $dy = 5;
            for ($wk = 0; $wk < 520; $wk++) {
                $index = $shift + $dy + $wk * 7;
                $matrix[$index] = 1;
            }
        }
        echo "379: <pre>$matrix</pre><br/>";
        if (strpos($PK, '-8-') !== false) {
            // sobota
            $dy = 6;
            for ($wk = 0; $wk < 520; $wk++) {
                $index = $shift + $dy + $wk * 7;
                $matrix[$index] = 1;
            }
        }
        echo "388: <pre>$matrix</pre><br/>";
        if (strpos($PK, '-9-') !== false) {
            // neděle
            $dy = 0;
            for ($wk = 0; $wk < 520; $wk++) {
                $index = $shift + $dy + $wk * 7;
                $matrix[$index] = 1;
            }
        }
        echo "397: <pre>$matrix</pre><br/>";
        echo "<hr/>";
        $matrix2 = "";

        for ($i = 0; $i < 3650; $i++) {
            $matrix2 .= "1";
        }
        echo "403: <pre>$matrix2</pre><br/>";
        $caskody = fopen("$dir/Caskody.txt.txt", 'r');
        if ($caskody) {
            while (($buffer5 = fgets($caskody, 4096)) !== false) {
                $newbuffer5 = str_replace('"', '', $buffer5);
                $caskod = explode(',', $newbuffer5);

                if ($verze == '1.8' || $verze == '1.9') {
                    $routeno = "1";
                }
                if ($verze == '1.10' || $verze == '1.11') {
                    $routeno = explode(';', $caskod[8]);
                }

                $linka = "$caskod[0]$routeno[0]";
                $spoj = $caskod[1];
                $caskod_trip_id = "$linka$spoj";
                $poradikodu = $caskod[2];
                $typkodu = $caskod[4];
                $datumod = $caskod[5];
                $datumdo = $caskod[6];
                if ($datumdo == "") {
                    $datumdo = $datumod;
                }

                if (substr($trip_id, 0, -6) == $caskod_trip_id) {
                    switch ($typkodu) {
                        case "1":
                            $timeod = date_create_from_format('dmY', $datumod);
                            $zacdiff = date_diff($maticestart, $timeod);
                            $zacdnu = $zacdiff->days;

                            $timedo = date_create_from_format('dmY', $datumdo);
                            $kondiff = date_diff($maticestart, $timedo);
                            $kondnu = $kondiff->days;

                            if ($poradikodu == "1") {
                                $matrix2 = "";
                                for ($i = 0; $i < 3650; $i++) {
                                    $matrix2 .= "0";
                                }
                            }

                            for ($g = 0; $g < 3650; $g++) {
                                if ($g >= $zacdnu && $g <= $kondnu) {
                                    $matrix2[$g] = "1";
                                }
                            }
                            echo "444: <pre>$matrix2</pre><br/>";
                            break;

                        case "2":
                            $timeod = date_create_from_format('dmY', $datumod);
                            $zacdiff = date_diff($maticestart, $timeod);
                            $zacdnu = $zacdiff->days;

                            for ($g = 0; $g < 3650; $g++) {
                                if ($g == $zacdnu) {
                                    $matrix2[$g] = "2";
                                }
                            }
                            echo "457: <pre>$matrix2</pre><br/>";
                            break;

                        case "3":
                            $timeod = date_create_from_format('dmY', $datumod);
                            $zacdiff = date_diff($maticestart, $timeod);
                            $zacdnu = $zacdiff->days;

                            if ($poradikodu == "1") {
                                $matrix2 = "";
                                for ($i = 0; $i < 3650; $i++) {
                                    $matrix2 .= "0";
                                }
                            }
                            for ($g = 0; $g < 3650; $g++) {
                                if ($g == $zacdnu) {
                                    $matrix2[$g] = "2";
                                }
                            }
                            echo "476: <pre>$matrix2</pre><br/>";
                            break;

                        case "4":
                            $timeod = date_create_from_format('dmY', $datumod);
                            $zacdiff = date_diff($maticestart, $timeod);
                            $zacdnu = $zacdiff->days;

                            $timedo = date_create_from_format('dmY', $datumdo);
                            $kondiff = date_diff($maticestart, $timedo);
                            $kondnu = $kondiff->days;

                            for ($g = 0; $g < 3650; $g++) {
                                if ($g >= $zacdnu && $g <= $kondnu) {
                                    $matrix2[$g] = "0";
                                }
                            }
                            echo "493: <pre>$matrix2</pre><br/>";
                            break;

                        case "5":
                            $current .= "* Spoj $caskod_trip_id jede jen v lichých týdnech\n";
                            break;

                        case "6":
                            $current .= "* Spoj $caskod_trip_id jede jen v sudých týdnech\n";
                            break;

                        case "7":
                            $current .= "* Spoj $caskod_trip_id jede jen v lichých týdnech od $datumod do $datumdo\n";
                            break;

                        case "8":
                            $current .= "* Spoj $caskod_trip_id jede jen v sudých týdnech od $datumod do $datumdo\n";
                            break;
                    }
                }
            }
            fclose($caskody);
        }

        $plod = date_create_from_format('dmY', $platnostod);
        $zacpldiff = date_diff($maticestart, $plod);
        $zacinv = $zacpldiff->invert;
        $zacplat = ($zacinv == '1') ? 0 : $zacpldiff->days;

        $pldo = date_create_from_format('dmY', $platnostdo);
        $konpldiff = date_diff($maticestart, $pldo);
        $koninv = $konpldiff->invert;
        $konplat = ($koninv == '1') ? 0 : $konpldiff->days;

        for ($g = 0; $g < 3650; $g++) {
            if ($g < $zacplat || $g > $konplat) {
                $matrix2[$g] = "0";
            }
        }

        $mixmatrix = "";
        for ($g = 0; $g < 3650; $g++) {
            $soucet = $matrix[$g] + $matrix2[$g];
            $mixmatrix[$g] = $soucet;
        }
        echo "539: <pre>$mixmatrix</pre><br/>";
        $wheelchair = 0;
        if (strpos($PK, '-14-') !== false) {
            $wheelchair = 1;
        }

        $bike = 0;
        if (strpos($PK, '-27-') !== false) {
            $bike = 1;
        }

        for ($h = 0; $h < 3650; $h++) {
            $fixdate = date_create($linkaod);
            $prirustek = "$h days";
            date_add($fixdate, date_interval_create_from_date_string($prirustek));
            $totodatum = date_format($fixdate, 'Y-m-d');

            if ($mixmatrix[$h] > 1) {
                $query550 = "INSERT INTO jizdy (spoj, trip_id, datum) VALUES ('$tripspoj','$trip_id','$totodatum');";
                echo "$query550<br/>";
                //                $prikaz550 = mysqli_query($link, $query550);
            }
        }

        $query556 = "INSERT INTO trip (route_id, trip_id, trip_headsign, direction_id, wheelchair_accessible, bikes_allowed, active, spoj) VALUES ('$label$route_id', '$trip_id', '', '$smer', '$wheelchair','$bike', '0', '$tripspoj');";
        echo "$query556<br/>";
        //        $prikaz556 = mysqli_query($link, $query556);
    }
    fclose($spoje);
}

$zastavky = fopen("$dir/Zastavky.txt.txt", 'r');
if ($zastavky) {
    while (($buffer6 = fgets($zastavky, 4096)) !== false) {
        $newbuffer6 = str_replace('"', '', $buffer6);
        $zastav = explode(',', $newbuffer6);
        $zastav_no = $zastav[0];
        $zast_name = "$zastav[1],$zastav[2],$zastav[3]";
        if ($zastav[3] == '') {
            $zast_name = "$zastav[1],$zastav[2]";
        }
        if ($zastav[2] == '' && $zastav[3] == '') {
            $zast_name = $zastav[1];
        }
        $lastPK = explode(';', $zastav[11]);
        $zastPK = "-$zastav[6]-$zastav[7]-$zastav[8]-$zastav[9]-$zastav[10]-$lastPK[0]-";

        $query579 = "DELETE FROM pomstop WHERE pom_cislo = '$route_id$zastav_no';";
        echo "$query579<br/>";
        //        $prikaz579 = mysqli_query($link, $query579);
        $query582 = "INSERT INTO pomstop (pom_cislo, stop_name, stop_PK) VALUES ('$route_id$zastav_no', '$zast_name', '$zastPK');";
        echo "$query582<br/>";
        //        $prikaz582 = mysqli_query($link, $query582);
    }
    fclose($zastavky);
}

$querystopDB = "DELETE FROM linestopsDB WHERE stop_linka LIKE '$label$route_id';";
echo "$querystopDB<br/>";
//$cististopDB = mysqli_query($link, $querystopDB);

$querytripDB = "DELETE FROM triptimesDB WHERE trip_id LIKE '$route_id%';";
echo "$querytripDB<br/>";
//$cistitripDB = mysqli_query($link, $querytripDB);

$zaslinky = fopen("$dir/Zaslinky.txt.txt", 'r');
if ($zaslinky) {
    while (($buffer7 = fgets($zaslinky, 4096)) !== false) {
        $zastavlin = explode('"', $buffer7);

        if ($verze == '1.8' || $verze == '1.9') {
            $routeno = "1";
            $linka_id = $zastavlin[1] . $routeno;
        }

        if ($verze == '1.10' || $verze == '1.11') {
            $linka_id = $zastavlin[1] . $zastavlin[17];
        }

        $zastporadi = $zastavlin[3];
        $zastcode = $zastavlin[7];
        $stop_id = "{$linka_id}{$zastporadi}P$zastcode";
        $hledejpom = "SELECT stop_name, stop_PK FROM pomstop WHERE pom_cislo = '$linka_id$zastcode';";
        $najdipom = mysqli_fetch_row(mysqli_query($link, $hledejpom));
        $stop_name = $najdipom[0];
        $zastPK = $najdipom[1];

        if ($verze == '1.8' || $verze == '1.9') {
            $nove_PK = "$zastavlin[9]-$zastavlin[11]-$zastavlin[13]-";
        }

        if ($verze == '1.10' || $verze == '1.11') {
            $nove_PK = "$zastavlin[11]-$zastavlin[13]-$zastavlin[15]-";
        }

        $stopPK = "$zastPK$nove_PK";

        $query629 = "INSERT INTO linestopsDB (stop_id, stop_name, stop_pk, stop_linka, stop_poradi, stop_smer, stop_vazba) VALUES ('$label$stop_id+', '$stop_name', '$stopPK', '$label$linka_id', '$zastporadi', '0', '');";
        echo "$query629<br/>";
        //        $prikaz629 = mysqli_query($link, $query629);

        $query633 = "INSERT INTO linestopsDB (stop_id, stop_name, stop_pk, stop_linka, stop_poradi, stop_smer, stop_vazba) VALUES ('$label$stop_id-', '$stop_name', '$stopPK', '$label$linka_id', '$zastporadi', '1', '');";
        echo "$query633<br/>";
        //        $prikaz633 = mysqli_query($link, $query633);

        $query637 = "SELECT stop_id, stop_vazba FROM linevazba WHERE stop_id = '$label$stop_id+';";
        if ($result637 = mysqli_query($link, $query637)) {
            while ($row637 = mysqli_fetch_row($result637)) {
                $stopid = $row637[0];
                $stopvazba = $row637[1];

                $querymig = "UPDATE linestopsDB SET stop_vazba = '$stopvazba' WHERE stop_id LIKE '$stopid';";
                echo "$querymig<br/>";
                //                $migrate = mysqli_query($link, $querymig);
            }
        }

        $query649 = "SELECT stop_id, stop_vazba FROM linevazba WHERE stop_id = '$label$stop_id-';";
        if ($result649 = mysqli_query($link, $query649)) {
            while ($row649 = mysqli_fetch_row($result649)) {
                $stopid = $row649[0];
                $stopvazba = $row649[1];

                $querymig = "UPDATE linestopsDB SET stop_vazba = '$stopvazba' WHERE stop_id LIKE '$stopid';";
                echo "$querymig<br/>";
                //                $migrate = mysqli_query($link, $querymig);
            }
        }
    }
    fclose($zaslinky);
}

$zasspoje = fopen("$dir/Zasspoje.txt.txt", 'r');
if ($zasspoje) {
    while (($buffer8 = fgets($zasspoje, 4096)) !== false) {
        $newbuffer8 = str_replace('"', '', $buffer8);
        $zastspoj = explode(',', $newbuffer8);

        if ($verze == '1.8' || $verze == '1.9') {
            $routeno = "1";
        }

        if ($verze == '1.10') {
            $routeno = explode(';', $zastspoj[11]);
        }

        if ($verze == '1.11') {
            $routeno = explode(';', $zastspoj[14]);
        }

        $linka = "$zastspoj[0]$routeno[0]";
        $spoj = $zastspoj[1];
        $trip_find = "$linka$spoj";
        $trip_id = "";
        $query686 = "SELECT trip_id FROM trip WHERE (route_id LIKE 'F$linka' AND spoj = '$trip_find');";
        echo "$query686<br/>";
        if ($result686 = mysqli_query($link, $query686)) {
            while ($row686 = mysqli_fetch_row($result686)) {
                $trip_id = $row686[0];
            }
        }

        $zastav_poradi = $zastspoj[2];
        $zastav_code = $zastspoj[3];

        $smer = $spoj % 2;
        $direct = match ($smer) {
            0 => "-",
            1 => "+",
        };

        $zastav_id = "{$linka}{$zastav_poradi}P$zastav_code$direct";

        if ($verze == '1.8' || $verze == '1.9') {
            $km = $zastspoj[7];
            $prijezd = $zastspoj[8];
            $lastodj = explode(";", $zastspoj[9]);
            $odjezd = $lastodj[0];
            $tripstopPK = "-$zastspoj[5]-$zastspoj[6]-";
        }

        if ($verze == '1.10') {
            $km = $zastspoj[8];
            $prijezd = $zastspoj[9];
            $odjezd = $zastspoj[10];
            $tripstopPK = "-$zastspoj[6]-$zastspoj[7]-";
        }

        if ($verze == '1.11') {
            $km = $zastspoj[9];
            $prijezd = $zastspoj[10];
            $odjezd = $zastspoj[11];
            $tripstopPK = "-$zastspoj[6]-$zastspoj[7]-$zastspoj[8]-";
        }

        if ($prijezd != '<' && $prijezd != '|' && $odjezd != '<' && $odjezd != '|' && $trip_id != "") {
            $query728 = "INSERT INTO triptimesDB (zastav_id,trip_id,trip_pk,prijezd,odjezd,km) VALUES ('$label$zastav_id','$trip_id', '$tripstopPK', '$prijezd', '$odjezd', '$km');";
            echo "$query728<br/>";
            //            $prikaz728 = mysqli_query($link, $query728);
        }
    }
    fclose($zasspoje);
}

$linka_short = substr($linka, 0, 6);
$query737 = "INSERT INTO anal_done (route_id, datumod, datumdo) VALUES ('$linka_short','$linkaod', '$linkado');";
echo "$query737<br/>";
//$zapis737 = mysqli_query($link, $query737);

file_put_contents($log, $current, FILE_APPEND);

echo "<a href=\"routeedit.php?id=$label$route_id\">Editace linky $linka</a>";

mysqli_close($link);

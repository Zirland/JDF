<?php
ini_set('memory_limit', '-1');
set_time_limit(0);

require_once 'dbconnect.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$link) {
    echo "Error: Unable to connect to database." . PHP_EOL;
    echo "Reason: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$time_start = microtime(true);

$query11 = "SELECT shape_id, tvartrasy FROM shapetvary WHERE complete = 0;";
if ($result11 = mysqli_query($link, $query11)) {
    while ($row11 = mysqli_fetch_row($result11)) {
        $shape_id  = $row11[0];
        $tvartrasy = $row11[1];

        $smaz16       = "DELETE FROM shape WHERE shape_id = '$shape_id';";
        $smazanitrasy = mysqli_query($link, $smaz16);

        $i        = 0;
        $prevstop = "";

        $output = explode('|', $tvartrasy);

        foreach ($output as $prujstop) {
            $query29 = "SELECT du.path FROM du WHERE (STOP1 = '$prevstop') AND (STOP2 = '$prujstop');";
            if ($result29 = mysqli_query($link, $query29)) {
                while ($row29 = mysqli_fetch_row($result29)) {
                    $linie = $row29[0];
                    $body  = explode(';', $linie);

                    foreach ($body as $point) {
                        $sourad = explode(',', $point);
                        $lon    = $sourad[0];
                        $lat    = $sourad[1];

                        if ($lat != '' && $lon != '') {
                            $i        = $i + 1;
                            $query144 = "INSERT INTO shape VALUES ('$shape_id','$lat','$lon','$i',0);";
                            $command  = mysqli_query($link, $query144);
                        }
                    }
                }
            }
            $prevstop = $prujstop;
        }

        $query217   = "UPDATE shapetvary SET complete = '1' WHERE shape_id = '$shape_id';";
        $command217 = mysqli_query($link, $query217);
    }
    mysqli_free_result($result11);
}

$now = microtime(true);
echo "Check shapes: ";
echo $now - $time_start;
echo "<br>\n";
$time_start = $now;

$current = "";

$query46 = "SELECT agency_id,agency_name,agency_url,agency_timezone,agency_phone,agency_lang FROM agency WHERE agency_id IN (SELECT DISTINCT agency_id FROM ag_use);";
if ($result46 = mysqli_query($link, $query46)) {
    while ($row46 = mysqli_fetch_row($result46)) {
        $agency_id       = $row46[0];
        $agency_name     = $row46[1];
        $agency_url      = $row46[2];
        $agency_timezone = $row46[3];
        $agency_phone    = $row46[4];
        $agency_lang     = $row46[5];

        if ($agency_id == "25332473") {
            $agency_phone = "+420353613613";
        }
        if ($agency_lang == '') {
            $agency_lang = 'cs';
        }

        $current .= "$agency_id,\"$agency_name\",$agency_url,$agency_timezone,$agency_lang,\"$agency_phone\"\n";
    }
    mysqli_free_result($result46);
}

$file = 'agency.txt';
file_put_contents($file, $current, FILE_APPEND);

$now = microtime(true);
echo "Agencies: ";
echo $now - $time_start;
echo "<br>\n";
$time_start = $now;

$current = "";

$file     = 'stops.txt';
$query233 = "SELECT stop_id,stop_name,stop_lat,stop_lon,location_type,parent_station,wheelchair_boarding,stop_code,zone_id FROM `stop` WHERE stop_id IN (SELECT stop_id FROM stop_use);";
if ($result233 = mysqli_query($link, $query233)) {
    while ($row233 = mysqli_fetch_row($result233)) {
        $stop_id             = $row233[0];
        $stop_name           = $row233[1];
        $stop_lat            = $row233[2];
        $stop_lon            = $row233[3];
        $location_type       = $row233[4];
        $parent_station      = $row233[5];
        $wheelchair_boarding = $row233[6];
        $stop_code           = $row233[7];
        $zone_id             = $row233[8];

        $current = "$stop_id,$stop_code,\"$stop_name\",$stop_lat,$stop_lon,\"$zone_id\",$location_type,$parent_station,$wheelchair_boarding\n";
        file_put_contents($file, $current, FILE_APPEND);

        if ($parent_station != '') {
            $mark_parent = mysqli_query($link, "INSERT INTO parent_use (stop_id) VALUES ('$parent_station');");
        }
    }
    mysqli_free_result($result233);
}

$now = microtime(true);
echo "Stops: ";
echo $now - $time_start;
echo "<br>\n";
$time_start = $now;

$query313 = "SELECT stop_id,stop_name,stop_lat,stop_lon,location_type,parent_station,wheelchair_boarding,stop_code,zone_id FROM `stop` WHERE stop_id IN (SELECT stop_id FROM parent_use);";
if ($result313 = mysqli_query($link, $query313)) {
    while ($row313 = mysqli_fetch_row($result313)) {
        $stop_id             = $row313[0];
        $stop_name           = $row313[1];
        $stop_lat            = $row313[2];
        $stop_lon            = $row313[3];
        $location_type       = $row313[4];
        $parent_station      = $row313[5];
        $wheelchair_boarding = $row313[6];
        $stop_code           = $row313[7];
        $zone_id             = $row313[8];

        $current = "$stop_id,$stop_code,\"$stop_name\",$stop_lat,$stop_lon,\"$zone_id\",$location_type,$parent_station,$wheelchair_boarding\n";
        file_put_contents($file, $current, FILE_APPEND);
    }
    mysqli_free_result($result313);
}

$now = microtime(true);
echo "Parents: ";
echo $now - $time_start;
echo "<br>\n";
$time_start = $now;

$current = "";
$file    = 'shapes.txt';

$query260 = "SELECT shape_id,shape_pt_lat,shape_pt_lon,shape_pt_sequence FROM shape WHERE shape_id IN (SELECT DISTINCT shape_id FROM shapecheck);";
if ($result260 = mysqli_query($link, $query260)) {
    while ($row260 = mysqli_fetch_row($result260)) {
        $shape_id          = $row260[0];
        $shape_pt_lat      = $row260[1];
        $shape_pt_lon      = $row260[2];
        $shape_pt_sequence = $row260[3];

        $current = "J$shape_id,$shape_pt_lat,$shape_pt_lon,$shape_pt_sequence\n";
        file_put_contents($file, $current, FILE_APPEND);
    }
    mysqli_free_result($result260);
}

$now = microtime(true);
echo "Shapes: ";
echo $now - $time_start;
echo "<br>\n";
$time_start = $now;

$current = "";

$dnes  = date("Y-m-d", time());
$konec = date("Y-m-d", strtotime("+ 63 days"));

$dnesden   = substr($dnes, 8, 2);
$dnesmesic = substr($dnes, 5, 2);
$dnesrok   = substr($dnes, 0, 4);
$dnestime  = mktime(0, 0, 0, $dnesmesic, $dnesden, $dnesrok);

$konecden   = substr($konec, 8, 2);
$konecmesic = substr($konec, 5, 2);
$konecrok   = substr($konec, 0, 4);
$konectime  = mktime(0, 0, 0, $konecmesic, $konecden, $konecrok);

$calendar_start_format = date("Ymd", $dnestime);
$calendar_stop_format  = date("Ymd", $konectime);

$dnes_poradi = date("z", $dnestime);

$query262 = "SELECT DISTINCT kalendar FROM cal_use ORDER BY kalendar;";
if ($result262 = mysqli_query($link, $query262)) {
    while ($row262 = mysqli_fetch_row($result262)) {
        $kalendar = $row262[0];

        $cal_pole = explode("_", $kalendar);
        $cal_no   = $cal_pole[0];

        $query193 = "SELECT monday,tuesday,wednesday,thursday,friday,saturday,sunday FROM calendar WHERE service_id = '$cal_no';";
        if ($result193 = mysqli_query($link, $query193)) {
            while ($row193 = mysqli_fetch_row($result193)) {
                $monday    = $row193[0];
                $tuesday   = $row193[1];
                $wednesday = $row193[2];
                $thursday  = $row193[3];
                $friday    = $row193[4];
                $saturday  = $row193[5];
                $sunday    = $row193[6];

                $current = "$kalendar,$monday,$tuesday,$wednesday,$thursday,$friday,$saturday,$sunday,$calendar_start_format,$calendar_stop_format\n";
            }
        }

        $file = 'calendar.txt';
        file_put_contents($file, $current, FILE_APPEND);

        $except  = $cal_pole[1];
        $query58 = "SELECT vyjimky FROM calendar_except WHERE id = '$except';";
        if ($result58 = mysqli_query($link, $query58)) {
            while ($row58 = mysqli_fetch_row($result58)) {
                $exc_seznam = $row58[0];
            }
        }

        $rozpad = explode("_", $exc_seznam);

        for ($l = 1; $l < count($rozpad); $l++) {
            $vyjimka    = explode("(", $rozpad[$l]);
            $day_id     = $vyjimka[0];
            $vyjimka_id = str_replace(")", "", $vyjimka[1]);
            if ($day_id < $dnes_poradi) {
                $zacatek = date("Y-m-d", mktime(0, 0, 0, 1, 1, $dnesrok + 1));
            } else {
                $zacatek = date("Y-m-d", mktime(0, 0, 0, 1, 1, $dnesrok));
            }

            $zaznam_posun = $zacatek . " + " . $day_id . " days";
            $zaznam       = date("Ymd", strtotime($zaznam_posun));

            $current = "$kalendar,$zaznam,$vyjimka_id\n";

            $file = 'calendar_dates.txt';
            file_put_contents($file, $current, FILE_APPEND);
        }
    }
}

$now = microtime(true);
echo "Calendars: ";
echo $now - $time_start;
echo "<br>\n";
$time_start = $now;

mysqli_close($link);

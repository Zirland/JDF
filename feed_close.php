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

$query15 = "SELECT shape_id, tvartrasy FROM shapetvary WHERE complete = 0;";
if ($result15 = mysqli_query($link, $query15)) {
    while ($row15 = mysqli_fetch_row($result15)) {
        $shape_id = $row15[0];
        $tvartrasy = $row15[1];

        $smaz21 = "DELETE FROM shape WHERE shape_id = '$shape_id';";
        $smazanitrasy = mysqli_query($link, $smaz21);

        $i = 0;
        $prevstop = "";

        $output = explode('|', $tvartrasy);

        foreach ($output as $prujstop) {
            $query30 = "SELECT du.path FROM du WHERE (STOP1 = '$prevstop') AND (STOP2 = '$prujstop');";
            if ($result30 = mysqli_query($link, $query30)) {
                while ($row30 = mysqli_fetch_row($result30)) {
                    $linie = $row30[0];
                    $body = explode(';', $linie);

                    foreach ($body as $point) {
                        $sourad = explode(',', $point);
                        $lon = $sourad[0];
                        $lat = $sourad[1];

                        if ($lat != '' && $lon != '') {
                            $i = $i + 1;
                            $query43 = "INSERT INTO shape VALUES ('$shape_id','$lat','$lon','$i',0);";
                            $command = mysqli_query($link, $query43);
                        }
                    }
                }
            }
            $prevstop = $prujstop;
        }

        $query52 = "UPDATE shapetvary SET complete = '1' WHERE shape_id = '$shape_id';";
        $command52 = mysqli_query($link, $query52);
    }
    mysqli_free_result($result15);
}

$now = microtime(true);
echo "Check shapes: ";
echo $now - $time_start;
echo "<br>\n";
$time_start = $now;

$current = "";

$query66 = "SELECT agency_id,agency_name,agency_url,agency_timezone,agency_phone,agency_lang FROM agency WHERE agency_id IN (SELECT DISTINCT agency_id FROM ag_use);";
if ($result66 = mysqli_query($link, $query66)) {
    while ($row66 = mysqli_fetch_row($result66)) {
        $agency_id = $row66[0];
        $agency_name = $row66[1];
        $agency_url = $row66[2];
        $agency_timezone = $row66[3];
        $agency_phone = $row66[4];
        $agency_lang = $row66[5];

        if ($agency_id == "48364282") {
            $agency_phone = "+420353613613";
        }
        if ($agency_id == "17134641") {
            $agency_phone = "+420731666673";
            $agency_url = "http://www.dpch.cz/";
        }
        if ($agency_lang == '') {
            $agency_lang = 'cs';
        }

        $current .= "$agency_id,\"$agency_name\",$agency_url,$agency_timezone,$agency_lang,\"$agency_phone\"\n";
    }
    mysqli_free_result($result66);
}

$file = 'agency.txt';
file_put_contents($file, $current, FILE_APPEND);

$now = microtime(true);
echo "Agencies: ";
echo $now - $time_start;
echo "<br>\n";
$time_start = $now;

$current = "";

$file = 'stops.txt';
$query104 = "SELECT stop_id,stop_name,stop_lat,stop_lon,location_type,parent_station,wheelchair_boarding,stop_code,zone_id FROM `stop` WHERE stop_id IN (SELECT stop_id FROM stop_use);";
if ($result104 = mysqli_query($link, $query104)) {
    while ($row104 = mysqli_fetch_row($result104)) {
        $stop_id = $row104[0];
        $stop_name = $row104[1];
        $stop_lat = $row104[2];
        $stop_lon = $row104[3];
        $location_type = $row104[4];
        $parent_station = $row104[5];
        $wheelchair_boarding = $row104[6];
        $stop_code = $row104[7];
        $zone_id = $row104[8];

        $current = "$stop_id,$stop_code,\"$stop_name\",$stop_lat,$stop_lon,\"$zone_id\",$location_type,$parent_station,$wheelchair_boarding\n";
        file_put_contents($file, $current, FILE_APPEND);

        if ($parent_station != '') {
            $mark_parent = mysqli_query($link, "INSERT INTO parent_use (stop_id) VALUES ('$parent_station');");
        }
    }
    mysqli_free_result($result104);
}

$now = microtime(true);
echo "Stops: ";
echo $now - $time_start;
echo "<br>\n";
$time_start = $now;

$query133 = "SELECT stop_id,stop_name,stop_lat,stop_lon,location_type,parent_station,wheelchair_boarding,stop_code,zone_id FROM `stop` WHERE stop_id IN (SELECT stop_id FROM parent_use);";
if ($result133 = mysqli_query($link, $query133)) {
    while ($row133 = mysqli_fetch_row($result133)) {
        $stop_id = $row133[0];
        $stop_name = $row133[1];
        $stop_lat = $row133[2];
        $stop_lon = $row133[3];
        $location_type = $row133[4];
        $parent_station = $row133[5];
        $wheelchair_boarding = $row133[6];
        $stop_code = $row133[7];
        $zone_id = $row133[8];

        $current = "$stop_id,$stop_code,\"$stop_name\",$stop_lat,$stop_lon,\"$zone_id\",$location_type,$parent_station,$wheelchair_boarding\n";
        file_put_contents($file, $current, FILE_APPEND);
    }
    mysqli_free_result($result133);
}

$now = microtime(true);
echo "Parents: ";
echo $now - $time_start;
echo "<br>\n";
$time_start = $now;

$current = "";
$file = 'shapes.txt';

$query161 = "SELECT shape_id,shape_pt_lat,shape_pt_lon,shape_pt_sequence FROM shape WHERE shape_id IN (SELECT DISTINCT shape_id FROM shapecheck);";
if ($result161 = mysqli_query($link, $query161)) {
    while ($row161 = mysqli_fetch_row($result161)) {
        $shape_id = $row161[0];
        $shape_pt_lat = $row161[1];
        $shape_pt_lon = $row161[2];
        $shape_pt_sequence = $row161[3];

        $current = "J$shape_id,$shape_pt_lat,$shape_pt_lon,$shape_pt_sequence\n";
        file_put_contents($file, $current, FILE_APPEND);
    }
    mysqli_free_result($result161);
}

$now = microtime(true);
echo "Shapes: ";
echo $now - $time_start;
echo "<br>\n";
$time_start = $now;

$current = "";

$fixdate = date_create();
$dnes = date_format($fixdate, 'Ymd');

$dnes_poradi = date_format($fixdate, "z");
$dnes_rok = date_format($fixdate, "Y");

$prirustek = "63 days";
date_add($fixdate, date_interval_create_from_date_string($prirustek));
$konec = date_format($fixdate, 'Ymd');

$query193 = "SELECT DISTINCT kalendar FROM cal_use ORDER BY kalendar;";
if ($result193 = mysqli_query($link, $query193)) {
    while ($row193 = mysqli_fetch_row($result193)) {
        $kalendar = $row193[0];

        $cal_pole = explode("_", $kalendar);
        $cal_no = $cal_pole[0];

        $query201 = "SELECT monday,tuesday,wednesday,thursday,friday,saturday,sunday FROM calendar WHERE service_id = '$cal_no';";
        if ($result201 = mysqli_query($link, $query201)) {
            while ($row201 = mysqli_fetch_row($result201)) {
                $monday = $row201[0];
                $tuesday = $row201[1];
                $wednesday = $row201[2];
                $thursday = $row201[3];
                $friday = $row201[4];
                $saturday = $row201[5];
                $sunday = $row201[6];

                $current = "$kalendar,$monday,$tuesday,$wednesday,$thursday,$friday,$saturday,$sunday,$dnes,$konec\n";
            }
        }

        $file = 'calendar.txt';
        file_put_contents($file, $current, FILE_APPEND);

        $except = $cal_pole[1];
        $query220 = "SELECT vyjimky FROM calendar_except WHERE id = '$except';";
        if ($result220 = mysqli_query($link, $query220)) {
            while ($row220 = mysqli_fetch_row($result220)) {
                $exc_seznam = $row220[0];
            }
        }

        $rozpad = explode("_", $exc_seznam);

        for ($l = 1; $l < count($rozpad); $l++) {
            $vyjimka = explode("(", $rozpad[$l]);
            $day_id = $vyjimka[0];
            $vyjimka_id = str_replace(")", "", $vyjimka[1]);
            if ($day_id < $dnes_poradi) {
                $zacatek = date_create_from_format("Ymd", $dnes_rok + 1 . "0101");
            } else {
                $zacatek = date_create_from_format("Ymd", $dnes_rok . "0101");
            }

            $posun1 = "$day_id days";
            date_add($zacatek, date_interval_create_from_date_string($posun1));
            $zaznam = date_format($zacatek, "Ymd");

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
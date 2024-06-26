<?php
include 'header.php';

$dnes_den = date("j", time());
$dnes_mesic = date("n", time());
$dnes_rok = date("Y", time());
$today = mktime(0, 0, 0, $dnes_mesic, $dnes_den, $dnes_rok);

$query6 = "SELECT DISTINCT route_id, route_name, route_type, dopravce FROM analyza WHERE route_id NOT IN (SELECT DISTINCT route_id FROM anal_done) ORDER BY route_id;";
if ($result6 = mysqli_query($link, $query6)) {
    while ($row6 = mysqli_fetch_row($result6)) {
        $route_id = $row6[0];
        $route_name = $row6[1];
        $route_type = $row6[2];
        $dopravce = $row6[3];

        echo "$route_id - $route_name ($route_type) $dopravce<br />";

        $halt = 0;
        $label = "";

        $query11 = "SELECT dir, verze, datumod, datumdo, vyluka FROM analyza WHERE route_id = '$route_id' ORDER BY datumod DESC;";
        if ($result11 = mysqli_query($link, $query11)) {
            while ($row11 = mysqli_fetch_row($result11)) {
                $dir = $row11[0];
                $verze = $row11[1];
                $datumod = $row11[2];
                $datumdo = $row11[3];
                $vyluka = $row11[4];

                $od_den = substr($datumod, -2);
                $od_mesic = substr($datumod, 5, 2);
                $od_rok = substr($datumod, 0, 4);
                $od_time = mktime(0, 0, 0, $od_mesic, $od_den, $od_rok);

                if ($od_time > $today) {
                    $label = "F";
                }
                if ($od_time <= $today) {
                    $label = "";
                }
                if ($halt == 0) {
                    echo "$dir ($verze) $datumod > $datumdo $label $vyluka > <a href=\"genroute.php?file=$dir&linkaod=$datumod&linkado=$datumdo\" target=\"_blank\">Generovat</a><br />";
                }
                if ($od_time <= $today && $vyluka == "0") {
                    $halt = 1;
                }
            }
        }

    }
}

include 'footer.php';

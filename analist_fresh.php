<?php
include 'header.php';

$cisti4 = mysqli_query($link, "DELETE FROM analyza WHERE datumdo < current_date ();");
$cisti5 = mysqli_query($link, "DELETE FROM anal_done WHERE datumdo < current_date ();");
$cisti6 = mysqli_query($link, "DELETE FROM svatky WHERE datum < current_date ();");

$cisti8 = mysqli_query($link, "DELETE FROM analyza WHERE route_id IN (SELECT route_id FROM ignorace);");

$dnes_den = date("j", time());
$dnes_mesic = date("n", time());
$dnes_rok = date("Y", time());
$today = mktime(0, 0, 0, $dnes_mesic, $dnes_den, $dnes_rok);

$query15 = "SELECT DISTINCT route_id, route_name, route_type FROM analyza WHERE SUBSTRING(route_id, 1, 3) IN (SELECT substring(route_id, 1, 3) as prefix FROM anal_done GROUP BY prefix) AND route_id NOT IN (SELECT DISTINCT route_id FROM anal_done) ORDER BY route_id;";
if ($result15 = mysqli_query($link, $query15)) {
    while ($row15 = mysqli_fetch_row($result15)) {
        $route_id = $row15[0];
        $route_name = $row15[1];
        $route_type = $row15[2];

        echo "<span style=\"background-color:#dd0000;\">$route_id - $route_name ($route_type)</span><br />";

        $halt = 0;
        $label = "";

        $query27 = "SELECT DISTINCT dir, verze, datumod, datumdo, vyluka FROM analyza WHERE route_id = '$route_id' ORDER BY datumod DESC, datumdo;";
        if ($result27 = mysqli_query($link, $query27)) {
            while ($row27 = mysqli_fetch_row($result27)) {
                $dir = $row27[0];
                $verze = $row27[1];
                $datumod = $row27[2];
                $datumdo = $row27[3];
                $vyluka = $row27[4];

                $query31 = mysqli_query($link, "SELECT id FROM anal_done WHERE route_id = '$route_id' AND datumod = '$datumod';");
                $platnost = mysqli_num_rows($query31);

                $od_den = substr($datumod, -2);
                $od_mesic = substr($datumod, 5, 2);
                $od_rok = substr($datumod, 0, 4);
                $od_time = mktime(0, 0, 0, $od_mesic, $od_den, $od_rok);

                if ($platnost > 0) {
                    break;
                }
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

$query65 = "SELECT DISTINCT route_id, route_name, route_type FROM analyza WHERE route_id IN (SELECT DISTINCT route_id FROM anal_done) ORDER BY route_id;";
if ($result65 = mysqli_query($link, $query65)) {
    while ($row65 = mysqli_fetch_row($result65)) {
        $route_id = $row65[0];
        $route_name = $row65[1];
        $route_type = $row65[2];

        echo "<span style=\"background-color:#dd0000;\">$route_id - $route_name ($route_type)</span><br />";

        $halt = 0;
        $label = "";

        $query77 = "SELECT DISTINCT dir, verze, datumod, datumdo, vyluka FROM analyza WHERE route_id = '$route_id' ORDER BY datumod DESC, datumdo;";
        if ($result77 = mysqli_query($link, $query77)) {
            while ($row77 = mysqli_fetch_row($result77)) {
                $dir = $row77[0];
                $verze = $row77[1];
                $datumod = $row77[2];
                $datumdo = $row77[3];
                $vyluka = $row77[4];

                $query31 = mysqli_query($link, "SELECT id FROM anal_done WHERE route_id = '$route_id' AND datumod = '$datumod';");
                $platnost = mysqli_num_rows($query31);

                $od_den = substr($datumod, -2);
                $od_mesic = substr($datumod, 5, 2);
                $od_rok = substr($datumod, 0, 4);
                $od_time = mktime(0, 0, 0, $od_mesic, $od_den, $od_rok);

                if ($platnost > 0) {
                    break;
                }
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

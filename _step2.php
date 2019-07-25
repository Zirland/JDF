<?php
include 'header.php';

$action   = $_POST['action'];
$route_id = $_POST['routeid'];
if ($route_id == "") {
    $route_id = $_GET['routeid'];
}

switch ($action) {
    case 'route':
        $agencyid        = $_POST['agencyid'];
        $route_long_name = $_POST['fullname'];
        $route_type      = $_POST['routetype'];
        $datumod         = $_POST['jedeod'];
        $datumdo         = $_POST['jededo'];

        $query15   = "INSERT INTO mroutes (route_id, route_long_name, agency_id, route_type, platnost_od, platnost_do) VALUES ('$route_id','$route_long_name','$agencyid','$route_type','$datumod','$datumdo');";
        $command15 = mysqli_query($link, $query15);
        break;

    case 'variant':
        $varianta = $_POST['varianta'];
        $stop_id  = $_POST['stop_id'];
        $odstup   = $_POST['odstup'];
        $rezim    = $_POST['rezim'];

        $query28 = "SELECT max(stop_seq) FROM mvarianty WHERE route_id = '$route_id' AND varianta = '$varianta';";
        if ($result28 = mysqli_query($link, $query28)) {
            while ($row28 = mysqli_fetch_row($result28)) {
                $max_seq = $row28[0];
            }
        }

        $seq = $max_seq + 1;

        $query38  = "INSERT INTO mvarianty (route_id, varianta, stop_id, odstup, stop_seq, rezim) VALUES ('$route_id', '$varianta', '$stop_id', '$odstup', '$seq', '$rezim')";
        $prikaz38 = mysqli_query($link, $query38);
        break;
}

$query42 = "SELECT max(varianta) FROM mvarianty WHERE route_id = '$route_id';";
if ($result42 = mysqli_query($link, $query42)) {
    while ($row42 = mysqli_fetch_row($result42)) {
        $max_varianta = $row42[0];
    }
}
echo "Max varianta: $max_varianta<br/>";

echo "<form action=\"_step2.php\" method=\"post\">";
echo "VARIANTA <input type=\"text\" name=\"varianta\" value=\"$varianta\" size=\"3\"><br />";
echo "<input type=\"hidden\" name=\"action\" value=\"variant\">";
echo "<input type=\"hidden\" name=\"routeid\" value=\"$route_id\">";

$query55 = "SELECT mvarianty.stop_id, mvarianty.stop_seq, mvarianty.odstup, stop.stop_name, mvarianty.rezim FROM mvarianty LEFT JOIN stop ON mvarianty.stop_id = stop.stop_id WHERE route_id = '$route_id' AND varianta = '$varianta';";
if ($result55 = mysqli_query($link, $query55)) {
    while ($row55 = mysqli_fetch_row($result55)) {
        $stop_id   = $row55[0];
        $stop_seq  = $row55[1];
        $odstup    = $row55[2];
        $stop_name = $row55[3];
        $rezim     = $row55[4];

        echo "$stop_seq | $stop_id | $stop_name | $odstup | ";
        switch ($rezim) {
            case '01':
                echo "Pouze nástup";
                break;
            case '10':
                echo "Pouze výstup";
                break;
            case '33':
                echo "Zastavuje na znamení";
                break;
        }
        echo "<br/>";
    }
}

echo "<select name=\"stop_id\" autofocus><option value=\"\">-----</option>";
$query81 = "SELECT stop_id, sortname, pomcode FROM stop WHERE active=1 ORDER BY sortname;";
if ($result81 = mysqli_query($link, $query81)) {
    while ($row81 = mysqli_fetch_row($result81)) {
        $stopid   = $row81[0];
        $sortname = $row81[1];
        $pomcode  = $row81[2];

        echo "<option value=\"$stopid\">$sortname $pomcode</option>";
    }
}
echo "</select>";
echo "<input type=\"text\" name=\"odstup\" value=\"\" size=\"3\">";
echo "<select name=\"rezim\"><option value=\"00\">-----</option>";
echo "<option value=\"01\">Pouze výstup</option>";
echo "<option value=\"10\">Pouze nástup</option>";
echo "<option value=\"33\">Zastavuje na znamení</option>";
echo "<select>";

echo "<input type=\"submit\" value=\"Zapsat\">";
echo "</form>";

echo "<a href=\"_step3.php?routeid=$route_id\">Přejít na jízdy spojů</a>";
include 'footer.php';

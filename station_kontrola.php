<?php
$area = @$_GET['area'];
if (!isset($area)) {
    $area = @$_POST['area'];
}

include 'header.php';

function linky($stop_id)
{
    global $link;

    $query13 = "SELECT stop_name, pomcode, stop_code FROM stop WHERE stop_id = '$stop_id';";
    if ($result13 = mysqli_query($link, $query13)) {
        while ($row13 = mysqli_fetch_row($result13)) {
            $stop_name = $row13[0];
            $pomcode   = $row13[1];
            $stop_code = $row13[2];

            $nazev = $stop_name;
            if ($stop_code != "") {
                $nazev .= " ($stop_code)";
            }
            if ($pomcode != "") {
                $nazev .= " - $pomcode";
            }
        }
    }

    echo "$nazev<br/>";
    $query65 = "SELECT DISTINCT route.route_short_name, trip.trip_headsign, stop.stop_name FROM trip LEFT JOIN route ON route.route_id = trip.route_id LEFT join stop ON stop.stop_id = trip.trip_headsign WHERE trip_id IN (SELECT DISTINCT trip_id FROM stoptime WHERE stop_id = '$stop_id') ORDER BY CAST(route_short_name AS UNSIGNED), route_short_name;";
    if ($result65 = mysqli_query($link, $query65)) {
        while ($row65 = mysqli_fetch_row($result65)) {
            $route_name = $row65[0];
            $headsign   = $row65[2];

            echo "$route_name, $headsign<br/>";
        }
    }
    echo "<hr/>";
}

$action = @$_POST['action'];
$filtr2  = @$_POST['filtr2'];

echo "<form method=\"post\" action=\"station_kontrola.php\" name=\"filtr\">
	<input name=\"action\" value=\"filtr\" type=\"hidden\">";

$query0 = "SELECT DISTINCT stop_name FROM stop WHERE stop_id IN (SELECT DISTINCT stop_id FROM stoptime) ORDER BY CAST(stop_name AS UNSIGNED), stop_name;";

echo "<select name=\"filtr2\">";
if ($result0 = mysqli_query($link, $query0)) {
    while ($row0 = mysqli_fetch_row($result0)) {
        $nazev = $row0[0];

        echo "<option value=\"$nazev\"";
        if ($nazev == $filtr2) {echo " SELECTED";}

        echo ">$nazev</option>";
    }
    mysqli_free_result($result0);
}
echo "</select>";
echo "<input type=\"submit\" value=\"Select stop\"></form>";

switch ($action) {
    case "filtr":

        $stopArr = [];
        $query81 = "SELECT stop_id FROM stop WHERE stop_name = '$filtr2' AND location_type = 0 ORDER BY CAST(stop_code AS unsigned), stop_code, pomcode;";
        if ($result81 = mysqli_query($link, $query81)) {
            while ($row81 = mysqli_fetch_row($result81)) {
                $value = $row81[0];

                $stopArr[] = $value;
            }
        }

        foreach ($stopArr as $stop_id) {
            linky($stop_id);
        }
        break;
}

include 'footer.php';

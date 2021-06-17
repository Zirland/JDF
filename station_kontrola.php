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
    $query65 = "SELECT DISTINCT route.route_short_name, trip.trip_headsign FROM trip LEFT JOIN route ON route.route_id = trip.route_id WHERE trip_id IN (SELECT DISTINCT trip_id FROM stoptime WHERE stop_id = '$stop_id') ORDER BY CAST(route_short_name AS unsigned);";
    if ($result65 = mysqli_query($link, $query65)) {
        while ($row65 = mysqli_fetch_row($result65)) {
            $route_name = $row65[0];
            $headsign   = $row65[1];

            echo "$route_name, $headsign<br/>";
        }
    }
    echo "<hr/>";
}

$action = @$_POST['action'];
$filtr  = @$_POST['filtr'];

echo "<form method=\"post\" action=\"station_kontrola.php\" name=\"filtr\">
	<input name=\"action\" value=\"filtr\" type=\"hidden\">";

$query0 = "SELECT DISTINCT stop_name FROM stop ORDER BY stop_name;";

echo "<select name=\"filtr\">";
if ($result0 = mysqli_query($link, $query0)) {
    while ($row0 = mysqli_fetch_row($result0)) {
        $nazev = $row0[0];

        echo "<option value=\"$nazev\"";
        if ($nazev == $filtr) {echo " SELECTED";}

        echo ">$nazev</option>";
    }
    mysqli_free_result($result0);
}
echo "</select>";
echo "<input type=\"submit\" value=\"Select stop\"></form>";

switch ($action) {
    case "filtr":

        $stopArr = [];
        $query81 = "SELECT stop_id FROM stop WHERE stop_name = '$filtr' AND location_type = 0 ORDER BY stop_code, pomcode;";
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

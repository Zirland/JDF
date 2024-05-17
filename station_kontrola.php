<?php
$area = @$_GET['area'];
if (!isset($area)) {
    $area = @$_POST['area'];
}

include 'header.php';

function linky($stop_id, $p)
{
    global $link;
    $query13 = "SELECT stop_name, pomcode, stop_code FROM `stop` WHERE stop_id = '$stop_id';";
    if ($result13 = mysqli_query($link, $query13)) {
        while ($row13 = mysqli_fetch_row($result13)) {
            $stop_name = $row13[0];
            $pomcode = $row13[1];
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

    echo "$nazev <input type=\"hidden\" name=\"stop$p\" value=\"$stop_id\"><input type=\"checkbox\" name=\"grp$p\" value=\"1\"><br/>";
    $query65 = "SELECT DISTINCT route.route_short_name, trip.trip_headsign, stop.stop_name FROM trip LEFT JOIN route ON route.route_id = trip.route_id LEFT join stop ON stop.stop_id = trip.trip_headsign WHERE trip_id IN (SELECT DISTINCT trip_id FROM stoptime WHERE stop_id = '$stop_id') ORDER BY CAST(route_short_name AS UNSIGNED), route_short_name;";
    if ($result65 = mysqli_query($link, $query65)) {
        while ($row65 = mysqli_fetch_row($result65)) {
            $route_name = $row65[0];
            $headsign = $row65[2];

            echo "$route_name, $headsign<br/>";
        }
    }
    echo "<hr/>";
}

function teziste($points)
{
    if (empty($points)) {
        throw new Exception('Prázdné pole');
    }
    return (min($points) + max($points)) / 2;
}

$action = @$_POST['action'];
$filtr2 = @$_POST['filtr2'];

echo "<form method=\"post\" action=\"station_kontrola.php\" name=\"filtr\">
	<input name=\"action\" value=\"filtr\" type=\"hidden\">";

$query0 = "SELECT DISTINCT stop_name FROM stop WHERE stop_id IN (SELECT DISTINCT stop_id FROM stoptime) ORDER BY CAST(stop_name AS UNSIGNED), stop_name;";

echo "<select name=\"filtr2\">";
if ($result0 = mysqli_query($link, $query0)) {
    while ($row0 = mysqli_fetch_row($result0)) {
        $nazev = $row0[0];

        echo "<option value=\"$nazev\"";
        if ($nazev == $filtr2) {
            echo " SELECTED";
        }

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

        echo "<form action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\" method=\"post\">";

        $p = 0;
        foreach ($stopArr as $stop_id) {
            linky($stop_id, $p);
            $p += 1;
        }

        echo "<input type=\"hidden\" name=\"items\" value=\"$p\">";
        echo "<input type=\"submit\" name=\"action\" value=\"Group\">";
        echo "</form>";
        break;

    case 'Group':
        $maxitems = $_POST['items'];
        $itemlist = [];

        for ($j = 0; $j < $maxitems; $j++) {
            $grp_ind = "grp" . $j;
            $grup = @$_POST[$grp_ind];
            if ($grup == 1) {
                $index = "stop" . $j;
                $itemlist[] = $_POST[$index];
            }
        }

        $latitudes = [];
        $longitudes = [];

        foreach ($itemlist as $itemcode) {
            $query115 = "SELECT stop_lat, stop_lon, stop_name, obec, castobce, misto FROM `stop` WHERE stop_id = '$itemcode';";
            if ($result115 = mysqli_query($link, $query115)) {
                while ($row115 = mysqli_fetch_row($result115)) {
                    $latitudes[] = $row115[0];
                    $longitudes[] = $row115[1];
                    $parentname = $row115[2];
                    $parentobec = $row115[3];
                    $parentcast = $row115[4];
                    $parentmisto = $row115[5];
                }
            }
        }

        $citycode = substr($itemlist[0], 0, 6);

        $query120 = "SELECT stop_id FROM `stop` WHERE stop_id LIKE '%G%';";
        if ($result120 = mysqli_query($link, $query120)) {
            $hit_num = mysqli_num_rows($result120);
        }

        $parentcode = $citycode . "G" . $hit_num + 1;

        $medium_lat = teziste($latitudes);
        $medium_lon = teziste($longitudes);

        $query142 = "INSERT INTO `stop` (stop_id, stop_code, stop_name, stop_desc, stop_lat, stop_lon, zone_id, stop_url, location_type, parent_station, stop_timezone, wheelchair_boarding, active, pomcode, obec, castobce, misto, sortname) VALUES ('$parentcode', '', '$parentname', '', '$medium_lat', '$medium_lon', '', '', '1', '', '', '0', '1', '', '$parentobec', '$parentcast', '$parentmisto', '$parentmisto $parentcast $parentobec');";

        if ($prikaz142 = mysqli_query($link, $query142)) {
            foreach ($itemlist as $itemcode) {
                $query125 = "UPDATE `stop` SET parent_station = '$parentcode' WHERE stop_id = '$itemcode';";
                $prikaz125 = mysqli_query($link, $query125);
            }
        }
        break;
}
include 'footer.php';
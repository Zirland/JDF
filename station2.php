<?php
include 'header.php';

function odjezdy($date, $time)
{
    global $filtr, $i, $max, $link;

    $query7 = "SELECT route.route_short_name, stoptime.departure_time, trip.trip_headsign, stoptime.pickup_type, stoptime.drop_off_type, route.route_color, route.route_text_color, stoptime.trip_id, trip.wheelchair_accessible, trip.bikes_allowed FROM stoptime LEFT JOIN trip ON stoptime.trip_id = trip.trip_id LEFT JOIN route on trip.route_id = route.route_id WHERE stop_id = '$filtr' AND departure_time > '$time' AND stoptime.trip_id IN (SELECT trip_id FROM jizdy WHERE datum = '$date') AND trip.trip_headsign NOT IN (SELECT stop_name FROM stop WHERE stop_id = '$filtr') ORDER BY departure_time;";
    if ($result7 = mysqli_query($link, $query7)) {
        while ($row7 = mysqli_fetch_row($result7)) {
            $route_short_name = $row7[0];
            $departure_time   = $row7[1];
            $trip_headsign    = $row7[2];
            $pickup_type      = $row7[3];
            $drop_off_type    = $row7[4];
            $route_color      = $row7[5];
            $route_text_color = $row7[6];
            $trip_id          = $row7[7];
            $wheelchair       = $row7[8];
            $bikes            = $row7[9];

            if ($i == $max) {
                break;
            }

            echo "<tr>";
            echo "<td style=\"background-color: #$route_color; text-align: center;\"><span style=\"color: #$route_text_color;\">";
            echo "$route_short_name";
            echo "</td><td>";

            $zde = substr($departure_time, 0, 5);
            echo $zde;
            echo "</td>";

            echo "<td>$trip_headsign</td>";
            echo "<td>";
            if ($wheelchair == "1") {
                echo "&#9855;\t";
            }
            if ($bikes == "1") {
                echo "&#128690;\t";
            }
            if ($pickup_type == "1") {
                echo "pouze výstup\t";
            }
            if ($drop_off_type == "1") {
                echo "pouze nástup\t";
            }
            $i = $i + 1;
            echo "</tr>";
        }
        mysqli_free_result($result7);
    }
}

$action = @$_POST['action'];
$filtr  = @$_POST['filtr'];

echo "<form method=\"post\" action=\"station2.php\" name=\"filtr\">
	<input name=\"action\" value=\"filtr\" type=\"hidden\">";
echo "<select name=\"filtr\">";
$query0 = "SELECT stop_id, sortname, pomcode FROM stop WHERE active=1 ORDER BY sortname, pomcode;";
if ($result0 = mysqli_query($link, $query0)) {
    while ($row0 = mysqli_fetch_row($result0)) {
        $kod    = $row0[0];
        $nazev  = $row0[1];
        $pomkod = $row0[2];

        echo "<option value=\"$kod\"";
        if ($kod == $filtr) {echo " SELECTED";}
        echo ">$nazev $pomkod</option>";
    }
    mysqli_free_result($result0);
}
echo "</select>";
echo "<input type=\"submit\" value=\"Vybrat zastávku\"></form>";

switch ($action) {
    case "filtr":
        echo "<table><tr>";
        $h = "00";
        echo "<td><b>$h</b></td><td>";
        $query28 = "SELECT route.route_short_name AS short, trip.wheelchair_accessible AS wheel, stoptime.departure_time AS time, COUNT(*) AS cnt FROM stoptime LEFT JOIN trip ON stoptime.trip_id = trip.trip_id LEFT JOIN route ON trip.route_id = route.route_id WHERE stoptime.stop_id = '$filtr' GROUP BY short, time ORDER BY departure_time, short;";
        if ($result28 = mysqli_query($link, $query28)) {
            while ($row28 = mysqli_fetch_row($result28)) {
                $linka          = $row28[0];
                $wheelchair     = $row28[1];
                $departure_time = $row28[2];

                $hodina = substr($departure_time, 0, 2);
                $minuta = substr($departure_time, 3, 2);

                if ($hodina == $h) {
                    echo "<small>$linka</small><br/>$minuta</td><td>";
//                    echo "$minuta\t";
                } else {
                    $h = $h + 1;
                    if ($h < 10) {
                        $h = "0" . $h;
                    }
                    echo "</td></tr><tr><td><b>$h</b></td><td><small>$linka</small><br/>$minuta</td><td>";
                }
            }
            mysqli_free_result($result28);
        }

        echo "</td></tr></table>";

}

include 'footer.php';

<?php
include 'header.php';

$action    = @$_POST['action'];
$sel_route = @$_POST['route'];
$old_stop  = @$_POST['stop_old'];
$sel_order = @$_POST['poradi'];
$new_stop  = @$_POST['stop_new'];

echo "<form method=\"post\" action=\"reroute.php\" name=\"findstops\"><input name=\"action\" value=\"findstops\" type=\"hidden\">";
echo "Route: <select name=\"route\">";
$query6 = "SELECT route_id, route_short_name, route_long_name FROM `route` ORDER BY route_short_name, route_long_name;";
if ($result6 = mysqli_query($link, $query6)) {
    while ($row6 = mysqli_fetch_row($result6)) {
        $route_id    = $row6[0];
        $route_short = $row6[1];
        $route_long  = $row6[2];
        echo "<option value=\"$route_id\"";
        if ($route_id == $sel_route) {
            echo " SELECTED";
        }
        echo ">$route_short – $route_long</option>";
    }
    mysqli_free_result($result6);
} else {
    echo "Error description: " . mysqli_error($link);
}
echo "</select>";
echo "<input type=\"submit\"></form>";

switch ($action) {
    case "findstops":
        $find_route = str_replace('F', '', $sel_route);
        echo "<form method=\"post\" action=\"reroute.php\" name=\"findtrips\"><input name=\"action\" value=\"findtrips\" type=\"hidden\">";
        echo "<input name=\"route\" value=\"$sel_route\" type=\"hidden\">";
        echo "Stop: <select name=\"stop_old\">";
        $query34 = "SELECT stop_id, sortname, stop_code, pomcode FROM stop WHERE stop_id IN (SELECT stop_id FROM stoptime WHERE trip_id LIKE '$find_route%') ORDER BY sortname;";
        if ($result34 = mysqli_query($link, $query34)) {
            while ($row34 = mysqli_fetch_row($result34)) {
                $old_stop_id   = $row34[0];
                $old_stop_name = $row34[1];
                $old_stop_code = $row34[2];
                $old_pomcode   = $row34[3];
                echo "<option value=\"$old_stop_id\"";
                if ($old_stop_id == $old_stop) {
                    echo " SELECTED";
                }
                echo ">$old_stop_name $old_stop_code $old_pomcode</option>";
            }
            mysqli_free_result($result34);
        } else {
            echo "Error description: " . mysqli_error($link);
        }
        echo "</select>";

        echo "<input type=\"radio\" id=\"start\" name=\"poradi\" value=\"start\"";
        if ($sel_order == 'start') {
            echo " CHECKED";
        }
        echo ">";
        echo "<label for=\"start\">začíná</label>";
        echo "<input type=\"radio\" id=\"end\" name=\"poradi\" value=\"end\"";
        if ($sel_order == 'end') {
            echo " CHECKED";
        }
        echo ">";
        echo "<label for=\"end\">končí</label>";

        echo " |  New stop: <select name=\"stop_new\">";
        $query69 = "SELECT stop_id, sortname, stop_code, pomcode FROM stop ORDER BY sortname;";
        if ($result69 = mysqli_query($link, $query69)) {
            while ($row69 = mysqli_fetch_row($result69)) {
                $newstop_id   = $row69[0];
                $newstop_name = $row69[1];
                $newstop_code = $row69[2];
                $new_pomcode  = $row69[3];
                echo "<option value=\"$newstop_id\"";
                echo ">$newstop_name $newstop_code $new_pomcode</option>";
            }
            mysqli_free_result($result69);
        } else {
            echo "Error description: " . mysqli_error($link);
        }
        echo "</select>";

        echo "<input type=\"submit\"></form>";

    case "findtrips":
        echo "Replace $old_stop - $sel_order on $sel_route - for $new_stop<br/>";
        break;
}
echo "== Konec ==";
include 'footer.php';

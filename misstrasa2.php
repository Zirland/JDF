<?php
include 'header.php';

$action = @$_POST['action'];
$from = @$_POST['from'];
$to = @$_POST['to'];
$path = @$_POST['path'];

echo "<form method=\"post\" action=\"misstrasa2.php\" name=\"odkud\"><input name=\"action\" value=\"odkud\" type=\"hidden\">";
echo "Odkud: <select name=\"from\">";
$query0 = "SELECT stop_id, stop_name, stop_code, pomcode FROM `stop` WHERE stop_id IN (SELECT DISTINCT stop1 FROM du WHERE final = '2') ORDER BY stop_name;";
if ($result0 = mysqli_query($link, $query0)) {
    while ($row0 = mysqli_fetch_row($result0)) {
        $kodf = $row0[0];
        $nazevf = $row0[1];
        $stcodef = $row0[2];
        $codef = $row0[3];
        echo "<option value=\"$kodf\"";
        if ($kodf == $from) {
            echo " SELECTED";
        }
        echo ">$nazevf $stcodef $codef $kodf</option>";
    }
    mysqli_free_result($result0);
} else {
    echo "Error description: " . mysqli_error($link);
}

echo "</select>";
echo "<input type=\"submit\"></form>";

switch ($action) {
    case "uloz":
        $body = explode("),(", $path);
        $pass = "";
        foreach ($body as $point) {
            $upr_point = str_replace(")", "", $point);
            $upr_point2 = str_replace("(", "", $upr_point);

            $pass .= $upr_point2 . ";";
        }

        $pass = substr($pass, 0, -1);
        $query51 = "UPDATE du SET `path` = '$pass', final = '1' WHERE stop1 = '$from' AND stop2 = '$to';";
        $zapis51 = mysqli_query($link, $query51);
        $query54 = "UPDATE shapetvary SET complete = '0' WHERE tvartrasy LIKE '%$from|$to|%';";
        $zapis54 = mysqli_query($link, $query54);
        $action = "kam";

    case "odkud":
        echo "<form method=\"post\" action=\"misstrasa2.php\" name=\"kam\"><input name=\"action\" value=\"kam\" type=\"hidden\"><input name=\"from\" value=\"$from\" type=\"hidden\">";
        echo "Kam: <select name=\"to\">";
        $query1 = "SELECT stop_id, stop_name, stop_code, pomcode FROM `stop` WHERE stop_id IN (SELECT stop2 FROM du WHERE stop1 = '$from' AND final = '2') ORDER BY stop_name;";
        if ($result1 = mysqli_query($link, $query1)) {
            while ($row1 = mysqli_fetch_row($result1)) {
                $kodt = $row1[0];
                $nazevt = $row1[1];
                $stcodet = $row1[2];
                $codet = $row1[3];
                echo "<option value=\"$kodt\"";
                if ($kodt == $to) {
                    echo " SELECTED";
                }
                echo ">$nazevt $stcodet $codet $kodt</option>";
            }
            mysqli_free_result($result1);
        } else {
            echo "Error description: " . mysqli_error($link);
        }

        echo "</select>";
        echo "<input type=\"submit\"></form>";
        break;

    case "kam":
        echo "<form method=\"post\" action=\"misstrasa2.php\" name=\"kam\"><input name=\"action\" value=\"kam\" type=\"hidden\"><input name=\"from\" value=\"$from\" type=\"hidden\">";
        echo "Kam: <select name=\"to\">";
        $query1 = "SELECT stop_id, stop_name, stop_code, pomcode FROM `stop` WHERE stop_id IN (SELECT stop2 FROM du WHERE stop1 = '$from' AND final = '2') ORDER BY stop_name;";
        if ($result1 = mysqli_query($link, $query1)) {
            while ($row1 = mysqli_fetch_row($result1)) {
                $kodt = $row1[0];
                $nazevt = $row1[1];
                $stcodet = $row1[2];
                $codet = $row1[3];
                echo "<option value=\"$kodt\"";
                if ($kodt == $to) {
                    echo " SELECTED";
                }
                echo ">$nazevt $stcodet $codet $kodt</option>";
            }
            mysqli_free_result($result1);
        } else {
            echo "Error description: " . mysqli_error($link);
        }

        echo "</select>";
        echo "<input type=\"submit\"></form>";

        $query47 = "SELECT stop_lat, stop_lon FROM `stop` WHERE stop_id = '$from';";
        if ($result47 = mysqli_query($link, $query47)) {
            $row47 = mysqli_fetch_row($result47);
            $fromlat = $row47[0];
            $fromlon = $row47[1];
        }
        $query53 = "SELECT stop_lat, stop_lon FROM `stop` WHERE stop_id = '$to';";
        if ($result53 = mysqli_query($link, $query53)) {
            $row53 = mysqli_fetch_row($result53);
            $tolat = $row53[0];
            $tolon = $row53[1];
        }

        $query96 = "SELECT du_id FROM du WHERE stop1 = '$from' AND stop2 = '$to';";
        $pom96 = mysqli_fetch_row(mysqli_query($link, $query96));
        $du_id = $pom96[0];
        echo "$du_id | <a href=\"usek.php?du_id=$du_id\" target=\"blank\">Editace úseku</a> | <a href=\"reroute.php?du_id=$du_id\" target=\"blank\">Reroute</a><br/>";

        $prujezdy = $fromlon . "," . $fromlat . "|" . $tolon . "," . $tolat;

        echo "<details>";
        $query146 = "SELECT trip_id FROM trip WHERE shape_id LIKE '%$from|$to|%';";
        if ($result146 = mysqli_query($link, $query146)) {
            $count = mysqli_num_rows($result146);
            while ($row146 = mysqli_fetch_row($result146)) {
                $trip_id = $row146[0];

                echo "<a href=\"tripedit.php?id=$trip_id\" target=\"_blank\">$trip_id</a> > ";
            }
            echo "<summary>$count</summary></details>";
        }

        echo "<details>";
        $query146 = "SELECT trip_id FROM du_use WHERE du_id = '$du_id';";
        if ($result146 = mysqli_query($link, $query146)) {
            $count = mysqli_num_rows($result146);
            while ($row146 = mysqli_fetch_row($result146)) {
                $trip_id = $row146[0];

                echo "<a href=\"tripedit.php?id=$trip_id\" target=\"_blank\">$trip_id</a> > ";
            }
            echo "<summary>$count</summary></details>";
        }

        ?>

        <div id="m" style="height:800px"></div>

        <script type="text/javascript">
            function click(e, elm) {
                var click_coords = SMap.Coords.fromEvent(e.data.event, m);

                skrz.push(click_coords);
                sour = [];
                sour.push(first);
                var jizda = sour.concat(skrz);
                jizda.push(last);

                SMap.Route.route(jizda, {
                    geometry: true
                }).then(nalezeno);
            }

            var skrz = [];
            var centerMap = SMap.Coords.fromWGS84(14.40, 50.08);
            var m = new SMap(JAK.gel("m"), centerMap, 16);
            var l = m.addDefaultLayer(SMap.DEF_BASE).enable();
            m.addDefaultControls();

            m.getSignals().addListener(window, "map-click", click);

            var nalezeno = function (route) {
                var vrstva = new SMap.Layer.Geometry();
                m.addLayer(vrstva).enable();

                var coords = route.getResults().geometry;
                document.getElementById("path").value = coords;
                var cz = m.computeCenterZoom(coords);
                m.setCenterZoom(cz[0], cz[1]);
                var g = new SMap.Geometry(SMap.GEOMETRY_POLYLINE, null, coords);
                vrstva.addGeometry(g);
            };

            var first = SMap.Coords.fromWGS84(<?php echo "$fromlon, $fromlat"; ?>);
            var last = SMap.Coords.fromWGS84(<?php echo "$tolon, $tolat"; ?>);
            var coords = [first, last];

            SMap.Route.route(coords, {
                geometry: true
            }).then(nalezeno);
        </script>

        <?php
        echo "<form method=\"post\" action=\"misstrasa2.php\" name=\"uloz\"><input name=\"action\" value=\"uloz\" type=\"hidden\"><input name=\"from\" value=\"$from\" type=\"hidden\"><input name=\"to\" value=\"$to\" type=\"hidden\"><input id=\"path\" name=\"path\" value=\"\" type=\"hidden\"><input type=\"submit\" value=\"Zapsat\"></form>";
        break;
}

include 'footer.php';
?>
<?php
include 'header.php';

$action = $_POST['action'];
$delete = $_POST['delete'];
$usek_id = $_GET['du_id'];
if (!$usek_id) {
    $usek_id = $_POST['id_usek'];
}
;
$path = $_POST['path'];
$from = @$_POST['from'];
$to = @$_POST['to'];

$koleje = [];
$query15 = "SELECT DISTINCT stoptime.stop_id from stoptime WHERE trip_id IN (SELECT trip_id FROM trip WHERE route_id IN (SELECT route_id FROM route WHERE route_type = 0));";
if ($result15 = mysqli_query($link, $query15)) {
    while ($row15 = mysqli_fetch_row($result15)) {
        $koleje[] = $row15[0];
    }
}

echo "<form method=\"post\" action=\"koleje.php\" name=\"usek\"><input name=\"action\" value=\"usek\" type=\"hidden\">";
echo "<input type=\"text\" name=\"id_usek\" value=\"$usek_id\"><input type=\"submit\"></form>";

echo "<form method=\"post\" action=\"koleje.php\" name=\"odkud\"><input name=\"action\" value=\"odkud\" type=\"hidden\">";
echo "Odkud: <select name=\"from\">";
$query0 = "SELECT stop_id, stop_name, pomcode FROM `stop` WHERE stop_id IN (SELECT stop1 FROM du WHERE final = 3) ORDER BY stop_name;";
if ($result0 = mysqli_query($link, $query0)) {
    while ($row0 = mysqli_fetch_row($result0)) {
        $kodf = $row0[0];
        $nazevf = $row0[1];
        $codef = $row0[2];
        echo "<option value=\"$kodf\"";
        if ($kodf == $from) {
            echo " SELECTED";
        }
        echo ">$nazevf $codef $kodf";
        if (in_array($kodf, $koleje)) {
            echo " ##";
        }

        echo "</option>";
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

            if ($upr_point2 != "") {
                $pass .= $upr_point2 . ";";
            }
        }

        $pass = substr($pass, 0, -1);
        $query51 = "UPDATE du SET `path` = '$pass', final = 1 WHERE du_id = '$usek_id';";
        $zapis51 = mysqli_query($link, $query51);

        $query31 = "SELECT stop1, stop2 FROM du WHERE du_id = '$usek_id';";
        if ($result31 = mysqli_query($link, $query31)) {
            while ($row31 = mysqli_fetch_row($result31)) {
                $from = $row31[0];
                $to = $row31[1];
            }
        }

        $query54 = "UPDATE shapetvary SET complete = '0' WHERE tvartrasy LIKE '%$from|$to|%';";
        $zapis54 = mysqli_query($link, $query54);
        $action = "usek";

    case "usek":
        $query102 = "SELECT path, stop1, stop2 FROM du WHERE du_id = '$usek_id';";
        echo $query102;
        if ($result102 = mysqli_query($link, $query102)) {
            $row102 = mysqli_fetch_row($result102);
            $path = $row102[0];
            $from = $row102[1];
            $to = $row102[2];
        }

        $query2 = "SELECT stop_name, pomcode, stop_code FROM stop WHERE stop_id = '$from';";
        if ($result2 = mysqli_query($link, $query2)) {
            while ($row2 = mysqli_fetch_row($result2)) {
                $nazevv = $row2[0];
                $codev = $row2[1];
                $stopcodev = $row2[2];
                echo "From: $nazevv $codev $stopcodev | ";
            }
            mysqli_free_result($result2);
        }

        $query2 = "SELECT stop_name, pomcode, stop_code FROM stop WHERE stop_id = '$to';";
        if ($result2 = mysqli_query($link, $query2)) {
            while ($row2 = mysqli_fetch_row($result2)) {
                $nazevv = $row2[0];
                $codev = $row2[1];
                $stopcodev = $row2[2];
                echo "To: $nazevv $codev $stopcodev<br/>";
            }
            mysqli_free_result($result2);
        }

        if ($delete == "1") {
            $smazat = mysqli_query($link, "DELETE FROM du WHERE du_id = '$usek_id';");
            echo "<br/>Smazáno";
        }

        echo "<div id=\"text\"></div>";
        echo "<form method=\"post\" action=\"koleje.php\" name=\"trasa\"><input name=\"action\" value=\"uloz\" type=\"hidden\">";
        echo "<input type=\"hidden\" name=\"id_usek\" value=\"$usek_id\"><input type=\"hidden\" name=\"path\" id=\"path\" value=\"\"><input type=\"submit\"></form>";
        break;

    case "odkud":
        echo "<form method=\"post\" action=\"koleje.php\" name=\"kam\"><input name=\"action\" value=\"kam\" type=\"hidden\"><input name=\"from\" value=\"$from\" type=\"hidden\">";
        echo "Kam: <select name=\"to\">";
        $query1 = "SELECT stop_id, stop_name, pomcode FROM stop WHERE stop_id IN (SELECT stop2 FROM du WHERE stop1 = '$from' AND final = 3) ORDER BY stop_name;";
        echo $query1;
        if ($result1 = mysqli_query($link, $query1)) {
            while ($row1 = mysqli_fetch_row($result1)) {
                $kodt = $row1[0];
                $nazevt = $row1[1];
                $codet = $row1[2];
                echo "<option value=\"$kodt\"";
                if ($kodt == $to) {
                    echo " SELECTED";
                }
                echo ">$nazevt $codet $kodt";
                if (in_array($kodt, $koleje)) {
                    echo " ##";
                }
                echo "</option>";
            }
            mysqli_free_result($result1);
        } else {
            echo "Error description: " . mysqli_error($link);
        }

        echo "</select>";
        echo "<input type=\"submit\"></form>";
        break;

    case "kam":
        echo "<form method=\"post\" action=\"koleje.php\" name=\"kam\"><input name=\"action\" value=\"kam\" type=\"hidden\"><input name=\"from\" value=\"$from\" type=\"hidden\">";
        echo "Kam: <select name=\"to\">";
        $query1 = "SELECT stop_id, stop_name, pomcode FROM stop WHERE stop_id IN (SELECT stop2 FROM du WHERE stop1 = '$from' AND final = 3) ORDER BY stop_name;";
        echo $query1;
        if ($result1 = mysqli_query($link, $query1)) {
            while ($row1 = mysqli_fetch_row($result1)) {
                $kodt = $row1[0];
                $nazevt = $row1[1];
                $codet = $row1[2];
                echo "<option value=\"$kodt\"";
                if ($kodt == $to) {
                    echo " SELECTED";
                }
                echo ">$nazevt $codet $kodt";
                if (in_array($kodt, $koleje)) {
                    echo " ##";
                }
                echo "</option>";
            }
            mysqli_free_result($result1);
        } else {
            echo "Error description: " . mysqli_error($link);
        }

        echo "</select>";
        echo "<input type=\"submit\"></form>";

        $query102 = "SELECT `path`, du_id FROM du WHERE stop1 = '$from' AND stop2 = '$to';";
        echo $query102;
        if ($result102 = mysqli_query($link, $query102)) {
            $row102 = mysqli_fetch_row($result102);
            $path = $row102[0];
            $du_id = $row102[1];
        }

        echo "<br/>ID = $du_id | ";
        $query2 = "SELECT stop_name, pomcode, stop_code FROM stop WHERE stop_id = '$from';";
        if ($result2 = mysqli_query($link, $query2)) {
            while ($row2 = mysqli_fetch_row($result2)) {
                $nazevv = $row2[0];
                $codev = $row2[1];
                $stopcodev = $row2[2];
                echo "From: $nazevv $codev $stopcodev | ";
            }
            mysqli_free_result($result2);
        }

        $query2 = "SELECT stop_name, pomcode, stop_code FROM stop WHERE stop_id = '$to';";
        if ($result2 = mysqli_query($link, $query2)) {
            while ($row2 = mysqli_fetch_row($result2)) {
                $nazevv = $row2[0];
                $codev = $row2[1];
                $stopcodev = $row2[2];
                echo "To: $nazevv $codev $stopcodev<br/>";
            }
            mysqli_free_result($result2);
        }
        break;
}
?>

<div id="m" style="height:600px"></div>

<script type="text/javascript">
    function addMarker(nazev, id, x, y) {
        var znacka = JAK.mel("div");
        var obrazek = JAK.mel("img", {
            src: SMap.CONFIG.img + "/marker/drop-red.png"
        });
        znacka.appendChild(obrazek);

        var popisek = JAK.mel("div", {}, {
            position: "absolute",
            left: "0px",
            top: "2px",
            textAlign: "center",
            width: "22px",
            color: "white",
            fontWeight: "bold"
        });
        popisek.innerHTML = nazev;
        znacka.appendChild(popisek);


        var options = {
            title: nazev,
            url: znacka
        };

        var pozice = SMap.Coords.fromWGS84(Number(x), Number(y));
        var marker = new SMap.Marker(pozice, id, options);
        marker.decorate(SMap.Marker.Feature.Draggable);
        vrstva.addMarker(marker);
        markers.push(pozice);
    }

    function removeMarker(e) {
        var marker = e.target;
        var id = marker.getId();
        vrstva.removeMarker(marker);
        markers[id] = "()";

        vystup();
    }

    function removePoint(id) {
        markers[id] = "()";

        vystup("1");
    }

    function start(e) {
        var node = e.target.getContainer();
        node[SMap.LAYER_MARKER].style.cursor = "pointer";
    }

    function stop(e) {
        var node = e.target.getContainer();
        node[SMap.LAYER_MARKER].style.cursor = "";
        var marker = e.target;
        var id = marker.getId();
        var coords = marker.getCoords();
        var souradnice = coords.toString().split(",");
        var souradnice_x = souradnice[0].replace(/\(/g, "");
        var souradnice_y = souradnice[1].replace(/\)/g, "");

        var pozice = SMap.Coords.fromWGS84(souradnice_x, souradnice_y);
        markers.splice(id, 1, pozice);

        vystup();
    }

    function vystup(open) {
        var vystup = "<details";
        if (open == "1") {
            vystup += " open";
        }
        vystup += "><summary>Points</summary>";
        for (var i = 0; i < markers.length; i++) {
            vystup += i + ": " + markers[i] + "<input type=\"button\" onClick=\"removePoint(" + i + ")\"><br/>";
        }
        vystup += "</details>";

        document.getElementById("text").innerHTML = vystup;
        document.getElementById("path").value = markers;
    }

    var m = new SMap(JAK.gel("m"));
    m.addDefaultLayer(SMap.DEF_OPHOTO);
    m.addDefaultLayer(SMap.DEF_BASE).enable();

    var layerSwitch = new SMap.Control.Layer({
        width: 65,
        items: 2,
        page: 2
    });
    layerSwitch.addDefaultLayer(SMap.DEF_BASE);
    layerSwitch.addDefaultLayer(SMap.DEF_OPHOTO);
    m.addControl(layerSwitch, { left: "8px", top: "9px" });

    m.addControl(new SMap.Control.Sync());
    var mouse = new SMap.Control.Mouse(SMap.MOUSE_PAN | SMap.MOUSE_WHEEL | SMap.MOUSE_ZOOM);
    m.addControl(mouse);

    var vrstva = new SMap.Layer.Marker();
    m.addLayer(vrstva);
    vrstva.enable();
    var markers = [];

    <?php
    $body = explode(";", $path);
    for ($i = 0; $i < count($body); $i++) {
        echo "addMarker($i, $i, $body[$i]);";
    }
    ?>

    var cz = m.computeCenterZoom(markers);
    m.setCenterZoom(cz[0], cz[1]);
        vystup();

    var signals = m.getSignals();
    signals.addListener(window, "marker-click", removeMarker);
    signals.addListener(window, "marker-drag-stop", stop);
    signals.addListener(window, "marker-drag-start", start);
</script>


<?php
include 'footer.php';
?>
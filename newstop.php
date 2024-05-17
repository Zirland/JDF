<?php
include 'header.php';

$action = @$_POST['action'];

$getlat = @$_GET['getlat'];
$getlon = @$_GET['getlon'];
$getobec = @$_GET['getobec'];
$getcastobce = @$_GET['getcastobce'];
$getmisto = @$_GET['getmisto'];
$getpomcode = @$_GET['getpomcode'];
$getid = @$_GET['getid'];
$kodobec = "";

switch ($action) {
    case 'nova':
        $stopcode = $_POST['stopcode'];
        $stoplat = $_POST['stoplat'];
        $stoplon = $_POST['stoplon'];
        $pomcode = $_POST['pomcode'];
        $kodobec = $_POST['kodobec'];
        $castobce = $_POST['castobce'];
        $misto = $_POST['misto'];
        $impid = $_POST['impid'];

        $pom18 = mysqli_fetch_row(mysqli_query($link, "SELECT nazev_obce FROM obce WHERE lau2 = '$kodobec';"));
        $obec = $pom18[0];

        $query21 = "SELECT max FROM stop_count WHERE kodobce = '$kodobec';";
        if ($result21 = mysqli_query($link, $query21)) {
            while ($row21 = mysqli_fetch_row($result21)) {
                $max = $row21[0];
            }
        }

        $hit = mysqli_num_rows($result21);
        if ($hit == 0) {
            $max = 0;
            $insert31 = mysqli_query($link, "INSERT INTO stop_count (kodobce, max) VALUES ('$kodobec', '0');");
        }

        $newmax = $max + 1;
        $stopid = $kodobec . "Z" . $newmax;
        $update28 = mysqli_query($link, "UPDATE stop_count SET max = '$newmax' WHERE kodobce = '$kodobec';");

        $stopname = $obec;
        if ($castobce != '') {
            $stopname .= ", " . $castobce;
        }
        if ($misto != '') {
            $stopname .= ", " . $misto;
        }

        $sortname = "";
        if ($misto != '') {
            $sortname .= "$misto ";
        }
        if ($castobce != '') {
            $sortname .= "$castobce ";
        }
        $sortname .= $obec;
        if ($stopcode != '') {
            $sortname .= " $stopcode";
        }

        $query14 = "INSERT INTO stop (stop_id, stop_code, stop_name, stop_desc, stop_lat, stop_lon, zone_id, stop_url, location_type, stop_timezone, wheelchair_boarding, active, pomcode, obec, castobce, misto, sortname)  VALUES ('$stopid','$stopcode','$stopname','','$stoplat','$stoplon','','','0','','0','1', '$pomcode', '$obec', '$castobce', '$misto', '$sortname');";
        $prikaz14 = mysqli_query($link, $query14);

        $deaktivace = "UPDATE shapetvary SET complete = '0' WHERE (tvartrasy LIKE '%$stopid|%');";
        $prikaz19 = mysqli_query($link, $deaktivace);

        $delimport = "DELETE FROM importstop WHERE id='$impid';";
        $prikazdel = mysqli_query($link, $delimport);
        break;
}

echo "<table>";
echo "<tr><td colspan=\"4\">Insert new stop</td></tr>";

echo "<form method=\"post\" action=\"newstop.php\" name=\"nova\"><input name=\"action\" value=\"nova\" type=\"hidden\"><input name=\"impid\" value=\"$getid\" type=\"hidden\">";

echo "<tr><td>Obec</td><td>Část obce</td><td>Místo</td><td>Pomcode</td><td>Stop code</td><td>Latitude ~50.123456</td><td>Longitude ~16.987654</td></tr>";
echo "<tr><td><select id=\"kodobec\" name=\"kodobec\" autofocus>";
$query53 = "SELECT lau1, lau2, nazev_obce FROM obce ORDER BY nazev_obce;";
if ($result53 = mysqli_query($link, $query53)) {
    while ($row53 = mysqli_fetch_row($result53)) {
        $kodokres = $row53[0];
        $kodobce = $row53[1];
        $nazevobce = $row53[2];

        echo "<option value=\"$kodobce\"";
        if ($kodobec == $kodobce) {
            echo " SELECTED";
        }
        echo ">$nazevobce $kodokres</option>";
    }
}
echo "</select>$getobec</td><td><input name=\"castobce\" value=\"$getcastobce\" type=\"text\"></td><td><input name=\"misto\" value=\"$getmisto\" type=\"text\"></td><td><input name=\"pomcode\" value=\"$getpomcode\" type=\"text\"></td><td><input name=\"stopcode\" value=\"\" type=\"text\"></td><td><input name=\"stoplat\" id=\"stoplat\" value=\"$getlat\" type=\"text\"></td><td><input name=\"stoplon\" id=\"stoplon\" value=\"$getlon\" type=\"text\"></td></tr>";
echo "<tr><td></td><td colspan=\"3\"><input type=\"submit\" value=\"Insert\"></form></td></tr>";
echo "</table>";
?>

<div id="mapa" style="width:1200px; height:800px;"></div>

<script type="text/javascript">
    function SelectElement(id, valueToSelect) {
        var element = document.getElementById(id);
        element.value = valueToSelect;
    }

    function start(e) {
        var node = e.target.getContainer();
        node[SMap.LAYER_MARKER].style.cursor = "pointer";
    }

    function stop(e) {
        var node = e.target.getContainer();
        node[SMap.LAYER_MARKER].style.cursor = "";
        var coords = e.target.getCoords();
        var souradnice = coords.toString().split(",");
        var souradnice_x = souradnice[0].replace(/\(/g, "");
        var souradnice_y = souradnice[1].replace(/\)/g, "");

        document.getElementById("stoplat").value = souradnice_y;
        document.getElementById("stoplon").value = souradnice_x;

        var pozice = SMap.Coords.fromWGS84(souradnice_x, souradnice_y);
        mapa.setCenter(pozice);
    }

    <?php
    if (isset($stoplon) && isset($stoplat)) {
        echo "var stred = SMap.Coords.fromWGS84($stoplon, $stoplat);\n";
    } else {
        echo "var stred = SMap.Coords.fromWGS84(14.41, 50.08);\n";
    }
    ?>

    var mapa = new SMap(document.querySelector("#mapa"), stred, 18);

    mapa.addDefaultLayer(SMap.DEF_OPHOTO);
    mapa.addDefaultLayer(SMap.DEF_BASE).enable();

    var layerSwitch = new SMap.Control.Layer({
        width: 65,
        items: 2,
        page: 2
    });
    layerSwitch.addDefaultLayer(SMap.DEF_BASE);
    layerSwitch.addDefaultLayer(SMap.DEF_OPHOTO);
    mapa.addControl(layerSwitch, { left: "8px", top: "9px" });

    mapa.addControl(new SMap.Control.Sync());
    var mouse = new SMap.Control.Mouse(SMap.MOUSE_PAN | SMap.MOUSE_WHEEL | SMap.MOUSE_ZOOM);
    mapa.addControl(mouse);

    var layer = new SMap.Layer.Marker();
    mapa.addLayer(layer);
    layer.enable();

    var options = {
        title: ""
    };
    var marker = new SMap.Marker(stred, "myMarker", options);
    marker.decorate(SMap.Marker.Feature.Draggable);
    layer.addMarker(marker);

    var layer2 = new SMap.Layer.Marker(undefined, {
        poiTooltip: true
    });
    mapa.addLayer(layer2).enable();

    var dataProvider = mapa.createDefaultDataProvider();
    dataProvider.setOwner(mapa);
    dataProvider.addLayer(layer2);
    dataProvider.setMapSet(SMap.MAPSET_BASE);
    dataProvider.enable();

    var signals = mapa.getSignals();
    signals.addListener(window, "marker-drag-stop", stop);
    signals.addListener(window, "marker-drag-start", start);


</script>

<?php
include 'footer.php';
?>
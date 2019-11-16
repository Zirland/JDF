<?php
include 'header.php';

$action = @$_POST['action'];

switch ($action) {
    case 'nova':
        $getNazev = @$_POST['getNazev'];

        $stopArr = [];
        $minX    = $minY    = 100;
        $maxX    = $maxY    = 0;

        $query81 = "SELECT stop_id, stop_lat, stop_lon FROM stop WHERE stop_name = '$getNazev';";
        if ($result81 = mysqli_query($link, $query81)) {
            while ($row81 = mysqli_fetch_row($result81)) {
                $stop_id  = $row81[0];
                $stop_lat = $row81[1];
                $stop_lon = $row81[2];

                $stopArr[] = $stop_id;
                if ($stop_lat < $minX) {
                    $minX = $stop_lat;
                }
                if ($stop_lat > $maxX) {
                    $maxX = $stop_lat;
                }
                if ($stop_lon < $minY) {
                    $minY = $stop_lon;
                }
                if ($stop_lon > $maxY) {
                    $maxY = $stop_lon;
                }
            }
        }

        $stopList = '\'' . implode("','", $stopArr) . '\'';
        echo "$stopList<br/>";

        echo "$minX > $maxX >> $minY > $maxY<br/>";
        $centerX = ($minX + $maxX) / 2;
        $centerY = ($minY + $maxY) / 2;
        echo "$centerX >> $centerY<br/>";

        $kodobec = substr($stopArr[0],0,6);
        $pom18 = mysqli_fetch_row(mysqli_query($link, "SELECT nazev_obce FROM obce WHERE lau2 = '$kodobec';"));
        $obec  = $pom18[0];

        $query21 = "SELECT max FROM stop_count WHERE kodobce = '$kodobec';";
        if ($result21 = mysqli_query($link, $query21)) {
            while ($row21 = mysqli_fetch_row($result21)) {
                $max = $row21[0];
            }
        }

        $hit = mysqli_num_rows($result21);
        if ($hit == 0) {
            $max      = 0;
            $insert31 = mysqli_query($link, "INSERT INTO stop_count (kodobce, max) VALUES ('$kodobec', '0');");
        }

        $newmax     = $max + 1;
        $new_stopid = $kodobec . "G" . $newmax;
        $update28   = mysqli_query($link, "UPDATE stop_count SET max = '$newmax' WHERE kodobce = '$kodobec';");

        $query65 = "SELECT stop_name, obec, castobce, misto, sortname FROM stop WHERE stop_id = '$stopArr[0]';";
        echo "$query65<br/>";
        if ($result65 = mysqli_query($link, $query65)) {
            while ($row65 = mysqli_fetch_row($result65)) {
                $stopname = $row65[0];
                $obec     = $row65[1];
                $castobce = $row65[2];
                $misto    = $row65[3];

                $stopname = $obec;
                if ($castobce != '') {
                    $stopname .= ", " . $castobce;
                }
                if ($misto != '') {
                    $stopname .= ", " . $misto;
                }

                if ($stopcode != '') {
                    $stopname .= " (" . $stopcode . ")";
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
            }
        }

        $query14 = "INSERT INTO stop (stop_id, stop_code, stop_name, stop_desc, stop_lat, stop_lon, zone_id, stop_url, location_type, stop_timezone, wheelchair_boarding, active, pomcode, obec, castobce, misto, sortname) VALUES ('$new_stopid','','$stopname','','$centerX','$centerY','','','1','','0','1', '', '$obec', '$castobce', '$misto', '$sortname');";
        $prikaz14 = mysqli_query($link, $query14);

        foreach ($stopArr as $stopCode) {
            $query106 = "UPDATE stop SET parent_station = '$new_stopid' WHERE stop_id = '$stopCode';";
            $prikaz106 = mysqli_query($link, $query106);
        }
        break;
}

echo "<form method=\"post\" action=\"newparent.php\" name=\"nova\"><input name=\"action\" value=\"nova\" type=\"hidden\">";

$query0 = "SELECT DISTINCT stop_name FROM stop WHERE (parent_station = '' OR parent_station IS NULL) ORDER BY stop_name;";

echo "<select name=\"getNazev\">";
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
?>

<div id="mapa" style="width:1200px; height:800px;"></div>

<script type="text/javascript">
	function SelectElement(id, valueToSelect)
	{
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
		var souradnice_x = souradnice[0].replace(/\(/g,"");
		var souradnice_y = souradnice[1].replace(/\)/g,"");

		document.getElementById("stoplat").value = souradnice_y;
		document.getElementById("stoplon").value = souradnice_x;

		var pozice = SMap.Coords.fromWGS84(souradnice_x, souradnice_y);
		mapa.setCenter(pozice);
	}

<?php
if (isset($centerX) && isset($centerY)) {
    echo "var stred = SMap.Coords.fromWGS84($centerY, $centerX);\n";
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
	mapa.addControl(layerSwitch, {left:"8px", top:"9px"});

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

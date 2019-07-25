<?php
include 'header.php';

$action  = $_POST['action'];
$delete  = $_POST['delete'];
$usek_id = $_GET['du_id'];
if (!$usek_id) {
    $usek_id = $_POST['id_usek'];
}
;
$path = $_POST['path'];

echo "<form method=\"post\" action=\"usek.php\" name=\"odkud\"><input name=\"action\" value=\"search\" type=\"hidden\">";
echo "<input type=\"text\" name=\"id_usek\" value=\"$usek_id\"><input type=\"checkbox\" name=\"delete\" value=\"1\"><input type=\"submit\"></form>";

switch ($action) {
    case "uloz":
        $body = explode("),(", $path);
        $pass = "";
        foreach ($body as $point) {
            $upr_point  = str_replace(")", "", $point);
            $upr_point2 = str_replace("(", "", $upr_point);

            if ($upr_point2 != "") {
                $pass .= $upr_point2 . ";";
            }
        }

        $pass    = substr($pass, 0, -1);
        $query51 = "UPDATE du SET path = '$pass' WHERE du_id = '$usek_id';";
        $zapis51 = mysqli_query($link, $query51);

        $query31 = "SELECT stop1, stop2 FROM du WHERE du_id = '$usek_id';";
        if ($result31 = mysqli_query($link, $query31)) {
            while ($row31 = mysqli_fetch_row($result31)) {
                $from = $row31[0];
                $to   = $row31[1];
            }
        }

        $query54 = "UPDATE shapetvary SET complete = '0' WHERE tvartrasy LIKE '%$from|$to|%';";
        $zapis54 = mysqli_query($link, $query54);
        $action  = "search";

    case "search":
        $query102 = "SELECT path, stop1, stop2 FROM du WHERE du_id = '$usek_id';";
        echo $query102;
        if ($result102 = mysqli_query($link, $query102)) {
            $row102 = mysqli_fetch_row($result102);
            $path   = $row102[0];
            $from   = $row102[1];
            $to     = $row102[2];
        }

        $query2 = "SELECT stop_name, pomcode FROM stop WHERE stop_id = '$from';";
        if ($result2 = mysqli_query($link, $query2)) {
            while ($row2 = mysqli_fetch_row($result2)) {
                $nazevv = $row2[0];
                $codev  = $row2[1];
                echo "From: $nazevv $codev | ";
            }
            mysqli_free_result($result2);
        }

        $query2 = "SELECT stop_name, pomcode FROM stop WHERE stop_id = '$to';";
        if ($result2 = mysqli_query($link, $query2)) {
            while ($row2 = mysqli_fetch_row($result2)) {
                $nazevv = $row2[0];
                $codev  = $row2[1];
                echo "To: $nazevv $codev<br/>";
            }
            mysqli_free_result($result2);
        }

        $query146 = "SELECT trip_id FROM trip WHERE shape_id LIKE '%$from|$to|%';";
        if ($result146 = mysqli_query($link, $query146)) {
            $count = mysqli_num_rows($result146);
            while ($row146 = mysqli_fetch_row($result146)) {
                $trip_id = $row146[0];

                echo "$trip_id > ";
            }
            echo "$count<br/>";
        }

        if ($delete == "1") {
            $smazat = mysqli_query($link, "DELETE FROM du WHERE du_id = '$usek_id';");
            echo "<br/>Smazáno";
        }

        echo "<div id=\"text\"></div>";
        echo "<form method=\"post\" action=\"usek.php\" name=\"trasa\"><input name=\"action\" value=\"uloz\" type=\"hidden\">";
        echo "<input type=\"hidden\" name=\"id_usek\" value=\"$usek_id\"><input type=\"hidden\" name=\"path\" id=\"path\" value=\"\"><input type=\"submit\"></form>";
        break;
}
?>

<div id="m" style="height:600px"></div>

<script type="text/javascript">
	function addMarker(nazev, id, x, y) {
		var znacka = JAK.mel("div");
		var obrazek = JAK.mel("img", {src:SMap.CONFIG.img+"/marker/drop-red.png"});
		znacka.appendChild(obrazek);

		var popisek = JAK.mel("div", {}, {position:"absolute", left:"0px", top:"2px", textAlign:"center", width:"22px", color:"white", fontWeight:"bold"});
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
		var souradnice_x = souradnice[0].replace(/\(/g,"");
		var souradnice_y = souradnice[1].replace(/\)/g,"");

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
	m.addDefaultLayer(SMap.DEF_BASE).enable();

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

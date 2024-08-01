<?php
include 'header.php';

$action = @$_POST['action'];
$delete = @$_POST['delete'];
$usek_id = @$_GET['du_id'];
if (!$usek_id) {
    $usek_id = @$_POST['id_usek'];
}

$from = @$_POST['from'];
$to = @$_POST['to'];
$path = @$_POST['path'];

$koleje = [];
$query15 = "SELECT DISTINCT stoptime.stop_id from stoptime WHERE trip_id IN (SELECT trip_id FROM trip WHERE route_id IN (SELECT route_id FROM `route` WHERE route_type = 0));";
if ($result15 = mysqli_query($link, $query15)) {
    while ($row15 = mysqli_fetch_row($result15)) {
        $koleje[] = $row15[0];
    }
}

echo "<form method=\"post\" action=\"usek.php\" name=\"usek\"><input name=\"action\" value=\"usek\" type=\"hidden\">";
echo "<input type=\"text\" name=\"id_usek\" value=\"$usek_id\"><input type=\"checkbox\" name=\"delete\" value=\"1\"><input type=\"submit\"></form>";

echo "<form method=\"post\" action=\"usek.php\" name=\"odkud\"><input name=\"action\" value=\"odkud\" type=\"hidden\">";
echo "Odkud: <select name=\"from\">";
$query0 = "SELECT stop_id, stop_name, pomcode, stop_code FROM `stop` WHERE stop_id IN (SELECT stop1 FROM du WHERE final = 1) ORDER BY stop_name;";
if ($result0 = mysqli_query($link, $query0)) {
    while ($row0 = mysqli_fetch_row($result0)) {
        $kodf = $row0[0];
        $nazevf = $row0[1];
        $codef = $row0[2];
        $platf = $row0[3];
        echo "<option value=\"$kodf\"";
        if ($kodf == $from) {
            echo " SELECTED";
        }
        echo ">$nazevf $platf $codef $kodf";
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
        $body = explode("],[", $path);
        $pass = "";
        foreach ($body as $point) {
            $upr_point = str_replace("]", "", $point);
            $upr_point2 = str_replace("[", "", $upr_point);

            if ($upr_point2 != "") {
                $pass .= "$upr_point2;";
            }
        }

        $pass = substr($pass, 0, -1);
        $query51 = "UPDATE du SET `path` = '$pass' WHERE du_id = '$usek_id';";
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
        $query102 = "SELECT `path`, stop1, stop2 FROM du WHERE du_id = '$usek_id';";
        echo $query102;
        if ($result102 = mysqli_query($link, $query102)) {
            $row102 = mysqli_fetch_row($result102);
            $path = $row102[0];
            $from = $row102[1];
            $to = $row102[2];
        }

        $query2 = "SELECT stop_name, pomcode, stop_code FROM `stop` WHERE stop_id = '$from';";
        if ($result2 = mysqli_query($link, $query2)) {
            while ($row2 = mysqli_fetch_row($result2)) {
                $nazevv = $row2[0];
                $codev = $row2[1];
                $stopcodev = $row2[2];
                echo "From: $nazevv $codev $stopcodev | ";
            }
            mysqli_free_result($result2);
        }

        $query2 = "SELECT stop_name, pomcode, stop_code FROM `stop` WHERE stop_id = '$to';";
        if ($result2 = mysqli_query($link, $query2)) {
            while ($row2 = mysqli_fetch_row($result2)) {
                $nazevv = $row2[0];
                $codev = $row2[1];
                $stopcodev = $row2[2];
                echo "To: $nazevv $codev $stopcodev<br/>";
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

    case "odkud":
        echo "<form method=\"post\" action=\"usek.php\" name=\"kam\"><input name=\"action\" value=\"kam\" type=\"hidden\"><input name=\"from\" value=\"$from\" type=\"hidden\">";
        echo "Kam: <select name=\"to\">";
        $query1 = "SELECT stop_id, stop_name, pomcode, stop_code FROM `stop` WHERE stop_id IN (SELECT stop2 FROM du WHERE stop1 = '$from' AND final = 1) ORDER BY stop_name;";
        echo $query1;
        if ($result1 = mysqli_query($link, $query1)) {
            while ($row1 = mysqli_fetch_row($result1)) {
                $kodt = $row1[0];
                $nazevt = $row1[1];
                $codet = $row1[2];
                $platt = $row1[3];
                echo "<option value=\"$kodt\"";
                if ($kodt == $to) {
                    echo " SELECTED";
                }
                echo ">$nazevt $platt $codet $kodt";
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
        echo "<form method=\"post\" action=\"usek.php\" name=\"kam\"><input name=\"action\" value=\"kam\" type=\"hidden\"><input name=\"from\" value=\"$from\" type=\"hidden\">";
        echo "Kam: <select name=\"to\">";
        $query1 = "SELECT stop_id, stop_name, pomcode, stop_code FROM `stop` WHERE stop_id IN (SELECT stop2 FROM du WHERE stop1 = '$from' AND final = 1) ORDER BY stop_name;";
        echo $query1;
        if ($result1 = mysqli_query($link, $query1)) {
            while ($row1 = mysqli_fetch_row($result1)) {
                $kodt = $row1[0];
                $nazevt = $row1[1];
                $codet = $row1[2];
                $platt = $row1[3];
                echo "<option value=\"$kodt\"";
                if ($kodt == $to) {
                    echo " SELECTED";
                }
                echo ">$nazevt $platt $codet $kodt";
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
        $query2 = "SELECT stop_name, pomcode, stop_code FROM `stop` WHERE stop_id = '$from';";
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

        $query146 = "SELECT trip_id FROM trip WHERE shape_id LIKE '%$from|$to|%';";
        if ($result146 = mysqli_query($link, $query146)) {
            $count = mysqli_num_rows($result146);
            while ($row146 = mysqli_fetch_row($result146)) {
                $trip_id = $row146[0];

                echo "$trip_id > ";
            }
            echo "$count<br/>";
        }
        break;
}
?>

<input type="number" id="positionInput" value="1">
<div id="map"></div>

<script type="text/javascript">
    let inverseArray = [];
    let linie = [];
    let layerGroup = L.layerGroup();

    function vystup(open) {
        var vystup = "<details";
        if (open == "1") {
            vystup += " open";
        }
        vystup += "><summary>Points</summary>";
        for (var i = 0; i < inverseArray.length; i++) {
            vystup += i + ": " + inverseArray[i] + "<input type=\"button\" onClick=\"removePoint(" + i + ")\"><br/>";
        }
        vystup += "</details>";

        let linie = [];
        inverseArray.forEach(item => {
            linie.push('[' + item[1] + ',' + item[0] + ']');
        });
        document.getElementById("path").value = linie.toString();
        document.getElementById("text").innerHTML = vystup;

    }

    function removePoint(id) {
        inverseArray.splice(id, 1);
        vystup("1");
    }

    function drawMap(zoom) {
        layerGroup.clearLayers();
        for (let i = 0; i < inverseArray.length; i++) {
            let latlon = inverseArray[i];
            let marker = L.marker(latlon, {
                draggable: true,
                bodid: i.toString()
            })
                .bindTooltip(i.toString(),
                    {
                        permanent: true,
                        direction: 'right'
                    }
                )
            layerGroup.addLayer(marker);
            marker.on("dragend", dragedMaker);
            marker.on("click", removeMarker);
        }

        map.removeLayer(polyline);
        polyline = L.polyline(inverseArray, {
            color: 'red',
            weight: 5
        }).addTo(map);
        if (zoom == 1) {
            map.fitBounds(polyline.getBounds());
        }
        vystup("0");
    }

    function increaseValue(inputValue) {
        let intValue = parseInt(inputValue, 10);
        if (!isNaN(intValue)) {
            intValue++;
        }
        return intValue;
    }

    function addNewMarker(e) {
        let pole = document.getElementById("positionInput");
        let poradi = pole.value;
        let souradnice = e.latlng.toString().split(', ');
        let souradnice_x = souradnice[0].replace(/LatLng\(/g, '');
        let souradnice_y = souradnice[1].replace(/\)/g, '');
        newpos = [souradnice_x, souradnice_y];
        inverseArray.splice(poradi, 0, newpos);
        pole.value = increaseValue(poradi);
        drawMap("0");
    }

    function removeMarker() {
        let bodid = this.options.bodid;
        inverseArray.splice(bodid, 1);
        drawMap("0");
    }

    function dragedMaker() {
        let bodid = this.options.bodid;
        let newlat = this.getLatLng().lat;
        let newlon = this.getLatLng().lng;
        inverseArray[bodid] = [newlat, newlon];
        drawMap("0");
    }

    const init_pos = [50.08, 14.41];
    const map = L.map('map').setView(init_pos, 16);
    const tileLayers = {
        'Základní': L.tileLayer(
            `https://api.mapy.cz/v1/maptiles/basic/256/{z}/{x}/{y}?apikey=${API_KEY}`,
            {
                minZoom: 0,
                maxZoom: 19,
                attribution:
                    '<a href="https://api.mapy.cz/copyright" target="_blank">&copy; Seznam.cz a.s. a další</a>',
            }
        ),
        'Letecká': L.tileLayer(
            `https://api.mapy.cz/v1/maptiles/aerial/256/{z}/{x}/{y}?apikey=${API_KEY}`,
            {
                minZoom: 0,
                maxZoom: 20,
                attribution:
                    '<a href="https://api.mapy.cz/copyright" target="_blank">&copy; Seznam.cz a.s. a další</a>',
            }
        ),
        'OpenStreetMap': L.tileLayer(
            'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,
                attribution:
                    '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }
        ),
    };

    tileLayers['OpenStreetMap'].addTo(map);
    L.control.layers(tileLayers).addTo(map);

    const LogoControl = L.Control.extend({
        options: {
            position: 'bottomleft',
        },

        onAdd: function (map) {
            const container = L.DomUtil.create('div');
            const link = L.DomUtil.create('a', '', container);

            link.setAttribute('href', 'http://mapy.cz/');
            link.setAttribute('target', '_blank');
            link.innerHTML =
                '<img src="https://api.mapy.cz/img/api/logo.svg" />';
            L.DomEvent.disableClickPropagation(link);

            return container;
        },
    });

    new LogoControl().addTo(map);
    let polyline = L.polyline([]).addTo(map);
    layerGroup.addTo(map);

    <?php
    $body = explode(";", $path);
    for ($i = 0; $i < count($body); $i++) {
        $point = explode(",", $body[$i]);
        $bod = "[{$point[1]}, {$point[0]}]";
        echo "inverseArray.push($bod);";
    }
    ?>

    drawMap("1");
    map.on("click", addNewMarker);
</script>


<?php
include 'footer.php';
?>
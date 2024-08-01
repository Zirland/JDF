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
        $body = explode("],[", $path);
        $pass = "";
        foreach ($body as $point) {
            $upr_point = str_replace("]", "", $point);
            $upr_point2 = str_replace("[", "", $upr_point);

            $pass .= "$upr_point2;";
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
        echo "$du_id | <a href=\"usek.php?du_id=$du_id\" target=\"blank\">Editace úseku</a> | <a href=\"reroute.php?du_id=$du_id\" target=\"blank\">Reroute</a> | ";
        echo "<button onclick=\"switchKontra()\">Změnit směr</button>";
        echo "<br/>";
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

        <div id="map"></div>

        <script type="text/javascript">
            let first = <?php echo "[$fromlat, $fromlon]"; ?>;
            let last = <?php echo "[$tolat, $tolon]"; ?>;
            let skrz = [];
            let kontra = 0;
            let inverseArray = [];
            let polyline = L.polyline([]);
            let layerGroup = L.layerGroup();

            function switchKontra() {
                kontra = kontra == 1 ? 0 : 1;
                skrz = [];
                findRoute(first, last, skrz, kontra);
            }

            function drawMap() {
                layerGroup.clearLayers();
                for (let i = 0; i < inverseArray.length; i++) {
                    let latlon = inverseArray[i];
                    let marker = L.marker(latlon)
                        .bindTooltip(i.toString(),
                            {
                                permanent: true,
                                direction: 'right'
                            }
                        )
                    layerGroup.addLayer(marker);
                }

                map.removeLayer(polyline);
                polyline = L.polyline(inverseArray, {
                    color: 'red',
                    weight: 5
                }).addTo(map);
                map.fitBounds(polyline.getBounds());
            }

            function findRoute(first, last, pass, kontra) {
                let url = `https://api.mapy.cz/v1/routing/route?lang=cs&apikey=${API_KEY}&`;
                if (kontra == 1) {
                    url += 'start=' + last[1] + '%2C' + last[0] + '&';
                    url += 'end=' + first[1] + '%2C' + first[0] + '&';
                } else {
                    url += 'start=' + first[1] + '%2C' + first[0] + '&';
                    url += 'end=' + last[1] + '%2C' + last[0] + '&';
                }
                url += 'routeType=car_fast&format=geojson&avoidToll=false';
                if (pass) {
                    for (let i = 0; i < pass.length; i++) {
                        url += '&waypoints=';
                        let bod = pass[i].toString().split(',');
                        url += bod[1].replace(/\]/g, '') + '%2C' + bod[0].replace(/\[/g, '');
                    }
                }
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        let coords = data.geometry.geometry.coordinates;
                        inverseArray = coords.map(item => [item[1], item[0]]);
                        let linie = [];
                        inverseArray.forEach(item => {
                            linie.push('[' + item[1] + ',' + item[0] + ']');
                        });
                        if (kontra == 1) {
                            linie.reverse();
                            inverseArray.reverse();
                        }
                        document.getElementById("path").value = linie.toString();
                        drawMap();
                    });
            }

            function passPoint(e) {
                let clickPoint = e.latlng.toString().split(', ');
                let souradnice_x = clickPoint[0].replace(/LatLng\(/g, '');
                let souradnice_y = clickPoint[1].replace(/\)/g, '');
                let pass = '[' + souradnice_x + ',' + souradnice_y + ']';
                skrz.push(pass);
                findRoute(first, last, skrz, kontra);
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
            layerGroup.addTo(map);

            map.on('click', passPoint);

            findRoute(first, last, skrz, kontra);
        </script>

        <?php
        echo "<form method=\"post\" action=\"misstrasa2.php\" name=\"uloz\">
        <input name=\"action\" value=\"uloz\" type=\"hidden\">
        <input name=\"from\" value=\"$from\" type=\"hidden\">
        <input name=\"to\" value=\"$to\" type=\"hidden\">
        <input id=\"path\" name=\"path\" value=\"\" type=\"hidden\">
        <input type=\"submit\" value=\"Zapsat\">
        </form>";
        break;
}

include 'footer.php';
?>
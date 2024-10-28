<?php
include 'header.php';

$stop_id = @$_GET['id'];
$action = @$_POST['action'];

switch ($action) {
    case 'edit':
        $stop_id = $_POST['stopid'];
        $oldkodobce = $_POST['oldobec'];

        $stopcode = trim($_POST['stopcode']);
        $stop_lat = $_POST['stoplat'];
        $stop_lon = $_POST['stoplon'];
        $pomcode = trim($_POST['pomcode']);
        $kodobec = $_POST['kodobec'];
        $castobce = trim($_POST['castobce']);
        $misto = trim($_POST['misto']);

        if ($oldkodobce != $kodobec) {
            $query21 = "SELECT max FROM stop_count WHERE kodobce = '$kodobec';";
            if ($result21 = mysqli_query($link, $query21)) {
                while ($row21 = mysqli_fetch_row($result21)) {
                    $max = $row21[0];
                }
            }

            $hit = mysqli_num_rows($result21);

            if ($hit == 0) {
                $max = 0;
                $query32 = "INSERT INTO stop_count (kodobce, max) VALUES ('$kodobec', '0');";
                $insert32 = mysqli_query($link, $query32);
            }

            $newmax = $max + 1;
            $newstopid = "{$kodobec}Z$newmax";

            $query39 = "UPDATE stop_count SET max = '$newmax' WHERE kodobce = '$kodobec';";
            $update39 = mysqli_query($link, $query39);

            $query42 = "UPDATE stoptime SET stop_id = '$newstopid' WHERE stop_id = '$stop_id';";
            $update42 = mysqli_query($link, $query42);

            $query45 = "UPDATE linevazba SET stop_vazba = '$newstopid' WHERE stop_vazba = '$stop_id';";
            $update45 = mysqli_query($link, $query45);
        } else {
            $newstopid = $stop_id;
        }

        $pom51 = mysqli_fetch_row(mysqli_query($link, "SELECT nazev_obce FROM obce WHERE lau2 = '$kodobec';"));
        $obec = $pom51[0];

        $stopname = $obec;
        if ($castobce != '') {
            $stopname .= ", $castobce";
        }
        if ($misto != '') {
            $stopname .= ", $misto";
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

        $query74 = "UPDATE stop SET stop_id = '$newstopid', obec = '$obec', castobce = '$castobce', misto = '$misto', stop_name = '$stopname', pomcode = '$pomcode', stop_code = '$stopcode', stop_lat = '$stop_lat', stop_lon = '$stop_lon', sortname = '$sortname' WHERE stop_id = '$stop_id';";
        $prikaz74 = mysqli_query($link, $query74);

        $deaktivace = "UPDATE shapetvary SET complete='0' WHERE (tvartrasy LIKE '%$stop_id|%');";
        $prikaz78 = mysqli_query($link, $deaktivace);
        $reroute = "UPDATE du SET final='0' WHERE (stop1='$stop_id') OR (stop2='$stop_id');";
        $prikaz79 = mysqli_query($link, $reroute);
        $stop_id = $newstopid;
        break;
}

echo "<table>";
echo "<tr><td colspan=\"4\">Edit stop</td></tr>";

echo "<form method=\"post\" action=\"stopedit.php\" name=\"edit\">
<input name=\"action\" value=\"edit\" type=\"hidden\">
<input name=\"stopid\" value=\"$stop_id\" type=\"hidden\">";

$query92 = "SELECT castobce, misto, pomcode, stop_code, stop_lat, stop_lon, obec, stop_name, stop_id FROM `stop` WHERE stop_id = '$stop_id';";
if ($result92 = mysqli_query($link, $query92)) {
    while ($row92 = mysqli_fetch_row($result92)) {
        $kod_obec = substr($stop_id, 0, 6);
        $stop_cast = $row92[0];
        $stop_misto = $row92[1];
        $stop_pomcode = $row92[2];
        $stop_stopcode = $row92[3];
        $stop_lat = $row92[4];
        $stop_lon = $row92[5];
        $stop_obec = $row92[6];
        $stop_name = $row92[7];

        echo "<tr><td>Obec</td><td>Část obce</td><td>Místo</td><td>Pomcode</td><td>Stop code</td><td>Latitude ~50.123456</td><td>Longitude ~16.987654</td></tr>";
        echo "<tr><td><select name=\"kodobec\">";
        $query107 = "SELECT lau1, lau2, nazev_obce FROM obce ORDER BY nazev_obce;";
        if ($result107 = mysqli_query($link, $query107)) {
            while ($row107 = mysqli_fetch_row($result107)) {
                $kodokres = $row107[0];
                $kodobce = $row107[1];
                $nazevobce = $row107[2];

                echo "<option value=\"$kodobce\"";
                if ($kodobce == $kod_obec) {
                    echo " SELECTED";
                }
                echo ">$nazevobce $kodokres</option>";
            }
        }
        echo "</select><input type=\"hidden\" name=\"oldobec\" value=\"$kod_obec\">$stop_obec</td><td><input name=\"castobce\" value=\"$stop_cast\" type=\"text\"></td><td><input name=\"misto\" value=\"$stop_misto\" type=\"text\"></td><td>$stop_name<input name=\"pomcode\" value=\"$stop_pomcode\" type=\"text\"></td><td><input name=\"stopcode\" value=\"$stop_stopcode\" type=\"text\"></td><td><input name=\"stoplat\" type=\"text\" id=\"stoplat\" value=\"$stop_lat\"></td><td><input name=\"stoplon\" id=\"stoplon\" type=\"text\" value=\"$stop_lon\"></td></tr>";

        echo "<tr><td colspan=\"7\"><input type=\"submit\" value=\"Insert\"></td></tr>";
        echo "</table>";
    }
}
?>

<div id="map"></div>

<script type="text/javascript">
    function moveMarker(e) {
        let coords = e.target.getLatLng();
        let souradnice = coords.toString().split(', ');
        let souradnice_x = souradnice[0].replace(/LatLng\(/g, '');
        let souradnice_y = souradnice[1].replace(/\)/g, '');

        document.getElementById('stoplat').value = souradnice_x;
        document.getElementById('stoplon').value = souradnice_y;

        map.panTo([souradnice_x, souradnice_y]);
    }

    <?php
    if (isset($stop_lon) && isset($stop_lat)) {
        echo "const init_pos = [$stop_lat, $stop_lon];";
    } else {
        echo 'const init_pos = [50.08, 14.41];';
    }
    ?>
    const map = L.map('map').setView(init_pos, 18);
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

    let marker = L.marker(init_pos, {
        draggable: true,
    }).addTo(map);

    marker.on('dragend', moveMarker);

</script>

<?php
include 'footer.php';
?>
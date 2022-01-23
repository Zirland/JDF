<?php
include 'header.php';
?>

<div id="mapa" style="width:1280px; height:960px;"></div>

<script type="text/javascript">
	function addMarker(nazev, id, x, y) {
		var options = {
			title: nazev
		};

		var pozice = SMap.Coords.fromWGS84(Number(x), Number(y));
		var marker = new SMap.Marker(pozice, id, options);
		layer.addMarker(marker);
		markers.push(pozice);
	}

	var stred = SMap.Coords.fromWGS84(14.41, 50.08);
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
	var markers = [];
	var layer2 = new SMap.Layer.Marker(undefined, {
		poiTooltip: true
	});
	mapa.addLayer(layer2).enable();

	var dataProvider = mapa.createDefaultDataProvider();
	dataProvider.setOwner(mapa);
	dataProvider.addLayer(layer2);
	dataProvider.setMapSet(SMap.MAPSET_BASE);
	dataProvider.enable();

<?php
$query30 = "SELECT stop_id, stop_name, stop_lon, stop_lat FROM stop WHERE stop_id NOT IN (SELECT DISTINCT stop_id FROM stoptime) ORDER BY stop_id;";
if ($result30 = mysqli_query($link, $query30)) {
    while ($row30 = mysqli_fetch_row($result30)) {
        $stop_id   = $row30[0];
        $stop_name = $row30[1];
        $longitude = $row30[2];
        $latitude  = $row30[3];

        echo "addMarker('$stop_name', '$stop_id', $longitude, $latitude);\n";
    }
}

?>

	var cz = mapa.computeCenterZoom(markers);
	mapa.setCenterZoom(cz[0], cz[1]);
</script>


<?php
include 'footer.php';
?>
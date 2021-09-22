<?php
include 'header.php';

$trip   = @$_GET['id'];
$action = @$_POST['action'];

switch ($action) {
	case "hlava":
		$trip     = $_POST['trip_id'];
		$linka    = $_POST['route_id'];
		$smer     = $_POST['smer'];
		$blok     = $_POST['block_id'];
		$invalida = $_POST['invalida'];
		$cyklo    = $_POST['cyklo'];

		$ready0 = "UPDATE trip SET route_id='$linka', direction_id='$smer', block_id='$blok', wheelchair_accessible='$invalida', bikes_allowed='$cyklo' WHERE (trip_id = '$trip');";

		$aktualz0 = mysqli_query($link, $ready0);
		break;

	case "zastavky":
		$trip  = $_POST['trip_id'];
		$pocet = $_POST['pocet'];

		for ($y = 0; $y < $pocet; $y++) {
			$$ind           = $y;
			$arrindex       = "arrive" . ${$ind};
			$arrival_time   = $_POST[$arrindex];
			$depindex       = "leave" . ${$ind};
			$departure_time = $_POST[$depindex];
			$rzmindex       = "rezim" . ${$ind};
			$rzm            = $_POST[$rzmindex];
			$pickup_type    = substr($rzm, 0, 1);
			$drop_off_type  = substr($rzm, 1, 1);
			$seqindex       = "poradi" . ${$ind};
			$stop_sequence  = $_POST[$seqindex];
			$stpidindex     = "stop_id" . ${$ind};
			$stop_id        = $_POST[$stpidindex];
			$stp2idindex    = "stop2_id" . ${$ind};
			$stop2_id       = $_POST[$stp2idindex];
			$rertindex      = "reroute" . ${$ind};
			$reroute        = $_POST[$rertindex];
			$zstidindex     = "zastav_id" . ${$ind};
			$zastav_id      = $_POST[$zstidindex];

			$delindex = "delete" . ${$ind};
			$delete   = $_POST[$delindex];

			if ($reroute == 1) {
				$query54  = "UPDATE stoptime SET stop_id = '$stop2_id' WHERE ((trip_id = '$trip') AND (stop_sequence = '$stop_sequence'));";
				$prikaz54 = mysqli_query($link, $query54);

				$query72  = "UPDATE triptimesDB SET stop_vazba = '$stop2_id' WHERE ((trip_id = '$trip') AND (zastav_id = '$zastav_id'));";
				$prikaz72 = mysqli_query($link, $query72);
			}

			switch ($delete) {
				case 1:
					$query58  = "DELETE FROM stoptime WHERE ((trip_id = '$trip') AND (stop_sequence = '$stop_sequence'));";
					$prikaz58 = mysqli_query($link, $query58);
					break;

				default:
					$ready1   = "UPDATE stoptime SET arrival_time='$arrival_time', departure_time='$departure_time', pickup_type='$pickup_type', drop_off_type='$drop_off_type' WHERE ((trip_id ='$trip') AND (stop_sequence = '$stop_sequence'));";
					$aktualz1 = mysqli_query($link, $ready1);
					break;
			}
		}

		$pom163   = mysqli_fetch_row(mysqli_query($link, "SELECT max(stop_sequence) FROM stoptime WHERE (trip_id = '$trip');"));
		$max_trip = $pom163[0];

		$pomfinstop       = mysqli_fetch_row(mysqli_query($link, "SELECT stop_id FROM stoptime WHERE (trip_id='$trip' AND stop_sequence='$max_trip');"));
		$finstop          = $pomfinstop[0];
		$pomfinstopparent = mysqli_fetch_row(mysqli_query($link, "SELECT parent_station FROM stop WHERE stop_id='$finstop';"));
		$finstopparent    = $pomfinstopparent[0];
		if ($finstopparent == '') {
			$finstopid = $finstop;
		} else {
			$finstopid = $finstopparent;
		}

		$query180  = "SELECT stop_name FROM stop WHERE stop_id='$finstopid';";
		$result180 = mysqli_query($link, $query180);
		$pomhead   = mysqli_fetch_row($result180);
		$headsign  = $pomhead[0];

		$query72 = "SELECT stop_id FROM stoptime WHERE trip_id='$trip' ORDER BY stop_sequence;";
		if ($result72 = mysqli_query($link, $query72)) {
			$shape = "";
			while ($row72 = mysqli_fetch_row($result72)) {
				$stop_id = $row72[0];
				$shape .= $stop_id . "|";
			}
		}

		$query67  = "UPDATE trip SET trip_headsign = '$headsign', shape_id = '$shape' WHERE trip_id='$trip';";
		$prikaz67 = mysqli_query($link, $query67);
		break;
}

$hlavicka      = mysqli_fetch_row(mysqli_query($link, "SELECT route_id, trip_id, trip_headsign, trip_short_name, direction_id, block_id, shape_id, wheelchair_accessible, bikes_allowed FROM trip WHERE (trip_id='$trip');"));
$linka         = $hlavicka[0];
$trip_id       = $hlavicka[1];
$trip_headsign = $hlavicka[2];
$smer          = $hlavicka[4];
$blok          = $hlavicka[5];
$shape         = $hlavicka[6];
$invalida      = $hlavicka[7];
$cyklo         = $hlavicka[8];

echo "<table><tr><td>";
echo "<table>";
echo "<tr>";
echo "<td><a href = \"routeedit.php?id=$linka\">Zpět na linku</a><td>";
echo "<td><form method=\"get\" action=\"tripedit.php\" name=\"id\"><input type=\"text\" name=\"id\" value=\"\"><input type=\"submit\" value=\"Vyhledat spoj\"></form><td>";
echo "</tr>";
echo "</table>";
echo "<table>";
echo "<tr>";

echo "<form method=\"post\" action=\"tripedit.php\" name=\"hlava\"><input name=\"action\" value=\"hlava\" type=\"hidden\"><input name=\"trip_id\" value=\"$trip_id\" type=\"hidden\">";
echo "<td>$trip_id</td><td>Linka: <select name=\"route_id\">";

$query45 = "SELECT route_id, route_short_name, route_long_name FROM route ORDER BY route_short_name;";
if ($result45 = mysqli_query($link, $query45)) {
	while ($row45 = mysqli_fetch_row($result45)) {
		$roid     = $row45[0];
		$roshname = $row45[1];
		$rolgname = $row45[2];

		echo "<option value=\"$roid\"";
		if ($roid == $linka) {
			echo " SELECTED";
		}
		echo ">$roshname - $rolgname</option>";
	}
}

echo "</select></td><td>Směr: $trip_headsign<br />";
echo "<select name=\"smer\"><option value=\"0\"";
if ($smer == '0') {
	echo " SELECTED";
}
echo ">Odchozí</option><option value=\"1\"";
if ($smer == '1') {
	echo " SELECTED";
}
echo ">Příchozí</option></select></td>";
echo "<td>Blok <input type=\"text\" name=\"block_id\" value=\"$blok\"><br/>";
echo "Invalida: <select name=\"invalida\"><option value=\"0\"";
if ($invalida == '0') {
	echo " SELECTED";
}
echo "></option><option value=\"1\"";
if ($invalida == '1') {
	echo " SELECTED";
}
echo ">Spoj vhodný pro přepravu</option><option value=\"2\"";
if ($invalida == '2') {
	echo " SELECTED";
}
echo ">Spoj neumožňuje přepravu</option></select><br />";
echo "Cyklo: <select name=\"cyklo\"><option value=\"0\"";
if ($cyklo == '0') {
	echo " SELECTED";
}
echo "></option><option value=\"1\"";
if ($cyklo == '1') {
	echo " SELECTED";
}
echo ">Spoj vhodný pro přepravu</option><option value=\"2\"";
if ($cyklo == '2') {
	echo " SELECTED";
}
echo ">Spoj neumožňuje přepravu</option></select>";
echo "</td>";
echo "<td><input type=\"submit\" value=\"Uložit hlavičku\"></td></tr></form>";
echo "</table>";

echo "<table>";
echo "<tr><td>";
echo "<table>";
echo "<tr><th></th><th>Změna stanice</th><th>Stanice</th><th>Příjezd</th><th>Odjezd</th><th>Režim</th><th></th></tr>";

echo "<form method=\"post\" action=\"tripedit.php\" name=\"zastavky\"><input name=\"action\" value=\"zastavky\" type=\"hidden\"><input name=\"trip_id\" value=\"$trip_id\" type=\"hidden\">";
$z = 0;

$query108 = "SELECT stoptime.stop_id,stoptime.arrival_time,stoptime.departure_time,stoptime.pickup_type,stoptime.drop_off_type,stoptime.stop_sequence, stop.stop_name, stop.pomcode, stoptime.zastav_id FROM stoptime LEFT JOIN stop ON stoptime.stop_id = stop.stop_id WHERE (stoptime.trip_id = '$trip_id') ORDER BY stoptime.stop_sequence;";

if ($result108 = mysqli_query($link, $query108)) {
	while ($row108 = mysqli_fetch_row($result108)) {
		$stop_id        = $row108[0];
		$arrival_time   = $row108[1];
		$departure_time = $row108[2];
		$pickup_type    = $row108[3];
		$drop_off_type  = $row108[4];
		$stop_sequence  = $row108[5];
		$nazev_stanice  = $row108[6];
		$kod_stanice    = $row108[7];
		$zastav_id      = $row108[8];

		echo "<tr><td><input name=\"stop_id$z\" value=\"$stop_id\" type=\"hidden\"><input name=\"zastav_id$z\" value=\"$zastav_id\" type=\"hidden\"><input name=\"poradi$z\" value=\"$stop_sequence\" type=\"hidden\"><input type=\"checkbox\" name=\"reroute$z\" value=\"1\"></td><td><select name=\"stop2_id$z\">";
		$query194 = "SELECT stop_id, sortname, pomcode FROM stop WHERE active=1 ORDER BY sortname;";

		if ($result194 = mysqli_query($link, $query194)) {
			while ($row194 = mysqli_fetch_row($result194)) {
				$stopid   = $row194[0];
				$sortname = $row194[1];
				$stopcode = $row194[2];

				echo "<option value=\"$stopid\"";
				if ($stopid == $stop_id) {
					echo " SELECTED";
				}
				echo ">$sortname $stopcode</option>";
			}
		}

		echo "</select></td><td>";
		echo "$nazev_stanice <a href=\"stopedit.php?id=$stop_id\">E</a> $kod_stanice</td>";
		echo "<td><input type=\"text\" name=\"arrive$z\" value=\"$arrival_time\"></td>";
		echo "<td><input type=\"text\" name=\"leave$z\" value=\"$departure_time\"></td>";
		echo "<td><select name=\"rezim$z\"><option value=\"00\"></option>";
		echo "<option value=\"01\"";
		if ($drop_off_type == 1) {
			echo " SELECTED";
		}
		echo ">Pouze nástup</option>";
		echo "<option value=\"10\"";
		if ($pickup_type == 1) {
			echo " SELECTED";
		}
		echo ">Pouze výstup</option>";
		echo "<option value=\"33\"";
		if ($drop_off_type == 3) {
			echo " SELECTED";
		}
		echo ">Zastavuje na znamení</option>";
		echo "<select></td>";
		echo "<td><input type=\"checkbox\" name=\"delete$z\" value=\"1\"></td></tr>";
		$z = $z + 1;
	}
}

echo "<input type=\"hidden\" name=\"pocet\" value=\"$z-1\">";
echo "<input type=\"submit\" value=\"Uložit změny v zastávkách\"></form>";
echo "</table></td></tr>";
echo "</table>";

echo "</td></tr></table>";

echo "JÍZDY<br/>";
unset($datumy);
$query419 = "SELECT datum FROM jizdy WHERE trip_id = '$trip_id';";
if ($result419 = mysqli_query($link, $query419)) {
	while ($row419 = mysqli_fetch_row($result419)) {
		$datumy[] = $row419[0];
	}
}

$matice_start = date("Y-m-d", time());

echo "<table border=\"1\"><tr><td>";
for ($u = 0; $u < 365; $u++) {
	$datum         = strtotime($matice_start);
	$datum         = strtotime("+$u days", $datum);
	$datum_format  = date("d.m.", $datum);
	$datum_compare = date("Y-m-d", $datum);
	$denvtydnu     = date('w', $datum);
	if (in_array($datum_compare, $datumy)) {
		echo "<span style=\"background-color:green;\">";
	}
	echo "$datum_format<br />";
	if (in_array($datum_compare, $datumy)) {
		echo "</span>";
	}
	if ($denvtydnu == "0") {
		echo "</td><td>";
	}
}
echo "</td></tr></table>";

include 'footer.php';

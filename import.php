<?php
include 'header.php';
$action = @$_POST['action'];

$svatek = array(
	"28092019",
	"28102019",
	"17112019",
	"24122019",
	"25122019",
	"26122019",
	"01012020",
	"10042020",
	"13042020",
	"01052020",
	"08052020",
	"05072020",
	"06072020",
	"28092020",
	"28102020",
	"17112020",
	"24122020",
	"25122020",
	"26122020",
	"01012021",
	"02042021",
	"05042021",
	"01052021",
	"08052021",
	"05072021",
	"06072021",
	"28092021",
	"28102021",
	"17112021",
	"24122021",
	"25122021",
	"26122021",
	"01012022",
	"15042022",
	"18042022",
	"01052022",
	"08052022",
	"05072022",
	"06072022",
	"28092022",
	"28102022",
	"17112022",
	"24122022",
	"25122022",
	"26122022",
);

echo "<form method=\"post\" action=\"import.php\" name=\"linka\">";
echo "<input type=\"hidden\" name=\"action\" value=\"import\">";
echo "Import linky <select name=\"routes\">";

$query67 = "SELECT * FROM mroutes ORDER BY route_id";
if ($result67 = mysqli_query($link, $query67)) {
	while ($row67 = mysqli_fetch_row($result67)) {
		$route_id        = $row67[0];
		$route_long_name = $row67[1];

		echo "<option value=\"$route_id\">$route_id - $route_long_name</option>";
	}
}

echo "</select>";
echo "<input type=\"submit\" value=\"Generovat\">";
echo "</form>";

switch ($action) {
	case 'import':
		$route_no         = $_POST['routes'];
		$label            = "F";
		$route_color      = "017DC2";
		$route_text_color = "000000";
		$route_short_name = $route_no;
		$linkano          = 1;

		$query91 = "SELECT * FROM mroutes WHERE route_id = '$route_no';";
		if ($result91 = mysqli_query($link, $query91)) {
			while ($row91 = mysqli_fetch_row($result91)) {
				$route_long_name = $row91[1];
				$agency_id       = $row91[2];
				$route_type      = $row91[3];
				$platnost_od     = $row91[4];
				$platnost_do     = $row91[5];
			}
		}

		$route_long_name = str_replace(" - ", " – ", $route_long_name);
		$route_id        = $route_no . $linkano;

		$queryro = "DELETE FROM route WHERE route_id = '$label$route_id';";
		if (!mysqli_query($link, $queryro)) {
			echo ("Error description: " . mysqli_error($link)) . "<br/>";
		}

		$querytr = "DELETE FROM trip WHERE route_id = '$label$route_id';";
		if (!mysqli_query($link, $querytr)) {
			echo ("Error description: " . mysqli_error($link)) . "<br/>";
		}

		$query111 = "INSERT INTO route (route_id, agency_id, route_short_name, route_long_name, route_type, route_color, route_text_color, active) VALUES ('$label$route_id', '$agency_id', '$route_short_name', '$route_long_name', '$route_type', '$route_color', '$route_text_color', '0');";
		if (!mysqli_query($link, $query111)) {
			echo ("Error description: " . mysqli_error($link)) . "<br/>";
		}

		$query_jizdy = "DELETE FROM jizdy WHERE spoj LIKE '$route_id%' AND (datum BETWEEN '$platnost_od' AND '$platnost_do');";
		if (!mysqli_query($link, $query_jizdy)) {
			echo ("Error description: " . mysqli_error($link)) . "<br/>";
		}

		$query117 = "SELECT * FROM mtrips WHERE route_id = '$route_no';";
		if ($result117 = mysqli_query($link, $query117)) {
			while ($row117 = mysqli_fetch_row($result117)) {
				$trip_no    = $row117[1];
				$trip_var   = $row117[2];
				$depart     = $row117[3];
				$weekmatrix = $row117[4];

				$tripspoj = $route_id . $trip_no;

				$dnes_den    = date("j", time());
				$dnes_mesic  = date("n", time());
				$dnes_rok    = date("Y", time());
				$dnes_datum  = mktime(0, 0, 0, $dnes_mesic, $dnes_den, $dnes_rok);
				$dnes_format = date("Y-m-d", $dnes_datum);

				$query133 = "INSERT INTO log(trip_id, datum) VALUES ('$tripspoj','$dnes_format');";
				if (!mysqli_query($link, $query133)) {
					echo ("Error description: " . mysqli_error($link)) . "<br/>";
				}
				$logid = mysqli_insert_id($link);

				$vznik = $logid;
				if ($logid > 999999) {
					$vznik = substr($vznik, -6);
				}
				if ($logid < 100000) {
					$vznik = "0" . $vznik;
				}
				if ($logid < 10000) {
					$vznik = "0" . $vznik;
				}
				if ($logid < 1000) {
					$vznik = "0" . $vznik;
				}
				if ($logid < 100) {
					$vznik = "0" . $vznik;
				}
				if ($logid < 10) {
					$vznik = "0" . $vznik;
				}

				$trip_id = $tripspoj . $vznik;

				$smer = ($trip_no % 2) + 1;
				if ($smer == 2) {
					$smer = 0;
				}

				$matrix = "";

				$maticestart = date_create('1 week ago');
				$start       = date_format($maticestart, "N");
				$shift       = -1 * $start;

				for ($i = 0; $i < 420; $i++) {
					$matrix .= "0";
				}

				if (substr($weekmatrix, 0, 1) == 1) {
					// pondělí
					$dy = 1;
					for ($wk = 0; $wk < 60; $wk++) {
						$index          = $shift + $dy + ($wk * 7);
						$matrix[$index] = 1;
					}
				}

				if (substr($weekmatrix, 1, 1) == 1) {
					// úterý
					$dy = 2;
					for ($wk = 0; $wk < 60; $wk++) {
						$index          = $shift + $dy + ($wk * 7);
						$matrix[$index] = 1;
					}
				}

				if (substr($weekmatrix, 2, 1) == 1) {
					// středa
					$dy = 3;
					for ($wk = 0; $wk < 60; $wk++) {
						$index          = $shift + $dy + ($wk * 7);
						$matrix[$index] = 1;
					}
				}

				if (substr($weekmatrix, 3, 1) == 1) {
					// čtvrtek
					$dy = 4;
					for ($wk = 0; $wk < 60; $wk++) {
						$index          = $shift + $dy + ($wk * 7);
						$matrix[$index] = 1;
					}
				}

				if (substr($weekmatrix, 4, 1) == 1) {
					// pátek
					$dy = 5;
					for ($wk = 0; $wk < 60; $wk++) {
						$index          = $shift + $dy + ($wk * 7);
						$matrix[$index] = 1;
					}
				}

				if (substr($weekmatrix, 5, 1) == 1) {
					// sobota
					$dy = 6;
					for ($wk = 0; $wk < 60; $wk++) {
						$index          = $shift + $dy + ($wk * 7);
						$matrix[$index] = 1;
					}
				}

				foreach ($svatek as $datumsvatek1) {
					$svatek_date = date_create_from_format('dmY', $datumsvatek1);
					$svatekdiff  = date_diff($maticestart, $svatek_date);
					$dnusvatek1  = $svatekdiff->days;

					for ($h = 0; $h < 420; $h++) {
						if ($h == $dnusvatek1) {
							$matrix[$h] = 0;
						}
					}
				}

				if (substr($weekmatrix, 6, 1) == 1) {
					// neděle a svátky
					$dy = 0;
					for ($wk = 0; $wk < 60; $wk++) {
						$index          = $shift + $dy + ($wk * 7);
						$matrix[$index] = 1;
					}

					foreach ($svatek as $datumsvatek1) {
						$svatek_date = date_create_from_format('dmY', $datumsvatek1);
						$svatekdiff  = date_diff($maticestart, $svatek_date);
						$dnusvatek1  = $svatekdiff->days;

						for ($h = 0; $h < 420; $h++) {
							if ($h == $dnusvatek1) {
								$matrix[$h] = 1;
							}
						}
					}
				}

				$query242 = "SELECT * FROM man_ck WHERE route_id = '$route_no' AND negative IN (SELECT kod FROM manspoje WHERE route_id = '$route_no' AND spoj = '$trip_no');";
				if ($result242 = mysqli_query($link, $query242)) {
					while ($row242 = mysqli_fetch_row($result242)) {
						$negative = $row242[1];
						$typkodu  = $row242[2];
						$datumod  = $row242[3];
						$datumdo  = $row242[4];
						if ($datumdo == "") {
							$datumdo = $datumod;
						}

						switch ($typkodu) {
							case "1":
								$timeod  = date_create_from_format('dmY', $datumod);
								$zacdiff = date_diff($maticestart, $timeod);
								$zacdnu  = $zacdiff->days;

								$timedo  = date_create_from_format('dmY', $datumdo);
								$kondiff = date_diff($maticestart, $timedo);
								$kondnu  = $kondiff->days;

								for ($g = 0; $g < 420; $g++) {
									if ($g < $zacdnu || $g > $kondnu) {
										$matrix[$g] = 0;
									}
								}
								break;

							case "2":
								$timeod  = date_create_from_format('dmY', $datumod);
								$zacdiff = date_diff($maticestart, $timeod);
								$zacdnu  = $zacdiff->days;

								for ($g = 0; $g < 420; $g++) {
									if ($g == $zacdnu) {
										$matrix[$g] = 1;
									}
								}
								break;

							case "3":
								$timeod  = date_create_from_format('dmY', $datumod);
								$zacdiff = date_diff($maticestart, $timeod);
								$zacdnu  = $zacdiff->days;

								if ($poradikodu == "1") {
									$matrix = "";
									for ($i = 0; $i < 420; $i++) {
										$matrix .= "0";
									}
								}

								for ($g = 0; $g < 420; $g++) {
									if ($g == $zacdnu) {
										$matrix[$g] = 1;
									}
								}
								break;

							case "4":
								$timeod  = date_create_from_format('dmY', $datumod);
								$zacdiff = date_diff($maticestart, $timeod);
								$zacdnu  = $zacdiff->days;

								$timedo  = date_create_from_format('dmY', $datumdo);
								$kondiff = date_diff($maticestart, $timedo);
								$kondnu  = $kondiff->days;

								for ($g = 0; $g < 420; $g++) {
									if ($g >= $zacdnu && $g <= $kondnu) {
										$matrix[$g] = 0;
									}
								}
								break;

							case "5":
								$current .= "* Spoj $caskod_trip_id jede jen v lichých týdnech\n";
								break;

							case "6":
								$current .= "* Spoj $caskod_trip_id jede jen v sudých týdnech\n";
								break;

							case "7":
								$current .= "* Spoj $caskod_trip_id jede jen v lichých týdnech od $datumod do $datumdo\n";
								break;

							case "8":
								$current .= "* Spoj $caskod_trip_id jede jen v sudých týdnech od $datumod do $datumdo\n";
								break;
						}
					}
				}

				$plod      = date_create_from_format('Y-m-d', $platnost_od);
				$zacpldiff = date_diff($maticestart, $plod);
				$zacinv    = $zacpldiff->invert;
				if ($zacinv == '1') {
					$zacplat = 0;
				} else {
					$zacplat = $zacpldiff->days;
				}

				$pldo      = date_create_from_format('Y-m-d', $platnost_do);
				$konpldiff = date_diff($maticestart, $pldo);
				$koninv    = $konpldiff->invert;
				if ($koninv == '1') {
					$konplat = 0;
				} else {
					$konplat = $konpldiff->days;
				}

				for ($g = 0; $g < 420; $g++) {
					if ($g < $zacplat || $g > $konplat) {
						$matrix[$g] = 0;
					}
				}

				for ($h = 0; $h < 420; $h++) {
					$fixdate   = date_create('1 week ago');
					$prirustek = "$h days";
					date_add($fixdate, date_interval_create_from_date_string($prirustek));
					$totodatum = date_format($fixdate, 'Y-m-d');
					$route     = substr($trip_id, 0, 6);

					if ($matrix[$h] == "1") {
						$query363 = "INSERT INTO jizdy (spoj, trip_id, datum) VALUES ('$tripspoj','$trip_id','$totodatum');";
						if (!mysqli_query($link, $query363)) {
							echo ("Error description: " . mysqli_error($link)) . "<br/>";
						}
					}
				}

				$query368 = "INSERT INTO trip (route_id, trip_id, trip_headsign, direction_id, wheelchair_accessible, bikes_allowed, active) VALUES ('$label$route_id', '$trip_id', '', '$smer', '0','0', '0');";
				if (!mysqli_query($link, $query368)) {
					echo ("Error description: " . mysqli_error($link)) . "<br/>";
				}

				$dep_hour = substr($depart, 0, 2);
				$dep_min  = substr($depart, 2, 2);
				$query374 = "SELECT * FROM mvarianty WHERE route_id = '$route_no' AND varianta = '$trip_var';";
				if ($result374 = mysqli_query($link, $query374)) {
					while ($row374 = mysqli_fetch_row($result374)) {
						$stop_id  = $row374[3];
						$stop_seq = $row374[4];
						$odstup   = $row374[5];
						$rezim    = $row374[6];

						$dep_min = $dep_min + $odstup;
						if ($dep_min > 59) {
							$dep_hour = $dep_hour + 1;
							$dep_min  = $dep_min - 60;
						}
						if ($dep_min < 10) {
							$dep_min = "0" . $dep_min;
						}

						$time = $dep_hour . ":" . $dep_min . ":00";

						$pickup  = substr($rezim, 0, 1);
						$dropoff = substr($rezim, 1, 1);

						$query391 = "INSERT INTO stoptime (trip_id, arrival_time, departure_time, stop_id, stop_sequence, pickup_type, drop_off_type) VALUES ('$trip_id', '$time', '$time', '$stop_id', '$stop_seq', '$pickup', '$dropoff');";
						if (!mysqli_query($link, $query391)) {
							echo ("Error description: " . mysqli_error($link)) . "<br/>";
						}
					}
				}

				$query404 = "SELECT stop_name FROM stop WHERE stop_id IN (SELECT stop_id FROM stoptime WHERE trip_id = '$trip_id' AND stop_sequence = (SELECT MAX(stop_sequence) FROM stoptime WHERE trip_id='$trip_id'));";
				$row404   = mysqli_fetch_row(mysqli_query($link, $query404));
				$headsign = $row404[0];
				$query408 = "UPDATE trip SET trip_headsign='$headsign' WHERE trip_id='$trip_id';";
				if (!mysqli_query($link, $query408)) {
					echo ("Error description: " . mysqli_error($link)) . "<br/>";
				}

				$shape    = "";
				$query411 = "SELECT stop_id FROM stoptime WHERE trip_id='$trip_id' ORDER BY stop_sequence;";
				if ($result411 = mysqli_query($link, $query411)) {
					while ($row411 = mysqli_fetch_row($result411)) {
						$stop_id = $row411[0];
						$shape .= $stop_id . "|";
					}
				}
				$query418 = "UPDATE trip SET shape_id='$shape', active = '1' WHERE trip_id='$trip_id';";
				if (!mysqli_query($link, $query418)) {
					echo ("Error description: " . mysqli_error($link)) . "<br/>";
				}
			}
		}

		break;
}

mysqli_close($link);

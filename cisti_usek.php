<?php
include 'header.php';

$query68 = "SELECT du_id, stop1, stop2 FROM du;";
$query68 = "";
if ($result68 = mysqli_query($link, $query68)) {
	while ($row68 = mysqli_fetch_row($result68)) {
		$du_id = $row68[0];
		$stop1 = $row68[1];
		$stop2 = $row68[2];

		$query75 = "SELECT trip_id FROM trip WHERE shape_id LIKE '%$stop1|$stop2|%';";
		$hits = mysqli_num_rows(mysqli_query($link, $query75));
		if ($hits == 0) {
			$query93 = "SELECT stop_name FROM stop WHERE (stop_id = '$stop1');";
			if ($result93 = mysqli_query($link, $query93)) {
				while ($row93 = mysqli_fetch_row($result93)) {
					$name1 = $row93[0];
				}
			}

			$query102 = "SELECT stop_name FROM stop WHERE (stop_id = '$stop2');";
			if ($result102 = mysqli_query($link, $query102)) {
				while ($row102 = mysqli_fetch_row($result102)) {
					$name2 = $row102[0];
				}
			}

			echo "$du_id | $name1 | $name2<br/>";

			$purge_du = mysqli_query($link, "DELETE FROM du WHERE du_id = $du_id;");
		}
	}
}

$query86 = "SELECT du_id, stop1, stop2 FROM du WHERE (final = 0);";
//$query86 = "";
if ($result86 = mysqli_query($link, $query86)) {
	while ($row86 = mysqli_fetch_row($result86)) {
		$du_id = $row86[0];
		$stop1 = $row86[1];
		$stop2 = $row86[2];

		$query93 = "SELECT stop_lat, stop_lon FROM stop WHERE (stop_id = '$stop1');";
		echo "$query93<br/>";
		if ($result93 = mysqli_query($link, $query93)) {
			while ($row93 = mysqli_fetch_row($result93)) {
				$begin_lat = $row93[0];
				$begin_lon = $row93[1];
			}
		}

		$query102 = "SELECT stop_lat, stop_lon FROM stop WHERE (stop_id = '$stop2');";
		echo "$query102<br/>";
		if ($result102 = mysqli_query($link, $query102)) {
			while ($row102 = mysqli_fetch_row($result102)) {
				$end_lat = $row102[0];
				$end_lon = $row102[1];
			}
		}

		$cesta = "$begin_lon,$begin_lat;$end_lon,$end_lat";
		echo "$cesta<br/>";

		$rovnej_du = mysqli_query($link, "UPDATE du SET path = '$cesta' WHERE du_id = $du_id;");
	}
}

$query86 = "SELECT du_id, stop1, stop2 FROM du WHERE (final = 2);";
//$query86 = "";
if ($result86 = mysqli_query($link, $query86)) {
	while ($row86 = mysqli_fetch_row($result86)) {
		$du_id = $row86[0];
		$stop1 = $row86[1];
		$stop2 = $row86[2];

		$query93 = "SELECT stop_lat, stop_lon FROM stop WHERE (stop_id = '$stop1');";
		echo "$query93<br/>";
		if ($result93 = mysqli_query($link, $query93)) {
			while ($row93 = mysqli_fetch_row($result93)) {
				$begin_lat = $row93[0];
				$begin_lon = $row93[1];
			}
		}

		$query102 = "SELECT stop_lat, stop_lon FROM stop WHERE (stop_id = '$stop2');";
		echo "$query102<br/>";
		if ($result102 = mysqli_query($link, $query102)) {
			while ($row102 = mysqli_fetch_row($result102)) {
				$end_lat = $row102[0];
				$end_lon = $row102[1];
			}
		}

		$cesta = "$begin_lon,$begin_lat;$end_lon,$end_lat";
		echo "$cesta<br/>";

		$rovnej_du = mysqli_query($link, "UPDATE du SET path = '$cesta' WHERE du_id = $du_id;");
	}
}

echo "== Konec ==";
include 'footer.php';
?>

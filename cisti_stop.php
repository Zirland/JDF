<?php
include 'header.php';

$query4 = "SELECT stop_id, stop_name FROM `stop` WHERE stop_id NOT IN (SELECT DISTINCT stop_id FROM stoptime) ORDER BY stop_name;";
if ($result4 = mysqli_query($link, $query4)) {
    while ($row4 = mysqli_fetch_row($result4)) {
        $stop_id = $row4[0];
        $stop_name = $row4[1];

        echo "$stop_id | $stop_name<br/>";
    }
}

echo "== Konec ==";
include 'footer.php';

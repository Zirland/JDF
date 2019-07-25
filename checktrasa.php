<?php
include 'header.php';

$query4 = "SELECT * FROM du WHERE (final = 0);";
if ($result4 = mysqli_query($link, $query4)) {
    while ($row4 = mysqli_fetch_row($result4)) {
        $du_id = $row4[0];
        $stop1 = $row4[1];
        $stop2 = $row4[2];

        $query11 = "SELECT trip_id FROM trip WHERE shape_id LIKE '%$stop1|$stop2|%';";
        echo "$query11<br/>";
        if ($result11 = mysqli_query($link, $query11)) {
            $count       = mysqli_num_rows($result11);
            $fromnamepom = mysqli_fetch_row(mysqli_query($link, "SELECT stop_name FROM stop WHERE stop_id = '$stop1';"));
            $fromname    = $fromnamepom[0];
            $tonamepom   = mysqli_fetch_row(mysqli_query($link, "SELECT stop_name FROM stop WHERE stop_id = '$stop2';"));
            $toname      = $tonamepom[0];
            echo "$du_id | $fromname | $toname = $count<br/>";
            if ($count == "0") {
                $query21 = "DELETE FROM du WHERE du_id = '$du_id';";
                echo "$query21<br/>";
                $prikaz21 = mysqli_query($link, $query21);
            }
        }
        mysqli_free_result($result11);
    }
    mysqli_free_result($result4);
}

include 'footer.php';

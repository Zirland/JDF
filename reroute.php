<script>
function toggle(source) {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i] != source) {
            checkboxes[i].checked = source.checked;
        }
    }
}
</script>

<?php
include 'header.php';

$action = @$_POST['action'];
$usek_id = @$_GET['du_id'];
if (!$usek_id) {
    $usek_id = @$_POST['id_usek'];
}

$from = @$_POST['from'];
$to = @$_POST['to'];

switch ($action) {
    case "reroute":
        $stp1 = @$_POST['stp1'];
        $stp2 = @$_POST['stp2'];
        $stop1_id = @$_POST['stop1_id'];
        $stop2_id = @$_POST['stop2_id'];
        $pocet = @$_POST['trip_count'];

        for ($y = 1; $y <= $pocet; $y++) {
            $ind = $y;
            $tripindex = "trip" . $ind;
            $trp = @$_POST[$tripindex];
            $rerindex = "rer" . $ind;
            $reroute = @$_POST[$rerindex];

            if ($reroute == 1) {
                $query30 = "SELECT stop_sequence FROM stoptime WHERE stop_id = '$stp1' AND trip_id = '$trp';";
                if ($result30 = mysqli_query($link, $query30)) {
                    while ($row30 = mysqli_fetch_row($result30)) {
                        $sequence1 = $row30[0];
                        $sequence2 = $sequence1 + 1;

                        $query36 = "SELECT stop_sequence FROM stoptime WHERE stop_id = '$stp2' AND trip_id = '$trp';";
                        if ($result36 = mysqli_query($link, $query36)) {
                            $hit = mysqli_num_rows($result36);
                        }
                    }

                    if ($hit > 0) {
                        $query43 = "UPDATE stoptime SET stop_id = '$stop1_id' WHERE ((trip_id = '$trp') AND (stop_sequence = '$sequence1'));";
                        $prikaz43 = mysqli_query($link, $query43);

                        $query46 = "UPDATE stoptime SET stop_id = '$stop2_id' WHERE ((trip_id = '$trp') AND (stop_sequence = '$sequence2'));";
                        $prikaz46 = mysqli_query($link, $query46);
                    }

                    $pom50 = mysqli_fetch_row(mysqli_query($link, "SELECT max(stop_sequence) FROM stoptime WHERE (trip_id = '$trp');"));
                    $max_trip = $pom50[0];

                    $pomfinstop = mysqli_fetch_row(mysqli_query($link, "SELECT stop_id FROM stoptime WHERE (trip_id='$trp' AND stop_sequence='$max_trip');"));
                    $finstop = $pomfinstop[0];
                    $pomfinstopparent = mysqli_fetch_row(mysqli_query($link, "SELECT parent_station FROM stop WHERE stop_id='$finstop';"));
                    $finstopparent = $pomfinstopparent[0];
                    if ($finstopparent == '') {
                        $finstopid = $finstop;
                    } else {
                        $finstopid = $finstopparent;
                    }

                    $query63 = "SELECT stop_id FROM stoptime WHERE trip_id='$trp' ORDER BY stop_sequence;";
                    if ($result63 = mysqli_query($link, $query63)) {
                        $shape = "";
                        while ($row63 = mysqli_fetch_row($result63)) {
                            $stop_id = $row63[0];
                            $shape .= $stop_id . "|";
                        }
                    }

                    $query72 = "UPDATE trip SET trip_headsign = '$finstopid', shape_id = '$shape' WHERE trip_id='$trp';";
                    $prikaz72 = mysqli_query($link, $query72);

                }
            }
        }
        $usek_id = $_POST['du_id'];
        $action = "usek";
        break;
}

$query102 = "SELECT stop1, stop2 FROM du WHERE du_id = '$usek_id';";
if ($result102 = mysqli_query($link, $query102)) {
    $row102 = mysqli_fetch_row($result102);
    $from = $row102[0];
    $to = $row102[1];
}

echo "<form action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\" method=\"post\"><input name=\"action\" value=\"reroute\" type=\"hidden\">";

echo "<br/>ID = $usek_id <input type=\"hidden\" name=\"du_id\" value=\"$usek_id\">| ";
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

$z = 1;
echo "<input type=\"hidden\" name=\"stp1\" value=\"$from\">";
echo "<input type=\"hidden\" name=\"stp2\" value=\"$to\">";

echo "<select name=\"stop1_id\">";
$query194 = "SELECT stop_id, sortname, pomcode FROM `stop` WHERE active=1 ORDER BY sortname;";

if ($result194 = mysqli_query($link, $query194)) {
    while ($row194 = mysqli_fetch_row($result194)) {
        $stopid = $row194[0];
        $sortname = $row194[1];
        $stopcode = $row194[2];

        echo "<option value=\"$stopid\"";
        if ($stopid == $from) {
            echo " SELECTED";
        }
        echo ">$sortname $stopcode</option>";
    }
}

echo "</select> | ";
echo "<select name=\"stop2_id\">";
$query194 = "SELECT stop_id, sortname, pomcode FROM `stop` WHERE active=1 ORDER BY sortname;";

if ($result194 = mysqli_query($link, $query194)) {
    while ($row194 = mysqli_fetch_row($result194)) {
        $stopid = $row194[0];
        $sortname = $row194[1];
        $stopcode = $row194[2];

        echo "<option value=\"$stopid\"";
        if ($stopid == $to) {
            echo " SELECTED";
        }
        echo ">$sortname $stopcode</option>";
    }
}

echo "</select><br/>";

echo "<input type=\"checkbox\" onclick=\"toggle(this);\">Check all<br/>";
$query146 = "SELECT trip_id FROM trip WHERE shape_id LIKE '%$from|$to|%';";
if ($result146 = mysqli_query($link, $query146)) {
    $count = mysqli_num_rows($result146);
    while ($row146 = mysqli_fetch_row($result146)) {
        $trip_id = $row146[0];

        echo "<a href=\"tripedit.php?id=$trip_id\" target=\"_blank\">$trip_id</a>";
        echo "<input type=\"hidden\" name=\"trip$z\" value=\"$trip_id\">";
        echo "<input type=\"checkbox\" name=\"rer$z\" value=\"1\">";
        echo "<br/>";
        $z += 1;
    }
    echo "$count <input type=\"hidden\" name=\"trip_count\" value=\"$count\"><br/>";
}
echo "<input type=\"submit\"></form>";

include 'footer.php';
?>
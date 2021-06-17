<?php
include 'header.php';
$action   = @$_POST['action'];
$route_no = $_POST['routes'];

echo "<form method=\"post\" action=\"manuallist.php\" name=\"linka\">";
echo "<input type=\"hidden\" name=\"action\" value=\"list\">";
echo "Výpis linky <select name=\"routes\">";

$query67 = "SELECT route_id, route_long_name FROM mroutes ORDER BY route_id";
if ($result67 = mysqli_query($link, $query67)) {
    while ($row67 = mysqli_fetch_row($result67)) {
        $route_id        = $row67[0];
        $route_long_name = $row67[1];

        echo "<option value=\"$route_id\"";
        if ($route_id == $route_no) {
            echo " SELECTED";
        }
        echo ">$route_id - $route_long_name</option>";
    }
}

echo "</select>";
echo "<input type=\"submit\" value=\"Vypsat\">";
echo "</form>";

echo "$action<br/>";
switch ($action) {
    case 'header':
        $route_no    = $_POST['route_id'];
        $dopravce    = $_POST['dopravce'];
        $longname    = $_POST['longname'];
        $routetype   = $_POST['routetype'];
        $platnost_od = $_POST['platnostod'];
        $platnost_do = $_POST['platnostdo'];

        $ready0 = "UPDATE mroutes SET agency_id = '$dopravce', route_long_name = '$longname', route_type = '$routetype',
        platnost_od = '$platnost_od', platnost_do = '$platnost_do' WHERE (route_id = '$route_no');";
        echo "$ready0<br/>";
        $aktualz0 = mysqli_query($link, $ready0);

    case 'list':
        echo "<table><tr><td>";
        echo "<table>";
        echo "<tr>";
        $query50 = "SELECT route_id, route_long_name, agency_id, route_type, platnost_od, platnost_do FROM mroutes WHERE (route_id='$route_no');";
        if ($result50 = mysqli_query($link, $query50)) {
            while ($row50 = mysqli_fetch_row($result50)) {
                $route_id        = $row50[0];
                $route_long_name = $row50[1];
                $agency_id       = $row50[2];
                $route_type      = $row50[3];
                $platnost_od     = $row50[4];
                $platnost_do     = $row50[5];
            }
        }

        echo "<form method=\"post\" action=\"manuallist.php\" name=\"header\">
            <input name=\"action\" value=\"header\" type=\"hidden\">
            <input name=\"route_id\" value=\"$route_id\" type=\"hidden\">";
        echo "<td>Dopravce: <select name=\"dopravce\">";

        $query24 = "SELECT agency_id, agency_name FROM agency ORDER BY agency_id;";
        if ($result24 = mysqli_query($link, $query24)) {
            while ($row24 = mysqli_fetch_row($result24)) {
                $agid   = $row24[0];
                $agname = $row24[1];

                echo "<option value=\"$agid\"";
                if ($agid == $agency_id) {
                    echo " SELECTED";
                }
                echo ">$agname</option>";
            }
        }

        echo "</select><br>Typ linky: <select name=\"routetype\">";
        echo "<option value=\"0\"";
        if ($route_type == "0") {
            echo " SELECTED";
        }
        echo ">tramvaj</option>";
        echo "<option value=\"1\"";
        if ($route_type == "1") {
            echo " SELECTED";
        }
        echo ">metro</option>";
        echo "<option value=\"2\"";
        if ($route_type == "2") {
            echo " SELECTED";
        }
        echo ">vlak</option>";
        echo "<option value=\"3\"";
        if ($route_type == "3") {
            echo " SELECTED";
        }
        echo ">autobus</option>";
        echo "<option value=\"4\"";
        if ($route_type == "4") {
            echo " SELECTED";
        }
        echo ">přívoz</option>";
        echo "<option value=\"5\"";
        if ($route_type == "5") {
            echo " SELECTED";
        }
        echo ">trolejbus</option>";
        echo "<option value=\"6\"";
        if ($route_type == "6") {
            echo " SELECTED";
        }
        echo ">visutá lanovka</option>";
        echo "<option value=\"7\"";
        if ($route_type == "7") {
            echo " SELECTED";
        }
        echo ">kolejová lanovka</option>";
        echo "</select>";

        echo "</td><td>Linka: <input type=\"text\" name=\"longname\" value=\"$route_long_name\" size=\"60\"></td>";

        echo "<td>Platnost od: <input type=\"text\" name=\"platnostod\" value=\"$platnost_od\"><br/>";
        echo "Platnost do: <input type=\"text\" name=\"platnostdo\" value=\"$platnost_do\"></td>";

        echo "<td><input type=\"submit\"></td></tr></form></table>";

        unset($listVarianty);
        $query128 = "SELECT DISTINCT varianta FROM mvarianty WHERE route_id = '$route_no';";
        if ($result128 = mysqli_query($link, $query128)) {
            while ($row128 = mysqli_fetch_row($result128)) {
                $listVarianty[] = $row128[0];
            }
        }

        foreach ($listVarianty as $itemVarianty) {
            echo "<table><tr style=\"text-align:center;\"><td></td>";
            $query163 = "SELECT trip_no, matrix FROM mtrips WHERE route_id = '$route_no' AND trip_var = '$itemVarianty' ORDER BY CAST(trip_no AS unsigned);";
            if ($result163 = mysqli_query($link, $query163)) {
                while ($row163 = mysqli_fetch_row($result163)) {
                    $trip_id = $row163[0];
                    $matrix  = $row163[1];
                    $cal     = "";

                    if ($matrix[0] == 1) {$cal .= "①";}
                    if ($matrix[1] == 1) {$cal .= "②";}
                    if ($matrix[2] == 1) {$cal .= "③";}
                    if ($matrix[3] == 1) {$cal .= "④";}
                    if ($matrix[4] == 1) {$cal .= "⑤";}
                    if ($cal == "①②③④⑤") {$cal = "⚒︎";}
                    if ($matrix[5] == 1) {$cal .= "⑥";}
                    if ($matrix[6] == 1) {$cal .= "✝︎";}
                    if ($cal == "⚒︎⑥✝︎") {$cal = "";}
                    $query142 = "SELECT kod FROM manspoje WHERE route_id = '$route_no' AND spoj = '$trip_id';";
                    if ($result142 = mysqli_query($link, $query142)) {
                        while ($row142 = mysqli_fetch_row($result142)) {
                            $kod = $row142[0];
                        }
                    }
                    $calwrap = wordwrap($cal, 6, "<br/>", true);
                    echo "<td>$trip_id<br/>$calwrap <span style=\"background-color:black; color:white;\">$kod</span></td>";
                }
            }
            echo "</tr>";

            $query137 = "SELECT id, stop.stop_name, stop_seq, odstup, rezim FROM mvarianty LEFT JOIN stop ON mvarianty.stop_id = stop.stop_id WHERE route_id = '$route_no' AND varianta = '$itemVarianty' ORDER BY stop_seq;";
            if ($result137 = mysqli_query($link, $query137)) {
                while ($row137 = mysqli_fetch_row($result137)) {
                    $id_var   = $row137[0];
                    $stop     = $row137[1];
                    $stop_seq = $row137[2];
                    $odstup   = $row137[3];
                    $rezim    = $row137[4];

                    switch ($rezim) {
                        case '01':
                            $symbol = "◗";
                            break;
                        case '10':
                            $symbol = "◖";
                            break;
                        case '33':
                            $symbol = "×";
                            break;
                        case '00':
                        default:
                            $symbol = "";
                            break;
                    }
                    echo "<tr><td>$stop $symbol</td>";

                    $query163 = "SELECT depart FROM mtrips WHERE route_id = '$route_no' AND trip_var = '$itemVarianty' ORDER BY CAST(trip_no AS unsigned);";
                    if ($result163 = mysqli_query($link, $query163)) {
                        while ($row163 = mysqli_fetch_row($result163)) {
                            $odjezd = $row163[0];

                            $odj_hour = substr($odjezd, 0, 2);
                            $odj_min  = substr($odjezd, 2, 4);

                            $pos_min = $odj_min + $odstup;
                            if ($pos_min > 60) {
                                $pos_min  = $pos_min - 60;
                                $odj_hour = $odj_hour + 1;}
                            if ($pos_min < 10) {$pos_min = "0" . $pos_min;}
                            $cas = $odj_hour . ":" . $pos_min;
                            echo "<td>$cas</td>";
                        }
                    }
                    echo "</tr>";
                }
            }
            echo "</tr></table>";
            echo "<hr/>";
        }

        unset($texty);
        $query218 = "SELECT popis FROM ck_enum ORDER BY kod;";
        if ($result218 = mysqli_query($link, $query218)) {
            while ($row218 = mysqli_fetch_row($result218)) {
                $texty[] = $row218[0];
            }
        }

        $query216 = "SELECT DISTINCT negative FROM man_ck WHERE route_id = '$route_no' ORDER BY negative;";
        if ($result216 = mysqli_query($link, $query216)) {
            while ($row216 = mysqli_fetch_row($result216)) {
                $neg_code = $row216[0];
                $prev_typ = 0;
                $radek    = "<span style=\"background-color:black; color:white;\">$neg_code</span> &nbsp;";

                $query221 = "SELECT typ, kodod, koddo FROM man_ck WHERE route_id = '$route_no' AND negative = '$neg_code' ORDER BY typ, kodod;";
                if ($result221 = mysqli_query($link, $query221)) {
                    while ($row221 = mysqli_fetch_row($result221)) {
                        $typ   = $row221[0];
                        $kodod = $row221[1];
                        $koddo = $row221[2];
                        $index = $typ - 1;

                        if ($typ != $prev_typ) {
                            if ($prev_typ != 0) {
                                $radek .= ". ";
                            }
                            $radek .= $texty[$index] . " ";
                        }
                        if ($typ == $prev_typ) {
                                $radek .= ", ";
                        }
                        if ($kodod != $koddo) {
                            $radek .= "od ";
                        }
                        $radek .= substr($kodod, 0, 2) . "." . substr($kodod, 2, 2) . "." . substr($kodod, 4, 4);
                        if ($kodod != $koddo) {
                            $radek .= " do ";
                            $radek .= substr($koddo, 0, 2) . "." . substr($koddo, 2, 2) . "." . substr($koddo, 4, 4);
                        }
                        $prev_typ = $typ;
                    }
                }
                echo "$radek<br/>";
            }
        }

        break;
}

include 'footer.php';

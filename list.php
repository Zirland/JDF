<?php
include 'header.php';

echo "<table>";
echo "<tr>";
echo "<th>Přepravce</th>
<th>Linka</th>
<th>Trasa</th>
<th>Typ</th>
<th></th>";
echo "</tr>";
$query = "SELECT route_id, agency_id, route_short_name, route_long_name, route_desc, route_type, route_url, route_color, route_text_color, active FROM route WHERE active=1 ORDER BY agency_id, CAST(route_short_name as unsigned)";
if ($result = mysqli_query($link, $query)) {
    while ($row = mysqli_fetch_row($result)) {
        $route_id         = $row[0];
        $agency_id        = $row[1];
        $route_short      = $row[2];
        $route_long       = $row[3];
        $route_desc       = $row[4];
        $route_type       = $row[5];
        $route_url        = $row[6];
        $route_color      = $row[7];
        $route_text_color = $row[8];
        $route_active     = $row[9];

        echo "<tr>";

        $ro_ag_pom = mysqli_fetch_row(mysqli_query($link, "SELECT agency_name FROM agency WHERE (agency_id = $agency_id);"));
        $ro_ag     = $ro_ag_pom['0'];
        echo "<td>$ro_ag</td>";

        echo "<td style=\"background-color: #$route_color; text-align: center;\"><span style=\"color: #$route_text_color;\">$route_short";

        if (strpos($route_id, 'F') !== false) {
            echo "F";
        }

        echo "</td>";

        $query73 = "SELECT kod_linky from exter WHERE linka = '$route_id';";
        $mhd     = mysqli_fetch_row(mysqli_query($link, $query73));
//        echo "<td>$mhd[0]</td>";

        echo "<td";
        if ($route_active == "1") {
            echo " style=\"background-color: green;\"";
        }
        echo ">$route_long</td>";

        switch ($route_type) {
            case 0:
                echo "<td>tramvaj</td>";
                break;
            case 1:
                echo "<td>metro</td>";
                break;
            case 2:
                echo "<td>vlak</td>";
                break;
            case 3:
                echo "<td>autobus</td>";
                break;
            case 4:
                echo "<td>přívoz</td>";
                break;
            case 6:
                echo "<td>kabinová lanovka</td>";
                break;
            case 7:
                echo "<td>kolejová lanovka</td>";
                break;
            case 11:
                echo "<td>trolejbus</td>";
                break;
            default:
                echo "<td></td>";
                break;
        }
        echo "<td><a href=\"routeedit.php?id=$route_id\">Detaily</a>";

        if (strpos($route_id, 'F') !== false) {
            echo " <a href=\"migrace.php?route=$route_id\"> Migrace</a>";
        }

        echo "</td></tr>";
    }
    mysqli_free_result($result);
}
echo "<table>";

include 'footer.php';

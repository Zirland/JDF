<?php
require_once 'dbconnect.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$link) {
    echo "Error: Unable to connect to database." . PHP_EOL;
    echo "Reason: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$cheb = @$_GET["cheb"];
$current = "";

if ($cheb == 1) {
    $akt_route = "SELECT route_id, agency_id, route_short_name, route_long_name, route_type, route_color, route_text_color FROM `route` WHERE (agency_id = '25332473' AND active='1' AND SUBSTRING(route_id,1,3) IN (416));";
} else {
    $akt_route = "SELECT route_id, agency_id, route_short_name, route_long_name, route_type, route_color, route_text_color FROM `route` WHERE active='1';";
}
if ($result69 = mysqli_query($link, $akt_route)) {
    while ($row69 = mysqli_fetch_row($result69)) {
        $route_id         = $row69[0];
        $agency_id        = $row69[1];
        $route_short_name = $row69[2];
        $route_long_name  = $row69[3];
        $route_type       = $row69[4];
        $route_color      = $row69[5];
        $route_text_color = $row69[6];

        if ($route_color == "ffffff") {
            $route_color      = "";
            $route_text_color = "";
        }

        $current .= "$route_id,$agency_id,\"$route_short_name\",\"$route_long_name\",$route_type,$route_color,$route_text_color\n";

        $zapisag = mysqli_query($link, "INSERT INTO ag_use VALUES ('$route_id','$agency_id');");
    }
    mysqli_free_result($result69);
}

$file = 'routes.txt';
file_put_contents($file, $current, FILE_APPEND);

$current = "";

mysqli_close($link);

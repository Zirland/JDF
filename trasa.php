<?php
include 'header.php';

function distance($lat1, $lon1, $lat2, $lon2)
{
    $pi80 = M_PI / 180;
    $lat1 *= $pi80;
    $lon1 *= $pi80;
    $lat2 *= $pi80;
    $lon2 *= $pi80;

    $r    = 6372.797; // mean radius of Earth in km
    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;
    $a    = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
    $c    = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $km   = $r * $c;

    return $km;
}

$action = @$_POST['action'];
$from   = @$_POST['from'];
$to     = @$_POST['to'];
$via    = @$_POST['via'];
$via2   = @$_POST['via2'];
$pass   = @$_POST['pass'];
$path   = @$_POST['path'];

echo "<form method=\"post\" action=\"trasa.php\" name=\"odkud\"><input name=\"action\" value=\"odkud\" type=\"hidden\">";
echo "Odkud: <select name=\"from\">";
$query0 = "SELECT stop_id, stop_name, pomcode FROM stop WHERE stop_id IN (SELECT stop1 FROM du WHERE final = 1) ORDER BY stop_name;";
if ($result0 = mysqli_query($link, $query0)) {
    while ($row0 = mysqli_fetch_row($result0)) {
        $kodf   = $row0[0];
        $nazevf = $row0[1];
        $codef  = $row0[2];
        echo "<option value=\"$kodf\"";
        if ($kodf == $from) {
            echo " SELECTED";
        }
        echo ">$nazevf $codef $kodf</option>";
    }
    mysqli_free_result($result0);
} else {
    echo "Error description: " . mysqli_error($link);
}

echo "</select>";
echo "<input type=\"submit\"></form>";

switch ($action) {
    case "uloz":
        $query51 = "UPDATE du SET via = '$pass', path = '$path', final = '1' WHERE stop1 = '$from' AND stop2 = '$to';";
        $zapis51 = mysqli_query($link, $query51);
        $query54 = "UPDATE shapetvary SET complete = '0' WHERE tvartasy LIKE '%$from|$to|%';";
        $zapis54 = mysqli_query($link, $query54);
        $action  = "kam";

    case "odkud":
        echo "<form method=\"post\" action=\"trasa.php\" name=\"kam\"><input name=\"action\" value=\"kam\" type=\"hidden\"><input name=\"from\" value=\"$from\" type=\"hidden\">";
        echo "Kam: <select name=\"to\">";
        $query1 = "SELECT stop_id, stop_name, pomcode FROM stop WHERE stop_id IN (SELECT stop2 FROM du WHERE stop1 = '$from' AND final = 1) ORDER BY stop_name;";
        echo $query1;
        if ($result1 = mysqli_query($link, $query1)) {
            while ($row1 = mysqli_fetch_row($result1)) {
                $kodt   = $row1[0];
                $nazevt = $row1[1];
                $codet  = $row1[2];
                echo "<option value=\"$kodt\"";
                if ($kodt == $to) {
                    echo " SELECTED";
                }
                echo ">$nazevt $codet $kodt</option>";
            }
            mysqli_free_result($result1);
        } else {
            echo "Error description: " . mysqli_error($link);
        }

        echo "</select>";
        echo "<input type=\"submit\"></form>";
        break;

    case "kam":
        echo "<form method=\"post\" action=\"trasa.php\" name=\"kam\"><input name=\"action\" value=\"kam\" type=\"hidden\"><input name=\"from\" value=\"$from\" type=\"hidden\">";
        echo "Kam: <select name=\"to\">";
        $query1 = "SELECT stop_id, stop_name, pomcode FROM stop WHERE stop_id IN (SELECT stop2 FROM du WHERE stop1 = '$from' AND final = 1) ORDER BY stop_name;";
        echo $query1;
        if ($result1 = mysqli_query($link, $query1)) {
            while ($row1 = mysqli_fetch_row($result1)) {
                $kodt   = $row1[0];
                $nazevt = $row1[1];
                $codet  = $row1[2];
                echo "<option value=\"$kodt\"";
                if ($kodt == $to) {
                    echo " SELECTED";
                }
                echo ">$nazevt $codet $kodt</option>";
            }
            mysqli_free_result($result1);
        } else {
            echo "Error description: " . mysqli_error($link);
        }

        echo "</select>";
        echo "Přes: <select name=\"via\">";
        echo "<option value=\"\">---</option>";
        $query2 = "SELECT stop_id, stop_name, pomcode FROM stop ORDER BY stop_name;";
        if ($result2 = mysqli_query($link, $query2)) {
            while ($row2 = mysqli_fetch_row($result2)) {
                $kodv   = $row2[0];
                $nazevv = $row2[1];
                $codev  = $row2[2];
                echo "<option value=\"$kodv\"";
                if ($kodv == $via) {
                    echo " SELECTED";
                }
                echo ">$nazevv $codev $kodv</option>";
            }
            mysqli_free_result($result2);
        } else {
            echo "Error description: " . mysqli_error($link);
        }

        echo "</select>";

        echo "<input type=\"text\" name=\"via2\">";
        echo "<input type=\"submit\"></form>";

        $query47 = "SELECT stop_lat, stop_lon FROM stop WHERE stop_id = '$from';";
        if ($result47 = mysqli_query($link, $query47)) {
            $row47   = mysqli_fetch_row($result47);
            $fromlat = $row47[0];
            $fromlon = $row47[1];
        }
        $query53 = "SELECT stop_lat, stop_lon FROM stop WHERE stop_id = '$to';";
        if ($result53 = mysqli_query($link, $query53)) {
            $row53 = mysqli_fetch_row($result53);
            $tolat = $row53[0];
            $tolon = $row53[1];
        }
        $query128 = "SELECT stop_lat, stop_lon FROM stop WHERE stop_id = '$via';";
        if ($result128 = mysqli_query($link, $query128)) {
            $row128 = mysqli_fetch_row($result128);
            $vialat = $row128[0];
            $vialon = $row128[1];
        }

        $query96 = "SELECT du_id FROM du WHERE stop1 = '$from' AND stop2 = '$to';";
        $pom96   = mysqli_fetch_row(mysqli_query($link, $query96));
        $du_id   = $pom96[0];
        echo "$du_id<br/>";

        $prujezdy = $fromlon . "," . $fromlat . "|";
        if ($via != "") {
            $pass  = $vialon . "," . $vialat;
            $stred = $pass . "|";
        } else {
            $query96 = "SELECT via FROM du WHERE stop1 = '$from' AND stop2 = '$to';";
            $pom96   = mysqli_fetch_row(mysqli_query($link, $query96));
            $pass    = $pom96[0];

            if ($pass != "") {
                $stred = $pass . "|";
            }
        }

        if ($via2 != "") {
            $pass  = $via2;
            $stred = $pass . "|";
        }

        $prujezdy .= $stred . $tolon . "," . $tolat;

//        $url = "https://api.openrouteservice.org/directions?api_key=$token&coordinates=$prujezdy&profile=driving-car&preference=fastest&format=json&units=m&language=en&geometry=true&geometry_format=geojson&geometry_simplify=false&instructions=false&instructions_format=text&roundabout_exits=&attributes=&maneuvers=&radiuses=&bearings=&continue_straight=&elevation=&extra_info=&optimized=true&options=%7B%7D&id=";

        $contents = file_get_contents($url);
//        $contents = utf8_encode($contents);
        $results = json_decode($contents, true);

        $trasa      = "";
        $souradnice = $results["routes"][0]["geometry"]["coordinates"];
        foreach ($souradnice as $bod) {
            $X = $bod[0];
            $Y = $bod[1];

            $trasa .= "$X,$Y;";
        }
        $trasa = substr($trasa, 0, -1);
        $body  = explode(";", $trasa);

/*        $xmin = 20;
$xmax = 0;
$ymin = 60;
$ymax = 0;
foreach ($body as $point) {
$point = explode (",", $point);
$pt_x = $point[0];
$pt_y = $point[1];

if ($pt_x < $xmin) {
$xmin = $pt_x;
}
if ($pt_y < $ymin) {
$ymin = $pt_y;
}
if ($pt_x > $xmax) {
$xmax = $pt_x;
}
if ($pt_y > $ymax) {
$ymax = $pt_y;
}
}
 */
        $box    = $results["bbox"];
        $xmin   = $box[0];
        $ymin   = $box[1];
        $xmax   = $box[2];
        $ymax   = $box[3];
        $deltax = $xmax - $xmin;
        $deltay = $ymax - $ymin;

        echo "<canvas id=\"a\" width=\"800\" height=\"600\">";
        echo "This text is displayed if your browser does not support HTML5 Canvas.";
        echo "</canvas>";
        echo "<script type='text/javascript'>";
        echo "	var a_canvas = document.getElementById(\"a\");";
        echo "	var context = a_canvas.getContext(\"2d\");";

        $i = 0;
        foreach ($body as $point) {
            $point = explode(",", $point);
            $pt_x  = $point[0];
            $pt_y  = $point[1];

            $coorx = 784 * (($pt_x - $xmin) / $deltax) + 8;
            $coory = 600 - (584 * (($pt_y - $ymin) / $deltay) + 8);
            if ($i == 0) {
                echo "context.beginPath();";
                echo "context.moveTo($coorx,$coory);";
            }
            if ($i > 0) {
                echo "context.lineTo($coorx,$coory);";
            }
            $i = $i + 1;
        }
//        echo "context.closePath();";
        echo "context.strokeStyle = \"#000\";";
        echo "context.stroke();";
        echo "</script>";

        echo "<form method=\"post\" action=\"trasa.php\" name=\"uloz\"><input name=\"action\" value=\"uloz\" type=\"hidden\"><input name=\"from\" value=\"$from\" type=\"hidden\"><input name=\"to\" value=\"$to\" type=\"hidden\"><input name=\"via\" value=\"$via\" type=\"hidden\"><input name=\"pass\" value=\"$pass\" type=\"hidden\"><input name=\"path\" value=\"$trasa\" type=\"hidden\"><input type=\"submit\" value=\"Zapsat\"></form>";
        break;
}

include 'footer.php';

<?php
$link = mysqli_connect('localhost', 'root', 'root', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

$source = $_GET['file'];
$dir = "data/" . $source;

$version = fopen("$dir/VerzeJDF.txt.txt", 'r');
if ($version) {
    while (($buffer0 = fgets($version, 4096)) !== false) {
        $vrz   = explode('"', $buffer0);
        $verze = $vrz[1];
    }
    fclose($version);
}

$dopravci = fopen("$dir/Dopravci.txt.txt", 'r');
if ($dopravci) {
    while (($buffer1 = fgets($dopravci, 4096)) !== false) {
        $dopr     = explode('"', $buffer1);
        $dopravce = $dopr[5];
    }
    fclose($dopravci);
}

$linky = fopen("$dir/Linky.txt.txt", 'r');
if ($linky) {
    while (($buffer2 = fgets($linky, 4096)) !== false) {
        $line             = explode('"', $buffer2);
        $route_no         = $line[1];
        $route_short_name = $route_no;
        $route_long_name  = $line[3];
        $vyluka           = 0;

        if ($verze == '1.8' || $verze == '1.9') {
            $platnostod = $line[17];
            $platnostdo = $line[19];
            $route_type = "3";
        }

        if ($verze == '1.10' || $verze == '1.11') {
            $typ = $line[9];
            switch ($typ) {
                case "A":
                    $route_type = "3";
                    break;
                case "E":
                    $route_type = "0";
                    break;
                case "L":
                    $route_type = "6";
                    break;
                case "M":
                    $route_type = "1";
                    break;
                case "P":
                    $route_type = "4";
                    break;
                case "T":
                    $route_type = "11";
                    break;
            }
        }

        if ($verze == '1.10') {
            $vyluka     = $line[11];
            $platnostod = $line[25];
            $platnostdo = $line[27];
        }

        if ($verze == '1.11') {
            $vyluka     = $line[11];
            $platnostod = $line[27];
            $platnostdo = $line[29];
        }
    }
    fclose($linky);
}

$datumod  = substr($platnostod, -4) . "-" . substr($platnostod, 2, 2) . "-" . substr($platnostod, 0, 2);
$datumdo  = substr($platnostdo, -4) . "-" . substr($platnostdo, 2, 2) . "-" . substr($platnostdo, 0, 2);
$query68  = "INSERT INTO analyza (dir, verze, route_id, route_name, route_type, datumod, datumdo, dopravce, vyluka) VALUES ('$source', '$verze', '$route_short_name', '$route_long_name', '$route_type', '$datumod', '$datumdo', '$dopravce', '$vyluka');";
$prikaz68 = mysqli_query($link, $query68);

mysqli_close($link);

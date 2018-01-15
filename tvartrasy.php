<?php
$oblast = $_GET["oblast"];

$link = mysqli_connect('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

                                        $query162 = "SELECT shape_id, tvartrasy, complete FROM shapetvary WHERE complete = 0;";
                                        if ($result162 = mysqli_query($link, $query162)) {
                                                while ($row162 = mysqli_fetch_row($result162)) {
                                                $shape_id = $row162[0];
						$tvartrasy = $row162[1];
                                                $kompltrasa = $row162[2];
                                                if ($kompltrasa != 1) {
                                                        $smaz182 = "DELETE FROM shape WHERE shape_id = '$shape_id';";
                                                        $smazanitrasy = mysqli_query($link,$smaz182);

                                                        $i = 0;
                                                        $prevstop = "";
                                                        $vzdal = 0;
                                                        $komplet = 1;

                                                        $output = explode('|', $tvartrasy);

                                                        foreach ($output as $prujbod) {
                                                                $pom139 = mysqli_fetch_row(mysqli_query($link, "SELECT stop_name,stop_lat,stop_lon FROM stop WHERE (stop_id='$prujbod');"));
                                                                $name = $pom139[0];
                                                                $lat = $pom139[1];
                                                                $lon = $pom139[2];
                                                                $i = $i + 1;

                                                                $result235 = mysqli_query($link, "SELECT DELKA FROM DU_pom WHERE (STOP1 = '$prevstop') AND (STOP2 = '$prujbod');");
                                                                $pom235 = mysqli_fetch_row($result235);
                                                                $ujeto = $pom235[0];
                                                                $radky = mysqli_num_rows($result235);
                                                                $vzdal = $vzdal + $ujeto;
                                                                $prevstop = $prujbod;

                                                                if ($lat != '' && $lon != '') {
                                                                        if ($i == 1) {$vzdal = 0;}
                                                                        $query144 = "INSERT INTO shape VALUES (
                                                                                '$shape_id',
                                                                                '$lat',
                                                                                '$lon',
                                                                                '$i',
                                                                                '$vzdal'
                                                                        );";
                                                                        $command = mysqli_query($link, $query144);
                                                                }
//                                                              else {$komplet = 0;}
// zápis nové trasy do databáze
                                                        }
                                                }
                                                $query217 = "UPDATE shapetvary SET complete = '$komplet' WHERE shape_id = '$shape_id';";
                                                $command217 = mysqli_query($link, $query217);
                                                }
                                        }

mysqli_close($link);
?>

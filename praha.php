<?php
$link = mysqli_connect('localhost', 'root', 'root', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

$dir = "praha";

$licence = fopen("$dir/route_sub_agencies.txt", 'r');
if ($licence) {
    while (($buffer0 = fgets($licence, 4096)) !== false) {
        $lice        = explode(',', $buffer0);
        $cislo_linky = $lice[1];

        $del_cislo = mysqli_query($link, "DELETE FROM ignorace WHERE route_id = '$cislo_linky';");
        $ins_cislo = mysqli_query($link, "INSERT INTO ignorace (route_id) VALUES ('$cislo_linky');");
    }
    fclose($licence);
}

mysqli_close($link);

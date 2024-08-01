<?php
require_once 'dbconnect.php';
$link = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
if (!$link) {
    echo "Error: Unable to connect to database." . PHP_EOL;
    echo "Reason: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$dir = "praha";
$i = 0;

$licence = fopen("$dir/route_sub_agencies.txt", 'r');
if ($licence) {
    while (($buffer0 = fgets($licence, 4096)) !== false) {
        $lice = explode(',', $buffer0);
        $cislo_linky = $lice[1];

        if ($i > 0) {
            $del_cislo = mysqli_query($link, "DELETE FROM ignorace WHERE route_id = '$cislo_linky';");
            $ins_cislo = mysqli_query($link, "INSERT INTO ignorace (route_id) VALUES ('$cislo_linky');");
        }
        $i = $i + 1;
    }
    fclose($licence);
}

mysqli_close($link);

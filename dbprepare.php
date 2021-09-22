<?php
$link = mysqli_connect('localhost', 'root', 'root', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

$cistianal = mysqli_query($link, "TRUNCATE TABLE analyza;");

mysqli_close($link);

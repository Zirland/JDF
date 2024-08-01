<?php
$file = './test.sh';
require_once 'dbconnect.php';
$link = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
if (!$link) {
    echo "Error: Unable to connect to database." . PHP_EOL;
    echo "Reason: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$head_file = "#!/bin/bash\n\n";
file_put_contents($file, $head_file);

$command = "cd /var/www/JDF/\n\n";
$command .= "curl http://localhost/JDF/feed_headers.php\n\n";
$command .= "curl http://localhost/JDF/feed_routes.php\n\n";

$routelist = [];
$query18 = "SELECT route_id FROM `route` WHERE active = 1 ORDER BY route_id;";
if ($result18 = mysqli_query($link, $query18)) {
    while ($row18 = mysqli_fetch_row($result18)) {
        $routelist[] = substr($row18[0], 0, 5);
    }
}
$routelist = array_unique($routelist);

foreach ($routelist as $oblast) {
    $command .= "curl http://localhost/JDF/feed_trips.php?oblast=$oblast\n";
}

$command .= "curl http://localhost/JDF/feed_close.php\n\n";
$command .= "zip MHD *.txt\n\n";
$command .= "java -jar gtfs-validator-4.1.0-cli.jar -c cz -i MHD.zip -o output\n\n";
$command .= "exit;\n";

file_put_contents($file, $command, FILE_APPEND);

exec($file);
?>
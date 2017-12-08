<?php
$link = mysqli_connect('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

$now = microtime(true);
$timestart = $now;
echo "Start: $now\n";
$prevnow = $now;

$file = 'agency.txt';
$current = "agency_id,agency_name,agency_url,agency_timezone,agency_phone\n";
file_put_contents($file, $current);

$file = 'routes.txt';
$current = "route_id,agency_id,route_short_name,route_long_name,route_type,route_color,route_text_color\n";
file_put_contents($file, $current);

$file = 'trips.txt';
$current = "route_id,service_id,trip_id,trip_headsign,direction_id,shape_id,wheelchair_accessible,bikes_allowed\n";
file_put_contents($file, $current);

$file = 'stop_times.txt';
$current = "trip_id,arrival_time,departure_time,stop_id,stop_sequence,pickup_type,drop_off_type\n";
file_put_contents($file, $current);

$calendar_trunc = mysqli_query($link, "TRUNCATE TABLE cal_use;");
$stop_trunc = mysqli_query($link, "TRUNCATE TABLE stop_use;");
$shapecheck_trunc = mysqli_query($link, "TRUNCATE TABLE shapecheck;");
$parent_trunc = mysqli_query($link, "TRUNCATE TABLE parent_use;");

$agencynums = 0;

$current = "";

$now = microtime(true);
$dlouho = $now-$prevnow;
echo "Headers: $dlouho\n";
$prevnow = $now;

$query46 = "SELECT agency_id,agency_name,agency_url,agency_timezone,agency_phone FROM agency WHERE agency_id IN (SELECT DISTINCT agency_id FROM route WHERE (active='1'));";

if ($result46 = mysqli_query($link, $query46)) {
	while ($row46 = mysqli_fetch_row($result46)) {
		$agency_id = $row46[0];
		$agency_name = $row46[1];
		$agency_url = $row46[2];
		$agency_timezone = $row46[3];
		$agency_phone = $row46[4];
		$agencynums = mysqli_num_rows($result46);
				
		$current .= "$agency_id,\"$agency_name\",$agency_url,$agency_timezone,\"$agency_phone\"\n";
}}

$file = 'agency.txt';
file_put_contents($file, $current, FILE_APPEND);

echo "Exported agencies: $agencynums\n";

$now = microtime(true);
$dlouho = $now-$prevnow;
echo "Agencies: $dlouho\n";
$prevnow = $now;

mysqli_close($link);
?>

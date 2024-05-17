<?php
require_once 'dbconnect.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$link) {
    echo "Error: Unable to connect to database." . PHP_EOL;
    echo "Reason: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$file = 'agency.txt';
$current = "agency_id,agency_name,agency_url,agency_timezone,agency_lang,agency_phone\n";
file_put_contents($file, $current);

$file = 'routes.txt';
$current = "route_id,agency_id,route_short_name,route_long_name,route_type,route_color,route_text_color\n";
file_put_contents($file, $current);

$file = 'trips.txt';
$current = "route_id,service_id,trip_id,trip_headsign,trip_short_name,direction_id,shape_id,wheelchair_accessible,bikes_allowed\n";
file_put_contents($file, $current);

$file = 'stop_times.txt';
$current = "trip_id,arrival_time,departure_time,stop_id,stop_sequence,stop_headsign,pickup_type,drop_off_type,timepoint\n";
file_put_contents($file, $current);

$file = 'calendar.txt';
$current = "service_id,monday,tuesday,wednesday,thursday,friday,saturday,sunday,start_date,end_date\n";
file_put_contents($file, $current);

$file = 'calendar_dates.txt';
$current = "service_id,date,exception_type\n";
file_put_contents($file, $current);

$file = 'stops.txt';
$current = "stop_id,stop_code,stop_name,stop_lat,stop_lon,zone_id,location_type,parent_station,wheelchair_boarding\n";
file_put_contents($file, $current);

$file = 'shapes.txt';
$current = "shape_id,shape_pt_lat,shape_pt_lon,shape_pt_sequence\n";
file_put_contents($file, $current);

$agency_trunc = mysqli_query($link, "TRUNCATE TABLE ag_use;");
$calendar_trunc = mysqli_query($link, "TRUNCATE TABLE cal_use;");
$except_trunc = mysqli_query($link, "TRUNCATE TABLE calendar_except;");
$parent_trunc = mysqli_query($link, "TRUNCATE TABLE parent_use;");
$shapecheck_trunc = mysqli_query($link, "TRUNCATE TABLE shapecheck;");
$stop_trunc = mysqli_query($link, "TRUNCATE TABLE stop_use;");

mysqli_close($link);

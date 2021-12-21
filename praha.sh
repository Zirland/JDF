#!/bin/bash

cd /Applications/MAMP/htdocs/JDF/

rm -rf praha

mkdir praha
chmod 777 praha
cd praha

wget https://data.pid.cz/PID_GTFS.zip

unzip PID_GTFS.zip

curl http://localhost/JDF/praha.php

exit;

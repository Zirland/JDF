#!/bin/bash

cd /home/zirland/git/JDF

rm -rf praha

mkdir praha
chmod 777 praha
cd praha

wget http://data.pid.cz/PID_GTFS.zip

unzip PID_GTFS.zip

curl http://localhost/JDF/praha.php

exit;

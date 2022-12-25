#!/bin/bash

cd ~/www/JDF/

curl http://localhost/JDF/feed_headers.php

curl http://localhost/JDF/feed_routes.php?cheb=1
curl http://localhost/JDF/feed_trips.php?oblast=416

curl http://localhost/JDF/feed_close.php

zip MHD *.txt

java -jar gtfs-validator-4.0.0-cli.jar -c cz -i MHD.zip -o output

exit;

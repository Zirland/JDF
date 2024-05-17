#!/bin/bash

cd /var/www/JDF/

curl http://localhost/JDF/feed_headers.php

curl http://localhost/JDF/feed_routes.php

curl http://localhost/JDF/feed_trips.php?oblast=41500
curl http://localhost/JDF/feed_trips.php?oblast=41600
curl http://localhost/JDF/feed_trips.php?oblast=42110
curl http://localhost/JDF/feed_trips.php?oblast=42111
curl http://localhost/JDF/feed_trips.php?oblast=42114
curl http://localhost/JDF/feed_trips.php?oblast=42120
curl http://localhost/JDF/feed_trips.php?oblast=42500
curl http://localhost/JDF/feed_trips.php?oblast=42501
curl http://localhost/JDF/feed_trips.php?oblast=42502
curl http://localhost/JDF/feed_trips.php?oblast=42504
curl http://localhost/JDF/feed_trips.php?oblast=42505
curl http://localhost/JDF/feed_trips.php?oblast=42600
curl http://localhost/JDF/feed_trips.php?oblast=44500
curl http://localhost/JDF/feed_trips.php?oblast=44501
curl http://localhost/JDF/feed_trips.php?oblast=44502
curl http://localhost/JDF/feed_trips.php?oblast=44503
curl http://localhost/JDF/feed_trips.php?oblast=44504
curl http://localhost/JDF/feed_trips.php?oblast=44505
curl http://localhost/JDF/feed_trips.php?oblast=44507
curl http://localhost/JDF/feed_trips.php?oblast=44520
curl http://localhost/JDF/feed_trips.php?oblast=44590
curl http://localhost/JDF/feed_trips.php?oblast=44600
curl http://localhost/JDF/feed_trips.php?oblast=44601
curl http://localhost/JDF/feed_trips.php?oblast=50520
curl http://localhost/JDF/feed_trips.php?oblast=50521
curl http://localhost/JDF/feed_trips.php?oblast=50522
curl http://localhost/JDF/feed_trips.php?oblast=50523
curl http://localhost/JDF/feed_trips.php?oblast=51520
curl http://localhost/JDF/feed_trips.php?oblast=51521
curl http://localhost/JDF/feed_trips.php?oblast=51522
curl http://localhost/JDF/feed_trips.php?oblast=51523
curl http://localhost/JDF/feed_trips.php?oblast=51619
curl http://localhost/JDF/feed_trips.php?oblast=52735
curl http://localhost/JDF/feed_trips.php?oblast=54500
curl http://localhost/JDF/feed_trips.php?oblast=54501
curl http://localhost/JDF/feed_trips.php?oblast=54502
curl http://localhost/JDF/feed_trips.php?oblast=54503
curl http://localhost/JDF/feed_trips.php?oblast=54505
curl http://localhost/JDF/feed_trips.php?oblast=54506
curl http://localhost/JDF/feed_trips.php?oblast=54509
curl http://localhost/JDF/feed_trips.php?oblast=54550
curl http://localhost/JDF/feed_trips.php?oblast=54560
curl http://localhost/JDF/feed_trips.php?oblast=54591
curl http://localhost/JDF/feed_trips.php?oblast=55636
curl http://localhost/JDF/feed_trips.php?oblast=55754
curl http://localhost/JDF/feed_trips.php?oblast=55755
curl http://localhost/JDF/feed_trips.php?oblast=55756
curl http://localhost/JDF/feed_trips.php?oblast=55757
curl http://localhost/JDF/feed_trips.php?oblast=55800
curl http://localhost/JDF/feed_trips.php?oblast=56500
curl http://localhost/JDF/feed_trips.php?oblast=59500
curl http://localhost/JDF/feed_trips.php?oblast=59501
curl http://localhost/JDF/feed_trips.php?oblast=59502
curl http://localhost/JDF/feed_trips.php?oblast=59504
curl http://localhost/JDF/feed_trips.php?oblast=59505
curl http://localhost/JDF/feed_trips.php?oblast=59506
curl http://localhost/JDF/feed_trips.php?oblast=59507
curl http://localhost/JDF/feed_trips.php?oblast=59590
curl http://localhost/JDF/feed_trips.php?oblast=86670
curl http://localhost/JDF/feed_trips.php?oblast=86671
curl http://localhost/JDF/feed_trips.php?oblast=87640
curl http://localhost/JDF/feed_trips.php?oblast=87641
curl http://localhost/JDF/feed_trips.php?oblast=87642
curl http://localhost/JDF/feed_trips.php?oblast=87643
curl http://localhost/JDF/feed_trips.php?oblast=87751
curl http://localhost/JDF/feed_trips.php?oblast=87752
curl http://localhost/JDF/feed_trips.php?oblast=87850
curl http://localhost/JDF/feed_trips.php?oblast=89500
curl http://localhost/JDF/feed_trips.php?oblast=89501
curl http://localhost/JDF/feed_trips.php?oblast=89502
curl http://localhost/JDF/feed_trips.php?oblast=89503
curl http://localhost/JDF/feed_trips.php?oblast=89504
curl http://localhost/JDF/feed_trips.php?oblast=89505
curl http://localhost/JDF/feed_trips.php?oblast=89506
curl http://localhost/JDF/feed_trips.php?oblast=89550
curl http://localhost/JDF/feed_trips.php?oblast=90520
curl http://localhost/JDF/feed_trips.php?oblast=90521
curl http://localhost/JDF/feed_trips.php?oblast=90522
curl http://localhost/JDF/feed_trips.php?oblast=91500
curl http://localhost/JDF/feed_trips.php?oblast=91501
curl http://localhost/JDF/feed_trips.php?oblast=91502
curl http://localhost/JDF/feed_trips.php?oblast=91503
curl http://localhost/JDF/feed_trips.php?oblast=91504
curl http://localhost/JDF/feed_trips.php?oblast=91505
curl http://localhost/JDF/feed_trips.php?oblast=91506
curl http://localhost/JDF/feed_trips.php?oblast=91507
curl http://localhost/JDF/feed_trips.php?oblast=91508
curl http://localhost/JDF/feed_trips.php?oblast=91509
curl http://localhost/JDF/feed_trips.php?oblast=91510
curl http://localhost/JDF/feed_trips.php?oblast=91511
curl http://localhost/JDF/feed_trips.php?oblast=91514
curl http://localhost/JDF/feed_trips.php?oblast=91520
curl http://localhost/JDF/feed_close.php

zip MHD *.txt

java -jar gtfs-validator-4.1.0-cli.jar -c cz -i MHD.zip -o output

exit;

#!/bin/bash

cd /Applications/MAMP/htdocs/JDF

curl http://localhost/JDF/feed_agency.php

curl http://localhost/JDF/feed_jdf_start.php
curl http://localhost/JDF/feed_jdf_route.php?oblast=103
curl http://localhost/JDF/feed_jdf_route.php?oblast=289
curl http://localhost/JDF/feed_jdf_route.php?oblast=416
curl http://localhost/JDF/feed_jdf_route.php?oblast=421
curl http://localhost/JDF/feed_jdf_route.php?oblast=425
curl http://localhost/JDF/feed_jdf_route.php?oblast=505
curl http://localhost/JDF/feed_jdf_route.php?oblast=515
curl http://localhost/JDF/feed_jdf_route.php?oblast=516
curl http://localhost/JDF/feed_jdf_route.php?oblast=556
curl http://localhost/JDF/feed_jdf_route.php?oblast=557
curl http://localhost/JDF/feed_jdf_route.php?oblast=558
curl http://localhost/JDF/feed_jdf_route.php?oblast=595
curl http://localhost/JDF/feed_jdf_route.php?oblast=870
curl http://localhost/JDF/feed_jdf_route.php?oblast=872
curl http://localhost/JDF/feed_jdf_route.php?oblast=874
curl http://localhost/JDF/feed_jdf_route.php?oblast=875
curl http://localhost/JDF/feed_jdf_route.php?oblast=876
curl http://localhost/JDF/feed_jdf_route.php?oblast=877
curl http://localhost/JDF/feed_jdf_route.php?oblast=878
curl http://localhost/JDF/feed_jdf_route.php?oblast=905
curl http://localhost/JDF/feed_jdf_route.php?oblast=91500
curl http://localhost/JDF/feed_jdf_route.php?oblast=91501
curl http://localhost/JDF/feed_jdf_route.php?oblast=91502
curl http://localhost/JDF/feed_jdf_route.php?oblast=91503
curl http://localhost/JDF/feed_jdf_route.php?oblast=91504
curl http://localhost/JDF/feed_jdf_route.php?oblast=91505
curl http://localhost/JDF/feed_jdf_route.php?oblast=91506
curl http://localhost/JDF/feed_jdf_route.php?oblast=91507
curl http://localhost/JDF/feed_jdf_route.php?oblast=91508
curl http://localhost/JDF/feed_jdf_route.php?oblast=91509
curl http://localhost/JDF/feed_jdf_route.php?oblast=91510
curl http://localhost/JDF/feed_jdf_route.php?oblast=91511
curl http://localhost/JDF/feed_jdf_route.php?oblast=91514
curl http://localhost/JDF/feed_jdf_route.php?oblast=999

curl http://localhost/JDF/feed_close.php

zip MHD *.txt

feedvalidator.py MHD.zip

exit;

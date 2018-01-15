#!/bin/bash

cd /home/zirland/git/JDF/

curl http://localhost/JDF/feed_jdf_agency.php

curl http://localhost/JDF/feed_jdf_route.php?oblast=91502
curl http://localhost/JDF/feed_jdf_route.php?oblast=91503
curl http://localhost/JDF/feed_jdf_route.php?oblast=91504
curl http://localhost/JDF/feed_jdf_route.php?oblast=91505
curl http://localhost/JDF/feed_jdf_route.php?oblast=91506
curl http://localhost/JDF/feed_jdf_route.php?oblast=91507
curl http://localhost/JDF/feed_jdf_route.php?oblast=91508
curl http://localhost/JDF/feed_jdf_route.php?oblast=91509

curl http://localhost/JDF/feed_jdf_close.php

zip MHD *.txt

feedvalidator.py MHD.zip 

exit;

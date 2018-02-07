#!/bin/bash

cd /home/zirland/git/JDF/

curl http://localhost/JDF/feed_jdf_agency.php

curl http://localhost/JDF/feed_jdf_route.php?oblast=425

curl http://localhost/JDF/feed_jdf_close.php

zip MHD *.txt

feedvalidator.py MHD.zip 

exit;

#!/bin/bash

cd /home/zirland/git/JDF/

curl http://localhost/JDF/feed_agency.php

curl http://localhost/JDF/feed_jdf_start.php
curl http://localhost/JDF/feed_jdf_route.php?oblast=103
curl http://localhost/JDF/feed_close.php

zip MHD *.txt

# feedvalidator.py MHD.zip

exit;

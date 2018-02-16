#!/bin/bash

cd /home/zirland/git/JDF

for i in *.gpx
do
	curl http://localhost/JDF/stopimport.php?file=$i
	rm $i
done

exit;

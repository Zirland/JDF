#!/bin/bash

cd /home/zirland/git/JDF

#git pull

for i in *.gpx
do
	curl http://localhost/JDF/stopimport.php?file=$i
	rm $i
done

exit;

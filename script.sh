#!/bin/bash

cd /home/zirland/git/JDF

rm -rf data

mkdir data
chmod 777 data
cd data

wget ftp://ftp.cisjr.cz/JDF/JDF.zip

unzip JDF.zip
rm JDF.zip

curl http://localhost/JDF/dbprepare.php

for i in {1..25000}
do
	mkdir $i
	chmod 777 $i
	unzip $i.zip -d $i
	cd $i
	for f in *.txt; do iconv -f CP1250 -t utf-8 $f > $f.txt; done
	cd ..
	rm $i.zip
	curl http://localhost/JDF/sort.php?file=$i
done

exit;

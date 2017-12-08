cd data

for i in {1..25000}
do
	curl http://localhost/JDF/sort.php?file=$i
done

cd ..

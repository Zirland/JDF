<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="content-type">
	<title>GTFS</title>
</head>
<body>

<?php
$link = mysqli_connect ('localhost', 'gtfs', 'gtfs', 'JDF');
if (!$link) {
	echo "Error: Unable to connect to MySQL.".PHP_EOL;
	echo "Debugging errno: ".mysqli_connect_errno ().PHP_EOL;
	exit;
}
?>

<table style="width:100%; height:100%;">
<tr>
<td style="background-color:#cccccc;">
<a href="list.php">&nbsp; &nbsp;</a>
</td>
<td style="height:5px; background-color:#cccccc;">
</td></tr>
<tr><td style="width:100px; background-color:yellow; vertical-align:top;">
<a href="list2.php">Neaktivní</a><br/>
<a href="cisti.php">Čisti</a><br/>
STOPS<br/>
<a href="newstop.php">Newstop</a><br/>
<a href="imported.php">Imported</a><br/>
<a href="stoprename.php">Stop rename</a><br/>
LINKY<br/>
<a href="analist.php">Analýza</a><br/>
<a href="_krok1.php">Manuální linka</a><br/>
<a href="import.php">Import linky</a><br/>
<a href="headsigns.php">Headsigns</a><br/>
<a href="vozovna.php">Vozovna</a><br/>
TRASY<br/>
<a href="trasa.php">Trasy</a><br/>
<a href="misstrasa.php">Chybějící trasy</a><br/>
</td>
<td>

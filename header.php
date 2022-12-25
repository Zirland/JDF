<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
	<meta content="text/html; charset=utf-8" http-equiv="content-type">
	<title>JDF</title>
	<script type="text/javascript" src="https://api.mapy.cz/loader.js"></script>
	<script type="text/javascript">
		Loader.lang = "cs";
		Loader.load(null, {
			poi: true
		});
	</script>
</head>

<body>

	<?php
require_once 'dbconnect.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$link) {
    echo "Error: Unable to connect to database." . PHP_EOL;
    echo "Reason: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$filtr = @$_POST['filtr'];

if ($filtr != '') {
    $query27 = "UPDATE config SET hodnota = '$filtr' WHERE parametr = 'filtr_obec';";
    echo "$query27<br/>";
    $prikaz27 = mysqli_query($link, $query27);
}
?>

	<table style="width:100%; height:100%;">
		<tr>
			<td style="background-color:#cccccc;">
				<a href="list.php">&nbsp; &nbsp;</a>
			</td>
			<td style="height:5px; background-color:#cccccc;">
			</td>
		</tr>
		<tr>
			<td style="width:100px; background-color:yellow; vertical-align:top;">
				<a href="list2.php">Neaktivní</a><br />
				<a href="cisti.php">Čisti</a><br />
				STOPS<br />
				<a href="newstop.php">Newstop</a><br />
				<a href="station_kontrola.php">Station</a><br />
				LINKY<br />
				<a href="analist_fresh.php">Analýza fresh</a><br />
				<a href="analist_regen.php">Analýza regen</a><br />
				<a href="analist_new.php">Analýza new</a><br />
				TRASY<br />
				<a href="misstrasa.php">Chybějící trasy</a><br />
				<a href="misstrasa2.php">Chybějící koleje</a><br />
				<a href="usek.php">Detail úseku</a><br />
				<a href="cisti_usek.php">Čisti úseky</a><br />
				<a href="network.php">Network analýza</a><br />
				EXPORT<br />
				<a href="test.php">Test export</a><br />
				<a href="output/report.html" target="_blank">Výsledek kontroly</a><br />
				<hr />

				<?php
$query63 = "SELECT hodnota FROM config WHERE parametr = 'filtr_obec';";
if ($result63 = mysqli_query($link, $query63)) {
    while ($row63 = mysqli_fetch_row($result63)) {
        $value = $row63[0];
    }
}

echo "<form method=\"post\" action=\"\" name=\"filtr\">";
echo "<select name=\"filtr\">";

$query74 = "SELECT obec FROM stop GROUP BY obec ORDER BY obec;";
if ($result74 = mysqli_query($link, $query74)) {
    while ($row74 = mysqli_fetch_row($result74)) {
        $obec = $row74[0];

        echo "<option value=\"$obec\"";
        if ($obec == $value) {
            echo " SELECTED";
        }
        echo ">$obec</option>";
    }
}

echo "<input type=\"submit\"></form>";
?>
			</td>
			<td>
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
$link = mysqli_connect('localhost', 'root', 'root', 'JDF');
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

$token = "5b3ce3597851110001cf624862e9c595e8b34a50b05222a654306f62";

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
				<a href="station.php">Station</a><br />
				LINKY<br />
				<a href="analist_fresh.php">Analýza fresh</a><br />
				<a href="analist_regen.php">Analýza regen</a><br />
				<a href="analist_new.php">Analýza new</a><br />
				<a href="_step1.php">Manuální linka</a><br />
				<a href="import.php">Import linky</a><br />
				TRASY<br />
				<a href="trasa.php">Trasy</a><br />
				<a href="misstrasa.php">Chybějící trasy</a><br />
				<a href="usek.php">Detail úseku</a><br />
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
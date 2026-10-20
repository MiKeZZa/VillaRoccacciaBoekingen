<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Boek VillaRoccaccia</title>
</head>
<body>

<?php
SESSION_START();
include ('../../wp-config.php');
$mysqli = mysqli_connect("localhost",DB_USER,DB_PASSWORD, DB_NAME);

$sql = "SELECT id, DATE_FORMAT(datumvan, '%d-%m-%Y') AS datumvan, DATE_FORMAT(datumtot, '%d-%m-%Y') AS datumtot, prijsperdag, prijsperweek, ishoofdseizoen, bh.naam AS huis
		FROM book_prices bp
		INNER JOIN book_house bh ON bp.huisid = bh.huisid
		ORDER BY bh.naam, bp.datumvan ASC";
$res = $mysqli->query($sql);

echo '<table>
		<tr>
			<td><strong>Huis</strong></td>
			<td><strong>Datum van</strong></td>
			<td><strong>Datum tot</strong></td>
			<td><strong>Dagprijs</strong></td>
			<td><strong>Week prijs</strong></td>
			<td><strong>Is hoofdseizoen</strong></td>
			<td><strong>Bewerken</strong></td>
		</tr>';

while ($periodes = mysqli_fetch_assoc($res))
{
	echo '<tr>
			<td>'.$periodes['huis'].'</td>
			<td>'.$periodes['datumvan'].'</td>
			<td>'.$periodes['datumtot'].'</td>
			<td>€ '.$periodes['prijsperdag'].'</td>
			<td>€ '.$periodes['prijsperweek'].'</td>
			<td>'; if($periodes['ishoofdseizoen'] == 1) { echo 'Ja'; } else { echo 'Nee'; } echo '</td>
			<td><a href="periodesbewerken.php?id='.$periodes['id'].'">Bewerken</a></td>
		  </tr>';
}

echo '</table>

<br /><br /><a href="periodestoevoegen.php">Periodes toevoegen</a>

<br /><br /><a href="index.php">Terug naar admin</a>
';
?>
 
</body>
</html>
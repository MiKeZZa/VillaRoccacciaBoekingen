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

$sql = "SELECT bb.bookingid, bb.naam, aankomstdatum, vertrekdatum, aantalpersonen, status, bh.naam AS huis
		FROM book_bookings bb
		INNER JOIN book_house bh ON bb.huisid = bh.huisid
		ORDER BY aankomstdatum DESC";
$res = $mysqli->query($sql);

echo '<table>
		<tr>
			<td><strong>Huis</strong></td>
			<td><strong>Naam boeker</strong></td>
			<td><strong>Aankomstdatum</strong></td>
			<td><strong>Vertrekdatum</strong></td>
			<td><strong>Aantal personen</strong></td>
			<td><strong>Status</strong></td>
			<td><strong>Bewerken</strong></td>
		</tr>';

while ($boekingen = mysqli_fetch_assoc($res))
{
	echo '<tr>
			<td>'.$boekingen['huis'].'</td>
			<td>'.$boekingen['naam'].'</td>
			<td>'.$boekingen['aankomstdatum'].'</td>
			<td>'.$boekingen['vertrekdatum'].'</td>
			<td>'.$boekingen['aantalpersonen'].'</td>
			<td>'.$boekingen['status'].'</td>
			<td><a href="bewerkboeking.php?bookingid='.$boekingen['bookingid'].'">Bewerken</a></td>
		  </tr>';
}

echo '</table>

<br /><br /><a href="index.php">Terug naar admin</a>
';
?>
 
</body>
</html>
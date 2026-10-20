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

$bookingid = $mysqli->real_escape_string($_GET['bookingid']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{	 
	echo 'Boeking aangepast.';
	
	$query = "UPDATE book_bookings
			  SET status = '".$_POST['status']."'
			  WHERE bookingid = '".$bookingid."'";
	$result = $mysqli->query($query);
	
	if($_POST['status'] == 'Aanbetaling ontvangen')
	{
		echo '<form method="post" action="verstuurklantmail.php">
				Wilt u de klant een bevestigingsmail sturen m.b.t. de aanbetaling?
				<br /><br />
				<input type="hidden" name="bookingid" value="'.$_GET['bookingid'].'" />
				<input type="hidden" name ="typemail" value="ontvangen aanbetaling" />
				<input name="Verzenden" type="submit" value="Ja" />
				</form>';
	}
}

$sql = "SELECT bb.bookingid, bb.naam, DATE_FORMAT(aankomstdatum, '%d-%m-%Y') AS aankomstdatum, DATE_FORMAT(vertrekdatum, '%d-%m-%Y') AS vertrekdatum, aantalpersonen, status, bh.naam AS huis
		FROM book_bookings bb
		INNER JOIN book_house bh ON bb.huisid = bh.huisid
		WHERE bb.bookingid = '".$bookingid."'";
$res = $mysqli->query($sql);

$boeking = mysqli_fetch_assoc($res);

echo 
'<h3>Bewerk boeking</h3><br />

<form method="post" action="">
	Huis: '.$boeking['huis'].'<br />
	Boeker: '.$boeking['naam'].'<br />
	Periode: '.$boeking['aankomstdatum'].' - '.$boeking['vertrekdatum'].'<br />
	Aantal personen:'.$boeking['aantalpersonen'].'<br />

	<select name="status">
		<option value="Nog niet bevestigd"'; if($boeking['status'] == 'Nog niet bevestigd') { echo 'selected="SELECTED"'; } echo '>Nog niet bevestigd</option>
		<option value="Aanbetaling ontvangen"'; if($boeking['status'] == 'Aanbetaling ontvangen') { echo 'selected="SELECTED"'; } echo '>Aanbetaling ontvangen</option>
		<option value="Volledige bedrag ontvangen"'; if($boeking['status'] == 'Volledige bedrag ontvangen') { echo 'selected="SELECTED"'; } echo '>Volledige bedrag ontvangen</option>
		<option value="Geannuleerd"'; if($boeking['status'] == 'Geannuleerd') { echo 'selected="SELECTED"'; } echo '>Geannuleerd</option>
	</select>
	<br /><br />
	<input name="Verzenden" type="submit" value="Bewerk" />
</form>

<br /><br /><a href="index.php">Terug naar admin</a>
';
?>
 
</body>
</html>
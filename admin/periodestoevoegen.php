<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Boek VillaRoccaccia</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#datepicker" ).datepicker();
	$( "#datepicker2" ).datepicker();
  } );
  </script>
</head>
<body>

<?php
SESSION_START();
include ('../../wp-config.php');
$mysqli = mysqli_connect("localhost",DB_USER,DB_PASSWORD, DB_NAME); 

echo '<form method="post" action="">
		<strong>Huis</strong><br />
		<select name="huis">';

$sql = "SELECT huisid, naam, maxpersonen FROM book_house";
$res = $mysqli->query($sql);
while ($huis = mysqli_fetch_assoc($res))
{
	echo '<option value="'.$huis['huisid'].'">'.$huis['naam'].'</option>';
}
echo '  </select><br /><br />

	 <strong>Datum van</strong><br />
	 <input type="text" id="datepicker" name="datumvan"><br /><br />
	 
	 <strong>Datum tot</strong><br />
	 <input type="text" id="datepicker2" name="datumtot"><br /><br />
	 
	 <strong>Dagprijs</strong><br />
	 <input name="dagprijs" type="text" maxlenght="250" /><br /><br />
	 
	 <strong>Weekprijs</strong><br />
	 <input name="weekprijs" type="text" maxlenght="250" /><br /><br />
	 
	 <strong>Is hoofdseizoen</strong><br />
	 <select name="ishoofdseizoen">
			<option value="Nee">Nee</option>
			<option value="Ja">Ja</option>
	 </select>
	 
	 <br /><br /><input name="Verzenden" type="submit" value="Toevoegen" />
</form>';

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{	 
	echo 'Periode toegevoegd.';
	
	if($_POST['ishoofdseizoen'] == 'Ja')
	{
		$ishoofdseizoen = 1;
	}
	else
	{
		$ishoofdseizoen = 0;
	}
	
	$pieces = explode("/", $_POST['datumvan']);
	$van = $pieces[2].'-'.$pieces[0].'-'.$pieces[1];
	$pieces = explode("/", $_POST['datumtot']);
	$tot = $pieces[2].'-'.$pieces[0].'-'.$pieces[1];
	
	$query = "INSERT INTO book_prices (huisid, datumvan, datumtot, prijsperdag, prijsperweek, ishoofdseizoen)
	VALUES ('".$_POST['huis']."', '".$van."', '".$tot."', '".$_POST['dagprijs']."', '".$_POST['weekprijs']."', '".$ishoofdseizoen."')";
	$res = $mysqli->query($query);
}



echo '<br /><br /><a href="index.php">Terug naar admin</a>';
?>
 
</body>
</html>
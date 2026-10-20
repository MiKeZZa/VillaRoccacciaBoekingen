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
$id = $mysqli->real_escape_string($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{	 
	echo 'Periode bewerkt.';
	
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

	$query = "UPDATE book_prices 
			  SET huisid = '".$_POST['huis']."', datumvan = '".$van."', datumtot = '".$tot."', prijsperdag = '".$_POST['dagprijs']."', prijsperweek = '".$_POST['weekprijs']."', ishoofdseizoen = '".$ishoofdseizoen."'
			  WHERE id = '".$id."'";
	$result = $mysqli->query($query);
}

echo '<form method="post" action="">
		<strong>Huis</strong><br />
		<select name="huis">';

$sqlperiode = "SELECT huisid, DATE_FORMAT(datumvan, '%m/%d/%Y') AS datumvan, DATE_FORMAT(datumtot, '%m/%d/%Y') AS datumtot, prijsperdag, prijsperweek, ishoofdseizoen FROM book_prices WHERE id = '".$id."'";
$resperiode = $mysqli->query($sqlperiode);
while ($periode = mysqli_fetch_assoc($resperiode))
{		
	$sql = "SELECT huisid, naam, maxpersonen FROM book_house";
	$res = $mysqli->query($sql);
	while ($huis = mysqli_fetch_assoc($res))
	{
		echo '<option value="'.$huis['huisid'].'"';
		if($huis['huisid'] == $periode['huisid'])
		{
			echo ' selected="selected"';
		}
		echo '">'.$huis['naam'].'</option>';
	}
	echo '  </select><br /><br />

		 <strong>Datum van</strong><br />
		 <input type="text" id="datepicker" name="datumvan" value="'.$periode['datumvan'].'"><br /><br />
		 
		 <strong>Datum tot</strong><br />
		 <input type="text" id="datepicker2" name="datumtot" value="'.$periode['datumtot'].'"><br /><br />
		 
		 <strong>Dagprijs</strong><br />
		 <input name="dagprijs" type="text" maxlenght="250" value="'.$periode['prijsperdag'].'" /><br /><br />
		 
		 <strong>Weekprijs</strong><br />
		 <input name="weekprijs" type="text" maxlenght="250" value="'.$periode['prijsperweek'].'" /><br /><br />
		 
		 <strong>Is hoofdseizoen</strong><br />
		 <select name="ishoofdseizoen">
				<option value="Nee"';
				if($periode['ishoofdseizoen'] == 0)
				{
					echo 'selected="selected"';
				}
				echo '>Nee</option>
				<option value="Ja"';
				if($periode['ishoofdseizoen'] == 1)
				{
					echo 'selected="selected"';
				}
				echo '>Ja</option>
		 </select>
		 
		 <br /><br /><input name="Verzenden" type="submit" value="Bewerk" />
	</form>';
}

echo '<br /><br /><a href="index.php">Terug naar admin</a>';
?>
 
</body>
</html>
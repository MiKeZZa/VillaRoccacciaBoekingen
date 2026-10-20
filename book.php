<?php
if($_GET['lang'] <> '') { $_SESSION['lang'] = $_GET['lang']; } else { $_SESSION['lang'] = 'nl'; }
include('vertalingen.php');
?>

<!doctype html>

<html lang="en">

<head>

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Boek VillaRoccaccia</title>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

  <link rel="stylesheet" href="../wp-content/themes/sparkling/assets/css/bootstrap.min.css">

  <link rel="stylesheet" href="../wp-content/themes/sparkling/style.css">

  <link rel="stylesheet" href="./style.css">

  <link rel="stylesheet" href="./style_include.css">

  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <script>



  <?php

  SESSION_START();

  include ('../wp-config.php');

  $mysqli = mysqli_connect("localhost",DB_USER,DB_PASSWORD, DB_NAME);

  $huis = $mysqli->real_escape_string($_GET['huis']);

  //aankomst

  $sql = "

  SELECT DATE_FORMAT(d.date, '%d-%m-%Y') AS date

  FROM dates d

  WHERE date < NOW()
 
  UNION 

  SELECT DATE_FORMAT(d.date, '%d-%m-%Y') AS date

  FROM dates d

  INNER JOIN book_bookings bb ON d.date BETWEEN bb.aankomstdatum AND bb.vertrekdatum

  WHERE huisid = '".$huis."' AND d.date <> bb.vertrekdatum AND bb.status <> 'Geannuleerd'";
  
  $res = $mysqli->query($sql);



  echo 'var disableddates_aankomst = [';

  while ($datums = mysqli_fetch_assoc($res))

  {

	  echo '"'.$datums['date'].'",';

  }

  echo '];';



  //vertrek

  $sql = "

  SELECT DATE_FORMAT(d.date, '%d-%m-%Y') AS date

  FROM dates d

  WHERE date < NOW()

  UNION  

  SELECT DATE_FORMAT(d.date, '%d-%m-%Y') AS date

  FROM dates d

  INNER JOIN book_bookings bb ON d.date BETWEEN bb.aankomstdatum AND bb.vertrekdatum

  WHERE huisid = '".$huis."' AND d.date <> bb.aankomstdatum AND bb.status <> 'Geannuleerd'";

  $res = $mysqli->query($sql);

  echo 'var disableddates_vertrek = [';

  while ($datums = mysqli_fetch_assoc($res))
  {
	  echo '"'.$datums['date'].'",';
  }

  echo '];';

  //custom_dates voor wel vrij maar geen aankomstdag (in hoogseizoen)

  $sql = "
  SELECT DATE_FORMAT(d.date, '%e-%m-%Y') AS date

  FROM dates d

  INNER JOIN book_prices bp ON d.date BETWEEN bp.datumvan AND bp.datumtot

  WHERE huisid = '".$huis."' AND ishoofdseizoen = 1 AND day_of_week <> 'Saturday'";

  $res = $mysqli->query($sql);

  echo 'var custom_dates = [';

  while ($datums = mysqli_fetch_assoc($res))
  {
	echo '"'.$datums['date'].'",';
	//echo '"10-1-2020", "22-1-2029", "23-12-2019", "24-12-2019", "25-12-2019", "26-12-2019", "27-10-2019", "27-12-2019"';
  }
  echo '"31-01-2099"];'; //deze onzindatum er achter om het komma probleem aan het einde op te lossen :)

  ?>

  var empty_array = [];

  for (i = 0; i <= custom_dates.length; i++) {

      if (custom_dates[i] !== undefined) {

          var split_date = custom_dates[i].split('-');

          split_date[1]--;

          var split_date_string = "" + split_date[0] + "-" + split_date[1] + "-" + split_date[2] + "";

          empty_array.push(split_date_string);

      }

  }

  jQuery(document).ready(function () {

      $('input#from').on('click', function () {

	setTimeout(function(){
		$('input#from').click();
	});

          $('.ui-datepicker-calendar td').on('click', function () {

              if ($(this).hasClass('matched')) {

                  $('.popupbox').show();

                  $('input#from').val('');

              }

          })

      });



      $('input#from').on('click', function () {

	

          $('.ui-datepicker-calendar td').each(function () {

              var day = $(this).find('a').text();

              var month = $(this).attr('data-month');

              var year = $(this).attr('data-year');

              if (month == undefined) {

                  month = 0;

              }

              var fulldate = "" + day + "-" + month + "-" + year;

              if ($.inArray(fulldate, empty_array) !== -1) {

                  $(this).addClass('matched');

                  $(this).addClass('ui-state-disabled');

              }

          })

      });

  });

  var empty_array = [];

  for (i = 0; i <= custom_dates.length; i++) {

      if (custom_dates[i] !== undefined) {

          var split_date = custom_dates[i].split('-');

          split_date[1]--;

          var split_date_string = "" + split_date[0] + "-" + split_date[1] + "-" + split_date[2] + "";

          empty_array.push(split_date_string);

      }

  }

  jQuery(document).ready(function () {

      $('input#to').on('click', function () {

setTimeout(function(){
		$('input#to').click();
	});

          $('.ui-datepicker-calendar td').on('click', function () {

              if ($(this).hasClass('matched')) {

                  $('.popupbox').show();

                  $('input#from').val('');

              }

          })

      });

	$('.ui-state-disabled').on('click', function(){
		$('.popupbox').show();
	});

      jQuery('.closepopup').on('click', function () {

          jQuery('.popupbox').hide();

      });

      $('input#to').on('click', function () {

          $('.ui-datepicker-calendar td').each(function () {

              var day = $(this).find('a').text();

              var month = $(this).attr('data-month');

              var year = $(this).attr('data-year');

              if (month == undefined) {

                  month = 0;

              }

              var fulldate = "" + day + "-" + month + "-" + year;

              if ($.inArray(fulldate, empty_array) !== -1) {

                  $(this).addClass('matched');

                  $(this).addClass('ui-state-disabled');

              }

          })

      });

  });





  function DisableSpecificDates_aankomst(date) {

		var string = jQuery.datepicker.formatDate('dd-mm-yy', date);

		return [disableddates_aankomst.indexOf(string) == -1];

	  }



  function DisableSpecificDates_vertrek(date) {

		var string = jQuery.datepicker.formatDate('dd-mm-yy', date);

		return [disableddates_vertrek.indexOf(string) == -1];

	  }



  $( function() {

    var dateFormat = "mm/dd/yy",

      from = $( "#from" )

        .datepicker({

          defaultDate: "+1w",

          changeMonth: true,

          changeYear: true,

		  showOtherMonths: true,

		  selectOtherMonths: true,

          numberOfMonths: 2,

		  beforeShowDay: DisableSpecificDates_aankomst,

		  dateFormat: 'dd-mm-yy'

        })

        .on( "change", function() {

          to.datepicker( "option", "minDate", getDate( this ) );

        }),

      to = $( "#to" ).datepicker({

        defaultDate: "+1w",

        changeMonth: true,

        changeYear: true,

		  showOtherMonths: true,

		  selectOtherMonths: true,

          numberOfMonths: 2,

		  beforeShowDay: DisableSpecificDates_vertrek,

		  dateFormat: 'dd-mm-yy'

      })

      .on( "change", function() {

        from.datepicker( "option", "maxDate", getDate( this ) );

      });



    function getDate( element ) {

      var date;

      try {

        date = $.datepicker.parseDate( dateFormat, element.value );

      } catch( error ) {

        date = null;

      }



      return date;

    }

  } );

  </script>

</head>

<body>



<div>



<?php

$sql = "SELECT huisid, naam, maxpersonen FROM book_house WHERE huisid = '".$huis."'";

$res = $mysqli->query($sql);

if($res->num_rows <> 1)

{

	echo 'Er is iets mis gegaan';

	exit();

}

else

{

	$huis = mysqli_fetch_assoc($res);



	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{

		//vullen sessievariabelen

		$pieces = explode("-", $_POST['aankomstdatum']);

		$_SESSION['aankomstdatum'] = $pieces[0].'-'.$pieces[1].'-'.$pieces[2];

		$_SESSION['aankomstdatum_engels'] = $pieces[2].'-'.$pieces[1].'-'.$pieces[0];

		$pieces = explode("-", $_POST['vertrekdatum']);

		$_SESSION['vertrekdatum'] = $pieces[0].'-'.$pieces[1].'-'.$pieces[2];

		$_SESSION['vertrekdatum_engels'] = $pieces[2].'-'.$pieces[1].'-'.$pieces[0];

		$_SESSION['huis'] = $huis['naam'];

		$_SESSION['huisid'] = $huis['huisid'];

		$_SESSION['naam'] = $_POST['voornaam'].' '.$_POST['achternaam'];

		$_SESSION['email'] = $_POST['email'];

		$_SESSION['telefoon'] = $_POST['telefoon'];

		$_SESSION['adres'] = $_POST['adres'].' '.$_POST['plaats'];

		$_SESSION['land'] = $_POST['land'];

		$_SESSION['aantaldagen'] = (abs(strtotime($_SESSION['vertrekdatum']) - strtotime($_SESSION['aankomstdatum']))/60/60/24)+1;

		$_SESSION['aantalpersonen'] = $_POST['aantalpersonen'];

		$_SESSION['prijs'] = 0;
		
		$_SESSION['prijszonderborg'] = 0;
		
		if($_GET['lang'] <> '') { $_SESSION['lang'] = $_GET['lang']; } else { $_SESSION['lang'] = 'nl'; }


		//prijsbepaling

		$sql = "SELECT prijsperdag, prijsperweek, datediff(datumtot,datumvan)+1 AS aantaldagen

				FROM (

				SELECT 

				CASE WHEN '".$_SESSION['aankomstdatum_engels']."' BETWEEN datumvan AND datumtot THEN '".$_SESSION['aankomstdatum_engels']."' ELSE datumvan END AS datumvan,

				CASE WHEN '".$_SESSION['vertrekdatum_engels']."' BETWEEN datumvan AND datumtot THEN '".$_SESSION['vertrekdatum_engels']."' ELSE datumtot END AS datumtot,

				prijsperdag,

				prijsperweek

				FROM book_prices

				WHERE ('".$_SESSION['aankomstdatum_engels']."' BETWEEN datumvan AND datumtot OR '".$_SESSION['vertrekdatum_engels']."' BETWEEN datumvan AND datumtot)

				AND huisid = '".$_SESSION['huisid']."') a";

		$res = $mysqli->query($sql);

		while ($prijs = mysqli_fetch_assoc($res))
		{

			$aantalweken = floor(($prijs['aantaldagen']-1)/7);

			$aantaldagen = ($prijs['aantaldagen']-1)%7;



			//echo 'aantal dagen:'.$prijs['aantaldagen'].'<br />';

			//echo 'aantalweken:'.$aantalweken.'<br />';

			//echo 'aantaldagen:'.$aantaldagen.'<br />';



			$wekenprijs = $aantalweken*$prijs['prijsperweek'];

			$dagenprijs = $aantaldagen*$prijs['prijsperdag'];

			/*echo 'aaaa'.$prijs['aantaldagen'].'aaaa';



			echo 'weken:'.$aantalweken.'<br />';

			echo 'dagen:'.$aantaldagen.'<br />';

			echo 'wekenprijs:'.$wekenprijs.'<br />';

			echo 'dagenprijs:'.$dagenprijs.'<br />';*/

			$_SESSION['prijszonderborg'] = $_SESSION['prijszonderborg'] + $wekenprijs + $dagenprijs;
		}

		$borgbedrag = 150;
		$prijsinclborg = $_SESSION['prijszonderborg'] + $borgbedrag;
		
		$_SESSION['prijs'] = $prijsinclborg;

		$_SESSION['aanbetaling'] = number_format((float)$prijsinclborg*0.3, 2, '.', '');

		$_SESSION['restantbedrag'] = $prijsinclborg-$_SESSION['aanbetaling'];

		$_SESSION['restantbedrag'] = number_format((float)$_SESSION['restantbedrag'], 2, '.', '');



		echo '

		<h3>'.$boekingbevestigen.'</h3>

		'.$bedanktvooruwaanvraag.':

		<br/ ><br />

		<h3>'.ucfirst($boeking).'</h3>

		<table>

			<tr><td>'.ucfirst($appartement).':</td><td>'.$_SESSION['huis'].'</td></tr>

			<tr><td>'.ucfirst($periode).':</td><td>'.$_SESSION['aankomstdatum'].' - '.$_SESSION['vertrekdatum'].'</td></tr>

			<tr><td>'.ucfirst($aantaldagen_).':</td><td>'.$_SESSION['aantaldagen'].'</td></tr>

			<tr><td>'.ucfirst($prijs_).':</td><td>€ '.number_format((float)$_SESSION['prijszonderborg'], 2, '.', '').'</td></tr>
			
			<tr><td>'.ucfirst($borg).':</td><td>€ '.number_format((float)$borgbedrag, 2, '.', '').'</td></tr>
			
			<tr><td>'.ucfirst($totaalprijs).':</td><td>€ '.number_format((float)$_SESSION['prijs'], 2, '.', '').'</td></tr>';



		if(strtotime($_SESSION['aankomstdatum_engels']) >= strtotime("+28 day"))

		{

			echo '<tr><td>'.ucfirst($aanbetaling).':</td><td>€ '.$_SESSION['aanbetaling'].'</td></tr>

			<tr><td>'.ucfirst($restantbedrag).':</td><td>€ '.$_SESSION['restantbedrag'].'</td></tr>';

		}



		echo '<tr><td><h3>'.ucfirst($uwgegevens).'</h3></td></tr>

			<tr><td>'.ucfirst($naam).':</td><td>'.$_SESSION['naam'].'</td></tr>

			<tr><td>'.ucfirst($email).':</td><td>'.$_SESSION['email'].'</td></tr>

			<tr><td>'.ucfirst($telefoonnummer).':</td><td>'.$_SESSION['telefoon'].'</td></tr>

			<tr><td>'.ucfirst($adres).':</td><td>'.$_SESSION['adres'].'</td></tr>

			<tr><td>'.ucfirst($land).':</td><td>'.$_SESSION['land'].'</td></tr>

		</table>';

		echo '<br /><br />

			  <form method="post" action="book2.php?lang='.$_SESSION['lang'].'">

			  <input type="hidden" name="huis" value="'.$_SESSION['huis'].'">

			  <input type="hidden" name="huisid" value="'.$_SESSION['huisid'].'">

			  <input type="hidden" name="prijs" value="'.$_SESSION['prijs'].'">

			  <input type="hidden" name="aantaldagen" value="'.$_SESSION['aantaldagen'].'">

			  <input type="hidden" name="aantalpersonen" value="'.$_SESSION['aantalpersonen'].'">

			  <input type="hidden" name="aanbetaling" value="'.$_SESSION['aanbetaling'].'">

			  <input type="hidden" name="restantbedrag" value="'.$_SESSION['restantbedrag'].'">

			  <input type="hidden" name="naam" value="'.$_SESSION['naam'].'">

			  <input type="hidden" name="email" value="'.$_SESSION['email'].'">

			  <input type="hidden" name="telefoon" value="'.$_SESSION['telefoon'].'">

			  <input type="hidden" name="adres" value="'.$_SESSION['adres'].'">

			  <input type="hidden" name="land" value="'.$_SESSION['land'].'">

			  <input type="hidden" name="aankomstdatum_engels" value="'.$_SESSION['aankomstdatum_engels'].'">

			  <input type="hidden" name="vertrekdatum_engels" value="'.$_SESSION['vertrekdatum_engels'].'">

			  <input type="hidden" name="aankomstdatum" value="'.$_SESSION['aankomstdatum'].'">

			  <input type="hidden" name="vertrekdatum" value="'.$_SESSION['vertrekdatum'].'">
			  
			  <input type="hidden" name="prijszonderborg" value="'.number_format((float)$_SESSION['prijszonderborg'], 2, '.', '').'">
			  
			  <input type="hidden" name="borgbedrag" value="'.number_format((float)$borgbedrag, 2, '.', '').'">
			  
			  <input type="hidden" name="totaalprijsinclborg" value="'.number_format((float)$_SESSION['prijs'], 2, '.', '').'">

			  <input name="Verzenden" type="submit" value="'.$jaikwilboeken.'" class="wpcf7-form-control wpcf7-submit btn btn-default" />

			  </form>';

	}



	else

	{

		echo '	

		<h3>'.$gegevensinvoeren.'</h3>

		<form method="post" action="">

		

		<strong>'.ucfirst($appartement).'</strong><br />

		'.$huis['naam'].'<br /><br />

		

		<strong>'.$aantalpersonen.'</strong><br />

		<select name="aantalpersonen">';

			for ($x = 1; $x <= $huis['maxpersonen']; $x++)

			{

				echo '<option value="'.$x.'">'.$x.'</option>';

			}

		echo '

		</select><br /><br />

		

		<strong>'.$aankomstdatum.'</strong><br />

		<input type="text" id="from" name="aankomstdatum"><br />

		<span style="background-color: #00FF00; width: 10px; height: 10px">&nbsp;&nbsp;</span> '.$beschikbaar.'<br />

		<span style="background-color: #FF0000; width: 10px; height: 10px">&nbsp;&nbsp;</span> '.$nietbeschikbaar.'<br /><br />

		

		<strong>'.$vertrekdatum.'</strong><br />

		<input type="text" id="to" name="vertrekdatum"><br />

		<span style="background-color: #00FF00; width: 10px; height: 10px">&nbsp;&nbsp;</span> '.$beschikbaar_vertrek.'<br />

		<span style="background-color: #FF0000; width: 10px; height: 10px">&nbsp;&nbsp;</span> '.$nietbeschikbaar_vertrek.'<br /><br />

		

		<strong>'.$voornaam.'*</strong><br />

		<input name="voornaam" type="text" maxlenght="250" /><br /><br />

		

		<strong>'.$achternaam.'*</strong><br />

		<input name="achternaam" type="text" maxlenght="250" /><br /><br />

		

		<strong>'.$email.'*</strong><br />

		<input name="email" type="text" maxlenght="250" /><br /><br />

		

		<strong>'.$telefoonnummer.'</strong><br />

		<input name="telefoon" type="text" maxlenght="250" /><br /><br />



		<strong>'.$adres.'*</strong><br />

		<input name="adres" type="text" maxlenght="250" /><br /><br />

		

		<strong>'.$plaats.'*</strong><br />

		<input name="plaats" type="text" maxlenght="250" /><br /><br />

		

		<strong>'.$land.'*</strong><br />

		<select name="land">

			<option value="'.$belgie.'">'.$belgie.'</option>

			<option value="'.$duitsland.'"'; if($_SESSION['lang']=='de') { echo 'selected="selected"'; } echo '>'.$duitsland.'</option>

			<option value="'.$denemarken.'">'.$denemarken.'</option>

			<option value="'.$finland.'">'.$finland.'</option>

			<option value="'.$frankrijk.'">'.$frankrijk.'</option>

			<option value="'.$italie.'"'; if($_SESSION['lang']==='it') { echo 'selected="selected"'; } echo '>'.$italie.'</option>

			<option value="'.$nederland.'"'; if($_SESSION['lang']==='nl') { echo 'selected="selected"'; } echo '>'.$nederland.'</option>

			<option value="'.$spanje.'">'.$spanje.'</option>

			<option value="'.$zweden.'">'.$zweden.'</option>

			<option value="'.$overige.'"'; if($_SESSION['lang']==='en') { echo 'selected="selected"'; } echo '>'.$overige.'</option>

		</select><br /><br />

		

		<strong>'.$veldengemarkeerdmeteenster.'</strong><br /><br />

		

		<input name="Verzenden" type="submit" value="'.$nuboeken.'" class="wpcf7-form-control wpcf7-submit btn btn-default" /></form>';
		

	}

}

?>



</div>

<div class="popupbox" style="top: 20% !important;"> <?php echo $alleenzaterdagaankomst; ?> <div class="closepopup">X</div></div>

</body>

</html>
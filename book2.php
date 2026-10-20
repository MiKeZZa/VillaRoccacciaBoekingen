<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../wp-content/themes/sparkling/assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="../wp-content/themes/sparkling/style.css">
  <link rel="stylesheet" href="./style.css">
  <link rel="stylesheet" href="./style_include.css">
  <title>Boek VillaRoccaccia</title>
</head>
<body>

<div class="main-content-inner col-sm-12 col-md-8">

<?php
SESSION_START();
if($_GET['lang'] <> '') { $_SESSION['lang'] = $_GET['lang']; } else { $_SESSION['lang'] = 'nl'; }

include('vertalingen.php');
include ('../wp-config.php');
$mysqli = mysqli_connect("localhost",DB_USER,DB_PASSWORD, DB_NAME);
include('./mailen.php');

// Ingevulde gegevens, veilig gemaakt om te tonen in de pagina en de mails
$veilig = array_map('vr_html', $_POST);

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{	
	echo '	
	<h3>'.$boekingingevoerd.'</h3>
	'.$bedanktvooruwboeking.'<br /><br />';
	
	if(strtotime($_POST['aankomstdatum_engels']) < strtotime("+28 day"))
	{
		echo $binnen4weken.$veilig['prijs'].' '.$nietbinnen4weken_1.' '.$binnen4weken_2;
	}
	else
	{
		echo $nietbinnen4weken.$veilig['aanbetaling'].' '.$nietbinnen4weken_1.' '.$nietbinnen4weken_2;
	}

	echo '<br /><br />
	Bank: ING<br />
	IBAN: NL40 INGB 0661 0310 20 t.n.v. J.C. van der Vlies-van Gils<br />
	BIC: INGBNL2A<br />
	'.$ondervermelding.'<br /><br />

	'.$bedankt.'<br /><br />
	
	<strong>'.$spambox.'</strong>';
	// Prepared statement, zodat bijv. een naam als "O'Brien" de boeking niet laat mislukken
	$stmt = $mysqli->prepare("INSERT INTO book_bookings 
				(aankomstdatum, vertrekdatum, huisid, naam, email, telefoon, adres, land, aantalpersonen, prijs, aanbetaling, status, lang)
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Nog niet bevestigd', ?)");
	$stmt->bind_param('ssssssssssss', $_POST['aankomstdatum_engels'], $_POST['vertrekdatum_engels'], $_POST['huisid'], $_POST['naam'],
							$_POST['email'], $_POST['telefoon'], $_POST['adres'], $_POST['land'], $_POST['aantalpersonen'],
							$_POST['prijs'], $_POST['aanbetaling'], $_SESSION['lang']);
	if(!$stmt->execute())
	{
		error_log('Villa Roccaccia boeking: opslaan mislukt: '.$stmt->error);
	}
	$stmt->close();

	//mailen aan boeker
	$onderwerp = $boekingbij.' Villa Roccaccia';

	$tekstvoorboeker = $beste.' '.$veilig['naam'].',<br /><br />';
	
	if(strtotime($_POST['aankomstdatum_engels']) < strtotime("+28 day"))
	{
		$tekstvoorboeker .= $binnen4weken.$veilig['prijs'].' '.$binnen4weken_2;
	}
	else
	{
		$tekstvoorboeker .= $nietbinnen4weken.$veilig['aanbetaling'].' '.$nietbinnen4weken_2;
	}
	
	$tekstvoorboeker .= '<br /><br />
	Bank: ING<br />
	IBAN : NL40 INGB 0661 0310 20 t.n.v. J.C. van der Vlies-van Gils<br />
	BIC: INGBNL2A<br />
	'.$ondervermelding.'<br /><br />                                                                                                                                                       
	'.$vragen.'<br /><br />

	'.ucfirst($appartement).': '.$veilig['huis'].'<br />
	'.ucfirst($aankomstdatum).': '.$veilig['aankomstdatum'].'<br />
	'.ucfirst($vertrekdatum).': '.$veilig['vertrekdatum'].'<br />
	'.ucfirst($aantaldagen_).': '.$veilig['aantaldagen'].'<br />
	'.ucfirst($aantalpersonen).': '.$veilig['aantalpersonen'].'<br />
	'.ucfirst($prijs_).' € '.$veilig['prijszonderborg'].'<br />
	'.ucfirst($borg).' € '.number_format((float)$_POST['borgbedrag'], 2, '.', '').'<br />
	'.ucfirst($totaalprijs).' € '.$veilig['totaalprijsinclborg'].'<br /><br />';
	
	if(strtotime($_POST['aankomstdatum_engels']) >= strtotime("+28 day"))
	{
		$tekstvoorboeker .= $aanbetaling.' € '.$veilig['aanbetaling'].' + '.$aanbetalingborg.'<br />		
		'.$restantbedrag.' € '.$veilig['restantbedrag'].'<br />'.$eindschoonmaak.'<br />';
	}
	
	$tekstvoorboeker .= '<br />
	<strong>'.ucfirst($uwgegevens).':</strong><br />
	'.ucfirst($adres).': '.$veilig['adres'].'<br />
	'.ucfirst($land).': '.$veilig['land'].'<br />
	'.ucfirst($telefoonnummer).': '.$veilig['telefoon'].'<br />

	'.$bedankt.'
	<br /><br />
	<strong>'.$spambox.'</strong>';
	
	$htmlvoorboeker = '<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
    <head>
    	<!-- NAME: FLYER -->
        <!--[if gte mso 15]>
		<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
		</xml>
		<![endif]-->
		<meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>'.$onderwerp.'</title>
        
    <style type="text/css">
		p{
			margin:10px 0;
			padding:0;
		}
		table{
			border-collapse:collapse;
		}
		h1,h2,h3,h4,h5,h6{
			display:block;
			margin:0;
			padding:0;
		}
		img,a img{
			border:0;
			height:auto;
			outline:none;
			text-decoration:none;
		}
		body,#bodyTable,#bodyCell{
			height:100%;
			margin:0;
			padding:0;
			width:100%;
		}
		#outlook a{
			padding:0;
		}
		img{
			-ms-interpolation-mode:bicubic;
		}
		table{
			mso-table-lspace:0pt;
			mso-table-rspace:0pt;
		}
		.ReadMsgBody{
			width:100%;
		}
		.ExternalClass{
			width:100%;
		}
		p,a,li,td,blockquote{
			mso-line-height-rule:exactly;
		}
		a[href^=tel],a[href^=sms]{
			color:inherit;
			cursor:default;
			text-decoration:none;
		}
		p,a,li,td,body,table,blockquote{
			-ms-text-size-adjust:100%;
			-webkit-text-size-adjust:100%;
		}
		.ExternalClass,.ExternalClass p,.ExternalClass td,.ExternalClass div,.ExternalClass span,.ExternalClass font{
			line-height:100%;
		}
		a[x-apple-data-detectors]{
			color:inherit !important;
			text-decoration:none !important;
			font-size:inherit !important;
			font-family:inherit !important;
			font-weight:inherit !important;
			line-height:inherit !important;
		}
		a.mcnButton{
			display:block;
		}
		.mcnImage{
			vertical-align:bottom;
		}
		.mcnTextContent{
			word-break:break-word;
		}
		.mcnTextContent img{
			height:auto !important;
		}
		.mcnDividerBlock{
			table-layout:fixed !important;
		}
		body,#bodyTable{
			background-color:white;
		}
	
		#bodyCell{
			border-top:0;
		}
	
		h1{
			color:#a1cf03 !important;
			font-family:Helvetica;
			font-size:40px;
			font-style:normal;
			font-weight:bold;
			line-height:100%;
			letter-spacing:-1px;
			text-align:center;
		}
	
		h2{
			color:#404040 !important;
			font-family:Helvetica;
			font-size:26px;
			font-style:normal;
			font-weight:bold;
			line-height:125%;
			letter-spacing:-.75px;
			text-align:left;
		}
	
		h3{
			color:#a1cf03 !important;
			font-family:Helvetica;
			font-size:18px;
			font-style:normal;
			font-weight:bold;
			line-height:125%;
			letter-spacing:-.5px;
			text-align:left;
		}
	
		h4{
			color:#808080 !important;
			font-family:Helvetica;
			font-size:16px;
			font-style:normal;
			font-weight:bold;
			line-height:125%;
			letter-spacing:normal;
			text-align:left;
		}
	
		.headerContainer .mcnTextContent a{
			color:#a1cf03;
			font-weight:normal;
			text-decoration:underline;
		}
	
		#templateBody{
			background-color:#EEEEEE;
			border-top:0;
			border-bottom:0;
		}
	
		#bodyBackground{
			background-color:#FFFFFF;
			border:1px solid #D5D5D5;
		}
	
		.bodyContainer .mcnTextContent,.bodyContainer .mcnTextContent p{
			color:#606060;
			font-family:Helvetica;
			font-size:15px;
			line-height:150%;
			text-align:left;
		}
	
		.bodyContainer .mcnTextContent a{
			color:#a1cf03;
			font-weight:normal;
			text-decoration:underline;
		}
	
		#templateFooter{
			background-color:white;
			border-top:1px solid #000000;
			border-bottom:0;
		}
	
		.footerContainer .mcnTextContent,.footerContainer .mcnTextContent p{
			color:#CCCCCC;
			font-family:Helvetica;
			font-size:11px;
			line-height:125%;
			text-align:center;
		}
	
		.footerContainer .mcnTextContent a{
			color:#CCCCCC;
			font-weight:normal;
			text-decoration:underline;
		}
	@media only screen and (max-width: 480px){
		body,table,td,p,a,li,blockquote{
			-webkit-text-size-adjust:none !important;
		}

}	@media only screen and (max-width: 480px){
		body{
			width:100% !important;
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.templateContainer{
			max-width:600px !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImage{
			height:auto !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCaptionTopContent,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer{
			max-width:100% !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnBoxedTextContentContainer{
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupContent{
			padding:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCaptionLeftContentOuter .mcnTextContent,.mcnCaptionRightContentOuter .mcnTextContent{
			padding-top:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardTopImageContent,.mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{
			padding-top:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardBottomImageContent{
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockInner{
			padding-top:0 !important;
			padding-bottom:0 !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockOuter{
			padding-top:9px !important;
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnTextContent,.mcnBoxedTextContentColumn{
			padding-right:18px !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{
			padding-right:18px !important;
			padding-bottom:0 !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcpreview-image-uploader{
			display:none !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
	
		h1{
			font-size:24px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	
		h2{
			font-size:20px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	
		h3{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	
		h4{
			font-size:16px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	
		.mcnBoxedTextContentContainer .mcnTextContent,.mcnBoxedTextContentContainer .mcnTextContent p{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	
		#templatePreheader{
			display:block !important;
		}

}	@media only screen and (max-width: 480px){
	
		.preheaderContainer .mcnTextContent,.preheaderContainer .mcnTextContent p{
			font-size:14px !important;
			line-height:115% !important;
		}

}	@media only screen and (max-width: 480px){
	
		.headerContainer .mcnTextContent,.headerContainer .mcnTextContent p{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){

		.bodyContainer .mcnTextContent,.bodyContainer .mcnTextContent p{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	
		.footerContainer .mcnTextContent,.footerContainer .mcnTextContent p{
			font-size:14px !important;
			line-height:115% !important;
		}

}</style></head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
        <center>
            <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
                <tr>
                    <td align="center" valign="top" id="bodyCell">
                        <!-- BEGIN TEMPLATE // -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td align="center" valign="top">
                                    <!-- BEGIN PREHEADER // -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templatePreheader">
                                        <tr>
                                        	<td align="center" valign="top">
                                                <table border="0" cellpadding="0" cellspacing="0" width="600" class="templateContainer">
                                                    <tr>
                                                        <td valign="top" class="preheaderContainer" style="padding-top:10px; padding-bottom:10px;"><table border="0" cellpadding="0" cellspacing="0"
width="100%" class="mcnTextBlock" style="min-width:100%;">
                                                    </table></td>
                                                    </tr>
                                                </table>
                                            </td>                                            
                                        </tr>
                                    </table>
                                    <!-- // END PREHEADER -->
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <!-- BEGIN HEADER // -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateHeader">
                                        <tr>
                                            <td align="center" valign="top">
                                                <table border="0" cellpadding="0" cellspacing="0" width="600" class="templateContainer">
                                                    <tr>
                                                        <td valign="top" class="headerContainer" style="padding-top:15px; padding-bottom:10px;"><table border="0" cellpadding="0" cellspacing="0"
width="100%" class="mcnImageBlock" style="min-width:100%;">
    <tbody class="mcnImageBlockOuter">
            <tr>
                <td valign="top" style="padding:9px" class="mcnImageBlockInner">
                    <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width:100%;">
                        <tbody><tr>
                            <td class="mcnImageContent" valign="top" style="padding-right: 9px; padding-left: 9px; padding-top: 0; padding-bottom: 0; text-align:center;">
                                
                                    
                                        <img align="center" alt="" src="http://www.villaroccaccia.com/wp-content/uploads/2017/03/cropped-logo2.jpg" width="155"
style="max-width:155px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                                    
                                
                            </td>
                        </tr>
                    </tbody></table>
                </td>
            </tr>
    </tbody>
</table></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- // END HEADER -->
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <!-- BEGIN BODY // -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
                                        <tr>
                                            <td align="center" valign="top" style="padding-top:10px; padding-right:10px; padding-bottom:5px; padding-left:10px;">
                                                <table border="0" cellpadding="0" cellspacing="0" width="600" class="templateContainer">
                                                    <tr>
                                                        <td align="center" valign="top">
                                                            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="bodyBackground">
                                                                <tr>
                                                                    <td valign="top" class="bodyContainer" style="padding-top:10px; padding-bottom:10px;"><table border="0" cellpadding="0"
cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
                
                <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
                        
                            
                        </td>
                    </tr>
                </tbody></table>
                
            </td>
        </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width:100%;">
    <tbody class="mcnImageBlockOuter">
            <tr>
                <td valign="top" style="padding:9px" class="mcnImageBlockInner">
                    <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width:100%;">
                        <tbody><tr>
                            <td class="mcnImageContent" valign="top" style="padding-right: 9px; padding-left: 9px; padding-top: 0; padding-bottom: 0; text-align:center;">
                                
                            </td>
                        </tr>
                    </tbody></table>
                </td>
            </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
                
                <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
                        
<div style="color: #222222;font-family: arial, sans-serif;font-size: small;line-height: normal;">
'.str_replace('é','&eacute;',str_replace('€','&euro;',str_replace('ë','&euml;',$tekstvoorboeker))).'
</div>
</div>
</div>

                        </td>
                    </tr>
                </tbody></table>
                
            </td>
        </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnButtonBlock" style="min-width:100%;">
    <tbody class="mcnButtonBlockOuter">
        <tr>
            <td style="padding-top:0; padding-right:18px; padding-bottom:18px; padding-left:18px;" valign="top" align="center" class="mcnButtonBlockInner">
            </td>
        </tr>
    </tbody>
</table></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- // END BODY -->
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top" style="padding-bottom:40px;">
                                </td>
                            </tr>
                        </table>
                        <!-- // END TEMPLATE -->
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>';
	
	vr_verstuur_mail(array($_POST['email'], 'villaroccaccia@gmail.com'), $onderwerp, $htmlvoorboeker, $tekstvoorboeker);

	//mailen aan villa roccaccia
	// Afzender is ons eigen adres; met "beantwoorden" ga je direct naar de klant
	$tekstvoorvilla = 'Er is een boeking ontvangen van '.$veilig['naam'].'<br /><br />

	Appartement: '.$veilig['huis'].'<br />
	Aankomst: '.$veilig['aankomstdatum'].'<br />
	Vertrek: '.$veilig['vertrekdatum'].'<br />
	Aantal dagen: '.$veilig['aantaldagen'].'<br />
	Aantal personen: '.$veilig['aantalpersonen'].'<br />
	Prijs € '.$veilig['prijs'].'<br /><br />';
	
	if(strtotime($_POST['aankomstdatum_engels']) >= strtotime("+28 day"))
	{
		$tekstvoorvilla .= 'Aanbetaling € '.$veilig['aanbetaling'].'<br />Restantbedrag € '.$veilig['restantbedrag'].'<br /><br />';
	}
	
	$tekstvoorvilla .= '
	<strong>Uw ingevoerde gegevens:</strong><br />
	Adres: '.$veilig['adres'].'<br />
	Plaats: '.$veilig['plaats'].'<br />
	Land: '.$veilig['land'].'<br />
	Telefoonnummer: '.$veilig['telefoon'].'<br /><br />';

	$htmlvoorvilla = '<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
    <head>
    	<!-- NAME: FLYER -->
        <!--[if gte mso 15]>
		<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
		</xml>
		<![endif]-->
		<meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>'.$onderwerp.'</title>
        
    <style type="text/css">
		p{
			margin:10px 0;
			padding:0;
		}
		table{
			border-collapse:collapse;
		}
		h1,h2,h3,h4,h5,h6{
			display:block;
			margin:0;
			padding:0;
		}
		img,a img{
			border:0;
			height:auto;
			outline:none;
			text-decoration:none;
		}
		body,#bodyTable,#bodyCell{
			height:100%;
			margin:0;
			padding:0;
			width:100%;
		}
		#outlook a{
			padding:0;
		}
		img{
			-ms-interpolation-mode:bicubic;
		}
		table{
			mso-table-lspace:0pt;
			mso-table-rspace:0pt;
		}
		.ReadMsgBody{
			width:100%;
		}
		.ExternalClass{
			width:100%;
		}
		p,a,li,td,blockquote{
			mso-line-height-rule:exactly;
		}
		a[href^=tel],a[href^=sms]{
			color:inherit;
			cursor:default;
			text-decoration:none;
		}
		p,a,li,td,body,table,blockquote{
			-ms-text-size-adjust:100%;
			-webkit-text-size-adjust:100%;
		}
		.ExternalClass,.ExternalClass p,.ExternalClass td,.ExternalClass div,.ExternalClass span,.ExternalClass font{
			line-height:100%;
		}
		a[x-apple-data-detectors]{
			color:inherit !important;
			text-decoration:none !important;
			font-size:inherit !important;
			font-family:inherit !important;
			font-weight:inherit !important;
			line-height:inherit !important;
		}
		a.mcnButton{
			display:block;
		}
		.mcnImage{
			vertical-align:bottom;
		}
		.mcnTextContent{
			word-break:break-word;
		}
		.mcnTextContent img{
			height:auto !important;
		}
		.mcnDividerBlock{
			table-layout:fixed !important;
		}
		body,#bodyTable{
			background-color:white;
		}
	
		#bodyCell{
			border-top:0;
		}
	
		h1{
			color:#a1cf03 !important;
			font-family:Helvetica;
			font-size:40px;
			font-style:normal;
			font-weight:bold;
			line-height:100%;
			letter-spacing:-1px;
			text-align:center;
		}
	
		h2{
			color:#404040 !important;
			font-family:Helvetica;
			font-size:26px;
			font-style:normal;
			font-weight:bold;
			line-height:125%;
			letter-spacing:-.75px;
			text-align:left;
		}
	
		h3{
			color:#a1cf03 !important;
			font-family:Helvetica;
			font-size:18px;
			font-style:normal;
			font-weight:bold;
			line-height:125%;
			letter-spacing:-.5px;
			text-align:left;
		}
	
		h4{
			color:#808080 !important;
			font-family:Helvetica;
			font-size:16px;
			font-style:normal;
			font-weight:bold;
			line-height:125%;
			letter-spacing:normal;
			text-align:left;
		}
	
		.headerContainer .mcnTextContent a{
			color:#a1cf03;
			font-weight:normal;
			text-decoration:underline;
		}
	
		#templateBody{
			background-color:#EEEEEE;
			border-top:0;
			border-bottom:0;
		}
	
		#bodyBackground{
			background-color:#FFFFFF;
			border:1px solid #D5D5D5;
		}
	
		.bodyContainer .mcnTextContent,.bodyContainer .mcnTextContent p{
			color:#606060;
			font-family:Helvetica;
			font-size:15px;
			line-height:150%;
			text-align:left;
		}
	
		.bodyContainer .mcnTextContent a{
			color:#a1cf03;
			font-weight:normal;
			text-decoration:underline;
		}
	
		#templateFooter{
			background-color:white;
			border-top:1px solid #000000;
			border-bottom:0;
		}
	
		.footerContainer .mcnTextContent,.footerContainer .mcnTextContent p{
			color:#CCCCCC;
			font-family:Helvetica;
			font-size:11px;
			line-height:125%;
			text-align:center;
		}
	
		.footerContainer .mcnTextContent a{
			color:#CCCCCC;
			font-weight:normal;
			text-decoration:underline;
		}
	@media only screen and (max-width: 480px){
		body,table,td,p,a,li,blockquote{
			-webkit-text-size-adjust:none !important;
		}

}	@media only screen and (max-width: 480px){
		body{
			width:100% !important;
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.templateContainer{
			max-width:600px !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImage{
			height:auto !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCaptionTopContent,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer{
			max-width:100% !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnBoxedTextContentContainer{
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupContent{
			padding:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCaptionLeftContentOuter .mcnTextContent,.mcnCaptionRightContentOuter .mcnTextContent{
			padding-top:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardTopImageContent,.mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{
			padding-top:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardBottomImageContent{
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockInner{
			padding-top:0 !important;
			padding-bottom:0 !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockOuter{
			padding-top:9px !important;
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnTextContent,.mcnBoxedTextContentColumn{
			padding-right:18px !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{
			padding-right:18px !important;
			padding-bottom:0 !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcpreview-image-uploader{
			display:none !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
	
		h1{
			font-size:24px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	
		h2{
			font-size:20px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	
		h3{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	
		h4{
			font-size:16px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	
		.mcnBoxedTextContentContainer .mcnTextContent,.mcnBoxedTextContentContainer .mcnTextContent p{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	
		#templatePreheader{
			display:block !important;
		}

}	@media only screen and (max-width: 480px){
	
		.preheaderContainer .mcnTextContent,.preheaderContainer .mcnTextContent p{
			font-size:14px !important;
			line-height:115% !important;
		}

}	@media only screen and (max-width: 480px){
	
		.headerContainer .mcnTextContent,.headerContainer .mcnTextContent p{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){

		.bodyContainer .mcnTextContent,.bodyContainer .mcnTextContent p{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	
		.footerContainer .mcnTextContent,.footerContainer .mcnTextContent p{
			font-size:14px !important;
			line-height:115% !important;
		}

}</style></head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
        <center>
            <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
                <tr>
                    <td align="center" valign="top" id="bodyCell">
                        <!-- BEGIN TEMPLATE // -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td align="center" valign="top">
                                    <!-- BEGIN PREHEADER // -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templatePreheader">
                                        <tr>
                                        	<td align="center" valign="top">
                                                <table border="0" cellpadding="0" cellspacing="0" width="600" class="templateContainer">
                                                    <tr>
                                                        <td valign="top" class="preheaderContainer" style="padding-top:10px; padding-bottom:10px;"><table border="0" cellpadding="0" cellspacing="0"
width="100%" class="mcnTextBlock" style="min-width:100%;">
                                                    </table></td>
                                                    </tr>
                                                </table>
                                            </td>                                            
                                        </tr>
                                    </table>
                                    <!-- // END PREHEADER -->
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <!-- BEGIN HEADER // -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateHeader">
                                        <tr>
                                            <td align="center" valign="top">
                                                <table border="0" cellpadding="0" cellspacing="0" width="600" class="templateContainer">
                                                    <tr>
                                                        <td valign="top" class="headerContainer" style="padding-top:15px; padding-bottom:10px;"><table border="0" cellpadding="0" cellspacing="0"
width="100%" class="mcnImageBlock" style="min-width:100%;">
    <tbody class="mcnImageBlockOuter">
            <tr>
                <td valign="top" style="padding:9px" class="mcnImageBlockInner">
                    <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width:100%;">
                        <tbody><tr>
                            <td class="mcnImageContent" valign="top" style="padding-right: 9px; padding-left: 9px; padding-top: 0; padding-bottom: 0; text-align:center;">
                                
                                    
                                        <img align="center" alt="" src="http://www.villaroccaccia.com/wp-content/uploads/2017/03/cropped-logo2.jpg" width="155"
style="max-width:155px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                                    
                                
                            </td>
                        </tr>
                    </tbody></table>
                </td>
            </tr>
    </tbody>
</table></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- // END HEADER -->
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <!-- BEGIN BODY // -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
                                        <tr>
                                            <td align="center" valign="top" style="padding-top:10px; padding-right:10px; padding-bottom:5px; padding-left:10px;">
                                                <table border="0" cellpadding="0" cellspacing="0" width="600" class="templateContainer">
                                                    <tr>
                                                        <td align="center" valign="top">
                                                            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="bodyBackground">
                                                                <tr>
                                                                    <td valign="top" class="bodyContainer" style="padding-top:10px; padding-bottom:10px;"><table border="0" cellpadding="0"
cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
                
                <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
                        
                            
                        </td>
                    </tr>
                </tbody></table>
                
            </td>
        </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width:100%;">
    <tbody class="mcnImageBlockOuter">
            <tr>
                <td valign="top" style="padding:9px" class="mcnImageBlockInner">
                    <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width:100%;">
                        <tbody><tr>
                            <td class="mcnImageContent" valign="top" style="padding-right: 9px; padding-left: 9px; padding-top: 0; padding-bottom: 0; text-align:center;">
                                
                            </td>
                        </tr>
                    </tbody></table>
                </td>
            </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
                
                <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
                        
<div style="color: #222222;font-family: arial, sans-serif;font-size: small;line-height: normal;">
'.str_replace('é','&eacute;',str_replace('€','&euro;',str_replace('ë','&euml;',$tekstvoorvilla))).'
</div>
</div>
</div>

                        </td>
                    </tr>
                </tbody></table>
                
            </td>
        </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnButtonBlock" style="min-width:100%;">
    <tbody class="mcnButtonBlockOuter">
        <tr>
            <td style="padding-top:0; padding-right:18px; padding-bottom:18px; padding-left:18px;" valign="top" align="center" class="mcnButtonBlockInner">
            </td>
        </tr>
    </tbody>
</table></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- // END BODY -->
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top" style="padding-bottom:40px;">
                                </td>
                            </tr>
                        </table>
                        <!-- // END TEMPLATE -->
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>';

	vr_verstuur_mail(array('info@villaroccaccia.com'), 'Boeking bij Villa Roccaccia', $htmlvoorvilla, $tekstvoorvilla, $_POST['email'], $_POST['naam']);
}
?>
 
 </div>
</body>
</html>
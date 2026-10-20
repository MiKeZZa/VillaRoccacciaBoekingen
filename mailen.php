<?php
// Gedeelde mailfunctie voor het boekingssysteem.
//
// De SMTP-gegevens staan NIET in deze code, maar in wp-config.php op de server:
//   define('VR_SMTP_HOST', 'mail.villaroccaccia.com');
//   define('VR_SMTP_USER', 'mailactie@villaroccaccia.com');
//   define('VR_SMTP_PASS', '...');
// Optioneel (alleen als de mailserver dat vraagt):
//   define('VR_SMTP_PORT', 587);        // standaard 25, net als voorheen
//   define('VR_SMTP_SECURE', 'tls');    // 'tls' (STARTTLS), 'ssl' (poort 465) of '' (geen)

require_once __DIR__.'/phpmailer/src/Exception.php';
require_once __DIR__.'/phpmailer/src/PHPMailer.php';
require_once __DIR__.'/phpmailer/src/SMTP.php';

define('VR_AFZENDER', 'info@villaroccaccia.com');
define('VR_AFZENDERNAAM', 'Villa Roccaccia');

// Maakt invoer veilig om in een HTML-mail of -pagina te tonen
function vr_html($waarde)
{
	if(is_array($waarde)) { return ''; }
	return htmlspecialchars(trim((string)$waarde), ENT_QUOTES, 'UTF-8');
}

// Verstuurt een HTML-mail. Geeft true terug als het gelukt is.
// Fouten worden in de PHP error log geschreven, zodat een mislukte mail
// nooit meer de hele pagina laat crashen en altijd terug te vinden is.
function vr_verstuur_mail($ontvangers, $onderwerp, $html, $tekst, $antwoordadres = '', $antwoordnaam = '')
{
	if(!defined('VR_SMTP_HOST') || !defined('VR_SMTP_USER') || !defined('VR_SMTP_PASS'))
	{
		error_log('Villa Roccaccia mail: VR_SMTP_HOST/VR_SMTP_USER/VR_SMTP_PASS ontbreken in wp-config.php');
		return false;
	}

	try
	{
		$mail = new PHPMailer\PHPMailer\PHPMailer(true);
		$mail->CharSet = 'UTF-8';

		$mail->isSMTP();
		$mail->Host = VR_SMTP_HOST;
		if(defined('VR_SMTP_PORT')) { $mail->Port = VR_SMTP_PORT; }
		$mail->SMTPAuth = true;
		$mail->Username = VR_SMTP_USER;
		$mail->Password = VR_SMTP_PASS;
		if(defined('VR_SMTP_SECURE'))
		{
			$mail->SMTPSecure = VR_SMTP_SECURE;
			$mail->SMTPAutoTLS = (VR_SMTP_SECURE !== '');
		}

		// Altijd versturen vanaf ons eigen adres, anders wordt de mail door
		// Gmail/Hotmail/etc. als vervalst gezien en geweigerd (SPF/DMARC)
		$mail->setFrom(VR_AFZENDER, VR_AFZENDERNAAM);
		$mail->Sender = VR_AFZENDER;
		if($antwoordadres <> '' && PHPMailer\PHPMailer\PHPMailer::validateAddress($antwoordadres))
		{
			$mail->addReplyTo($antwoordadres, $antwoordnaam);
		}
		else
		{
			$mail->addReplyTo(VR_AFZENDER, VR_AFZENDERNAAM);
		}

		// Een ongeldig adres van de klant mag de kopie aan ons niet tegenhouden
		$aantal = 0;
		foreach((array)$ontvangers as $ontvanger)
		{
			try
			{
				$mail->addAddress(trim($ontvanger));
				$aantal++;
			}
			catch(PHPMailer\PHPMailer\Exception $e)
			{
				error_log('Villa Roccaccia mail: ongeldig adres overgeslagen: '.$ontvanger);
			}
		}
		if($aantal == 0)
		{
			error_log('Villa Roccaccia mail: geen geldige ontvangers voor "'.$onderwerp.'"');
			return false;
		}

		$mail->isHTML(true);
		$mail->Subject = $onderwerp;
		$mail->Body = $html;
		$mail->AltBody = html_entity_decode(strip_tags(preg_replace('#<br\s*/?>#i', "\n", $tekst)), ENT_QUOTES, 'UTF-8');

		return $mail->send();
	}
	catch(Exception $e)
	{
		error_log('Villa Roccaccia mail: versturen van "'.$onderwerp.'" mislukt: '.$e->getMessage());
		return false;
	}
}

<?php
require_once 'includes/header.php';

$gebruiker = new Gebruiker();

if(!$gebruiker->isLoggedIn()) {
	Redirect::to('index.php');
}

$link = Hash::unique();
$uri = 'http://iproject9.icasites.nl/verify.php?link=' . $link . '&upgrade=true';

$mailExists = database::getInstance()->get('Mail', array('email', '=', $gebruiker->gegevens()->mailbox));
			
if($mailExists->count() == 0) {
	Database::getInstance()->insert('Mail', array(
	'email' 	=> $gebruiker->gegevens()->mailbox,
	'link'		=> $link,
	'used'		=> 0 
	));
} else {
	database::getInstance()->update('Mail', 'email', $gebruiker->gegevens()->mailbox, array('link' => $link, 'used' => 0));
}

$isMailSent = false;

$to			= $gebruiker->gegevens()->mailbox;
$subject 	= 'Aanvraag verkoper EenmaalAndermaal';

$headers = "From: noreply@eenmaalandermaal.nl\r\n";
$headers .= "Reply-To: noreply@eenmaalandermaal.nl\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

$message = '<html><body>';
$message .= '<h3>Aanvraag verkoopaccount</h3>';
$message .= '<p>Beste ' . $gebruiker->gegevens()->voornaam . '</p>';
$message .= '<p>Met dit e-mail adres is een aanvraag gedaan voor het upgraden naar een verkoopaccount op EenmaalAndermaal. Bent u dit niet? Dan kunt u deze mail negeren en worden er geen verdere stappen ondernomen.</p>';
$message .= '<p>Om verder te gaan met upgraden gaat u naar de volgende link:</p>';
$message .= '<p><a href="' . $uri . '">Verder met upgraden naar verkoper</a></p>';
$message .= '<p>Werkt de link niet? Plan dan de volgende link in uw adresbalk:</p>';
$message .= '<p>' . $uri . '</p>';
$message .= '<p>Met vriendelijke groet,</br>Het EenmaalAndermaal team!</p>';
$message .= '</body></html>';

if(mail($to, $subject, $message, $headers)) {
	$isMailSent = true;
}
?>

<h1>Aanvraag verkoopaccount</h1>
<h3 class="text-success">Stap 1 van 2</h3>

<?php
if($isMailSent) {
	alert(false, 'success', 'Mail verzenden:', 'De e-mail is met success verzonden.');
}
?>

<div class="aanvraag-verkoop">
  <h4>Om het process voor aanvraag verkoopaccount aan te gaan is er een link gestuurd naar uw e-mailadres:</h4>
  <b><?= $gebruiker->gegevens()->mailbox; ?></b>
  <!-- Heeft u geen e-mail ontvangen? <button class="btn btn-warning">Stuur link opnieuw</button></h4> -->
</div>

<?php require_once "includes/footer.php"; ?>
<?php
include 'includes/header.php';
require_once 'core/init.php';

$isMailed = false;

if(Input::exists()) {
	if(Token::check(Input::get('token'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'mailbox' => array(
				'required' 		=> true,
				'ISO-8859-1'	=> true,
				'valid_email' 	=> true
				)
			));
		
		if($validation->passed()) {
			$link = Hash::unique();
			$uri = 'http://iproject9.icasites.nl/verify.php?link=' . $link . '&email=' . Input::get('mailbox');
			
			$mailExists = database::getInstance()->get('Mail', array('email', '=', Input::get('mailbox')));
			
			if($mailExists->count() == 0) {
				Database::getInstance()->insert('Mail', array(
				'email' 	=> Input::get('mailbox'),
				'link'		=> $link,
				'used'		=> 0 
				));
			} else {
				//sessie error toevoegen, insert kwam false terug
				Session::put('error', 'Er is geen mail geactiveerd.');
				database::getInstance()->update('Mail', 'email', Input::get('mailbox'), array('link' => $link, 'used' => 0));
			}

			$headers = "From: noreply@eenmaalandermaal.nl\r\n";
			$headers .= "Reply-To: noreply@eenmaalandermaal.nl\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$to = Input::get('mailbox');
			$subject = 'Activeer uw EenmaalAndermaal account';

			$message = '<html><body>';
			$message .= '<h3>Registreren bij EenmaalAndermaal</h3>';
			$message .= '<p>Beste meneer/mevrouw,</p>';
			$message .= '<p>Bedankt voor het registreren bij EenmaalAndermaal, u kunt nu uw account gaan maken.</p>';
			$message .= '<p>Om verder te gaan met registreren gaat u naar de volgende link:</p>';
			$message .= '<p><a href="' . $uri . '">Verder met registreren</a></p>';
			$message .= '<p>Werkt de link niet? Plan dan de volgende link in uw adresbalk:</p>';
			$message .= '<p>' . $uri . '</p>';
			$message .= '<p>Met vriendelijke groet,</br>Het EenmaalAndermaal team!</p>';
			$message .= '</body></html>';
			
			if(mail($to, $subject, $message, $headers)) {
				$isMailed = true;
				//verwijzen naar een pagina, check uw email
			} else {
				echo 'Er is iets fout gegaan met het versturen van de mail, probeer het nogmaals.';
				//sessie error toevoegen dat er iets fout gegaan is met de mail
			}
		} else {
			Session::put('errors', $validation->getErrors());
		}
	}
}
?>
<div class="row">
	<div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
		<div class="jumbotron account-content">
			<?php
			if($isMailed) {
				echo "<h4>Er is een mail verzonden naar <b>" . Input::get('mailbox') . "</b></h4>";
			} else {
				?>
				<h3>Geef een geldig e-mailadres op</h3>
				<p style="font-size:16px;">Naar dit e-mailadres wordt zal een unieke link verstuurd worden waarmee u verder kan registreren</p>
				<form name="email-form" method="post">
				    <p class="text-danger"><?php require_once 'core/init.php'; if(Session::exists('errors')) {foreach(Session::get('errors') as $error) {echo $error, '<br>';}} Session::delete('errors'); ?></p>
			        <div class="form-group">
			          	<label>Uw email adres</label>
			          	<input type="text" class="form-control" name="mailbox" required autofocus>
			        </div>
			        <div class="form-gap-fill"></div>
			        <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
			      	<input type="submit" value="Stuur mail" class="btn btn-warning pull-right">
			        <div class="clearfix"></div>
				</form>
				<?php
			}
			?>
		</div>
	</div>
</div>
<?php include 'includes/footer.php';
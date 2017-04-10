<?php
include 'includes/header.php';
require_once 'core/init.php';

if(!$gebruiker->isLoggedIn()) {
	Redirect::to('index.php');
}

if(Input::exists()) {
	if(Token::check(Input::get('token'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'wachtwoord_huidig' => array(
	  			'required'  		=> true,
	  			'min'   			=> 7,
	  			'max' 				=> 128,
	  			'ISO-8859-1'		=> true,
	  			'hasletter&number'	=> true
				),
			'wachtwoord_nieuw' => array(
	  			'required'  		=> true,
	  			'min'   			=> 7,
	  			'max' 				=> 128,
	  			'ISO-8859-1'		=> true,
	  			'hasletter&number'	=> true
				),
			'wachtwoord_nieuw_again' => array(	
	  			'matches'   		=> 'wachtwoord_nieuw',
	  			'required' 			=> true,
	  			'min' 				=> 7,
	  			'max' 				=> 128,
	  			'ISO-8859-1'		=> true,
	 			'hasletter&number'	=> true
				)
			));

		if($validation->passed()) {
			if(Hash::make(Input::get('wachtwoord_huidig'), $gebruiker->gegevens()->salt) == $gebruiker->gegevens()->wachtwoord) {
				$salt = Hash::salt(32);
				$gebruiker->update(array(
					'wachtwoord' => Hash::make(Input::get('wachtwoord_nieuw'), $salt),
					'salt' => $salt
					));
				Redirect::to('accountpagina.php');
			} else {
				echo 'Huidige wachtwoord is fout.'; //validation error toevoegen?
			}
		} else {
			foreach($validation->getErrors() as $error) { //in sessie stoppen
				echo $error, '<br>';
			}
		}
	}
}
?>
<form name="wachtwoord-aanpassen-form" method="post">
    <p class="text-danger"><?php require_once 'core/init.php'; if(Session::exists('errors')) {foreach(Session::get('errors') as $error) {echo $error, '<br>';}} Session::delete('errors'); ?></p>
    <div class="form-group"> 
        <label>Huidig wachtwoord</label>
      	<input type="password" class="form-control" name="wachtwoord_huidig" autofocus required>
    </div>
    <div class="form-gap-fill"></div>
	<div class="form-group">
        <label>Nieuw wachtwoord</label>
      	<input type="password" class="form-control" name="wachtwoord_nieuw" required>
    </div>
	<div class="form-group">
        <label>Herhaal nieuw wachtwoord</label>
      	<input type="password" class="form-control" name="wachtwoord_nieuw_again" required>
    </div>
  	<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
  	<a href="accountpagina.php" class="btn btn-warning">Annuleer</a>
  	<input type="submit" value="Opslaan" class="btn btn-warning pull-right">
</form>

<?php include 'includes/footer.php'; ?>
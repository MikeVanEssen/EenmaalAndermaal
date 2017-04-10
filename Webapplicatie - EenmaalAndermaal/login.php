<?php
include "includes/header.php"; 
require_once 'core/init.php';

if(Input::exists('post')) {
	if(Token::check(Input::get('token'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'gebruikersnaam' => array(
			  'required'  	=> true,
			  'min'   		=> 2,
			  'max'     	=> 20,
			  'ISO-8859-1'	=> true,
				),
			'wachtwoord' => array(
			  'required'  			=> true,
			  'min'   				=> 7,
			  'max' 				=> 128,
			  'ISO-8859-1'			=> true,
			  'hasletter&number'	=> true
				)
			));

		if($validation->passed()) {
			$gebruiker = new Gebruiker();
			$login = $gebruiker->login(Input::get('gebruikersnaam'), Input::get('wachtwoord'));

			if($login) {
				if(Session::exists('previous_page')) {
					Redirect::to(Session::get('previous_page'));
				}
				Redirect::to('login.php');
			} else {
				echo "Ongeldig gebruikersaccount.</br>";
			}
		} else {
			Session::put('errors', $validation->getErrors());
		}
	}
}
?>

<h1>Inloggen</h1>

<div class="row">
	<div class="col-lg-4 col-lg-offset-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Inloggen op EenmaalAndermaal</h3>
			</div>
			<div class="panel-body">
				<form name="login-form" method="post">
					<p class="text-danger">
						<?php
						require_once 'core/init.php';
						if(Session::exists('errors')) {
							foreach(Session::get('errors') as $error) {
								echo $error, '<br>';}
							}
						Session::delete('errors');
						echo Session::flash('succeslogin');
						?>

					</p>
					<div class="form-group">
						<input type="text" class="form-control" name="gebruikersnaam" placeholder="Gebruikersnaam" value="<?php echo Input::get('gebruikersnaam'); ?>" autofocus required>
					</div>
					<div class="form-group">
						<input type="password" class="form-control" name="wachtwoord" placeholder="Wachtwoord" required>
					</div>
					<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
					<input type="submit" value="Log in" class="btn btn-warning pull-right">
					<p class="help-block">Wachtwoord vergeten? Neem contact op met EenmaalAndermaal op: info@eenmaalandermaal.com</p>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php include "includes/footer.php"; ?>
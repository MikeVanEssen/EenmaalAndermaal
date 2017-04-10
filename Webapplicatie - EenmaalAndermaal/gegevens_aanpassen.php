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
			'voornaam' => array(
				'required'			=> true,
				'min'				=> 2,
				'max'				=> 20,
				'ISO-8859-1'		=> true,
				'alphachars_only'	=> true
				),
			'tussenvoegsel' => array(
				'min'				=> 1,
				'max'				=> 15,
				'ISO-8859-1'		=> true,
				'alphachars_only'	=> true
				),
			'achternaam' => array(
				'required' => true,
				'min' => 2,
				'max' => 20,
				'ISO-8859-1'		=> true,
				'alphachars_only'	=> true
				),
			'geboorte_dag' => array(
				'required'		=> true,
				'min'			=> 10,
				'ISO-8859-1'	=> true,
				'date'			=> true,
				'18jaar'		=> true
				),
			'adresregel1' => array(
				'required'			=> true,
				'min'				=> 2,
				'max'				=> 50,
				'ISO-8859-1'		=> true,
				'hasletter&number'	=> true
				),
			'adresregel2' => array(
				'min'				=> 2,
				'max'				=> 50,
				'ISO-8859-1'		=> true,
				'hasletter&number'	=> true
				),
			'postcode' => array(
				'required' 			=> true,
				'postcode'			=> true,
				'hasletter&number'	=> true,
				'ISO-8859-1'		=> true
				),
			'plaatsnaam' => array(
				'required'			=> true,
				'min'				=> 2,
				'max'				=> 50,
				'ISO-8859-1'		=> true,
				'alphachars_only'	=> true
				),
			'land' => array(
				'required' 			=> true,
				'min'				=> 3,
				'max'				=> 32,
				'ISO-8859-1'		=> true,
				'alphachars_only'	=> true
				),
			'telefoonnummer' => array(
				'required'		=> true,
				'max'			=> 15,
				'ISO-8859-1'	=> true,
				'vast_nummer'	=> true
				),
			'mobielnummer' => array(
				'max'			=> 15,
				'ISO-8859-1'	=> true,
				'mobiel_nummer'	=> true
				)
			));
		if($validation->passed()) {
			$dob = date('Y-m-d', strtotime(Input::get('geboorte_dag')));
			try {
				$gebruiker->update(array(
					'voornaam' 		=> Input::get('voornaam'),
					'tussenvoegsel' => Input::get('tussenvoegsel'),
					'achternaam' 	=> Input::get('achternaam'),
					'geboorte_dag' 	=> $dob,
					'adresregel1' 	=> Input::get('adresregel1'),
					'adresregel2' 	=> Input::get('adresregel2'),
					'postcode' 		=> Input::get('postcode'),
					'plaatsnaam' 	=> Input::get('plaatsnaam'),
					'land' 			=> Input::get('land'),
				));
				$gebruiker->updateTelefoon(array(
					'telefoonnummer'	=> Input::get('telefoonnummer'),
					'mobielnummer'		=> Input::get('mobielnummer')
					));
				Redirect::to('accountpagina.php');
			} catch(Exception $e) {
				die($e->getMessage());
			}
		} else {
			Session::put('errors', $validation->getErrors());
		}
	}
}
?>
<form name="persoonsgegevens-form" method="post">
  	<p class="text-danger"><?php require_once 'core/init.php'; if(Session::exists('errors')) {foreach(Session::get('errors') as $error) {echo $error, '<br>';}} Session::delete('errors'); ?></p>
 	<div class="row account-row">
    	<div class="jumbotron account-content col-lg-5 col-md-5 col-sm-12">
            <h4>Persoonsgegevens</h4>
			<div class="form-group">
                <label>Voornaam</label>
                <input type="text" class="form-control" name="voornaam" value="<?php echo escape($gebruiker->gegevens()->voornaam); ?>" required>
          	</div>
          	<div class="form-group">
                <label>Tussenvoegsel</label>
                <input type="text" class="form-control" name="tussenvoegsel" value="<?php echo escape($gebruiker->gegevens()->tussenvoegsel); ?>">
            </div>
            <div class="form-group">
                <label>Achternaam</label>
                <input type="text" class="form-control" name="achternaam" value="<?php echo escape($gebruiker->gegevens()->achternaam); ?>" required>
            </div>
          	<div class="form-group">
                <label>Geboortedatum (dd-mm-jjjj)</label>
                <input type="text" class="form-control" name="geboorte_dag" value="<?php $data = explode('-', escape($gebruiker->gegevens()->geboorte_dag)); echo $data[2] . "-" . $data[1] . "-" . $data[0] ?>" required>
          	</div>
          	<div class="form-group">
				<label>Telefoonnummer</label>
				<input type="text" class="form-control" name="telefoonnummer" value="<?php echo escape($gebruiker->getTelefoonnummer()); ?>">
			</div>
			<div class="form-group">
				<label>Mobiel nummer</label>
				<input type="text" class="form-control" name="mobielnummer" value="<?php echo escape($gebruiker->getMobielnummer()); ?>">
			</div>
          	<div class="form-gap-fill"></div>
      	</div>
      	<div class="jumbotron account-content col-lg-5 col-lg-offset-1 col-md-5 col-md-offset-1 col-sm-12">
            <h4>Adresgegevens</h4>
            <div class="form-group">
                <label>Adres</label>
                <input type="text" class="form-control" name="adresregel1" value="<?php echo escape($gebruiker->gegevens()->adresregel1); ?>" required>
                <input type="text" class="form-control" name="adresregel2" value="<?php echo escape($gebruiker->gegevens()->adresregel2); ?>">
            </div>
            <div class="form-group">
                <label>Postcode</label>
                <input type="text" class="form-control" name="postcode" value="<?php echo escape($gebruiker->gegevens()->postcode); ?>" required>
            </div>
            <div class="form-group">
                <label>Plaatsnaam</label>
                <input type="text" class="form-control" name="plaatsnaam" value="<?php echo escape($gebruiker->gegevens()->plaatsnaam); ?>" required>
            </div>
            <div class="form-group">
                <label>Land</label>
                <input type="text" class="form-control" name="land" value="<?php echo escape($gebruiker->gegevens()->land); ?>" required>
          	</div>
          	<div class="form-gap-fill"></div>
          	<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
          	<input type="submit" value="Opslaan" class="btn btn-warning pull-right">
          	<div class="clearfix"></div>
     	</div>
  	</div>
</form>
<?php include 'includes/footer.php'; ?>
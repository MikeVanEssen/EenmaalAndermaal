<?php
include 'includes/header.php';

$gebruiker = new Gebruiker();

if(!$gebruiker->isLoggedIn()) {
	Redirect::to('index.php');
}

if(Input::exists()) {
	if(is_null(Input::get('bankrekening')) == false && is_null(Input::get('banknaam')) == false && strlen(Input::get('creditcardnummer')) == 0) {
		//bankrekening en banknaam valideren
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'betalingswijze'=> array(
				'required'  => true
				),
			'bankrekening'  => array(
				'required'  => true,
				'min'       => 5,
				'max'       => 34,
				'iban'		=> true
				),
			'banknaam'		=> array(
				'required'	=> true,
				'min'		=> 2,
				'max'		=> 50
				)
			));
		if($validation->passed()) {
			try {
				Database::getInstance()->update('Gebruiker', 'gebruikersnaam', $gebruiker->gegevens()->gebruikersnaam, array('soort_gebruiker' => 'Verkoper'));
				Database::getInstance()->insert('Verkoper', array(
					'gebruikersnaam' => $gebruiker->gegevens()->gebruikersnaam,
					'betalingswijze' => Input::get('betalingswijze'),
					'banknaam' => Input::get('banknaam'),
					'bankrekening'	=> Input::get('bankrekening')
					));
				Redirect::to('verkoper-voltooid.php');
			} catch(Exception $e) {
				die($e->getMessage());
			}
		} else {
			Session::put('errors', $validation->getErrors());
		}
	} else if(strlen(Input::get('bankrekening')) == 0 && strlen(Input::get('banknaam')) == 0 && is_null(Input::get('creditcardnummer')) == false) {
		//creditcardnummer valideren
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'betalingswijze'=> array(
				'required'  => true
				),
			'creditcardnummer'  => array(
				'required'  => true,
				'min'       => 5,
				'max'       => 19
				)
			));
		if($validation->passed()) {
			try {
				Database::getInstance()->update('Gebruiker', 'gebruikersnaam', $gebruiker->gegevens()->gebruikersnaam, array('soort_gebruiker' => 'Verkoper'));
				Database::getInstance()->insert('Verkoper', array(
					'gebruikersnaam' => $gebruiker->gegevens()->gebruikersnaam,
					'betalingswijze' => Input::get('betalingswijze'),
					'creditcardnummer' => Input::get('creditcardnummer')
					));
				Redirect::to('verkoper-voltooid.php');
			} catch(Exception $e) {
				die($e->getMessage());
			}
		} else {
			Session::put('errors', $validation->getErrors());
		}
	} else {
		$validate = new Validate();
		$validate->addError("Voer een betalingswijze in.");
		var_dump(Input::get('creditcardnummer'));
		die('moet true zijn');
	}
}
?>
<h1>Aanvraag verkoopaccount</h1>
<h3 class="text-success">Stap 2 van 2</h3>
<div class="aanvraag-verkoop row">
  <h4>Uw e-mailadres is geverifieerd. Kies nu een betalingswijze om uw aanvraag af te ronden.</h4>
	<?php
	if(Session::exists('errors')) {
		$errors = Session::get('errors');
		echo '<div class="col-lg-8 col-md-10">';
		alert(false, 'danger', 'Foutmeldingen:', $errors);
		echo '</div>';
	}
	?>
	<form id="registreer-verkoper-form" action="#" method="post" class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
		<div class="input-group input-group-lg">
			<span class="input-group-addon">
				<input type="radio" value="Bank" id="bank" name="betalingswijze" aria-label="..." required>
			</span>
			<label for="bank" class="form-control">Bank</label>
		</div><!-- /input-group -->
		<div class="input-group input-group-lg">
			<span class="input-group-addon">
				<input type="radio" value="Creditcard" id="creditcard" name="betalingswijze" aria-label="..." required>
			</span>
			<label for="creditcard" class="form-control">Creditcard</label>
		</div><!-- /input-group -->
		<br/>
		<div id='bank-info' class="row" style="display:none">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<label>Banknaam</label>
				<input type="text" class="form-control" id="banknaam" name="banknaam" required>              
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">    
				<label>Bankrekening</label>
				<input type="text" class="form-control" id="bankrekening" name="bankrekening" required>    
			</div>
		</div>
		<div id="credit-info" style="display:none">
			<div class="input-group">
				<label>Creditcardnummer</label>
				<input type="text" class="form-control" id="creditcardnummer" name="creditcardnummer" required>
			</div>
		</div>
		<br/>
		<input type="submit" class="btn btn-warning" value="Voltooi">
	</form>
</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script>
	$(document).ready(function() {
		$('input[type="radio"]').click(function() {
			if($(this).attr('id') == 'bank') {
				$('#bank-info').show();
				$('#banknaam').prop('required',true);
				$('#bankrekening').prop('required',true);
				 $('#credit-info').hide();  
				$('#creditcardnummer').prop('required',false);
			}
			else if($(this).attr('id') == 'creditcard') {
				$('#credit-info').show(); 
				$('#creditcardnummer').prop('required',true);
				$('#bank-info').hide();
				$('#banknaam').prop('required',false);
				$('#bankrekening').prop('required',false);          
			} else {
				$('#credit-info').hide();  
				$('#creditcardnummer').prop('required',false);
				$('#bank-info').hide();
				$('#banknaam').prop('required',false);
				$('#bankrekening').prop('required',false);
			}
		});
	});
</script>
<?php include 'includes/footer.php'; ?>
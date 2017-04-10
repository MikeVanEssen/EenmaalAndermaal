<?php
include "includes/header.php";

$gebruiker = new Gebruiker();

if(!$gebruiker->isLoggedIn()) {
  Redirect::to('index.php');
}


$getRubrieken = database::getInstance()->query("SELECT * FROM Rubriek ORDER BY rubrieknaam");

if(Input::exists()) {
	$veilingData = array(
		'titel' 				=> Input::get('titel'),
		'beschrijving' 			=> Input::get('beschrijving'),
		'start_prijs' 			=> Input::get('start_prijs'),
		'betalingswijze' 		=> Input::get('betalingswijze'),
		'betalings_instructie' 	=> Input::get('betalings_instructie'),
		'looptijd' 				=> Input::get('looptijd'),
		'verzendkosten' 		=> Input::get('verzendkosten'),
		'verzend_instructies' 	=> Input::get('verzend_instructies'),
		'rubriek'				=> Input::get('rubriek')
	);

	Session::put('veilingData', $veilingData);

	$validate = new Validate();
	$validate->check($_POST, array(
		'titel' => array(
			'required' => true,
			'min' => 2,
			'max' => 200
		),
		'beschrijving' => array(
			'required' => true,
			'min' => 2,
			'max' => 500
		),
		'start_prijs' => array(
			'required' => true,
			'numeric' => true,
			'max' => 9
		),
		'betalingswijze' => array(
			'required' => true,
			'max' => 10
		),
		'betalings_instructie' => array(
			'required' => true,
			'max' => 500
		),
		'verzendkosten' => array(
			'required' => true,
			'numeric' => true,
			'max' => 3
		),
		'verzend_instructies' => array(
			'required' => true,
			'max' => 500
		),
	));

	if($validate->passed()) {

		header('Location: veiling-bevestigen.php');
	} else {
		$errorMessage = $validate->getErrors();
	}
}

?>

<h1>Veiling plaatsen</h1>

<div class="row">
	<?php
	if(isset($errorMessage)) {
		echo '<div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">';
		alert(false, 'danger', 'Foutmeldingen:', $errorMessage);
		echo '</div>';
	}
	?>
	<form name="accountgegevens-form" class="jumbotron col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1" action="#" method="post">
	  	<div class="form-group"> 
	  		<label>Titel</label>
	  		<input type="text" class="form-control" name="titel" value="<?php if(Session::exists('veilingData')) { echo Session::get('veilingData')['titel']; } ?>" maxlength="200" autofocus required>
	  	</div>
	  	<div class="form-group">
	  		<label>Beschrijving</label>
	  		<textarea name="beschrijving" id="beschrijving" class="form-control" maxlength="500" onkeyup="textCounter(this,'counter-beschrijving',500);" required><?php if(Session::exists('veilingData')) { echo Session::get('veilingData')['beschrijving']; } ?></textarea>
	  		<input class="pull-right" disabled  maxlength="3" size="3" value="500" id="counter-beschrijving">
	  	</div>
	  	<div class="form-group">
	  		<label>Rubriek</label>
	  		<select name="rubriek" class="form-control" required>
	  			<?php
	  			foreach ($getRubrieken->results() as $rubriek) {
	  				if(Session::get('veilingData')['rubriek'] == $rubriek->rubrieknummer) {
	  					$selected = 'selected';
	  				} else {
	  					$selected = '';
	  				}
	  				echo '<option ' . $selected . ' value="' . $rubriek->rubrieknummer . '">' . $rubriek->rubrieknaam . '</option>';
	  			}
	  			?>
	  		</select>
	  	</div>
	  	<div class="form-group">
	  		<label>Start prijs</label>
	  		<input type="text" class="form-control" name="start_prijs" value="<?php if(Session::exists('veilingData')) { echo Session::get('veilingData')['start_prijs']; } ?>" maxlength="8" required>
	  	</div>
	  	<div class="form-group">
	  		<label>Betalingswijze</label>
	  		<select class="form-control" name="betalingswijze" required>
	  			<option <?php if (Session::exists('veilingData') && Session::get('veilingData')['betalingswijze'] == 'bank' ) echo 'selected' ; ?> value="bank">Bank</option>
	  			<option <?php if (Session::exists('veilingData') && Session::get('veilingData')['betalingswijze'] == 'creditcard' ) echo 'selected' ; ?> value="creditcard">Creditcard</option>
	  		</select>
	  	</div>
	  	<div class="form-group">
	  		<label>Betalings instructie</label>
	  		<textarea class="form-control" name="betalings_instructie" maxlength="500" onkeyup="textCounter(this,'counter-betaling',500);" required><?php if(Session::exists('veilingData')) { echo Session::get('veilingData')['betalings_instructie']; } ?></textarea>
	  		<input class="pull-right" disabled  maxlength="3" size="3" value="500" id="counter-betaling">
	  	</div>
	  	<div class="form-group">
	  		<label>Looptijd</label>
	  		<select class="form-control" name="looptijd" required>
	  			<option <?php if (Session::exists('veilingData') && Session::get('veilingData')['looptijd'] == '1' ) echo 'selected' ; ?> value="1">1 dag</option>
	  			<option <?php if (Session::exists('veilingData') && Session::get('veilingData')['looptijd'] == '3' ) echo 'selected' ; ?> value="3">3 dagen</option>
	  			<option <?php if (Session::exists('veilingData') && Session::get('veilingData')['looptijd'] == '7' ) echo 'selected' ; ?> value="7">7 dagen</option>
	  			<option <?php if (Session::exists('veilingData') && Session::get('veilingData')['looptijd'] == '10' ) echo 'selected' ; ?> value="10">10 dagen</option>
	  		</select>
	  	</div>
	  	<div class="form-group">
	  		<label>Verzendkosten</label>
	  		<input type="text" class="form-control" name="verzendkosten" maxlength="5" value="<?php if(Session::exists('veilingData')) { echo Session::get('veilingData')['verzendkosten']; } ?>" required>
	  	</div>
	  	<div class="form-group">
	  		<label>Verzend instructies</label>
	  		<textarea class="form-control" name="verzend_instructies" maxlength="500" onkeyup="textCounter(this,'counter-verzend',500);" required><?php if(Session::exists('veilingData')) { echo Session::get('veilingData')['verzend_instructies']; } ?></textarea>
	  		<input class="pull-right" disabled  maxlength="3" size="3" value="500" id="counter-verzend">
	  	</div>
	  	<div class="form-group">
	  		<p>Ga voor het uploaden van een afbeelding naar de volgende pagina.</p>
	  	</div>
	  	<a href="veiling-annuleren.php" class="btn btn-warning">Annuleer</a>
	  	<button type="submit" class="btn btn-warning pull-right" name="plaatsen">Verder</button>
	  	<div class="clearfix"></div>
	</form>
</div>

<script>
function textCounter(field,field2,maxlimit)
{
 var countfield = document.getElementById(field2);
 if ( field.value.length > maxlimit ) {
  field.value = field.value.substring( 0, maxlimit );
  return false;
 } else {
  countfield.value = maxlimit - field.value.length;
 }
}
</script>

<?php include "includes/footer.php"; ?>
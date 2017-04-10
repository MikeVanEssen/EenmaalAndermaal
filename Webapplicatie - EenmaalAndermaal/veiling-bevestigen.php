<?php

include "includes/header.php";

$gebruiker = new Gebruiker();

if(!$gebruiker->isLoggedIn() || !$gebruiker->heeftToestemming('Verkoper')) {
	Redirect::to('index.php');
}

if(isset($_POST['submit'])) {
	$insert = database::getInstance()->insert('Voorwerp', array(
		'titel' 				=> Session::get('veilingData')['titel'],
		'beschrijving' 			=> Session::get('veilingData')['beschrijving'],
		'start_prijs' 			=> Session::get('veilingData')['start_prijs'],
		'betalingswijze' 		=> Session::get('veilingData')['betalingswijze'],
		'betalings_instructie' 	=> Session::get('veilingData')['betalings_instructie'],
		'plaatsnaam' 			=> $gebruiker->gegevens()->plaatsnaam,
		'land' 					=> $gebruiker->gegevens()->land,
		'looptijd' 				=> Session::get('veilingData')['looptijd'],
		'verzendkosten' 		=> Session::get('veilingData')['verzendkosten'],
		'verzend_instructies' 	=> Session::get('veilingData')['verzend_instructies'],
		'verkoper' 				=> $gebruiker->gegevens()->gebruikersnaam
	));

	$bestand = $_FILES['bestand'];
	$bestandsnaam = Session::get('veilingData')['titel'] . date('dmY') . rand(1000,100000);
	$uploads_map = 'uploads/';

	//die(var_dump($bestand));

	if(!database::getInstance()->error()) {
		$uploadedFile = uploadFile($bestand, $bestandsnaam, $uploads_map);
		if ($uploadedFile) {
			$lastVeiling = database::getInstance()->query("SELECT * FROM Voorwerp WHERE titel = '" . Session::get('veilingData')['titel'] . "' AND verkoper = '" . $gebruiker->gegevens()->gebruikersnaam . "' ORDER BY looptijd_begin DESC");
			$lastVeiling = $lastVeiling->first();

			$bestand = database::getInstance()->insert('Bestand', array(
				'filenaam' 			=> $uploadedFile,
				'voorwerpnummer' 	=> $lastVeiling->voorwerpnummer
			));
			if(database::getInstance()->error()) {
				die('Bestand gefaald');
			}
			$insertRubriek = database::getInstance()->insert('Voorwerp_In_Rubriek', array(
				'voorwerpnummer' 			=> $lastVeiling->voorwerpnummer,
				'rubriek_op_laagste_niveau' => Session::get('veilingData')['rubriek']
			));
			if(!database::getInstance()->error()){
				header('Location: accountpagina.php?geplaatst=true');
			} else {
				die('Rubriek gefaald');
			}
		}
	} else {
		alert(false, 'danger', 'Errors:', 'Fout met het invoeren van product.');
	}
}
?>

<div class="row">
	<div class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-12">
		<div class="jumbotron">
			<h3>Weet u zeker dat de gegevens van deze veiling kloppen?</h3>
			<br>
			<table class="table table-striped">
				<tr>
					<th>Veld</th>
					<th>Uw input</th>
				</tr>
				<?php
				foreach (Session::get('veilingData') as $key => $value) {
					if($key == 'bestand') {
						$value = $value['name'];
					}
					if($key == 'rubriek') {
						$getRubrieknaam = database::getInstance()->get('Rubriek', array('rubrieknummer', '=', $value));
						$value = $getRubrieknaam->first()->rubrieknaam;
					}
					echo "<tr>";
					echo "<td>" . $key . "</td>";
					echo "<td>" . $value . "</td>";
					echo "</tr>";
				}
				?>
			</table>
			<form action="#" method="post"  enctype="multipart/form-data">
				<div class="form-group">
			    	<label for="inputFile">Afbeelding</label>
			    	<input type="file" name="bestand" id="inputFile" required>
			  	</div>
				<a href="veiling-plaatsen.php" class="btn btn-warning">Terug</a>
				<input type="submit" name="submit" value="Plaatsen" class="btn btn-warning pull-right">
			</form>
		</div>
	</div>
</div>

<?php
include "includes/footer.php";
?>
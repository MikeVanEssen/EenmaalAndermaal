<?php
include "includes/header.php";
$gebruiker = new Gebruiker();

if(!$gebruiker->isLoggedIn()) {
  Redirect::to('index.php');
}


if(!$gebruiker->heeftToestemming('Verkoper')){
	header('Location: accountpagina.php');
}
?>

<h1>Aanvraag verkoopaccount</h1>
<h3 class="text-success">Voltooid</h3>

<div class="aanvraag-verkoop">
	<h4>Uw betaling is geslaagd, en uw account is omgezet tot verkoopaccount!</h4>
	<h4>Meteen beginnen met het plaatsen van een veiling? <a href="veiling-plaatsen.php" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Plaats veiling</a></h4>
</div>

<?php include "includes/footer.php"; ?>
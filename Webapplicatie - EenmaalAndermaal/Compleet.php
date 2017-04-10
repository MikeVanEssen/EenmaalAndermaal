<?php
include "includes/header.php";
Session::delete('verified');
Session::delete('errors');
?>
<h1>Registratie Compleet</h1>
<div class="text-center row account-row">
	<div class="jumbotron account-content col-lg-12 col-md-12 col-sm-12">
	<h2>Bedankt voor het registreren bij Eenmaal Andermaal.</h2>
	<h3>Het registreren is compleet.</h3>
	<a class="btn btn-warning" href="login.php">Log in</a>
	</div>
</div>
<?php include "includes/footer.php"; ?>
<?php
require_once 'core/init.php';
require_once 'includes/functions.php';
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
// Geblokkeerde urls waar niet naar terug genavigeerd mag worden na het inloggen
$blocked_uris = array(
  '/login.php',    // zodat de login pagina niet opgeslagen word
  '/gegevens.php',
  '/verify.php',
  '/registreren.php',
  '/compleet.php',
  '/wachtwoord.php'
);
// Urls waarop de veilinggegevens in een sessie opgeslagen mogen worden
$session_uris = array(
  '/veiling-plaatsen.php',
  '/veiling-bevestigen.php'
);
blockedPreviousPages($blocked_uris);
saveVeilingData($session_uris);
mailKopers();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
 	<meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>EenmaalAndermaal</title>
	<link rel="shortcut icon" type="image/png" href="images/favicon.png">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
  <script src="js/functions.js"></script>
</head>
<body>
<div class="container">
  <img id="EenmaalAndermaalLogo" class="img-responsive" src="images/logo.png" alt="EenmaalAndermaal">
  <div class="user-buttons">
    <?php
    $gebruiker = new Gebruiker();
    if($gebruiker->isLoggedIn()) {
      if($gebruiker->heeftToestemming('Verkoper')) {
        echo '<a class="btn btn-success" href="veiling-plaatsen.php"><span class="glyphicon glyphicon-plus"></span> Nieuwe veiling</a> ';
      }
      echo '<a class="btn btn-warning" href="accountpagina.php">Account: '. $gebruiker->gegevens()->gebruikersnaam . '</a> ';
      echo '<a class="btn btn-warning" href="includes/loguit.php" title="Uitloggen">Log uit</a>';
    } else {
      echo '<a class="btn btn-warning" href="registreren.php">Registreren</a> ';
      echo '<a class="btn btn-warning" href="login.php" title="Inloggen">Log in</a>';
    }
    ?>
  </div>
  <div class="clearfix"></div>
</div>
<nav class="navbar navbar-default">
  <div class="container">
    <!-- Toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="index.php"><span class="glyphicon glyphicon-home"></span></a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Rubrieken <span class="caret"></span></a>
          <ul class="dropdown-menu">
          <?php
          $rubriek = database::getinstance()->query("SELECT * FROM Rubriek WHERE hoofdrubrieknummer IS NULL ORDER BY rubrieknaam ASC");
          foreach($rubriek->results() as $rubriek) {
            echo '<li><a href="veilingoverzicht.php?rubriek=' . $rubriek->rubrieknummer . '">' . $rubriek->rubrieknaam . '</a></li>';
          }
          ?>
          </ul>
        </li>
        <li><a href="overons.php">Over ons</a></li>
      </ul>
      <form class="navbar-form navbar-right" method="get" action="veilingoverzicht.php">
        <div class="input-group">
      		<input type="text" class="form-control" name="search" placeholder="Zoek veiling...">
      		<span class="input-group-btn">
        		<button class="btn btn-primary" type="button" title="Zoeken"><span class="glyphicon glyphicon-search"></span></button>
      		</span>
    	</div><!-- /input-group -->
      </form>     
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<div class="container">
<?php
require_once '../core/init.php';
$gebruiker = new Gebruiker();
$gebruiker->loguit();
Redirect::to('../index.php');
?>
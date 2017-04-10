<?php
require_once 'core/init.php';
if(Input::exists('get')) {
	$temp_gebruiker = Database::getInstance()->get('Mail', array('link', '=', Input::get('link')));
	if(Input::get('link') === $temp_gebruiker->first()->link) {
		Database::getInstance()->update('Mail', 'link', Input::get('link'), array('used' => 1));
		Session::put('verified', Input::get('email'));
		if(Input::get('upgrade')) {
			Redirect::to('registreer-verkoopaccount.php');
			die();
		}
		Redirect::to('wachtwoord.php');
		die();
	}
	Redirect::to('index.php');
	die();
}
Redirect::to('index.php');
die();
?>
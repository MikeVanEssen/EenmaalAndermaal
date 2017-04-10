<?php
session_start();
ob_start();

$GLOBALS['config'] = array(
	'sqlsrv' => array(
		'host' => 'mssql.iproject.icasites.nl',
		'username' => 'iproject9',
		'password' => 'K7vEkXXW',
		'db' => 'iproject9'
		),
	'remember' => array(
		'cookie_name' => 'hash',
		'cookie_expiry' => 604800
		),
	'session' => array(
		'session_name' => 'user',
		'token_name' => 'token'
		)
	);

spl_autoload_register(function($class) {
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/' . $class . '.php';
});

require_once $_SERVER['DOCUMENT_ROOT'] . '/functions/sanitize.php';
?>
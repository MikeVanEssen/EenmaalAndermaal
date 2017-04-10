<?php
/*! 	\class Hash
*  		\brief Klasse voor het aanmaken van wachtwoorden. (salten en hashen.)
*/
class Hash {
/*! 	\fn make($wachtwoord, $salt = '')
*		\brief Hashed een string met optionieel salt.
*		\param $wachtwoord De string die je wil hashen.
*		\param $salt Het salt dat je mee kan geven. Als dit niet wordt meegegeven is het salt default leeg.
*  		\return De gehasde string.
*		\example testform_register.php
*/
	public static function make($wachtwoord, $salt = '') {
		$saltedwachtwoord = $wachtwoord . $salt;
		return hash('sha512', $saltedwachtwoord); //hash de string plus de salt
	}
/*! 	\fn salt($length)
*		\brief Maakt een random salt aan.
*		\param $length De lengte van het salt dat je wil maken.
*  		\return Een string.
*		\example testform_register.php
*/
	public static function salt($length) {
		return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
	}
/*! 	\fn unique()
*		\brief Functie is nog niet in gebruik.
*/
	public static function unique() {
		return self::make(uniqid());
	}
}
?>
<?php
/*! 	\class Session
*  		\brief Klasse om met session om te gaan.
*/
class Session {
/*! 	\fn exists($name)
*		\brief Functie om te kijken of de variabele bestaat.
* 		\param $name De naam van de variabele.
*		\return True als de variabele bestaat, anders vals.
*		\example testform_register.php
*/
	public static function exists($name) {
		if(isset($_SESSION[$name])) {
			return true;
		} else {
			return false;
		}
	}
/*! 	\fn put($name, $value)
*		\brief Functie om een sessie variabele te vullen.
* 		\param $name De naam van de sessie.
*		\param $value De waarde van de nieuwe variabele.
*		\return De nieuwe $_SESSION variabele.
*		\example testform_register.php
*/
	public static function put($name, $value) {
		return $_SESSION[$name] = $value;
	}
/*! 	\fn get($name)
*		\brief Getter voor session variabele.
* 		\param $name De naam van de variabele.
*		\return De $_SESSION variabele.
*		\example testform_register.php
*/
	public static function get($name) {
			return($_SESSION[$name]);
	}
/*! 	\fn delete($name)
*		\brief Functie om een session variabele te verwijderen.
* 		\param $name De naam van de variabele.
*		\example testform_register.php
*/
	public static function delete($name) {
		if(self::exists($name)) {
			unset($_SESSION[$name]);
		}
	}
/*! 	\fn flash($name, $string = '')
*		\brief Functie om een string te 'flashen'. Flashen houdt in dat een gebruiker de string maar eenmalig te zien krijgt. Bijvoorbeeld: U bent ingelogd.
* 		\param $name De naam van de nieuwe flash.
*		\param $string De tekst die je wil meegeven aan de flash.
*		\example testform_register.php
*/
	public static function flash($name, $string = '') {
		if(self::exists($name)) { 
			$session = self::get($name);
			self::delete($name);
			return $session;
		} else {
			self::put($name, $string);
		}
		return '';
	}
}
?>
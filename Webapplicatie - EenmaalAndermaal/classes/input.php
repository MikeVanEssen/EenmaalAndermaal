<?php
/*! 	\class Input
*  		\brief Klasse om met form input om te gaan.
*/
class Input {
/*! 	\fn exists($type = 'post')
*		\brief Functie om te kijken of er wel input is.
* 		\param $type Het type input, default post.
*		\return True als er input is, false als er geen input is.
*		\example testform_register.php
*/
	public static function exists($type = 'post') {
		switch($type) {
			case 'post':
				if(!empty($_POST)) {
					return true;
				} else {
					return false;
				}
			break;
			case 'get';
				if(!empty($_GET)) {
					return true;
				} else {
					return false;
				}
			break;
			default:
				return false;
			break;
		}
	}
/*! 	\fn get($item)
*		\brief Functie om input op te halen.
* 		\param $item De naam van de POST of GET variabele.
*		\return De POST of GET variabele als deze bestaat, anders ''
*		\example testform_register.php
*/
	public static function get($item) {
		if(isset($_POST[$item])) {
			return $_POST[$item];
		} else if(isset($_GET[$item])) {
			return $_GET[$item];
		}
		return '';
	}
}
?>
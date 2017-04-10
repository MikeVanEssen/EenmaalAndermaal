<?php
/*! 	\class Redirect
*  		\brief Klasse om met redirects om te gaan.
*/
class Redirect {
/*! 	\fn to($location = null)
*		\brief Redirect naar een locatie.
*		\example testform_register.php
*/
	public static function to($location = null) {
		if($location) {
			if(is_numeric($location)) {
				switch($location) {
					case 404:
						header("HTTP/1.0 404 Not Found");
						include 'includes/errors/404.php';
						exit();
					break;
				}
			}
			header('Location: ' . $location);
			exit();
		}
	}
}
?>
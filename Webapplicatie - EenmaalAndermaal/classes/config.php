<?php
/*! 	\class Config 
*  		\brief Klasse voor de config.
*/
class Config {
/*! 	\fn get($path = null)
*		\brief Haalt de variabele uit de $GLOBALS['config'] array. $GLOBALS['config'] wordt gevuld in core/init.php.
* 		\param $path het pad naar de variabele in de $GLOBALS['config'] array.
*  		\return De variabele uit $GLOBALS['config'] achter het meegegeven pad mits die aanwezig is.
*		\example database.php
*/
	public static function get($path = null) {
		if($path) {
			$config = $GLOBALS['config'];
			$path = explode('/', $path);

			foreach($path as $bit) {
				if(isset($config[$bit])) {
					$config = $config[$bit];
				}
			}
			return $config;
		}
		return false;
	}
}
?>
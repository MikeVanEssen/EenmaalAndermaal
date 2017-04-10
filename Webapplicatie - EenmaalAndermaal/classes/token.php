<?php
/*! 	\class Token
*  		\brief Klasse om tokens te generen en checken.
*/
class Token {
/*! 	\fn generate()
*		\brief Vult een sessie variabele met een token.
*		\return De nieuwe $_SESSION variabele.
*		\example testform_register.php
*/
	public static function generate() {
		//genereert een md5 token voor de form en stopt hem gelijk in de session
		return Session::put(Config::get('session/token_name'), md5(uniqid()));
	}
/*! 	\fn check($token)
*		\brief Check of de meegegeven token overeenkomt met de session token.
*		\param $token Een token die je wil checken.
*		\return True als de token overeenkomt, anders false.
*		\example testform_register.php
*/
	public static function check($token) {
		$tokenName = Config::get('session/token_name');

		if(Session::exists($tokenName) && $token === Session::get($tokenName)) {
			Session::delete($tokenName);
			return true;
		}
		return false;
	}
}
?>
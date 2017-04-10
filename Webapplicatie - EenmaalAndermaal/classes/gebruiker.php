<?php
/*! 	\class Gebruiker
*  		\brief Klasse voor gebruiker functies.
*/
class Gebruiker {
	private $_db,			///< om een Database object in op te slaan.
	 		$_gegevens,		///< om gebruiker gegevens in op te slaan.
	 		$_sessionname, 	///< om de naam van een sessie op te slaan.
			$_isLoggedIn;	///< om bij te houden of de gebruiker ingelogd is.
/*! 	\fn __construct()
*		\brief Constructor voor de Gebruiker klasse. Vult $_db met het Database object.
*/
	public function __construct($gebruiker = null) {
		$this->_db = Database::getInstance();
		$this->_sessionname = Config::get('session/session_name');
		if(!$gebruiker) { //als er geen gebruiker is meegegeven
			if(Session::exists($this->_sessionname)) { //en als de sessie bestaat
				$gebruiker = Session::get($this->_sessionname); //vul een variabele met de gegevens van de gebruiker
				$checkActief = Database::getInstance()->get('Gebruiker', array('gebruikersnaam', '=', Session::get($this->_sessionname)));
				if($this->find($gebruiker) && $checkActief->first()->actief == '1') { //als deze bestaat
					$this->_isLoggedIn = true; //is de gebruiker al ingelogd
				} else {
					$this->loguit();
				}
			}
		} else {
			$this->find($gebruiker);
		}
	}
/*! 	\fn create($fields = array())
*		\brief Maakt een gebruiker aan met de parameters.
* 		\param $fields Een array met de informatie die je wil inserten per kolom. Bijvoorbeeld: array('gebruikersnaam' => 'Edward', 'voornaam' => 'Edward')
*		\exception Als de insert mislukt wordt er een exception gegooid.
*		\example testform_register.php
*/
	public function create($fields = array()) {
		if(!$this->_db->insert('Gebruiker', $fields)) {
			throw new Exception('Er was een probleem met het aanmaken van uw account.');
		}
	}
/*! 	\fn find($gebruikersnaam = null)
*		\brief Vult Gebruiker _gegevens als de gebruikersnaam bestaat.
* 		\param $gebruikersnaam De gebruikersnaam waarop je wil zoeken.
*		\example gebruiker.php
*/
	public function find($gebruikersnaam = null) {
		$gebruiker = $this->_db->get('Gebruiker', array('gebruikersnaam', '=', $gebruikersnaam));
		if($gebruiker->count()) {
			$this->_gegevens = $gebruiker->first();
			return true;
		}
		return false;
	}
/*! 	\fn login($gebruikersnaam = null, $wachtwoord = null)
*		\brief Functie om een gebruiker in te loggen.
* 		\param $gebruikersnaam De gebruikersnaam waarmee je wil inloggen.
*		\param $wachtwoord Het wachtwoord waarmee je wil inloggen.
*		\example testform_login.php
*/
	public function login($gebruikersnaam = null, $wachtwoord = null) {
		$gebruiker = $this->find($gebruikersnaam);

		if($gebruiker) {
			if($this->gegevens()->wachtwoord === Hash::make($wachtwoord, $this->gegevens()->salt) && $this->gegevens()->actief == '1') {
				Session::put($this->_sessionname, $this->gegevens()->gebruikersnaam);
				return true;
			}
		}
		return false;
	}
/*! 	\fn loguit()
*		\brief Functie om een gebruiker uit te loggen.
*/
	public function loguit() {
		Session::delete($this->_sessionname);
	}
/*! 	\fn update($fields = array(), $gebruikersnaam = null)
*		\brief Functie om gebruiker gegevens up te daten.
* 		\param $fields de velden die je wilt aanpassen.
*		\param $gebruikersnaam de gebruikersnaam van de gebruiker waar je gegevens wilt aanpassen. Standaard null.
*		\example gegevens_aanpassen.php
*/
	public function update($fields = array(), $gebruikersnaam = null) {
		if(!$gebruikersnaam && $this->isLoggedIn()) {
			$gebruikersnaam = $this->gegevens()->gebruikersnaam;
		}

		if(!$this->_db->update('gebruiker', 'gebruikersnaam', $gebruikersnaam, $fields)) {
			throw new Exception('Er was een probleem met het updaten van uw gegevens.');
		}
	}
/*! 	\fn updateTelefoon($fields = array(), $gebruikersnaam = null)
*		\brief Functie om gebruiker telefoongegevens up te daten.
* 		\param $fields de velden die je wilt aanpassen.
*		\param $gebruikersnaam de gebruikersnaam van de gebruiker waar je gegevens wilt aanpassen. Standaard null.
*		\example gegevens_aanpassen.php
*/
	public function updateTelefoon($fields = array(), $gebruikersnaam = null) {
		if(!$gebruikersnaam && $this->isLoggedIn()) {
			$gebruikersnaam = $this->gegevens()->gebruikersnaam;
		}
		if(!$this->_db->update('Gebruikerstelefoon', 'gebruikersnaam', $gebruikersnaam, $fields)) {
			throw new Exception('Er was een probleem met het updaten van uw gegevens.');
		}
	}
/*! 	\fn heeftToestemming($groep)
*		\brief Functie om te kijken of de gebruiker toestemming heeft.
* 		\param $groep de groep waar je tegen wilt controleren.
*/
	public function heeftToestemming($groep) {
		switch($groep) {
			case 'Verkoper':
				if($this->_gegevens->soort_gebruiker == 'Verkoper') {
					return true;
				}
			break;
			case 'Beheerder':
				if($this->_gegevens->soort_gebruiker == 'Beheerder') {
					return true;
				}
			break;
		}
		return false;
	}
/*! 	\fn getTelefoonnummer()
*		\brief Getter voor de gebruikers telefoonnummer.
*  		\return telefoonnummer
*/
	public function getTelefoonnummer() {
		$this->_db->get('Gebruikerstelefoon', array('gebruikersnaam', '=', $this->_gegevens->gebruikersnaam));
		return $this->_db->first()->telefoonnummer;
	}
/*! 	\fn getMobielnummer()
*		\brief Getter voor de gebruikers mobielnummer.
*  		\return mobielnummer
*/
	public function getMobielnummer() {
		$this->_db->get('Gebruikerstelefoon', array('gebruikersnaam', '=', $this->_gegevens->gebruikersnaam));
		return $this->_db->first()->mobielnummer;
	}
/*! 	\fn gegevens()
*		\brief Getter voor de gebruikers gegevens.
*  		\return $_gegevens
*/
	public function gegevens() {
		return $this->_gegevens;
	}
/*! 	\fn isLoggedIn()
*		\brief Getter voor de login status.
*  		\return $_isLoggedIn
*/
	public function isLoggedIn() {
		return $this->_isLoggedIn;
	}
}
?>
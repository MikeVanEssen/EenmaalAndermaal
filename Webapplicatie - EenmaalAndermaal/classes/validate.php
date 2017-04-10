<?php
/*! 	\class Validate
*  		\brief Klasse om form input te valideren.
*/
class Validate {
	private $_passed = false,		///< om bij te houden of de validatie geslaagd is of niet.
			$_errors = array(),		///< om errors aan toe te voegen.
			$_db = null;			///< om een Database object in op te slaan.
/*! 	\fn __construct()
*		\brief Constructor voor de Validate klasse. Maakt een connectie met de database met gebruik van Database.
*/
	public function __construct() {
		$this->_db = Database::getInstance();
	}
/*! 	\fn check($source, $items = array())
*		\brief Functie om data mee te valideren.
* 		\param $source Waar de data vandaan komt, GET of POST.
*		\param $items Een geneste array met eerst het veld en daarnaa de regels waaraan het veld moet voldoen.
*		Bijvoorbeeld: array('gebruikersnaam' => array(
*			'required'	=> true,
*			'min'		=> 2,
*			'max' 		=> 20,
*			'unique' 	=> 'gebruiker')
*  		\return Returnt true als er geen errors zijn, anders dit Validate object.
*		\example testform_register.php
*/
	public function check($source, $items = array()) { //source is get/post, items zijn de arrays met de regels
		foreach($items as $item => $rules) {
			foreach($rules as $rule => $rule_value) {

				$value = trim($source[$item]); //value van de post/get van elke item in de items array
				$item = escape($item);

				if($rule === 'required' && empty($value)) { //als de value required is en leeg
					$this->addError("{$item} moet ingevuld zijn.");
				} else if(!empty($value)) { //als de value ingevuld is
					switch($rule) {
						case 'min':
							if(strlen($value) < $rule_value) {
								$this->addError("{$item} moet minimaal {$rule_value} lang zijn.");
							}
						break;
						case 'max':
							if(strlen($value) > $rule_value) {
								$this->addError("{$item} mag maximaal {$rule_value} lang zijn.");
							}
						break;
						case 'unique':
							$check = $this->_db->get($rule_value, array($item, '=', $value));
							if($check->count()) {
								$this->addError("{$item} bestaat al. Voer een andere {$item} in.");
							}
						break;
						case 'matches':
							if($value != $source[$rule_value]) { //check de value tegen de vorige ingevoerde value
								$this->addError("{$rule_value} moet hetzelfde zijn als {$item}.");
							}
						break;
						case 'valid_email':
							if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
								$this->addError("{$item} bevat geen geldig emailadres.");
							}
						break;
						case 'numeric':
							if(!is_numeric($value)) {
								$this->addError("{$item} moet een getal zijn.");
							}
						break;
						break;
						case 'postcode':
							if(!preg_match("/^\b[1-9]\d{3}\s*[a-zA-Z]{2}\b$/", $value)) {
								$this->addError("{$item} bevat geen geldige postcode.");
							}
						break;
						case 'date':
							if(!$this->checkDate($value)) {
								$this->addError("{$item} bevat geen geldige datum.");
							}
						break;
						case '18jaar':
							if(!$this->checkAge($value)) {
								$this->addError("U moet minstens 18 jaar oud zijn om te registreren op deze website.");
							}
						break;
						case 'ISO-8859-1':
							if(!$this->checkISO_8859_1($value)) {
								$this->addError("{$item} bevat ongeldige tekens.");
							}
						break;
						case 'hasletter&number':
							if(!preg_match('/[A-Za-z]/', $value) || !preg_match('/[0-9]/', $value)) {
								$this->addError("{$item} moet minstens één letter en één cijfer bevatten.");
							}
						break;
						case 'alphachars_only':
							if(!ctype_alpha($value)) { //
								$this->addError("{$item} mag alleen letters bevatten.");
							}
						break;
						case 'iban':
							if(!$this->checkIBAN($value)) {
								$this->addError("{$item} bevat geen geldig IBAN nummer");
							}
						break;
						case 'vast_nummer':
							if(!preg_match('/^(((0)[1-9]{2}[0-9][-]?[1-9][0-9]{5})|((\\+31|0|0031)[1-9][0-9][-]?[1-9][0-9]{6}))$/', $value)) {
								$this->addError("{$item} bevat geen geldig vast telefoonnummer.");
							}
						break;
						case 'mobiel_nummer':
							if(!preg_match('/^(((\\+31|0|0031)6){1}[-]?[1-9]{1}[0-9]{7})$/i', $value)) {
								$this->addError("{$item} bevat geen geldig mobiel telefoonnummer.");
							}
						break;
					}
				}
			}
		}
		if(empty($this->_errors)) { //als de error array leeg is
			$this->_passed = true;
		}
		return $this;
	}	
/*! 	\fn addError($error)
*		\brief Functie om errors aan de _errors array toe te voegen.
* 		\param $error De string met de error erin.
*		\example validate.php
*/
	public function addError($error) { //voegt errors toe aan de error array
		$this->_errors[] = $error;
	}
/*! 	\fn getErrors()
*		\brief Getter voor de _errors array
* 		\return De _errors array van dit Validate object.
*		\example testform_register.php
*/
	public function getErrors() {
		return $this->_errors;
	}
/*! 	\fn passed()
*		\brief Getter voor de _passed boolean
* 		\return De _boolean variabele van dit Validate object.
*		\example testform_register.php
*/
	public function passed() {
		return $this->_passed;
	}
/*! 	\fn checkISO_8859_1()
*		\brief Functie om te controleren of de input ISO 8859 1 is.
*		\param $string die je wil controleren.
* 		\return True als de string alleen ISO 8859 1 chars bevat, anders false.
*		\example validate.php
*/
	public function checkISO_8859_1($string) { 
    	return (preg_match('/^[\x00-\xFF]*$/u', $string) === 1); 
	}
/*! 	\fn checkDate()
*		\brief Functie om te controleren of de input een datum is
*		\param $string die je wil controleren op datum
* 		\return True als het een datum is opgesplits met '-', false als het geen geldige datum is
*		\example validate.php
*/
	public function checkDate($string) {
		if(!isset($string) || $string <= 0 || !strpos($string, '-')) {
			return false;
		} else {
			$data = explode("-", $string);
			if(checkDate($data[1], $data[0], $data[2])) {
				return true;
			}
			return false;
		}
	}
/*! 	\fn checkAge()
*		\brief Functie om te returnen of iemand ouder is dan een meegegeven datum
*		\param $geboortedatum de geboortedatum van de gebruiker. Format dd-mm-jjjj.
*		\param $min_age de leeftijd die de gebruiker minimaal moet zijn. Standaard 18.
* 		\return True als de gebruiker oud genoeg is, false als dit nog niet zo is.
*		\example validate.php
*/
	public function checkAge($geboortedatum, $min_age=18) {
		$vandaag = new DateTime(date('d-m-Y'));
		if($this->checkDate($geboortedatum)) {
			$geboortedatum = new DateTime($geboortedatum);
			$verschil = $vandaag->diff($geboortedatum);
			if($verschil->y >= 18) {
				return true;
			}
		}
		return false;
	}
/*! 	\fn checkIBAN($iban)
*		\brief Functie om te check of de input een geldig IBAN nummer is.
* 		\param $iban de waarde die je wil checken.
*  		\return Returnt true als de input een valid IBAN nummer is, anders false.
*		\example validate.php
*/
	public function checkIBAN($iban) {
	    $iban = strtolower(str_replace(' ','',$iban));
	    $Countries = array('al'=>28,'ad'=>24,'at'=>20,'az'=>28,'bh'=>22,'be'=>16,'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,'cy'=>28,'cz'=>24,'dk'=>18,'do'=>28,'ee'=>20,'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,'is'=>26,'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,'lt'=>20,'lu'=>20,'mk'=>19,'mt'=>31,'mr'=>27,'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,'pt'=>25,'qa'=>29,'ro'=>24,'sm'=>27,'sa'=>24,'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24);
	    $Chars = array('a'=>10,'b'=>11,'c'=>12,'d'=>13,'e'=>14,'f'=>15,'g'=>16,'h'=>17,'i'=>18,'j'=>19,'k'=>20,'l'=>21,'m'=>22,'n'=>23,'o'=>24,'p'=>25,'q'=>26,'r'=>27,'s'=>28,'t'=>29,'u'=>30,'v'=>31,'w'=>32,'x'=>33,'y'=>34,'z'=>35);

	    if(array_key_exists(substr($iban,0,2) ,$Countries)) {
		    if(strlen($iban) == $Countries[substr($iban,0,2)]) {

		        $MovedChar = substr($iban, 4).substr($iban,0,4);
		        $MovedCharArray = str_split($MovedChar);
		        $NewString = "";

		        foreach($MovedCharArray AS $key => $value){
		            if(!is_numeric($MovedCharArray[$key])) {
		                $MovedCharArray[$key] = $Chars[$MovedCharArray[$key]];
		            }
		            $NewString .= $MovedCharArray[$key];
		        }

		        if(bcmod($NewString, '97') == 1) {
		            return TRUE;
		        } else {
		            return FALSE;
		        }
		    } else {
		        return FALSE;
		    }
	    }
	    return false;
	}
}
?>
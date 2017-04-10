<?php
/*! 	\class Database
*  		\brief Klasse voor database connectie en interactie. 
*/
class Database {
	private static $_instance = null; 	///< om een Database object in op te slaan. Static zodat er maar 1 connectie mogelijk is.
	private $_pdo,						///< om een PDO connectie in op te slaan.
			$_query,					///< om een query in op te slaan.
			$_error = false,			///< om een error in op te slaan.
			$_results,					///< om het resultaat van de query op te slaan.
			$_count = 0;				///< om het aantal resultaten van de query op te slaan.
/*! 	\fn __construct()
*		\brief Constructor voor de Database klasse. Maakt een connectie met de database met gebruik van Config.
*		\exception Print de PDOException als de connectie niet gemaakt kan worden.
*/
	private function __construct() {
		try {
			$this->_pdo = new PDO(	'sqlsrv:Server=' . Config::get('sqlsrv/host') . ';Database=' . Config::get('sqlsrv/db'),
									Config::get('sqlsrv/username'),
									Config::get('sqlsrv/password'));
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}
/*! 	\fn getInstance()
*		\brief Maakt een Database object aan. Als deze al bestaat returnt het al bestaande object.
*  		\return Returnt $_instance met het Database object.
*		\example testform_register.php
*/
	public static function getInstance() {
		if(!isset(self::$_instance)) {
			self::$_instance = new Database();
		}
		return self::$_instance;
	}
/*! 	\fn query($sql, $params = array())
*		\brief Voert de meegegeven query uit en vult $_results, $_count of $_error.
* 		\param $sql Een geldig SQL statement. Bijvvoorbeeld: SELECT * FROM Gebruiker WHERE gebruikersnaam = ?
*		\param $params De parameters waarop je wil filteren in array form. Bijvoorbeeld: array('Edward', 'Mike', 'Roy')
*  		\return Returnt dit Database object.
*		\example database.php
*/
	public function query($sql, $params = array()) {
		$this->_error = false;
		if($this->_query = $this->_pdo->prepare($sql)) {
			$x = 1;
			if(count($params)) {
				foreach($params as $param) {
					$this->_query->bindValue($x, $param);
					$x++;
				}
			}
			if($this->_query->execute()) {
				$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_count = $this->_query->rowCount();
			} else {
				$this->_error = true;
				print_r($this->_query->errorInfo()); //voor het printen van SQL errors die terugkomen
			}
		}
		return $this;
	}
/*! 	\fn action($action, $table, $where = array())
*		\brief Maakt een SQL statement van de meegegeven parameters en voert deze uit.
* 		\param $action De actie die je wilt uitvoeren. Bijvoorbeeld: SELECT, DELETE of UPDATE
*		\param $table De tabel waarop je de actie wilt uitvoeren.
*		\param $where Een array die in drie delen meegeven moet worden. Op plek [0] het veld waarop je wil selecteren. Op plek [1] de operator en op plek [2] de value waarop je wil selecteren. Bijvoorbeeld: array('gebruikersnaam', '=', 'Edward')
*  		\return Returnt dit Database object.
*/
	public function action($action, $table, $where = array()) {
		if(count($where) === 3) {
			$operators = array('=', '>', '<', '>=', '<=', 'IS', 'LIKE');

			$field 		= $where[0];
			$operator 	= $where[1];
			$value 		= $where[2];

			if(in_array($operator, $operators)) {
				$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
				if(!$this->query($sql, array($value))->error()) {
					return $this;
				}
			}
		}
		return false;
	}
/*! 	\fn get($table, $where)
*		\brief Functie die alles selecteert uit een tabel op basis van parameters 
*		\param $table De tabel waar je de informatie uit wil hebben.
*		\param $where Een array die in drie delen meegeven moet worden. Op plek [0] het veld waarop je wil selecteren. Op plek [1] de operator en op plek [2] de value waarop je wil selecteren. Bijvoorbeeld: array('gebruikersnaam', '=', 'Edward')
*  		\return Returnt dit Database object.
*		\example validate.php
*/
	public function get($table, $where) {
		return $this->action('SELECT *', $table, $where);
	}
/*! 	\fn insert($table, $fields = array())
*		\brief Functie die iets insert in een tabel op basis van de parameters.
*		\param $table De tabel waar je de insert op wil uitvoeren.
*		\param $fields Een array met de informatie die je wil inserten per kolom. Bijvoorbeeld: array('gebruikersnaam' => 'Edward', 'voornaam' => 'Edward')
*  		\return Returnt true als er geen errors zijn, anders false.
*		\example testform_register.php
*/
	public function insert($table, $fields = array()) {
		if(count($fields)) {
			$keys = array_keys($fields); //pakt alle namen van de kolommen
			$values = '';
			$x = 1;
			
			foreach($fields as $field) { //values klaar maken, '' toevoegen en dergelijke
				$values .= '?';
				if($x < count($fields)) {
					$values .= ', ';
				}
				$x++;
			}

			//gebruik de keys en values om de insert statement te maken
			$sql = "INSERT INTO {$table} (" . implode(', ' , $keys) . ") VALUES ({$values})"; //{$values}
			
			if(!$this->query($sql, $fields)->error()) { //als er geen error is
				return true; //query is uitgevoerd, data is ingevoerd
			}
		}
		return false;
	}
/*! 	\fn update($table, $where, $id, $fields)
*		\brief Functie die een tabel update op basis van de parameters.
*		\param $table De tabel waarop je de update wilt uitvoeren.
*		\param $where De kolomnaam met een primary key.
*		\param $id De primary key waarde van het record dat je wil aanpassen.
*		\param $fields Een array met de informatie die je wil updaten per kolom. Bijvoorbeeld: array('gebruikersnaam' => 'Edward')
*  		\return Returnt true als er geen errors zijn, anders false.
*/
	public function update($table, $where, $id, $fields) {
		$set = '';
		$x = 1;

		foreach($fields as $field => $keys) { //voor elke key een ? toevoegen aan de set
			$set .= "{$field} = ?";
			if($x < count($fields)) {
				$set .= ', ';
			}
			$x++;
		}

		$sql = "UPDATE {$table} SET {$set} WHERE {$where} = '{$id}'";
		
		if(!$this->query($sql, $fields)->error()) {
			return true;
		}
		return false;
	}
/*! 	\brief Getter voor $_results.
*  		\return $_results
*/
	public function results() {
		return $this->_results;
	}
/*! 	\brief Getter voor $_results. Retourneert alleen het eerste resultaat.
*  		\return $_results()[0]
*/
	public function first() {
		return $this->results()[0];
	}
/*! 	\brief Getter voor $_error.
*  		\return $_error
*/
	public function error() {
		return $this->_error;
	}
/*! 	\fn count()
*		\brief Getter voor $_count.
*  		\return $_count
*		\example validate.php
*/
	public function count() {
		return $this->_count;
	}
}
?>

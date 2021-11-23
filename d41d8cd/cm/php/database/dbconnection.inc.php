<?php
namespace Cm;

class Database {
	const PGSQL = "PGSQL";
	const MYSQL = "MYSQL";
	const MSSQL = "MSSQL";

	private $m_driver = self::PGSQL;
	var $m_hostName='';
	var $m_databaseName='';
	var $m_userName='';
	var $m_password='';
	var $m_port = '';
	var $m_timeout=5; //default connect timeout
	var $m_encoding="UTF8";

	private $m_fnTextSearch='';

	var $_db = null;
	var $m_txid = null;

	function __construct($driver=self::PGSQL){
		$this->setDriver($driver);
	}

	//oculta la informacion importante de un var_dump o print_r
	final public function __debugInfo() {
		return [];
	}

	public function setDriver($driver) {
		$this->m_driver = strtoupper($driver);
	}
	public function driver() {
		return $this->m_driver;
	}

	public function setHostName($hostName) {
		$this->m_hostName = $hostName;
	}
	public function setPort($port){
		$this->m_port = $port;
	}

	public function setDatabaseName($databaseName) {
		$this->m_databaseName = $databaseName;
	}
	public function databaseName(){
		return $this->m_databaseName;
	}

	public function setUserName($userName) {
		$this->m_userName = $userName;
	}
	public function setPassword($password) {
		$this->m_password = $password;
	}

	public function setConnectTimeout($timeout){
		$this->m_timeout = $timeout;
	}

	public function setClientEncoding($encoding){
		$this->m_encoding = $encoding;
	}

	public function link() {
		return $this->_db;
	}

	public function isOpen(){
		if( $this->_db ) return true;
		return false;
	}

	public function open_() {
		if( $this->isOpen() ){
			return true;
		}

		switch ( $this->m_driver ) {
			case self::PGSQL:
				$this->m_port = !empty($this->m_port) ? $this->m_port:"5432";

				$dsn="pgsql:host={$this->m_hostName};dbname={$this->m_databaseName};user={$this->m_userName};password={$this->m_password};port={$this->m_port};connect_timeout={$this->m_timeout}";

				try {
					$this->_db = new \PDO($dsn);
				}
				catch(\PDOException $e){
					throw new PublicException("Unable to connect to database");
				}

				/*
				@ $status= pg_set_client_encoding($this->_db,$this->m_encoding);
				if($status==-1){
					throw new \Exception("Failed setting database encoding to {$this->m_encoding}");
				}
				*/
				break;

			case self::MYSQL:
			
				$this->_db = @ mysqli_connect($this->m_hostName,$this->m_userName,$this->m_password);
				if(!$this->_db){
					throw new PublicException("Unable to connect to database");
				}

				mysqli_set_charset($this->_db,'utf8');
				mysqli_select_db($this->_db, $this->m_databaseName);

				mysqli_query($this->_db, "set group_concat_max_len = 65535");
				break;
				

			case static::MSSQL:
				$this->m_port = !empty($this->m_port) ? $this->m_port:"5432";

				//new PDO("dblib:host=172.18.0.1;dbname=prueba_ssf_coordinadora", 'sa', 'Abc1234567890!');
				$dsn="dblib:version=7.0;charset=UTF-8;host={$this->m_hostName};dbname={$this->m_databaseName};";

				try {
					$this->_db = new \PDO($dsn,$this->m_userName,$this->m_password);
				}
				catch(\PDOException $e){
					throw new PublicException("Unable to connect to database");
				}

				/*
				$this->_db = @ mssql_connect($this->m_hostName,$this->m_userName,$this->m_password);
				if(!$this->_db){
					throw new PublicException("Unable to connect to mssql server");
				}

				mssql_select_db($this->m_databaseName,$this->_db);
				*/

				break;



			default:
				throw new \Exception("Invalid database driver '{$this->m_driver}'");
				break;
		}


		if(!$this->_db) {
			return false;
		}
		return true;
	}

	public function lastErrorText() {
		return "error conectando";
	}

	public function close() {
		if( !is_resource($this->_db) ){
			return false;
		}

		switch ( $this->m_driver ) {
			case self::PGSQL:
				$db->_db = null;
				break;
			case self::MYSQL:
				mysql_close($this->_db);
				break;

			case static::MSSQL:
				$db->_db = null;
				//mssql_close($this->_db);
				break;
		}

		return true;
	}

	public function exec($sql){
		switch ( $this->m_driver ){
			case self::PGSQL:
				//return pg_query($this->link(),$sql);
				$rs = $this->_db->query($sql);
				if( $rs === false ){
					$error = $this->_db->errorInfo();
					$code = $error[0];
					$message = $error[2];
					$message = str_replace("ERROR:  ","",$message);

					if( preg_match('/23[0-9]{3,3}/',$error[0]) ){
						throw new DbConstraintViolation($message,$code);
					}

					throw new DbException($message, (integer) $code);
				}
				return $rs;

			case self::MYSQL:
				return mysqli_query($this->link(), $sql);

			case static::MSSQL:
				$rs = $this->_db->query($sql);
				//$rs = $this->_db->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL] );
				//$rs->execute();
				//echo "rs->rowCount = ".$rs->rowCount();

				if( $rs === false ){
					$error = $this->_db->errorInfo();
					$code = $error[0];
					$message = $error[2];
					$message = str_replace("ERROR:  ","",$message);

					if( preg_match('/23[0-9]{3,3}/',$error[0]) ){
						throw new DbConstraintViolation($message,$code);
					}

					throw new DbException($message, (integer) $code);
				}
				return $rs;

				//return mssql_query($sql,$this->link());

			default:
				throw new \Exception("Invalid database driver {$this->m_driver}");
		}

		return;
	}

	public function transaction() {
		if( !is_null($this->m_txid) ){
			throw new DbException("There is already a transaction in progress");
		}


		switch ( $this->m_driver ){
			case self::PGSQL:
				$this->_db->beginTransaction();
				$this->m_txid=1;
				break;

			case self::MYSQL:
				$this->exec("START TRANSACTION");
				break;

			case static::MSSQL:
				//$this->exec("BEGIN TRANSACTION");
				$this->_db->beginTransaction();
				$this->m_txid=1;
				break;

		}

		return true;
	}

	public function commit() {
		//$this->_db->commit();

		switch ( $this->m_driver ){
			case self::PGSQL:
				$this->_db->commit();
				break;

			case self::MYSQL:
				$this->exec("COMMIT");
				break;

			case self::MYSQL:
				//$this->exec("COMMIT");
				$this->_db->commit();
				break;
		}


		$this->m_txid=null;
		return true;
	}

	public function rollback() {
		//$this->_db->rollback();

		switch ( $this->m_driver ){
			case self::PGSQL:
				$this->_db->rollback();
				break;

			case self::MYSQL:
				$this->exec("ROLLBACK");
				break;

			case self::MSSQL:
				//$this->exec("ROLLBACK");
				$this->_db->rollback();
				break;
		}

		$this->m_txid=null;
		return true;
	}

	/**
	 *
	 * @param string $name
	 * @return Database
	 */
	public static function &database($name='db') {
		return $GLOBALS[$name];
	}


	public function setFnTextSearch($name){
		$this->m_fnTextSearch = $name;
	}

	public function fnTextSearch(){
		return $this->m_fnTextSearch;
	}

	/**
	 *
	 * @return DbQuery
	 */
	/*public static function query($name='db') {
		return new DbQuery($GLOBALS[$name]);
	}*/

	public function nextVal($sequenceName,$tableName="") {
		$tableName = !empty($tableName) ? $tableName:(__NAMESPACE__."_sequences");

		$ca = new DbQuery($this);
		$sql="select sequence_value+1 as sequence_value from {$tableName} where sequence_name='{$sequenceName}' for update";
		$ca->exec($sql);
		if( $ca->size() == 0) {
			throw new \Exception("Sequence not found {$sequenceName}");
		}

		$ra = $ca->fetch();
		$sequenceValue = $ra->sequence_value;

		$sql="update {$tableName} set sequence_value={$sequenceValue} where sequence_name='{$sequenceName}'";
		$ca->exec($sql);
		return $sequenceValue;
	}


	public function oldNextVal($sequenceName,$tableName="") {
		$tableName = !empty($tableName) ? $tableName:(__NAMESPACE__."_sequences");

		$ca = new DbQuery($this);
		$sql="select value+1 as sequence_value from {$tableName} where name='{$sequenceName}' for update";
		$ca->exec($sql);
		if( $ca->size() == 0) {
			throw new \Exception("Sequence not found {$sequenceName}");
		}

		$ra = $ca->fetch();
		$sequenceValue = $ra->sequence_value;

		$sql="update {$tableName} set value={$sequenceValue} where name='{$sequenceName}'";
		$ca->exec($sql);
		return $sequenceValue;
	}


	public function customNextVal($tableName,$fieldName,$where='1=1') {
		$ca= new DbQuery($this);
		$sql="select {$fieldName}+1 as {$fieldName} from {$tableName} where {$where} for update";
		$ca->exec($sql);
		if( $ca->size() == 0) {
			throw new \Exception("Sequence not found {$sequenceName}");
		}

		$ra = $ca->assoc();
		$codigo = $ra[$fieldName];

		$sql="update {$tableName} set {$fieldName} = $codigo where {$where}";
		$ca->exec($sql);
		return $codigo;
	}

	public function cloneConnection(){
		$dbc=new Database();
		$dbc->setHostName($this->m_hostName);
		$dbc->setDatabaseName($this->m_databaseName);
		$dbc->setUserName($this->m_userName);
		$dbc->setPassword($this->m_password);
		return $dbc;
	}

	/**
	 *
	 * @return PDO
	 */
	public function pdoConnection(){
		//$dsn = "pgsql:dbname={$this->m_databaseName};host={$this->m_hostName};";
		//return new \PDO($dsn,$this->m_userName,$this->m_password);
		return $this->_db;
	}

	public function quote($string){
		return $this->_db->quote($string);
	}

	public static function uuid4(){
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

		// 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),

		// 16 bits for "time_mid"
		mt_rand(0, 0xffff),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,

		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}


}



?>

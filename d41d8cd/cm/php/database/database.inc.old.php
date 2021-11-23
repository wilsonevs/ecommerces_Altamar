<?php
namespace Cm;
require_once dirname(__FILE__)."/../core/core.inc.php";

class DbException extends \Exception {

};



class Database {
	const PGSQL = "PGSQL";
	const MYSQL = "MYSQL";

	private $m_driver = self::PGSQL;
	var $m_hostName='';
	var $m_databaseName='';
	var $m_userName='';
	var $m_password='';
	var $m_port = '';
	var $m_timeout=5; //default connect timeout
	var $m_encoding="UTF8";

	var $m_link = null;
	var $m_txid = null;

	function __construct($driver=self::PGSQL){
		$this->setDriver($driver);
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
		return $this->m_link;
	}

	public function isOpen(){
		if( $this->m_link ) return true;
		return false;
	}

	public function open_() {
		if( $this->isOpen() ){
			return true;
		}


		switch ( $this->m_driver ) {
			case self::PGSQL:
				$this->m_port = !empty($this->m_port) ? $this->m_port:"5432";
				$connection_string = "host={$this->m_hostName} dbname={$this->m_databaseName} user={$this->m_userName} password={$this->m_password} port={$this->m_port} connect_timeout={$this->m_timeout}";
				@ $this->m_link = pg_connect($connection_string,PGSQL_CONNECT_FORCE_NEW);

				if( !$this->m_link ){
					throw new PublicException("Unable to connect to database");
				}

				@ $status= pg_set_client_encoding($this->m_link,$this->m_encoding);
				if($status==-1){
					throw new \Exception("Failed setting database encoding to {$this->m_encoding}");
				}
				break;

			case self::MYSQL:
				$this->m_link = @ mysqli_connect($this->m_hostName,$this->m_userName,$this->m_password);
				if(!$this->m_link){
					throw new PublicException("Unable to connect to database");
				}

				mysqli_set_charset($this->m_link,'utf8');
				mysqli_select_db($this->m_link, $this->m_databaseName);

				mysqli_query($this->m_link, "set group_concat_max_len = 65535");
				break;

			default:
				throw new \Exception("Invalid database driver '{$this->m_driver}'");
				break;
		}


		if(!$this->m_link) {
			return false;
		}
		return true;
	}

	public function lastErrorText() {
		return "error conectando";
	}

	public function close() {
		if( !is_resource($this->m_link) ){
			return false;
		}

		switch ( $this->m_driver ) {
			case self::PGSQL:
				pg_close($this->m_link);
				break;
			case self::MYSQL:
				mysql_close($this->m_link);
				break;
		}

		return true;
	}

	public function exec($sql){
		switch ( $this->m_driver ){
			case self::PGSQL:
				return pg_query($this->link(),$sql);

			case self::MYSQL:
				return mysqli_query($this->link(), $sql);

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
				//$this->exec("BEGIN");
				$this->exec("BEGIN TRANSACTION");
				$this->m_txid=1;
				break;

			case self::MYSQL:
				$this->exec("START TRANSACTION");
				break;
		}

		return true;
	}

	public function commit() {
		$this->exec("COMMIT");
		$this->m_txid=null;
		return true;
	}

	public function rollback() {
		$this->exec("ROLLBACK");
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

	/**
	 *
	 * @return DbQuery
	 */
	public static function query($name='db') {
		return new DbQuery($GLOBALS[$name]);
	}

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
		$dsn = "pgsql:dbname={$this->m_databaseName};host={$this->m_hostName};";
		return new PDO($dsn,$this->m_userName,$this->m_password);
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



class DbQuery {

	public $m_db;
	public $m_driver;

	public $v_rs = null;
	public $m_st = null;
	public $v_at;
	public $v_preparedQuery;
	public $v_boundValues;
	public $v_boundTypes;

	public $m_boundWhere = null;
	public $m_boundWhereTypes=null;

	public $v_lastErrorText;


	function __construct(&$db) {

		if (!($db instanceof Database)) {
			throw new \Exception("Invalid Database connection");
		}

		$this->m_db = &$db;
		$this->m_driver = $db->driver();

		$this->clear();
		return;
	}

	public function clear() {
		$this->v_lastErrorText = 'no error';
		$this->v_preparedQuery = '';
		$this->v_at = -1;
		$this->v_boundValues = array();
		$this->v_boundTypes = array();

		$this->m_boundWhere = array();
		$this->m_boundWhereTypes = array();


		if ($this->v_rs !== null && !is_bool($this->v_rs) ) {
			switch ($this->m_driver) {
				case Database::PGSQL :
					pg_free_result($this->v_rs);
					break;
				case Database::MYSQL :
					mysqli_free_result($this->v_rs);
					break;
				default :
					break;
			}
		}
		$this->v_rs = null;
	}

	public function lastErrorText() {
		return $this->v_lastErrorText;
	}

	public function preparedQuery() {
		$sql = $this->v_preparedQuery;

		$boundValues = $this->v_boundValues;
		foreach ($this->v_boundTypes as $k => $v) {

			if ($v === true) {
				if( $this->m_driver==Database::PGSQL){
					$boundValues[$k] = "'" . pg_escape_string($boundValues[$k]) . "'";
				}
				elseif( $this->m_driver==Database::MYSQL){
					$boundValues[$k] = "'" . mysqli_real_escape_string($this->m_driver,$boundValues[$k]) . "'";
				}
				else {
					$boundValues[$k] = "'" . addslashes($boundValues[$k]) . "'";
				}
			}
		}

		/*
		 //SQL In
		 $callbackIn = function($c) use ($boundValues) {
		 $field = $c[2];
		 if(!isset($boundValues[$field])) throw new Exception("No bind value for field {$field}");
		 return "{$c[1]}{$boundValues[$field]}{$c[3]}";
		 };

		 $sql = preg_replace_callback("/(\\s+in\\s+\\(\\s*)(:[a-zA-Z]{1}[a-zA-Z0-9_]+)(\\s*\\)(\\s+|\\\$))/", $callbackIn, $sql);
		 unset($callbackIn);

		 //SQL Like
		 $callbackLike = function($c) use ($boundValues) {
		 $field = $c[2];
		 if(!isset($boundValues[$field])) throw new Exception("No bind value for field {$field}");
		 return "{$c[1]}{$boundValues[$field]}{$c[3]}";
		 };

		 $sql = preg_replace_callback("/(%)(:[a-zA-Z]{1}[a-zA-Z0-9_]+)(%)/", $callbackLike, $sql);
		 unset($callbackLike);
		 */

		//SQL Others
		$callbackOthers = function($c) use ($boundValues) {
			$field = $c[0];
			if (!isset($boundValues[$field])){
				//$backTrace=self::backtraceCleaner( debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,3) );
				//error_log( $message."\n".print_r(debug_backtrace(),1) );

				$message="No bind value for field {$field}";
				throw new \Exception($message);
			}
			return $boundValues[$field];
		};

		$sql = preg_replace_callback("/(?<!:)(:[a-zA-Z_]{1}[a-zA-Z0-9_]*)/", $callbackOthers, $sql);
		unset($callbackOthers);

		return $sql;
	}

	public static function backtraceCleaner($backTrace){
		$backTrace=print_r( $backTrace,1);

		$backTrace=str_replace(".php","",$backTrace);
		$backTrace=str_replace(".inc","",$backTrace);

		$pattern="/(\[file\]\s*=>\s*)(.*\/)([\w]+(\.inc)?\.php)/";
		$backTrace = preg_replace($pattern,"\\1\\3",$backTrace);
		$backTrace=str_replace("\n"," ",$backTrace);

		return $backTrace;
	}

	public function exec($sql = '') {
		$this->m_st = null;
		$this->v_at = -1;
		$this->v_lastErrorText = '';

		if (trim($sql) == '' && trim($this->v_preparedQuery) != '') {
			$sql = $this->preparedQuery();
		}

		switch ($this->m_db->driver()) {
			case Database::PGSQL :

				@ $this->v_rs = pg_query($this->m_db->link(), $sql);
				if (!$this->v_rs) {
					$this->v_lastErrorText = "query failed, " . pg_last_error($this->m_db->link());

					$backTrace="";
					if( phpversion()>="5.4"){
						$backTrace=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,3);
						$backTrace = self::backtraceCleaner($backTrace);
					}

					throw new DbException("{$this->v_lastErrorText}\n{$backTrace}");
				}

				//structure bool hack
				$this->m_st=array();
				for($i=0;$i<pg_num_fields($this->v_rs);$i++){
					if( pg_field_type($this->v_rs,$i)=="bool" ){
						$this->m_st[ pg_field_name($this->v_rs,$i) ] = "bool";
					}
				}

				break;

			case Database::MYSQL :
				@$this->v_rs = mysqli_query($this->m_db->link(), $sql);
				if (!$this->v_rs) {
					$this->v_lastErrorText = "query failed, " . mysql_error($this->m_db->link());
					throw new \Exception($this->v_lastErrorText);
				}
				break;

			default :
				throw new \Exception("Invalid database driver");
				break;
		}

		return true;
	}


	public function at() {
		return $this->v_at;
	}

	public function size() {
		if( !$this->v_rs ){
			throw new DbException("There is not valid query executed");
		}

		switch ($this->m_db->driver()) {
			case Database::PGSQL :
				return pg_num_rows($this->v_rs);

			case Database::MYSQL :
				return mysqli_num_rows($this->v_rs);
		}
	}

	public function affectedRows() {
		switch($this->m_driver) {
			case Database::PGSQL :
				return pg_affected_rows($this->v_rs);

			case Database::MYSQL :
				return mysql_affected_rows($this->m_db->link());

			default :
				return -1;
		}

	}

	public function fetch() {
		$this->v_at += 1;

		if ($this->v_at < 0 || ($this->v_at + 1) > $this->size()) {
			$size = $this->size();
			//throw new \Exception("DbQuery::assoc Index out of range index={$this->v_at} records={$size}");

			$backTrace="";
			if( phpversion()>="5.4"){
				$backTrace=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,3);
				$backTrace=self::backtraceCleaner($backTrace);
			}

			throw new \Exception("DbQuery::assoc Index out of range index={$this->v_at} records={$size}\n{$backTrace}");

		}

		switch ($this->m_driver) {
			case Database::PGSQL :
				//return pg_fetch_object($this->v_rs, $this->v_at);
				$r = pg_fetch_object($this->v_rs, $this->v_at);

				foreach($this->m_st as $k=>$t){
					if( $t=="bool" ){
						$v=$r->{$k};
						$r->{$k}=is_null($v) ? null:( in_array($v,array("true","t")) ? true:false );
					}
				}

				return $r;

			case Database::MYSQL :
				$r=mysqli_fetch_object($this->v_rs);
				return $r;
		}
	}

	public function fetchAll($mode='object') {

		if ($this->v_rs === null) {
			$backTrace=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,3);
			$backTrace=self::backtraceCleaner($backTrace);
			throw new \Exception("No executed query\n{$backTrace}");
		}

		switch ($this->m_db->driver()) {
			case Database::PGSQL :
				if (pg_num_rows($this->v_rs) == 0)
					return array();
				//return pg_fetch_all($this->v_rs);
				$result = array();
				for($i=0;$i<pg_num_rows($this->v_rs);$i++){
					//$result[] = pg_fetch_object($this->v_rs);
					$result[]=$this->fetch();
				}
				return $result;


			case Database::MYSQL :
				$result = array();
				if( $mode=='array' ){
					while (($rax = mysqli_fetch_array($this->v_rs)) !== false) {
						$result[] = $rax;
					}
				}
				else {
					while (($rax = mysqli_fetch_object($this->v_rs)) !== false) {
						$result[] = $rax;
					}
				}


				return $result;
		}
	}

	public function bindValue($k, $v, $quote='auto') {
		if( is_array($v) || is_object($v) ){
			throw new \Exception("Invalid bind value for field {$k}");
		}

		if( strpos($k,":") === false ){
			throw new PublicException("Invalid bindValue key field '{$k}'");
		}


		if ($quote === 'auto' && !is_bool($v) && !is_null($v)) {

			if (array_search(strtolower($v), array("current_time", "current_date", "current_timestamp")) !== false) {
				$this->bindValue($k, $v, false);
				return;
			}

			$quote = true;
			/*
			$match = array();
			preg_match("/[0-9\.,]+/", $v, $match);
			if (count($match) == 0 || $match[0] != $v) {
				$this->bindValue($k, $v, true);
				return;
			} else {
				$quote = false;
			}
			*/
		}

		if (is_bool($v)) {
			$quote = false;
			$v = $v === true ? "true" : "false";
		}

		if (is_null($v)) {
			$quote = false;
			$v = "null";
		}

		//error_log($v." ".addslashes($v));

		//$this->v_boundValues[$k] = $quote === true ? addslashes((string) $v) : $v;
		$this->v_boundValues[$k] = $v;
		$this->v_boundTypes[$k] = $quote;
	}

	public function bindValues($map){
		foreach($map as $k=>$v){
			$this->bindValue(":{$k}",$v);
		}
	}

	public function boundValues() {
		return $this->v_boundValues;
	}

	public function prepare($sql) {
		$this->clear();
		$this->v_preparedQuery = $sql;
		return true;
	}

	public function prepareSelect($tableName, $fields, $where = '', $order = '', $extra = '') {
		$this->clear();
		$sql = "select {$fields} from {$tableName} ";

		if (trim($where) != "") {
			$sql .= " where {$where} ";
		}

		if (trim($order) != "") {
			$sql .= " order by {$order} ";
		}

		if (trim($extra) != "") {
			$sql .= " {$extra} ";
		}

		$this->v_preparedQuery = $sql;
		return true;
	}

	public function prepareSelectForUpdate($tableName, $fields, $where, $order = '', $extra = '') {
		$extra .= " for update";
		return $this->prepareSelect($tableName, $fields, $where, $order, $extra);
	}

	public function prepareInsert($tableName, $fields, $extra = '') {
		$this->clear();

		$sql = "";
		$fieldList = explode(",", $fields);
		$valueList = $fieldList;
		$tmp = null;
		$tmp2 = null;

		for ($i = 0; $i < count($fieldList); $i++) {

			if (strpos($fieldList[$i], "=") !== false) {
				$tmp2 = explode("=", $fieldList[$i]);
				$fieldList[$i] = $tmp2[0];
				$valueList[$i] = $tmp2[1];
			} else {
				$fieldList[$i] = trim($fieldList[$i]);
				$valueList[$i] = ":" . trim($fieldList[$i]);
			}
		}

		/* if( trim($v_userName)!="" ){
		 fieldList << "r_iusuario" << "r_ifechahora" << "r_uusuario" << "r_ufechahora";
		 valueList << ":r_iusuario" << ":r_ifechahora" << ":r_uusuario" << ":r_ufechahora";

		 bindValue(":r_iusuario",v_userName);
		 bindValue(":r_ifechahora","CURRENT_TIMESTAMP");
		 bindValue(":r_uusuario",v_userName);
		 bindValue(":r_ufechahora","CURRENT_TIMESTAMP");

		 } */

		$sql = "insert into " . $tableName . " ( " . implode(",", $fieldList) . " ) values ( " . implode(",", $valueList) . " ) {$extra}";

		$this->v_preparedQuery = $sql;
		return true;
	}

	public function prepareUpdate($tableName, $fields, $where = '', $extra = '') {
		$this->clear();

		$sql = '';
		$fieldList = explode(",", $fields);
		$tmp = null;

		$sql = "update " . $tableName . " set ";

		for ($i = 0; $i < count($fieldList); $i++) {
			$fieldList[$i] = trim($fieldList[$i]);
			if (strpos($fieldList[$i], "=") !== false) {
				$sql .= $fieldList[$i] . ",";
			} else {
				$sql .= $fieldList[$i] . " = :" . $fieldList[$i] . ",";
			}
		}

		/* if( !v_userName.trimmed().isEmpty() ){
		 sql += "r_uusuario=:r_uusuario,r_ufechahora=:r_ufechahora,";
		 bindValue(":r_uusuario",v_userName);
		 bindValue(":r_ufechahora","CURRENT_TIMESTAMP");
		 } */

		$sql = substr(trim($sql), 0, -1);

		if (trim($where) != "") {
			$sql .= " where " . $where;
		}

		$sql .= " {$extra} ";

		$this->v_preparedQuery = $sql;
		return true;
	}

	public function prepareDelete($tableName, $where) {
		$this->clear();

		$sql = "delete from " . $tableName . " ";
		if (trim($where) != '') {
			$sql .= " where " . $where;
		}

		$sql = trim($sql);
		$this->v_preparedQuery = $sql;
		return true;
	}

	public function prepareTable($tableName){
		$this->clear();
		$this->m_table=$tableName;
	}

	public function bindWhere($k,$v,$quote='auto'){
		if( is_array($v) || is_object($v) ){
			throw new \Exception("Invalid bind value for field {$k}");
		}

		if( strpos($k,":") === false ){
			throw new PublicException("Invalid bindWhere key field '{$k}'");
		}

		if ($quote === 'auto' && !is_bool($v) && !is_null($v)) {

			if (array_search(strtolower($v), array("current_time", "current_date", "current_timestamp")) !== false) {
				$this->bindWhere($k, $v, false);
				return;
			}

			$quote=true;
		}

		if (is_bool($v)) {
			$quote = false;
			$v = $v === true ? "true" : "false";
		}

		if (is_null($v)) {
			$quote = false;
			$v = "null";
		}

		$this->m_boundWhere[$k] = $v;
		$this->m_boundWhereTypes[$k] = $quote;
	}

	public function preparedWhere($sql){

		$boundValues = $this->m_boundWhere;
		foreach ($this->m_boundWhereTypes as $k => $v) {

			if ($v === true) {
				switch($this->m_driver){
					case "PGSQL":
						$boundValues[$k] = "'" . pg_escape_string($boundValues[$k]) . "'";
						break;

					case "MYSQL":
						$boundValues[$k] = "'" . mysqli_real_escape_string($this->m_driver,$boundValues[$k]) . "'";
						break;

					default:
						throw new \Exception("Invalid database driver {$this->m_driver}");
				}
			}


		}

		//SQL Others
		$callbackOthers = function($c) use ($boundValues) {
			$field = $c[0];
			if (!isset($boundValues[$field]))
				throw new \Exception("No bind value for field {$field}");
			return $boundValues[$field];
		};

		$sql = preg_replace_callback("/(?<!:)(:[a-zA-Z]{1}[a-zA-Z0-9_]+)/", $callbackOthers, $sql);
		unset($callbackOthers);

		return $sql;
	}


	public function preparedInsert($extra=''){
		$sql=array();
		$sql[]="insert into";
		$sql[]=$this->m_table;
		$sql[]="(";
		$sql[]=str_replace(":","",implode( ",",array_keys($this->v_boundValues) ));
		$sql[]=") values (";
		$sql[]=implode( ",",array_keys($this->v_boundValues));
		$sql[]=")";
		$this->v_preparedQuery = implode(" ",$sql)." ".$extra;
		return $this->preparedQuery();
	}

	public function execInsert($extra=''){
		$this->preparedInsert($extra);
		$this->exec();
	}

	public function preparedUpdate($filter='',$extra=''){
		if( empty($filter) ){
			$filter=array();
			foreach(array_keys($this->m_boundWhere) as $k){
				$field=str_replace(":","",$k);
				$filter[]=" {$field}={$k} ";
			}
			$filter = implode(" and ",$filter);
		}

		$sql=array();
		$sql[]="update";
		$sql[]=$this->m_table;
		$sql[]="set";

		$fields=array();
		foreach($this->v_boundValues as $k=>$v){
			$fields[]=str_replace(":","",$k)."={$k}";
		}

		$sql[]=implode(",",$fields);
		$sql[]="where";
		$sql[]=$this->preparedWhere($filter);
		$this->v_preparedQuery = implode(" ",$sql)." ".$extra;
		return $this->preparedQuery();
	}

	public function execUpdate($filter='',$extra=''){
		$this->preparedUpdate($filter,$extra);
		$this->exec();
	}


	public function preparedDelete($filter='',$extra=''){
		if( empty($filter) ){
			$filter=array();
			foreach(array_keys($this->m_boundWhere) as $k){
				$field=str_replace(":","",$k);
				$filter[]=" {$field}={$k} ";
			}
			$filter = implode(" and ",$filter);
		}

		$sql=array();
		$sql[]="delete from ";
		$sql[]=$this->m_table;
		$sql[]="where";
		$sql[]=$this->preparedWhere($filter);
		$this->v_preparedQuery = implode(" ",$sql)." ".$extra;
		return $this->preparedQuery();
	}


	public function execDelete($filter='',$extra=''){
		$this->preparedDelete($filter,$extra);
		$this->exec();
	}

	public function execPage($p=[]) {
		$p=(object)$p;

		$page = isset($p->page) ? $p->page : 1;
		$count = isset($p->count) ? $p->count : 1000;
		$sort = !empty($p->sort) ? "order by {$p->sort}" : "";

		$sortColumn = !empty($p->sort_column) ? "order by {$p->sort_column}" : "";
		$sortOrder = !empty($p->sort_order) ? "{$p->sort_order}" : "";

		if( empty($sort) && !empty($sortColumn) ){
			$sort = "{$sortColumn} {$sortOrder}";
		}


		$columnFilters=array();
		$columnFilters[]="1=1";


		if( !empty($p->column_filters) ){ // && is_array($p->column_filters) ){ se comenta por que viene como objeto
			if( !is_object($p->column_filters) ){
				throw new PublicException("Invalid column filter data type, must be object");
			}

			foreach($p->column_filters as $k=>$v){
				if( trim($v) =="" ) continue;

				$v=addslashes($v);
				$columnFilters[] = "upper({$k}::text) like upper('%{$v}%')";
			}
		}


		$columnFilters = implode(" and ",$columnFilters);

		//throw new PublicException($p->column_filters);
		//throw new PublicException($columnFilters);

		if ($page < 1) {
			throw new \Exception("Invalid page number {$page}");
		}

		$sql = $this->preparedQuery();

		$cacheKey=md5($sql.json_encode($p));
		$cache=isset($p->cache) ? $p->cache : false;
		$ttl=isset($p->cache_ttl) ? $p->cache_ttl: 300;//5 minutos por defecto

		global $memcache;
		if( $cache && $memcache){
			$result = $memcache->get($cacheKey);
			if($result) return $result;
		}



		$limit = $count;
		$offset = ($page - 1) * $count;

		$ca = new DbQuery($this->m_db);

		$ca->clear();
		$countSql = "select count(*) as records from ({$sql}) execPage";

		$ca->exec($countSql);
		$r = $ca->fetch();

		$recordCount = $r->records;
		$pageCount = ceil($recordCount / $count);

		$ca->clear();

		$pageSql = "select * from ({$sql}) execPage where {$columnFilters} {$sort} limit {$limit} offset {$offset}";
		//throw new \Exception($pageSql);
		//Application::log($pageSql);


		$ca->exec($pageSql);
		$records = $ca->fetchAll();

		$result=new \stdClass();
		$result->currentPage=$page;
		$result->count = count($records);
		$result->pageCount=$pageCount;
		$result->recordCount=$recordCount;
		$result->recordsPerPage=$count;
		$result->records=$records;
		//$result->items=&$result->records;

		if( $cache && $memcache){
			$memcache->set( $cacheKey,$result,0,$ttl );
		}

		return $result;
	}

	/**
	 *
	 * @param string $fieldList cadena separada por comas
	 * @param string $value
	 * @return string
	 */
	public function sqlFieldsFilters($fieldList, $value, $normalizeDbFunction = '') {
		if (empty($normalizeDbFunction)) {
			switch($this->m_driver) {
				case Database::PGSQL :
					$normalizeDbFunction = "lower";//"func_text_simplified_lower";
					break;
				default :
					$normalizeDbFunction = "lower";
					break;
			}
		}

		if (is_string($fieldList)) {
			$fields = explode(",", $fieldList);
		}

		$sqlFilters = array();
		foreach ($fields as $f) {
			switch($this->m_driver) {
				case Database::PGSQL :
					$sqlFilters[] = "{$normalizeDbFunction}({$f}::text) like {$normalizeDbFunction}('%{$value}%')";
					break;
				case Database::MYSQL :
					$sqlFilters[] = "{$normalizeDbFunction}({$f}) like {$normalizeDbFunction}('%{$value}%')";
					break;
			}
		}
		$sqlFilters = implode(" or ", $sqlFilters);
		return "( {$sqlFilters} )";
	}

	public static function pgArrayToArray($data){
		$tmp = substr($data, 1,-1);
		if( empty($tmp) ) return array();
		return explode(",",$tmp);
	}

	public static function arrayToPgArray($arr,$quoted=false){
		$q = $quoted ? "'":"";

		if( !is_array($arr) ){
			throw new PublicException("Invalid arr parameter");
		}

		foreach($arr as $k=>$v){
			$arr[$k] =addslashes($v);
		}

		return "{ "."{$q}".implode("{$q},{$q}",$arr)."{$q}"." }";
	}

	public static function arrayToSqlIn($arr,$quoted=false){
		$sep=$quoted ? "','":",";
		$result = ($quoted?"'":"") . implode($sep,$arr) . ($quoted?"'":"");
		return $result;
	}

	public static function arrayColumnToSqlIn($arr,$column,$quoted=false){
		$sep=$quoted ? "','":",";

		$tmp=[];

		foreach($arr as $r){
			if( is_object($r) ){
				$tmp[]= $r->{$column};
			}
			else {
				$tmp[]= $r[$column];
			}
		}

		return ($quoted?"'":"") . implode($sep,$tmp) . ($quoted?"'":"");
	}

	public static function arrayColumnToPgArray(array $arr,$column,$quoted=false,$filterCallback=null){

		if( is_callable($filterCallback) ){
			$tmp=array_filter($arr,$filterCallback);
			$tmp = array_columnx($tmp,$column);
		}
		else {
			$tmp = array_columnx($arr,$column);
		}

		return '{'.implode(",",$tmp).'}';
	}

	public static function pgHStoreToArray($hstore){
		//"flete"=>"1", "venta"=>"1", "vis_cu"=>"1", "vis_ti"=>"1", "vis_link"=>"0", "compuesto"=>"0"
		return json_decode('{' . str_replace('"=>"', '":"', $hstore) . '}', true);
	}

	public static function arrayToPgHstore($arr){
		$hstore=array();
		foreach($arr as $k=>$v){
			$v = addslashes($v);
			$hstore[]="hstore('{$k}','{$v}')";
		}

		return implode(" || ", $hstore);
	}



	public function sendExec($sql=''){
		$this->v_at = -1;
		$this->v_lastErrorText = '';
		$this->v_rs = null;


		$sql=!empty($sql) ? $sql: $this->preparedQuery();
		pg_send_query($this->m_db->link(), $sql);
	}

	public function isBusy(){
		$busy = pg_connection_busy( $this->m_db->link() );

		if( !$busy && is_null($this->v_rs) ){
			$this->v_rs = pg_get_result( $this->m_db->link() );
		}

		return $busy;
	}


}


?>

<?php
namespace Cm;
require_once __DIR__."/dbconnection.inc.php";

use stdClass;

class DbQuery {

	public $_db;
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


	function __construct(Database &$db) {
		$this->_db = &$db;
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
					$this->v_rs->closeCursor();
					break;
				case Database::MYSQL :
					mysqli_free_result($this->v_rs);
					break;
				case Database::MSSQL :
					//mssql_free_result($this->v_rs);
					$this->v_rs->closeCursor();
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
					$boundValues[$k] = "'" . str_replace("'","''",$boundValues[$k]) . "'";
				}
				elseif( $this->m_driver==Database::MYSQL){
					$boundValues[$k] = "'" . mysqli_real_escape_string($this->_db->link(),$boundValues[$k]) . "'";
				}
				elseif( $this->m_driver==Database::MSSQL){
					$boundValues[$k] = "'" . str_replace("'","''",$boundValues[$k]) . "'";
				}
				else {
					//$boundValues[$k] = "'" . addslashes($boundValues[$k]) . "'";
					throw new Cm\Exception("Invalid database driver at preparedQuery");
				}
			}
		}


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

		//$sql = preg_replace_callback("/(?<!:)(:[a-zA-Z_]{1}[a-zA-Z0-9_]*)/", $callbackOthers, $sql);
		$sql = preg_replace_callback("/(?<![:\"])(:[a-zA-Z_]{1}[a-zA-Z0-9_]*)/", $callbackOthers, $sql);
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



		switch ($this->_db->driver()) {
			case Database::PGSQL :
				$this->v_rs = $this->_db->exec($sql);
				break;

			case Database::MYSQL :
				@$this->v_rs = mysqli_query($this->_db->link(), $sql);
				if (!$this->v_rs) {
					$this->v_lastErrorText = "query failed, " . mysqli_error ($this->_db->link());
					throw new \Exception($this->v_lastErrorText);
				}
				break;

			case Database::MSSQL :
				/*
				@$this->v_rs = mssql_query($sql, $this->_db->link());
				if (!$this->v_rs) {
					$this->v_lastErrorText = "query failed, " . mssql_get_last_message();
					throw new \Exception($this->v_lastErrorText);
				}
				*/
				$this->v_rs = $this->_db->exec($sql);
				break;

			default :
				throw new \Exception("Invalid database driver");
				break;
		}

		return true;
	}

	public function execMultiple($sqls){
		if( empty($sqls) ) return true;

		$this->_db->pdoConnection()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
		$this->exec( implode(";",$sqls) );
		$this->_db->pdoConnection()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

		return true;
	}


	public function at() {
		return $this->v_at;
	}

	public function size() {
		if( $this->v_rs===false ){
			throw new DbException("There is not valid query executed");
		}

		switch ($this->_db->driver()) {
			case Database::PGSQL :
				return $this->v_rs->rowCount();

			case Database::MYSQL :
				return mysqli_num_rows($this->v_rs);

			case Database::MSSQL :
				//return mssql_num_rows($this->v_rs);
				return $this->v_rs->rowCount();


			default:
				return -1;
		}
	}

	public function first(){
		return $this->at() == 0;
	}

	public function last(){
		return $this->at() == ( $this->size() -1 );
	}

	public function affectedRows() {
		switch($this->m_driver) {
			case Database::PGSQL :
				return $this->v_rs->rowCount();

			case Database::MYSQL :
				return mysqli_affected_rows ($this->_db->link());

			case Database::MYSQL :
				//$rs = mssql_query("select @@rowcount as rows", $this->_db->link());
				//$rows = mssql_result($rs, 0, "rows");
				//return $rows;
				//return mssql_rows_affected($this->_db->link());
				return $this->v_rs->rowCount();



			default :
				return -1;
		}

	}

	public function fetch() {
		$this->v_at += 1;

		if( $this->m_driver != Database::MSSQL ){

			if ( $this->v_at < 0 || ($this->v_at + 1) > $this->size()) {
				$size = $this->size();

				$backTrace="";
				if( phpversion()>="5.4"){
					$backTrace=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,3);
					$backTrace=self::backtraceCleaner($backTrace);
				}

				throw new \Exception("DbQuery::assoc Index out of range index={$this->v_at} records={$size}\n{$backTrace}");

			}

		}

		switch ($this->m_driver) {
			case Database::PGSQL :
				$r = $this->v_rs->fetchObject();
				return $r;

			case Database::MYSQL :
				$r=mysqli_fetch_object($this->v_rs);
				return $r;

			case Database::MSSQL :
				//$r=mssql_fetch_object($this->v_rs);
				$r = $this->v_rs->fetchObject();
				return $r;

		}
	}

	public function fetchAll($mode='object') {

		if ($this->v_rs === null) {
			$backTrace=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,3);
			$backTrace=self::backtraceCleaner($backTrace);
			throw new \Exception("No executed query\n{$backTrace}");
		}

		switch ($this->_db->driver()) {
			case Database::PGSQL :

				if( $mode=='array' || $mode=="assoc"){
					return $this->v_rs->fetchAll(\PDO::FETCH_ASSOC);
				}

				return $this->v_rs->fetchAll(\PDO::FETCH_OBJ);


			case Database::MYSQL :
				$result = array();
				if( $mode=='array' ){
					while ($rax = mysqli_fetch_array($this->v_rs)) {
						$result[] = $rax;
					}
				}
				else {
					while ($rax = mysqli_fetch_object($this->v_rs)) {
						$result[] = $rax;
					}
				}


				return $result;

			case Database::MSSQL :
				/*
				$result = array();
				if( $mode=='array' ){
					while (($rax = mssql_fetch_array($this->v_rs)) !== false) {
						$result[] = $rax;
					}
				}
				else {
					while (($rax = mssql_fetch_object($this->v_rs)) !== false) {
						$result[] = $rax;
					}
				}
				return $result;
				*/
				if( $mode=='array' || $mode=="assoc"){
					return $this->v_rs->fetchAll(\PDO::FETCH_ASSOC);
				}

				return $this->v_rs->fetchAll(\PDO::FETCH_OBJ);


		}
	}


	public function bindValue($k, $v, $quote='auto') {

		if( is_object($v) && isset($v->__quote__) ){
			$quote = false;
			$v = $v->value;
		}

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
		}

		if (is_bool($v)) {
			$quote = false;
			$v = $v === true ? "true" : "false";
		}

		if (is_null($v)) {
			$quote = false;
			$v = "null";
		}

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

	public function table($tableName){
		$this->clear();
		$this->m_table=$tableName;
	}

	public function bindWhere($k,$v,$quote='auto'){

		if( is_object($v) && isset($v->__quote__) ){
			$quote = false;
			$v = $v->value;
		}

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
						$boundValues[$k] = "'" . str_replace("'","''",$boundValues[$k]) . "'";
						break;

					case "MYSQL":
						$boundValues[$k] = "'" . mysqli_real_escape_string($this->_db->link(),$boundValues[$k]) . "'";
						break;

					case "MSSQL":
						$boundValues[$k] = "'" . str_replace("'","''",$boundValues[$k]) . "'";
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

		//$sql = preg_replace_callback("/(?<![:)(:[a-zA-Z_]{1}[a-zA-Z0-9_]+)/", $callbackOthers, $sql);
		$sql = preg_replace_callback("/(?<![:\"])(:[a-zA-Z_]{1}[a-zA-Z0-9_]*)/", $callbackOthers, $sql);
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


	//depreacted 20180818 en favor de page
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

		$ca = new DbQuery($this->_db);

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




	public function page($p=[]) {
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

		$ca = new DbQuery($this->_db);

		$ca->clear();
		$countSql = "select count(*) as records from ({$sql}) execPage";

		$ca->exec($countSql);
		$r = $ca->fetch();

		$recordCount = $r->records;
		$pageCount = ceil($recordCount / $count);

		$ca->clear();

		$pageSql = "
		select
			*
		from ({$sql}) execPage
		where
			{$columnFilters}

		{$sort}

		limit {$limit}
		offset {$offset}
		";


		$ca->exec($pageSql);

		$result=new \stdClass();
		$result->page=$page;
		$result->count = $ca->size();
		$result->page_count=$pageCount;
		$result->item_count=$recordCount;
		$result->items=$ca->fetchAll();
		$result->last = $page >= $pageCount;

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
	public function sqlFieldsFilters($fieldList, $value, $tsFn = '') {

		if (empty($tsFn)) {

			switch($this->m_driver) {
				case Database::PGSQL :
					$tsFn = "lower";//"func_text_simplified_lower";
					$tsOp = 'like';
					$tsCo = '%';
					break;

				case Database::MYSQL:
					//$tsFn = "lower";
					//$tsOp='collate utf8_general_ci like';
					//$tsCo = '%';
					$tsExp="replace({field},' ','') collate utf8_general_ci like replace('%{value}%',' ','')";
					break;

				default :
					$tsFn = "lower";
					$tsOp = 'like';
					$tsCo = '%';
					break;
			}

			if( !empty($this->_db->fnTextSearch()) ){
				$tsFn = $this->_db->fnTextSearch();
			}
		}


		if (is_string($fieldList)) {
			$fields = explode(",", $fieldList);
		}

		$sqlFilters = array();
		foreach ($fields as $field) {
			switch($this->m_driver) {

				case Database::PGSQL :
					$sqlFilters[] = "{$tsFn}({$field}::text) {$tsOp} '%'||{$tsFn}('{$value}')||'%'";
					break;

				case Database::MYSQL :
					//$sqlFilters[] = "{$tsFn}({$f}) {$tsOp} {$tsFn}('{$tsCo}{$value}{$tsCo}')";
					$sqlFilters[] = strtr($tsExp,['{field}'=>$field,'{value}'=>$value]);
					break;
			}
		}
		$sqlFilters = implode(" or ", $sqlFilters);

		//echo $sqlFilters;
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
		pg_send_query($this->_db->link(), $sql);
	}

	public function isBusy(){
		$busy = pg_connection_busy( $this->_db->link() );

		if( !$busy && is_null($this->v_rs) ){
			$this->v_rs = pg_get_result( $this->_db->link() );
		}

		return $busy;
	}



	public function insert(stdClass $fields){
		/*if( $fields === null ){
			$fields = new stdClass();
		}*/

		if( empty($fields) ){
			throw new Cm\Exception("Can't insert with empty where");
		}


		foreach($fields as $k=>$v){
			$this->bindValue(":{$k}",$v);
		}
		// return ($this->preparedInsert());

		$this->execInsert();
	}

	public function update(stdClass $fields,stdClass $where){
		/*
		if( $fields === null ){
			$fields = new stdClass();
		}

		if( $where === null ){
			$where = new stdClass();
		}
		*/
		if( empty($where) ){
			throw new Cm\Exception("Can't update with empty where");
		}


		foreach($fields as $k=>$v){
			$this->bindValue(":{$k}",$v);
		}

		foreach($where as $k=>$v){
			$this->bindWhere(":{$k}",$v);
		}

		$this->execUpdate();
	}


	public function delete(stdClass $where){
		/*
		foreach($where as $k=>$v){
			$this->bindWhere(":{$k}",$v);
		}
		*/
		if( empty($where) ){
			throw new Cm\Exception("Can't delete with empty where");
		}

		foreach($where as $k=>$v){
			$this->bindWhere(":{$k}",$v);
		}

		$this->execDelete();
	}

	public static function noquotes($value){
		return (object)[
			"__quote__"=>false,
			"value"=>$value
		];
	}
}



?>

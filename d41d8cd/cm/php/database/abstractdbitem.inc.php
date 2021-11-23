<?php
namespace Cm;

class AbstractDbItem {
	public $db=null;
	public $d=array();

	public static $tablename=null;
	public static $st=null;

	function __construct(&$db){
		$this->db=$db;
		return;
	}

	/*
	public function create(&$db){
		return new {className}($db);
	}
	*/

	public function exec($sql){
		echo $sql."\n";
	}

	public function toCamelCase($text){
		return str_replace(" ","",  ucwords( str_replace("_"," ",$text) )  );
	}
	public function toDbCase($text){
		return strtolower( preg_replace("/([A-Z])/","_\\1",$text) );
	}

	public function __set($name,$value){
		$func="set".self::toCamelCase($name);
		if( method_exists($this,$func) ){
			$this->{$func}($value);
			return $this;
		}

		if( !isset(static::$st[ $name ]) ){
			throw new DomainException("Inexistent property: ".static::$tablename.".{$name}");
		}

		$this->d[ $name ] = $value;
		return $this;
	}

	public function __get($name){
		$func="get".self::toCamelCase($name);
		if( method_exists($this,$func) ){
			return $this->{$func}();
		}

		return $this->d[ $name ];
	}

	public function __primaryFields(){
		$fields=array();
		foreach(static::$st as $k=>$v){
			if( !$v["primary"] ) continue;
			$fields[] = $k;
		}

		return $fields;
	}

	public function __primaryFieldsFilter(){
		$filter=array();
		$tmp = $this->__primaryFields();
		foreach($tmp as $k){
			$filter[] = "{$k}=".$this->__escapedField($k);
		}

		$filter=implode(" and ",$filter);
		return $filter;
	}

	public function __availableFieldsFilter(){
		$filter=array();
		foreach($this->d as $k=>$v){
			$filter[]="{$k}=:{$k}";
		}
		$filter=implode(" and ",$filter);
		return $filter;
	}


	public function __escapedField($k){
		$type = static::$st[$k]["type"];

		switch($type){
			case "integer":
			case "numeric":
				return addslashes($this->d[$k]);

			case "array":
				return "'{".implode(",",$this->d[$k])."}'";
			default:
				return "'".addslashes( $this->d[$k] )."'";
		}


		return;
	}

	public function select($filter=''){
		$ca=$this->db->query();

		if( empty($filter) ){
			$filter=$this->__availableFieldsFilter();
		}

		$ca->prepareSelect(self::tableName,"*",$filter);
		foreach($this->d as $k=>$v){
			$v=$this->__escapedField($k);
			$ca->bindValue(":{$k}",$v,false);
		}

		echo $ca->preparedQuery()."\n";
	}

	public function selectByPk(){
		$this->select($this->__primaryFieldsFilter());
	}

	public function insert(){
		$this->preInsert();

		$values = $this->d;
		foreach($values as $k=>$v){
			$v=$this->__escapedField($k);
			$values[$k]=$v;
		}

		$sql=array();
		$sql[]="insert into ";
		$sql[]=static::$tablename;
		$sql[]="(";
		$sql[]=implode(",", array_keys($values) );
		$sql[]=") values (";
		$sql[]=implode(",",$values);
		$sql[]=")";
		$sql=implode(" ",$sql);
		$this->exec($sql);
		$this->posInsert();
	}

	public function update(){
		$this->preUpdate();
		$filter=$this->__primaryFieldsFilter();

		$values=[];
		foreach($this->d as $k=>$v){
			$values[]="{$k} = ".$this->__escapedField($k);
		}

		$sql=array();
		$sql[]="update";
		$sql[]=static::$tablename;
		$sql[]="set";
		$sql[]=implode(",",$values);
		$sql[]="where";
		$sql[]=$filter;
		$sql=implode(" ",$sql);
		$this->exec($sql);
		$this->posUpdate();
	}

	/*public function updateByPk(){
		$this->update($this->__primaryFieldsFilter());
	}*/

	public function delete(){
		$this->preDelete();
		$filter=$this->__primaryFieldsFilter();

		$sql=array();
		$sql[]="delete from";
		$sql[]=static::$tablename;
		$sql[]="where";
		$sql[]=$filter;
		$sql=implode(" ",$sql);
		$this->exec($sql);
		$this->posDelete();
	}

	public function deleteByPk(){
		$this->delete($this->__primaryFieldsFilter());
	}

	public function preUpdate(){}
	public function posUpdate(){}
	public function preInsert(){}
	public function posInsert(){}
	public function preDelete(){}
	public function posDelete(){}
}

?>

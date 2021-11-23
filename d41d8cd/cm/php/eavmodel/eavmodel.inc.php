<?php
namespace Cm;

class EavStructException extends \Exception {}


class EavItem {
	public $_attrs;
	public $_st;
	
	public $plat_id;
	public $type_id;
	public $category_id;
	public $item_id;
	public $parent_id=null;
	
	
	function __construct(){
		$this->_attrs=new \stdClass();
	}
	
	public function __set($k,$v){
		if( isset($this->{$k}) ){
			$this->{$k}=$v;
			return;
		}
		
		$this->_attrs->{$k}=$v;
	}
	
	
	public function __get($k){
		if( isset($this->{$k}) ) return $this->{$k};
		return $this->_attrs->{$k};
	}

	
	public static function uuid(){
		return Database::uuid4();
	}
	
	public function struct(){
		global $db;
		$ca=new DbQuery($db);
	
		/*
		if( !empty($this->category_id) ){
			$sql="
			select
				type_id
			from eav_categories
			where category_id=:category_id
			";
			$ca->prepare($sql);
			$ca->bindValue(":category_id",$this->category_id);
			$ca->exec();
			
			if($ca->size()==0){
				throw new \Exception("Invalid category id");
			}
			
			$rCategory=$ca->fetch();
			if(empty($this->type_id)){
				$this->type_id=$rCategory->type_id;
			}
		}
		*/
		
		
		$sql="
		select
			attr_id,
			attr_name,
			attr_label,
			attr_type
		from eav_attrs
		where type_id=:type_id
		";
		$ca->prepare($sql);
		$ca->bindValue(":type_id",$this->type_id);
		$ca->exec();
		
		$this->_st=new \stdClass();
		foreach($ca->fetchAll() as $r){
			$this->_st->{$r->attr_name}=$r;
		}
	}
	
	public function insertAttr($itemId,$k,$v){
		global $db;
		$ca=new DbQuery($db);
		$st=&$this->_st;
		
		if( !isset($st->{$k}) ){
			throw new EavStructException("Invalid attribute name {$k}");
		}
		
		$attrId=$st->{$k}->attr_id;
		
		$sql="delete from eav_values where item_id=:item_id and attr_id=:attr_id";
		$ca->prepare($sql);
		$ca->bindValue(":item_id",$itemId);
		$ca->bindValue(":attr_id",$attrId);
		$ca->exec();
		
		if(!is_array($v)){
			$v=array($v);
		}

		foreach($v as $v0){
			$ca->prepareTable("eav_values");
			$ca->bindValue(":item_id",$itemId);
			$ca->bindValue(":attr_id",$attrId);
			$ca->bindValue(":attr_value",$v0);
			$ca->execInsert();
		}
	}
	
	public function insert(){
		//$si=Application::session();
		//$userId=$_SESSION["info"]->user_id;
		
		global $db;
		$ca=new DbQuery($db);
	
		$this->struct();
		$dt=&$this->_attrs;
		$st=&$this->_st;
		$itemId=!empty($this->item_id) ? $this->item_id : $this->uuid();
		
		$ca->prepareTable("eav_items");
		$ca->bindValue(":plat_id",$this->plat_id);
		$ca->bindValue(":category_id",$this->category_id);
		$ca->bindValue(":type_id",$this->type_id);
		$ca->bindValue(":item_id",$itemId);
		$ca->bindValue(":parent_id",$this->parent_id);
		$ca->bindValue(":attrs",json_encode($this->_attrs));
		//$ca->bindValue(":user_id",$userId);
		
		$ca->execInsert();
		
		
		foreach($dt as $k=>$v){
			//if( in_array($k,array("item_id","category_id")) ) continue;
			$this->insertAttr($itemId,$k,$v);
		}
		
		return $itemId;
	}
	
	
	public function update(){
		//$si=Application::session();
		//$userId=$_SESSION["info"]->user_id;

		global $db;
		$ca=new DbQuery($db);

		
		$this->struct();
		$dt=&$this->_attrs;
		
		foreach($dt as $k=>$v){
			$this->insertAttr($this->item_id,$k,$v);
		}
		
		$sql="
		update eav_items 
		set 
			parent_id=:parent_id,
			ts_update=current_timestamp 
		where item_id=:item_id";
		$ca->prepare($sql);
		$ca->bindValue(":item_id",$this->item_id);
		$ca->bindValue(":parent_id",$this->parent_id);
		
		//$ca->bindValue(":user_id",$si->user_id);
		
		$ca->exec();
		
		return $this->item_id;
	}
	
	public function load($itemId){
		global $db;
		$ca=new DbQuery($db);
		
		$sql="
		select
			items.item_id,
			items.parent_id,
			items.category_id,
			items.type_id,
			attrs.attr_id,
			attrs.attr_name,
			vals.attr_value
		from eav_items items
			join eav_attrs attrs on (items.type_id=attrs.type_id) 
			left join eav_values vals on (items.item_id=vals.item_id and attrs.attr_id=vals.attr_id)
		where items.item_id=:item_id
		";
		
		$ca->prepare($sql);
		$ca->bindValue(":item_id",$itemId);
		$ca->exec();
		
		foreach($ca->fetchAll() as $r){
			$this->_attrs->{$r->attr_name} = $r->attr_value;
		}
		
		$this->item_id=$r->item_id;
		$this->category_id=$r->category_id;
		$this->type_id=$r->type_id;
		
		return;
	}
}


?>

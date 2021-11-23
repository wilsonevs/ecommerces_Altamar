<?php
namespace Cm;

class ValidationException extends \Exception {
	public $validation=[];
	
	function __construct($message,$code,$validation){
		parent::__construct($message,$code);
		$this->validation = $validation;
	}
	
	public function getValidation(){
		return $this->validation;
	}
	
}



class Validation {
	public $rules=[];
	public $invalid=[];
	
	public function notEmpty($name,$message){
		$this->rules[$name]=[
			"message"=>$message
		];
	}
	
	public function addInvalid($field,$message){
		$this->invalid[ $field ] = $message;
	}
	
	public function exec($p){
		
		$res=$this->invalid;
		//$res=[];
		
		foreach($this->rules as $k=>$r){
			if( !isset($p->{$k}) || empty($p->{$k}) ){
				$res[$k] = $r["message"];
			}
		}
		
		if( empty($res) ){
			return;
		}
		
		
		//validation exception code=3
		throw new ValidationException("Datos de entrada invalido","3",$res);
	}

}


?>

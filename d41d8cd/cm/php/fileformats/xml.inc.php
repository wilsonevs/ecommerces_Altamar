<?php
namespace Cm;


class DOMDocument extends \DOMDocument {
	function __construct($version='1.0',$encoding='utf-8'){
		parent::__construct($version,$encoding);
	}

	public function appendElement($parent,$name,$value=null,$attributes=array()){
		$element = $this->createElement($name,$value);
		$parent->appendChild($element);

		foreach($attributes as $k=>$v){
			$element->setAttribute($k,$v);
		}

		return $element;
	}

	public function createElement($name,$value=null,$attributes=array()){
		$element = parent::createElement($name,$value);

		foreach( (array) $attributes as $k=>$v){
			$element->setAttribute($k,$v);
		}

		return $element;
	}
}

?>

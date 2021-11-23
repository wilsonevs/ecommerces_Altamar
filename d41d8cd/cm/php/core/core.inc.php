<?php
namespace Cm;

class PublicException extends  \Exception {
	function __construct($message = "",$code = 0,$previous = NULL){
		if( is_object($message) || is_array($message) ){
			$message = print_r($message,1);
		}

		parent::__construct($message,$code,$previous);
	}
}

class Exception extends PublicException {}



class PrivateException extends \Exception {

}

class RedirectException extends \Exception {
	function __construct($url,$code){
		parent::construct($url,$code);
	}

	public function getUrl($url){
		return $this->message;
	}

	public function getStatusCode(){
		return $this->code;
	}
}




?>

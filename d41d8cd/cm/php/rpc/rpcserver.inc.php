<?php
namespace Cm;
use stdClass;


class RpcServerException extends \Exception {}

class RpcServer {
	const MODE_AUTO = "AUTO";
	const MODE_SOAP = "SOAP";
	const MODE_JSON = "JSON";
	const MODE_XMLRPC = "XMLRPC";

	public static function create($mode='AUTO',$options=null){
		if ( $mode == self::MODE_AUTO ) {
			$tmp = file_get_contents("php://input");
			$tmp = trim($tmp);

			if ($tmp != "" && $tmp[0] == "{") {
				$mode = self::MODE_JSON;
			}
			else {
				$mode = self::MODE_SOAP;
			}
		}

		switch ($mode) {
			case self::MODE_JSON :
				return new RpcServerJson();

			case self::MODE_SOAP :
				return new RpcServerSoap($options);

			case self::MODE_XMLRPC :
				return new RpcServerXml();

			default:
				throw new RpcServerException("Invalid rpc server mode {$mode}");
		}

		return;
	}
}

class RpcServerBase {
	public $m_classes =  array();
	public $m_publicWsdl = true;
	public $m_id = -1;
	public $m_cfg=array();

	public function setPublicWsdl($value){
		$this->m_publicWsdl = $value;
	}

	public function setClasses($classes) {
		$this->m_classes = $classes;
	}

}



class RpcServerJson extends RpcServerBase {

	public function handle($request = null) {
		if( isset($_GET["wsdl"]) && $this->m_publicWsdl ){
			self::wsdl();
			exit;
		}

		try {
			//options check from client
			if( $_SERVER["REQUEST_METHOD"]=="OPTIONS" ){
				exit;
			}

			if ($request == null) {
				$request = file_get_contents("php://input");
			}

			$call = json_decode($request, false);


			if( is_null($call) ) {
				$jsonErrorMessage = "";
				switch (json_last_error ()) {
					case JSON_ERROR_DEPTH :
						$jsonErrorMessage = ' - Maximum stack depth exceeded';
						break;
					case JSON_ERROR_CTRL_CHAR :
						$jsonErrorMessage = ' - Unexpected control character found';
						break;
					case JSON_ERROR_SYNTAX :
						$jsonErrorMessage = ' - Syntax error, malformed JSON';
						break;
					case JSON_ERROR_NONE :
						$jsonErrorMessage = ' - No errors';
						break;

					default :
						$jsonErrorMessage = ' - Unknow error';
						break;
				}
				throw new \Exception("Invalid json string {$jsonErrorMessage}");
			}

			if (!isset($call->method)) {
				throw new \Exception("Missing method {$request}");
			}

			//default empty params
			if(!isset($call->params) ){
				$call->params=new \stdClass();
			}

			//qooxdoo rpc
			if (array_key_exists("service", $call) && count($call->params) > 0) {
				$call->params = $call->params[0];
			}

			$toCall = str_replace(".", "::", $call->method);
			//$toCall = str_replace("_", "::", $call->method);

			if (strpos($toCall, "::") === false) {
				throw new \Exception("Invalid method '{$toCall}'",501);//501 - Not Implemented

			}

			$toCall = explode("::", $toCall);

			$validClass = false;
			foreach ($this->m_classes as $className) {
				if (strtolower($toCall[0]) == strtolower($className)) {
					$validClass = true;
				}
			}

			if (!$validClass) {
				throw new \Exception("Invalid class '{$toCall[0]}'",501);//501 - Not Implemented
			}

			if (!is_callable($toCall)) {
				throw new \Exception("Unknown class or method name {$call->method}");//501 - Not Implemented
			}

			/*
			if( !method_exists($className,$toCall[1]) ){
				throw new \Exception("Unknown class or method name {$call->method}");
			}
			*/

			set_error_handler(__NAMESPACE__."\RpcServerJson::exceptionErrorHandler", E_ALL | E_NOTICE | E_STRICT);
			$this->m_id = isset($call->id) ? $call->id : -1;


			$obj = new $toCall[0]();
			$result = call_user_func([$obj,$toCall[1]], $call->params);

			//$result = call_user_func($toCall, $call->params);
		} catch(\Exception $e) {
			$this->exceptionHandler($e);
			return;
		}

		$this->response($result);
		return;
	}

	private function response($result) {
		$r = array("result" => $result); //"error" => null //"jsonrpc" => "2.0", "id" => $this->m_id, "result" => $result
		self::output(json_encode($r));
		exit ;
	}

	public static function output($data) {
		$compress = isset($_GET["compress"]) ? $_GET["compress"] : "";
		//error_log($data);


		if ($compress == "qt") {
			$nbytes = strlen($data);
			$header = ($nbytes & 0xff000000)>>24;
			$header .= ($nbytes & 0x00ff0000)>>16;
			$header .= ($nbytes & 0x0000ff00)>>8;
			$header .= ($nbytes & 0x000000ff);

			$output = pack("N", $header) . gzcompress($data, 9);
			header('Content-Length: ' . strlen($output));
			echo $output;
			exit ;
		} else if ($compress == "gzip") {
			header("Content-Type: text/html;");
			header("Content-Encoding: gzip;");
			$data = gzcompress($data, 9);
			header("Content-Length: " . strlen($data));
			print "\x1f\x8b\x08\x00\x00\x00\x00\x00";
			print $data;
			exit ;
		} else {
			header("Content-Type: text/json;");
			header("Content-Length: " . strlen($data));
			print $data;
			exit ;
		}
	}

	public function error($message, $op = array()) {
		$op["file"] = isset($op["file"]) ? $op["file"] : "";
		$op["line"] = isset($op["line"]) ? $op["line"] : "";
		$op["code"] = isset($op["code"]) ? $op["code"] : "-1";
		$op["strip_tags"] = isset($op["strip_tags"]) ? $op["strip_tags"] : false;

		$r = array("error" => null,); //"jsonrpc" => "2.0", "id" => $this->m_id, "error" => null,

		if ($op["strip_tags"] === true) {
			$message = strip_tags($message);
		}

		$message = html_entity_decode($message);
		$message = stripslashes($message);

		if (trim($op["file"]) != '') {
			$op["file"] = str_replace(".php", "", basename($op["file"]));
			$message .= "\nfile:{$op["file"]}, line:{$op["line"]}";
		}

		$r["error"] = array("code" => $op["code"], "message" => $message);

		if (isset($op["validation"])) {
			$r["error"]["validation"] = $op["validation"];
		}

		self::output(json_encode($r));
	}

	public function exceptionHandler($e) {

		if ($e instanceof PublicException) {
			$this->error($e->getMessage(), array("code" => $e->getCode()));
			return;
		}

		if( $e instanceof ValidationException){

			$this->error($e->getMessage(),array(
				"code" => $e->getCode(),
				"validation"=>$e->getValidation()
			));

			return;
		}

		$errfile = str_replace(".php", "", basename($e->getFile()));
		$errline = $e->getLine();
		$errno = $e->getCode();
		$errstr = "Exception: " . $e->getMessage();
		$this->error("{$errstr}, file: {$errfile}, line: {$errline}", array("code" => $e->getCode()));
		return;
	}

	public static function exceptionErrorHandler($errno, $errstr, $errfile, $errline) {
		if( error_reporting() == 0 ) return;
		throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
	}

	private function wsdl() {
		$methods = array();
		foreach ($this->m_classes as $className) {
			$reflection = new \ReflectionClass($className);
			foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
				$doc = $method->getDocComment();

				$parameters = array();
				foreach ($method->getParameters() as $param) {
					$parameters[] = "\${$param->name}";
				}

				$parameters = implode(",", $parameters);
				$methods[] = "{$doc}<br/>public static function {$className}.{$method->getName()}({$parameters});<br/><br/>";
			}
		}

		$code = implode("\n", $methods);
		echo $code;
	}
}

require_once dirname(__FILE__) . "/../zendframework/set_include_path.inc.php";
require_once 'Zend/Soap/AutoDiscover.php';
require_once 'Zend/Soap/Server.php';
require_once 'Zend/Soap/Wsdl/Strategy/ArrayOfTypeComplex.php';
require_once 'Zend/Soap/Wsdl/Strategy/ArrayOfTypeSequence.php';

class RpcServerSoap extends RpcServerBase {
	public $m_options = null;
	public static $m_types=array(
		"void",
		"boolean",
		"integer",
		"integer[]",
		"string",
		"string[]",
		"array",
		"object",
		"object[]",
		"float",
		"float[]",
		"double",
		"double[]"
	);

	function __construct($options){
		$this->m_options = (object) $options;


		if( !isset($this->m_options->bindingStyle) ){
			$this->m_options->bindingStyle=isset($_GET["bindingstyle"]) ? $_GET["bindingstyle"]:"document";
		}

		if( !isset($this->m_options->bodyStyle) ){
			$this->m_options->bodyStyle=isset($_GET["bodystyle"]) ? $_GET["bodystyle"]:"literal";
		}
	}


	public function handle($request = null) {
		self::serviceManager();

		if( isset($_GET["wsdl"]) && $this->m_publicWsdl ){
			self::wsdl();
			exit;
		}

		if( isset($_GET["doc"]) && $this->m_publicWsdl ){
			self::docHtml();
			exit;
		}

		set_error_handler(__NAMESPACE__ . "\RpcServerSoap::errorHandler", E_ALL | E_NOTICE | E_STRICT);
		set_exception_handler(__NAMESPACE__."\RpcServerSoap::exceptionHandler");
		use_soap_error_handler(false);

		$protocol=isset($_SERVER['HTTPS'])?"https":"http";
		$wsdlUrl = "{$protocol}://{$_SERVER["HTTP_HOST"]}{$_SERVER["REQUEST_URI"]}?wsdl";


		//SOAP_USE_XSI_ARRAY_TYPE array fix


		$soap = new \Zend_Soap_Server($wsdlUrl);
		$soap->setOptions(array("soap_version" => SOAP_1_2)); //,'features' => SOAP_SINGLE_ELEMENT_ARRAYS));
		$soap->setClass('RpcServerSoapManager');
		$soap->handle();
		return;
	}


	public function serviceManager() {
		$methods = array();
		$documentLiteral = ($this->m_options->bindingStyle == "document" && $this->m_options->bodyStyle=="literal") ?"true":"false";


		foreach ($this->m_classes as $className) {

			$reflection = new \ReflectionClass($className);
			foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
				$doc = $method->getDocComment();
				$methodName = $method->getName();

				$parameters = array();
				foreach ($method->getParameters() as $param) {
					$parameters[] = "\${$param->name}";
				}

				$parameters = implode(",", $parameters);
				$methods[] = "{$doc}
                public function {$className}_{$methodName}({$parameters}){
                    try {
						if( {$documentLiteral} ){
							\$p={$parameters}->p;

							//documentLitera array fix
							foreach(\$p as \$k=>\$v){
								if( isset(\$v->item) && is_array(\$v->item) ){
									\$p->{\$k} = \$v->item;
									continue;
								}

								if(isset(\$v->item) && is_object(\$v->item)){
									\$p->{\$k} = array( \$v->item );
									continue;
								}
							}

							//\$result = {$className}::{$method->getName()}(\$p); //{$parameters}->p); //p transform

							\$tmp = new {$className}();

							if( isset(\$this->auth) ){
								\$tmp->auth = \$this->auth;
							}

							\$result = \$tmp->{$method->getName()}(\$p); //non static call
							return array('{$className}_{$methodName}Result' => \$result);
						}
						else {
							\$result = {$className}::{$method->getName()}({$parameters});
							return \$result;
						}
                    }
                    catch(\SoapFault \$e){ throw \$e;  }
                    catch(\Exception \$e){ throw new \SoapFault((string)\$e->getCode(),(string)\$e->getMessage());  }
                }";
			}
		}



		$methods[]="
		public function Authorization(\$p){
			\$this->auth = \$p;
		}
		";


		$code = "class RpcServerSoapManager {\n" . implode("\n", $methods) . "\n}";
		eval($code);
		return;
	}

	public static function exceptionHandler($e) {
		throw new \SoapFault($e->getMessage(), $e->getCode());
	}

	public static function errorHandler($errno, $errstr, $errfile, $errline) {
		if( error_reporting() == 0 ) return;
		throw new \SoapFault($errstr, $errno);
	}

	private function wsdl(){
		$autodiscover=null;

		if( $this->m_options->bindingStyle=="document" && $this->m_options->bodyStyle="literal" ){
			//$autodiscover = new \Zend_Soap_AutoDiscover('Zend_Soap_Wsdl_Strategy_ArrayOfTypeSequence');
			$autodiscover = new \Zend_Soap_AutoDiscover('Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex');

			$autodiscover->setBindingStyle(array(
				'style' => $this->m_options->bindingStyle, //'document','encoded'
				//'transport' => 'http://schemas.xmlsoap.org/soap/http'
			));

			$autodiscover->setOperationBodyStyle(array(
				'use' => $this->m_options->bodyStyle //'literal','rpc'
			));
		}
		else {
			$autodiscover = new \Zend_Soap_AutoDiscover('Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex');
		}

		$autodiscover->setClass('RpcServerSoapManager');
		$autodiscover->handle();
	}

	public static function docIntro($doc) {
		$matches = array();
		$intro = "";
		$lines = explode("\n", $doc);
		foreach ($lines as $l) {
			if (preg_match("/\\*\s+@[a-zA-Z]+\s+/", $l) === 0) {

				$l = preg_replace("/(\\/\\*\\*|\\s+\\*\\/|^\\s+\\*\\s*|\\*\\/)/", "", $l);
				$intro .= $l . "\n";
			}
		}

		return trim($intro);
	}

	public static function docAttribFromDoc($attrib, $doc) {
		$matches = array();
		//\\[\\]
		preg_match("/({$attrib})\s+([a-zA-Z_\\[\\]]+)/", $doc, $matches);

		if (!$matches)
			return false;
		return $matches[2];
	}

	public static function docStructure($className) {
		$st = array();

		$className = str_replace("[]", "", $className);
		$reflection = new \ReflectionClass($className);

		foreach ($reflection->getProperties(\ReflectionMethod::IS_PUBLIC) as $property) {
			$doc = $property->getDocComment();
			$propertyName = $property->getName();
			$propertyType = self::docAttribFromDoc("@var", $doc);
			$propertyIntro = "";

			$array = false;
			if (strpos($propertyType, "[]") !== false) {
				$array = true;
				$propertyType = str_replace("[]", "", $propertyType);
			}

			$st[$propertyName] = array("name" => $propertyName, "type" => $propertyType, "doc" => self::docIntro($doc), "array" => $array);
		}

		return $st;
	}

	public static function docAppendStructure(&$types, $className) {

		$types[$className] = array();
		$st = array();

		$className = str_replace("[]", "", $className);
		$reflection = new \ReflectionClass($className);

		foreach ($reflection->getProperties(\ReflectionMethod::IS_PUBLIC) as $property) {
			$doc = $property->getDocComment();
			$propertyName = $property->getName();
			$propertyType = self::docAttribFromDoc("@var", $doc);
			//if (!in_array($propertyType, array("string", "array","object","integer", "float", "double"))) {
			if(  !in_array($propertyType, self::$m_types)  ) {
				$types[$propertyType] = array();
			}
		}
		return;
	}

	public static function docDiscoverTypes($typeNames) {

		$discoveredTypeNames = array();

		foreach ($typeNames as $className) {
			$className = str_replace("[]", "", $className);

			$reflection = new \ReflectionClass($className);

			foreach ($reflection->getProperties(\ReflectionMethod::IS_PUBLIC) as $property) {

				$doc = $property->getDocComment();
				$propertyName = $property->getName();
				$propertyType = self::docAttribFromDoc("@var", $doc);
				if ($propertyType === false)
					continue;

				if (in_array($propertyName, $typeNames)) {
					continue;
				}

				//if (in_array($propertyType, array("string", "array","object", "integer", "float", "double"))) {

				if (in_array($propertyType, self::$m_types )) {
					continue;
				}

				$discoveredTypeNames[] = $propertyType;
				$discoveredTypeNames = array_merge($discoveredTypeNames, self::docDiscoverTypes(array($propertyType)));
				$discoveredTypeNames[] = $className;
			}
		}

		return array_unique(array_merge($typeNames, $discoveredTypeNames));
	}

	public function doc() {
		$doc = array();
		$methods = array();
		$types = array();
		$typeNames = array();

		foreach ($this->m_classes as $className) {

			$reflection = new \ReflectionClass($className);
			$docComment = $reflection->getDocComment();

			$doc["intro"] = self::docIntro($docComment);

			foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
				$docComment = $method->getDocComment();
				$methodName = $method->getName();
				$paramType = self::docAttribFromDoc("param", $docComment);
				$returnType = self::docAttribFromDoc("return", $docComment);

				if ($paramType == false && $returnType == false)
					continue;

				$intro = self::docIntro($docComment);

				$methods[] = array("name" => "{$className}_{$methodName}", "param" => str_replace("[]", "", $paramType), "param_array" => strpos($paramType, "[]") !== false, "return" => str_replace("[]", "", $returnType), "return_array" => strpos($returnType, "[]") !== false, "doc" => $intro);

				//if (!in_array($paramType, array("string", "array","object","object[]","integer", "float", "double","boolean","void"))) {
				if (!in_array($paramType, self::$m_types )) {
					$typeNames[] = $paramType;
				}

				//if (!in_array($returnType, array("string", "array","object","object[]","integer", "float", "double","boolean", "void"))) {
				if (!in_array($returnType, self::$m_types )) {
					$typeNames[] = $returnType;
				}
			}
		}


		$typeNames = self::docDiscoverTypes($typeNames);

		sort($typeNames);
		foreach ($typeNames as $typeName) {
			$typeName = str_replace("[]", "", $typeName);
			$types[$typeName] = self::docStructure($typeName);
		}

		$doc["methods"] = $methods;
		$doc["types"] = $types;
		return $doc;
	}

	public function docHtml() {
		$sdoc = self::doc();

		if( empty($sdoc["methods"]) ){
			throw new PublicException("Exported methods not found");
		}

		$d = new \DOMDocument("1.0", "UTF-8");
		$d->formatOutput = true;
		//$xslt = $d->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="doc.xsl"');
		//$d->appendChild($xslt);

		$root = $d->createElement("doc");

		foreach ($sdoc["methods"] as $m) {
			$eMethod = $d->createElement("method");
			$eMethod->setAttribute("name", $m["name"]);
			$eMethod->setAttribute("param", $m["param"]);
			$eMethod->setAttribute("param_array", $m["param_array"] ? "[]" : "");
			$eMethod->setAttribute("return", $m["return"]);
			$eMethod->setAttribute("return_array", $m["return_array"] ? "[]" : "");
			$eMethod->setAttribute("doc", nl2br($m["doc"]));

			$root->appendChild($eMethod);
		}

		foreach ($sdoc["types"] as $k0 => $v0) {
			$eType = $d->createElement("datatype");
			$eType->setAttribute("name", $k0);

			foreach ($v0 as $k1 => $v1) {
				$e = $d->createElement("property");
				$e->setAttribute("name", $v1["name"]);
				$e->setAttribute("type", $v1["type"]);
				$e->setAttribute("doc", nl2br($v1["doc"]));
				$e->setAttribute("array", $v1["array"] ? "[]" : "");

				$eType->appendChild($e);
			}
			$root->appendChild($eType);
		}

		$e = $d->createElement("intro", nl2br($sdoc["intro"]));
		$root->appendChild($e);

		$e = $d->createElement("css", file_get_contents(dirname(__FILE__) . "/rpcserverdoc.css"));
		$root->appendChild($e);

		$d->appendChild($root);
		//echo $d->saveXML();exit;

		$xslt = new \XSLTProcessor();
		$xsl = new \DOMDocument();
		$xsl->load(dirname(__FILE__) . "/rpcserverdoc.xsl");
		//,LIBXML_NOCDATA);
		$xslt->importStylesheet($xsl);
		echo $xslt->transformToXml($d);
	}
}


class RpcServerXml {

}

class RpcServerRest  extends RpcServerBase {
	public static function wsdl(){
		echo "todo";
	}

	public function handle($request = null) {
		if( isset($_GET["wsdl"]) && $this->m_publicWsdl ){
			self::wsdl();
			exit;
		}

		if ($request == null) {
			$request = file_get_contents("php://input");
		}

		$call = json_decode($request, false);

		if( is_null($call) ) {
			$jsonErrorMessage = "";
			switch (json_last_error ()) {
				case JSON_ERROR_DEPTH :
					$jsonErrorMessage = ' - Maximum stack depth exceeded';
					break;
				case JSON_ERROR_CTRL_CHAR :
					$jsonErrorMessage = ' - Unexpected control character found';
					break;
				case JSON_ERROR_SYNTAX :
					$jsonErrorMessage = ' - Syntax error, malformed JSON';
					break;
				case JSON_ERROR_NONE :
					$jsonErrorMessage = ' - No errors';
					break;

				default :
					$jsonErrorMessage = ' - Unknow error';
					break;
			}
			throw new \Exception("Invalid json string {$jsonErrorMessage}");
		}

		if (!isset($call->method)) {
			throw new \Exception("Missing method {$request}");
		}

		//qooxdoo rpc
		if (array_key_exists("service", $call) && count($call->params) > 0) {
			$call->params = $call->params[0];
		}

		$toCall = str_replace(".", "::", $call->method);
		//$toCall = str_replace("_", "::", $call->method);

		if (strpos($toCall, "::") === false) {
			throw new \Exception("Invalid method '{$toCall}'");
		}

		$toCall = explode("::", $toCall);

		$validClass = false;
		foreach ($this->m_classes as $className) {
			if (strtolower($toCall[0]) == strtolower($className)) {
				$validClass = true;
			}
		}

		if (!$validClass) {
			throw new \Exception("Invalid class '{$toCall[0]}'");
		}

		if (!is_callable($toCall)) {
			throw new \Exception("Unknown class or method name {$call->method}");
		}

		set_error_handler(__NAMESPACE__."\RpcServerJson::exceptionErrorHandler", E_ALL | E_NOTICE | E_STRICT);
		$this->m_id = isset($call->id) ? $call->id : -1;

		try {
			$result = call_user_func($toCall, $call->params);
		} catch(\Exception $e) {
			$this->exceptionHandler($e);
			return;
		}

		$this->response($result);
		return;
	}

}


?>

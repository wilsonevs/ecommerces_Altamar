<?php
namespace Cm;

class JsonRpcClient {
	public $m_url='';
	public $m_raw=null;
	
	function __construct($url){
		$this->m_url=$url;
	}
	
	
	public function call($method,$params=null){
		if( $params==null ){
			$params = new \stdClass();
		}
		$params = (object) $params;
		
		$request = array(
			"method" => $method,
			"params" => $params
		);

		$requestEncoded = json_encode($request);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,$this->m_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($curl, CURLOPT_POST, 1);
		curl_setopt ($curl, CURLOPT_POSTFIELDS, $requestEncoded);
		$this->m_raw = curl_exec($curl);
		//$info = curl_getinfo($curl);
		
		$res = json_decode($this->m_raw,false);
		return $res;
	}
	
	/*
	public function curl_get_contents($url,$post=''){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		if( !empty($post) ){
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS,$post);
			
			if( !is_array($post) ){
				curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: text/data'));
			}
		}
		
		$data = curl_exec($ch);
		$info = curl_getinfo($ch);
		if ($data === false || $info['http_code'] != 200) {
			throw new Exception("No cURL data returned for $url [". $info['http_code']. "]");
		}
		
		return $data;
	}
	*/
}

?>

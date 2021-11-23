<?php
namespace Cm;


class Utils {
	public static function encryptUrlParams($password,$p){
		$tmp = json_encode($p);
		//$tmp=openssl_encrypt($tmp,'aes128',$password,0,substr($password,0,16));
		$tmp=base64_encode($tmp);
		$tmp=urlencode($tmp);
		return $tmp;
	}
	
	public static function decryptUrlParams($password,$data){
		//$tmp=openssl_decrypt($data,'aes128',$password,0,substr($password,0,16));
		$tmp=base64_decode($data);
		return json_decode($tmp,false);
	}

}

?>

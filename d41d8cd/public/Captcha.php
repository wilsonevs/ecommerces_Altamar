<?php
//require_once __DIR__."/recaptchalib.php";

class Captcha {
	public static function verify($secret,&$p){

		$reCaptcha=new ReCaptcha($secret);
		$resp = $reCaptcha->verifyResponse(
			$_SERVER["REMOTE_ADDR"],
			$p->{"g-recaptcha-response"}
		);

		if(!$resp->success){
			throw new Cm\PublicException("Por favor, realice el proceso requerido en el captcha");
		}


		unset($p->{"g-recaptcha-response"});
	}
}

?>

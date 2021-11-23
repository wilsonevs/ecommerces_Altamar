<?php

$exports[]="ContactenosData";

class ContactenosData{

	public function enviar(stdClass $p){
		global $db;
		global $cfg;

		$res=new stdClass();

		if( empty($p->nombre) ){
			throw new Cm\PublicException("Ingresa un nombre por favor.");
		}

		if( strlen($p->nombre) < 3 ){
			throw new Cm\PublicException("El nombre debe contener más de 3 letras.");
		}

		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $p->nombre)){
				throw new Cm\PublicException("El nombre no debe contener caracteres especiales.");
		}

		if (strcspn($p->nombre, '0123456789') != strlen($p->nombre)){
				throw new Cm\PublicException("El nombre no debe contener números.");
		}

		if( empty($p->correo_electronico) ){
			throw new Cm\PublicException("Ingresa un correo electrónico por favor.");
		}

		if(!filter_var($p->correo_electronico, FILTER_VALIDATE_EMAIL)){
			throw new Cm\PublicException("Ingresa un correo electrónico válido por favor");
		}

		if( empty($p->telefono) ){
			throw new Cm\PublicException("Ingresa un teléfono por favor.");
		}

		//Captcha::verify("6LeZxiUTAAAAAFDkhwB5kCck2CN6itIUg2lJCmTc",$p);

		$p->category_id = 8;
		$p->item_id = "";
		$p->enviado = 2;

		$db->transaction();
		$tmp = Models\Runtime\EavModel::save($p);
		$db->commit();

		$url_prefix = "http://{$_SERVER["HTTP_HOST"]}{$cfg["appRoot"]}/task/mail";
		$url="{$url_prefix}/correo_contactenos.php?item_id={$tmp->item_id}";
		file_get_contents($url);

		$res->message="Tu mensaje de contacto ha sido enviado";
		return $res;

	}

}

 ?>

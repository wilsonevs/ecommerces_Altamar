<?php
class BaseData {

	function __construct(){
		$this->si = isset($_SESSION["si"]) ? $_SESSION["si"] : (object)[];

		$this->pedido = CarroMdl::get();
		$this->pedido_enc = $this->pedido->enc();
	}

	public static function get(){
		return new static();
	}

	protected function checkSession(){
		if( empty($this->si) ){
			throw new Cm\PublicException("Sesión Invalida");
		}
	}

}

?>

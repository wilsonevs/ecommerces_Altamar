<?php

$exports[]="MonedaData";

class MonedaData{

	public function enviar(stdClass $p){
		
		$_SESSION['moneda'] = $p->moneda;
		$_SESSION['todo'] = "";
		$_SESSION['categoria'] = "";
		return $_SESSION['moneda'];
	}

}

 ?>

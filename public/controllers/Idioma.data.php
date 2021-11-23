<?php

$exports[]="IdiomaData";

class IdiomaData{

	public function enviar(stdClass $p){
		
		$_SESSION['idioma'] = $p->idioma;
		$_SESSION['todo'] = "";
		$_SESSION['categoria'] = "";
		return $_SESSION['idioma'];
	}

}

 ?>

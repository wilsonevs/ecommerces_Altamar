<?php
require_once __DIR__.'/Base.php';

class PaginaError extends Base {


	public function index($p){
		$res=new stdClass();

		$res->message = "Pagina no Encontrada";
		$res->resource_uri = $p->resource_uri;


		$this->render("404.php",$res);
	}

}

?>

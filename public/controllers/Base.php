<?php
use Models\Runtime\EavModel;
require_once __DIR__."/../modelos/Carro.php";

class Base extends Controller {

	function __construct(){
		global $db;
		$this->si = isset($_SESSION["si"]) ? $_SESSION["si"] : (object)[];
		$_SESSION["idioma"] = isset($_SESSION["idioma"]) ? $_SESSION["idioma"] : "espanol";
		$_SESSION["moneda"] = isset($_SESSION["moneda"]) ? $_SESSION["moneda"] : "cop";

		$this->pedido = CarroMdl::get();
		$this->pedido_enc = $this->pedido->enc();
		$this->pedido_enc->moneda = $_SESSION["moneda"];
		$this->pedido_enc->moneda = coalesce_blank($this->pedido_enc->moneda) ?:"cop";
		$this->campo_precio = "precio_{$this->pedido_enc->moneda}";

		$filter=[];
		$filter[]="items.category_id=''";
		$filter[]="and";
		$filter[]="{codigo} = :codigo";

		$q = filter_var( coalesce_blank($_GET["q"]) ,FILTER_SANITIZE_STRING,FILTER_NULL_ON_FAILURE);
		$this->q = strtolower($q);
		//$this->q = "%{$q}%";
	}

	public static function get(){
		return new static();
	}

	public function baseData(){
		global $app;
		$res=new stdClass();

		$res->http_host = $_SERVER["HTTP_HOST"];
		$res->root_uri=$app->request->getRootUri();
		$res->resource_uri=$app->request->getResourceUri();
		$res->current_uri = "{$res->root_uri}{$res->resource_uri}";
		$res->si = isset($_SESSION["si"]) ? $_SESSION["si"] : (object)[];

		$res->idioma = isset($_SESSION["idioma"]) ? $_SESSION["idioma"] : "espanol";
		$res->moneda = isset($_SESSION["moneda"]) ? $_SESSION["moneda"] : "cop";
		$res->_GET = &$_GET;

		// global $menuSuperior;
		// $res->menu_superior = $menuSuperior;

		global $menuInferior;
		$res->menu_inferior = $menuInferior;

		//PLANTILLA DEL SITIO
		$res->plantilla = EavModel::item([
			"filter"=>"items.category_id=21"
		]);


		//Items del carro
		$c = CarroMdl::get();
		$res->enc = $c->enc();
		$res->det = $c->det();

		foreach($res->det as $r){

			$tmp = Models\Runtime\EavModel::item( (object)[
				"filter"=>"items.item_id = :item_id",
				"params"=>[
					":item_id"=>$r->item_id
				]
			]);

			$r->attrs = $tmp->attrs;
			$r->slug = $tmp->slug;

			// $r->unidades_disponibles = TiendaMdl::unidadesDisponibles( (object)[
			// 	'item_id'=>$r->item_id,
			// 	'variacion'=>$r->variacion
			// ]);

			// $r->unidades_disponibles -= $r->unidades;

		}

		$res->num_items = count($res->det);

		global $cfg;
		$res->host = $cfg['appHost'];

		// SLIDER
		$filter=[];
		$filter[]="items.category_id=48";
		$res->slider = EavModel::items([
			"filter"=>$filter,
			"order"=>"items.item_order desc"
		]);

		// Categorias productos
		$filter=[];
		$filter[]="items.category_id=72";
		$res->categorias = EavModel::items([
			"filter"=>$filter,
			"order"=>"items.item_order desc"
		]);


		return $res;
	}

	public function render($tempalte,$data=null){
		global $app;
		global $menu;

		$tmp=$this->baseData();
		$data=object_merge($tmp,$data);
		$app->render($tempalte,$data);
		exit;
	}


}

?>

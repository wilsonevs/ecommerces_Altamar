<?php
require_once __DIR__.'/Base.php';

use Models\Runtime\EavModel;
use Models\Runtime\EavCategories;

class Catalogo extends Base {

	public function index(stdClass $p){
		$res=new stdClass();

		


		//parametros filtros
		$page = filter_input(INPUT_GET,'page',FILTER_SANITIZE_NUMBER_INT);
		$res->categoria = strtolower(filter_input(INPUT_GET,'categoria',FILTER_SANITIZE_STRING));

		if(!isset($res->categoria)){
			if(isset($_SESSION['categoria'])){
				$res->categoria = $_SESSION['categoria'];
			} else {
				$res->categoria = '';
			}
		} else {
			$_SESSION['categoria'] = $res->categoria;
		}

		//objeto de filtros
		$res->filtros = new stdClass();

		//valores de ordenamiento
		$res->filtros->order = 'asc';
		$res->filtros->orderby = 'titulo';

		$ordenamiento = "{{$res->filtros->orderby}} {$res->filtros->order}";

		//valores por defecto palabra clave
		$res->filtros->palabra_clave='';

		//valores por defecto precios
		$res->filtros->desde=6000;
		$res->filtros->hasta=1000000;

		// categoria id
		$filter=[];
		$filter[]="items.category_id = 35";
		$params=[];


		//filtro categorias
		if (isset($res->categoria) && !empty($res->categoria)) {
			// Consulta para la categoria
			$filter3=[];
			$filter3[]="items.category_id=72 and lower({titulo}) like lower('%{$res->categoria}%')";
			$res->categorias = EavModel::item([
				"filter"=>$filter3
			]);
			$filter[]="and";
			$filter[]="{categoria} = {$res->categorias->item_id}";
		}

		// filtro palabra clave
		if (isset($_GET['buscar'])) {
				$res->filtros->palabra_clave = filter_input(INPUT_GET,'palabra',FILTER_SANITIZE_STRING);
				if (!empty($res->filtros->palabra_clave)) {
					$filter[]="and";
					$filter[]="lower({titulo}) like lower('%{$res->filtros->palabra_clave}%')";
				}
		}

		//filtro precios
		if (isset($_GET['establecer'])) {
			$res->filtros->desde = filter_input(INPUT_GET,'desde',FILTER_SANITIZE_NUMBER_INT);
			$res->filtros->hasta = filter_input(INPUT_GET,'hasta',FILTER_SANITIZE_NUMBER_INT);
			if (!empty($res->filtros->desde) && !empty($res->filtros->hasta)) {
				$filter[]="and";
				$filter[]="{precio} BETWEEN {$res->filtros->desde} AND {$res->filtros->hasta}";
			}

			$res->filtros->orderby = filter_input(INPUT_GET,'orderby',FILTER_SANITIZE_STRING);
			$res->filtros->order = filter_input(INPUT_GET,'order',FILTER_SANITIZE_STRING);

			$ordenamiento = "{{$res->filtros->orderby}} {$res->filtros->order}";

		}

		$res->items = EavModel::page( (object)[
			"filter"=>$filter,
			"order"=>$ordenamiento,
			"page"=>$page,
			"count"=> 25
		]);

		// categorias
		$filter=[];
		$filter[]="items.category_id=72";
		$res->categorias_items = EavModel::items([
			"filter"=>$filter,
			"order"=>"{titulo} asc"
		]);


		$res->paginador_html = Cm\WebTools::pager($res->items);

		$this->render("productos.php",$res);
	}



	public function ampliacion(stdClass $p){
		$res=new stdClass();


		//Productos Amp
		$filter=[];
		$filter[]="items.category_id = 35";
		$filter[]="and";
		$filter[]="items.slug = :slug";

		$res->ampliacion = EavModel::item( (object)[
			"filter"=>$filter,
			"params"=>[
				":slug"=>$p->catalogo_slug
			]
		]);

		$tmp = explode("\n",trim($res->ampliacion->attrs->inventario->data[0]));
		

		$res->ampliacion->colores = [];
		$res->ampliacion->tallas = [];
		$tmp_color = [];
		$tmp_talla = [];

		foreach ($tmp as $aux) {
			if( empty($aux) ) continue;
			$aux = explode(":",trim($aux));
			$aux = (object)[
				// "talla"=>coalesce_blank($aux[0]),
				"color"=>coalesce_blank($aux[1]),
				"unidades"=>coalesce_blank($aux[2]),
				"talla"=>coalesce_blank($aux[0])
			];
			array_push($tmp_color, $aux);
			array_push($tmp_talla, $aux);
			// array_push($res->ampliacion->tallas, $aux);
		}

		for ($i=0; $i < count($tmp_color); $i++) {
			array_push($res->ampliacion->colores, $tmp_color[$i]->color);
		}

		for ($i=0; $i < count($tmp_talla); $i++) {
			array_push($res->ampliacion->tallas, $tmp_talla[$i]->talla);
		}


		$res->ampliacion->colores = array_unique($res->ampliacion->colores, SORT_STRING);
		$res->ampliacion->tallas = array_unique($res->ampliacion->tallas, SORT_STRING);


		$res->items = EavModel::items([
			"filter"=>"items.category_id = 35 and items.item_id <> {$res->ampliacion->item_id} and {categoria} = {$res->ampliacion->attrs->categoria->data[0]}",
			"order"=>"items.item_order asc",
			"limit" => 4
		]);


		$this->render("item_amp.php",$res);
	}



}

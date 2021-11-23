<?php
require_once __DIR__.'/Base.php';

use Models\Runtime\EavModel;
use Models\Runtime\EavCategories;

class Inicio extends Base {

	public function index(stdClass $p){
		$res=new stdClass();


		//productos destacados
		$filter=[];
		$filter[]="items.category_id = 35 and {destacado} = 1";
		$res->items_des = EavModel::items([
			"filter"=>$filter,
			"order"=>"items.item_order asc"
		]);


		//productos nuevos
		$filter=[];
		$filter[]="items.category_id = 35 and {nuevo} = 1";
		$res->items_new = EavModel::items([
			"filter"=>$filter,
			"order"=>"items.item_order asc"
		]);


		//Testimonios
		$filter=[];
		$filter[]="items.category_id = 91";
		$res->testimonios = EavModel::items([
			"filter"=>$filter,
			"order"=>"items.item_order asc"
		]);

		//Aliados
		$filter=[];
		$filter[]="items.category_id = 47";
		$res->aliados = EavModel::items([
			"filter"=>$filter,
			"order"=>"items.item_order asc"
		]);

		$this->render("inicio.php",$res);
	}


}

<?php

class RegionMdl {
	// Variables que almacenan las categorias de: pais, depto y ciudad.
	private static $cat_paises = 59;
	private static $cat_departamentos = 58;
	private static $cat_ciudades = 57;

	public static function paises(){
		$res=[];

		$tmp = Models\Runtime\EavModel::items([
			"filter"=>"items.category_id=:category_id",
			"params"=>[
				":category_id"=>static::$cat_paises
			],
			"order"=>"{nombre} asc"
		]);

		$res=[];

		foreach($tmp as $r){

			$res[]=[
				"data"=>$r->item_id,
				"label"=>$r->attrs->country_name->label[0]
			];
		}


		array_unshift($res,["data"=>"","label"=>"Pais"]);
		return $res;
	}


	public static function departamentos(stdClass $p){
		$res=[];

		$filter=[];
		$filter[]="items.category_id=:category_id";
		$filter[]="and";
		$filter[]="{country_id} = :country_id";

		$tmp = Models\Runtime\EavModel::items([
			"filter"=>$filter,
			"params"=>[
				":category_id"=>static::$cat_departamentos,
				":country_id"=>$p->country_id
			]
		]);

		foreach($tmp as $r){

			$res[]=[
				"data"=>$r->item_id,
				"label"=>$r->attrs->state_name->label[0]
			];
		}

		array_unshift($res,["data"=>"","label"=>"Departamento"]);
		return $res;
	}

	public static function ciudades(stdClass $p){

		$res=[];

		$filter=[];
		$filter[]="items.category_id=:category_id";
		$filter[]="and";
		$filter[]="{country_id} = :country_id";
		$filter[]="and";
		$filter[]="{state_id} = :state_id";

		if(isset($p->delivery)){
			$filter[]="and";
			$filter[]="{entregas} <> 0";
		}

		$tmp = Models\Runtime\EavModel::items([
			"filter"=>$filter,
			"params"=>[
				":category_id"=>static::$cat_ciudades,
				":country_id"=>$p->country_id,
				":state_id"=>$p->state_id
			]
		]);

		foreach($tmp as $r){

			$res[]=[
				"data"=>$r->item_id,
				"label"=>$r->attrs->city_name->label[0],
				"entrega"=>$r->attrs->entregas->data[0]
			];
		}

		array_unshift($res,["data"=>"","label"=>"Ciudad"]);
		return $res;
	}

	public static function ciudad(stdClass $p){

		if( empty($p->item_id) ){
			throw new Cm\PublicException("Id de ciudad invalido");
		}

		$filter=[];
		$filter[]="items.category_id=:category_id";
		$filter[]="and";
		$filter[]="items.item_id=:item_id";

		$tmp = Models\Runtime\EavModel::item([
			"filter"=>$filter,
			"params"=>[
				":category_id"=>static::$cat_ciudades,
				":item_id"=>$p->item_id
			]
		]);

		if( empty($tmp) ){
			throw new Cm\PublicException("Ciudad no localizada, {$p->item_id}");
		}

		return $tmp;
	}


	public static function paisDepartamentoCiudad($id_ciudad){

		$filter=[];
		$filter[]="items.category_id=:category_id and items.item_id = :id_ciudad";

		$tmp = Models\Runtime\EavModel::item([
			"filter"=>$filter,
			"params"=>[
				":category_id"=>static::$cat_ciudades,
				":id_ciudad"=>$id_ciudad
			]
		]);


		return "{$tmp->attrs->country_id->label[0]} / {$tmp->attrs->state_id->label[0]} / {$tmp->attrs->city_name->label[0]} - {$tmp->attrs->city_code->label[0]}";
	}
}

?>

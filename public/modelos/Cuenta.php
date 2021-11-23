<?php


class CuentaMdl {
	//Variable que almacena el código de la categoría donde se almacenarán los usuarios registrados.
	private static $cat_cuentas = 39;
	//Categoría donde se almacenan los tipo de identificación.
	private static $cat_tipos_identificacion = 35;
	//Función para cargar los tipos de identificación en el combo.
	public static function tiposIdentificacion(){
		$res=[];

		$tmp = Models\Runtime\EavModel::items([
			"filter"=>"items.category_id=:category_id",
			"params"=>[
				":category_id"=>static::$cat_tipos_identificacion
			],
			"order"=>"{nombre} asc"
		]);

		$res=[];

		foreach($tmp as $r){

			$res[]=[
				"data"=>$r->item_id,
				"label"=>$r->attrs->nombre->label[0]
			];
		}


		array_unshift($res,["data"=>"","label"=>"Seleccione"]);
		return $res;
	}
	//Función para agregar nuevos usuarios que se registran.
	public static function insert(stdClass $p){
		return Models\Runtime\EavModel::save($p);
	}
	//Función para actualizar datos de usuarios que ya estan registrados.
	public static function update(stdClass $p){
		if( empty($p->item_id) || !is_numeric($p->item_id) ){
			throw new Cm\PublicException("No se pueden actualizar la cuenta, falta el id.");
		}

		return Models\Runtime\EavModel::save($p);
	}

	//Función para validar los usuarios al momento de autenticarse.
	public static function item($correo_electronico){

		$filter=[];
		$filter[]="items.category_id = :category_id";
		$filter[]="and";
		$filter[]="{correo_electronico} = :correo_electronico";

		$tmp = Models\Runtime\EavModel::item([
			"filter"=>$filter,
			"params"=>[
				":category_id"=>static::$cat_cuentas,
				":correo_electronico"=>$correo_electronico
			]
		]);

		return $tmp;
	}

	public static function load($itemId){
		return Models\Runtime\EavModel::load($itemId);
	}

}

?>

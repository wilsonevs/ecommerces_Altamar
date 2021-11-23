<?php
require_once __DIR__.'/Tienda.php';

class TransporteMdl {

	public static $cat_tarifas= 65;
	public static $cat_rangos_tarifas = 76;

	public static function calcular($enc,$det){

		if( $enc->moneda == 'usd' ) return 0;

		$total = [0];


		if(empty($enc->ent_id_ciudad) || count($det) == 0){
			return 0;
		}

		$ciudad = RegionMdl::ciudad( (object)["item_id"=>$enc->ent_id_ciudad]);
		$tipo_destino = $ciudad->attrs->tipo_destino->data[0];

		foreach($det as $r){

			$producto = TiendaMdl::producto((object)[
				'item_id'=>$r->item_id
			]);

			// if( empty($producto->attrs->tarifa_transporte->data[0]) ){
			// 	throw new Cm\PublicException("Producto sin tarifa de transporte");
			// }


			// $idTarifa = $producto->attrs->tarifa_transporte->data[0];

			$filter=[];
			$filter[]="items.category_id=:category_id";
			// $filter[]="and";
			// $filter[]="{tarifa} = :tarifa";

			$order=[];
			$order[]="cast( {cantidad_inicial} as integer) asc";
			$order[]="cast( {cantidad_final} as integer) asc";

			$tarifas = Models\Runtime\EavModel::items((object)[
				"filter"=>$filter,
				"params"=>[
					":category_id"=>static::$cat_rangos_tarifas
				],
				"order"=>$order
			]);

			//print_r($tarifas);


			foreach($tarifas as $tarifa){

				if( $r->unidades >= $tarifa->attrs->cantidad_inicial->data[0] && $r->unidades <= $tarifa->attrs->cantidad_final->data[0] ){

					if($tipo_destino == '' || $tipo_destino == null ){
						throw new Cm\PublicException("Esta ciudad no esta configurada.");
					}
					else {
						$total[] = (float) $tarifa->attrs->{"valor_{$tipo_destino}"}->data[0];
						break;
					}

				}

			}
		}

		return max($total);
	}

}

?>

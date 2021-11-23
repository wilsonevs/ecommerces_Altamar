<?php
use Models\Runtime\EavModel;


class TiendaMdl {

	public static $cat_formas_pago = 61;
	public static $cat_moneda = 62;

	public static function formasPago(stdClass $p=null){
		$res=[];


		$filter=[];
		$filter[] = "items.category_id=:category_id";
		$filter[] = "and";
		$filter[] = "{codigo} = :codigo";
		$moneda = Models\Runtime\EavModel::item([
			"filter"=>$filter,
			"params"=>[
				":category_id"=>static::$cat_moneda,
				":codigo"=>$p->moneda
			],
			"order"=>"items.item_order desc"
		]);

		$filter=[];
		$filter[] = "items.category_id=:category_id";
		$filter[] = "and";
		$filter[] = "{activo} = '1'";
/*		$filter[] = "and";
		$filter[] = "{monedas} = :moneda";*/

		$tmp = Models\Runtime\EavModel::items([
			"filter"=>$filter,
			"params"=>[
				":category_id"=>static::$cat_formas_pago
				// ":moneda"=>$moneda->item_id
			],
			"order"=>"items.item_order desc"
		]);


		$res=[];

		foreach($tmp as $r){
			if ($_SESSION['idioma'] == 'espanol') {
				$label = $r->attrs->nombre->label[0];
			} else {
				$label = $r->attrs->nombre_ingles->label[0];
			}

			$res[]=[
				"data"=>$r->item_id,
				"label"=>$label
			];
		}


		//array_unshift($res,["data"=>"","label"=>"Seleccione"]);
		return $res;
	}

	public static function formaPago($id_forma_pago){
		$tmp = Models\Runtime\EavModel::item([
			"filter"=>"items.category_id=:category_id and items.item_id=:id_forma_pago",
			"params"=>[
				":category_id"=>static::$cat_formas_pago,
				":id_forma_pago"=>$id_forma_pago
			],
			"order"=>"{nombre} asc"
		]);

		return $tmp;
	}

	public static function __procesarUnidades($inventario){
		$inv = [];
		$tmp = explode("\n",trim($inventario));

		foreach($tmp as $aux){
			if( empty($aux) ) continue;

			$aux = explode(":",trim($aux));
			if (!isset($aux[2])) {
				$aux[2] = 0;
			}

			$inv[]=(object)[
				"variacion"=>"{$aux[0]}:{$aux[1]}:{$aux[2]}",
				// "talla"=>coalesce_blank($aux[0]),
				"color"=>coalesce_blank($aux[1]),
				"unidades"=>coalesce_zero($aux[2]),
				"talla"=>coalesce_zero($aux[0])
			];
		}

		return $inv;
	}

	public static function producto($p){
		$p->lock = coalesce_false($p->lock);
		$res = new stdClass();

		if( !empty($p->slug) ){
			//$filter="items.category_id=:category_id and items.slug=:slug";
			//$params=[":category_id"=>6,":slug"=>$p->slug];

			$filter="items.slug=:slug";
			$params=[":slug"=>$p->slug];
		}

		if( !empty($p->item_id) ){
			$filter="items.item_id=:item_id";
			$params=[":item_id"=>$p->item_id];
		}


		$res = EavModel::item((object)[
			"filter"=>$filter,
			"params"=>$params,
			"lock"=>$p->lock
		]);

		//cuando tiene variacion
		$res->inventario = ($res)?static::__procesarUnidades($res->attrs->inventario->data[0]):$res->inventario[]=null;

		return $res;
	}



	public static function unidadesDisponibles(stdClass $p,$lock=false){

		// resultado de $p
		// stdClass Object
		// (
		//     [plat_id] => 1
		//     [id_carro] => 521
		//     [id_detalle] => 4
		//     [item_id] => 178
		//     [referencia] => estuche_00001
		//     [descripcion] => Estuche AairPod LT01
		//     [variacion] =>
		//     [color] => rojo
		//     [unidades] => 1
		// 		 [talla] => L
		//     [precio] => 24000.00
		//     [por_impuesto] => 19.00
		//     [por_descuento] => 0.00
		//     [precio_venta] => 0
		//     [subtotal] => 24000.00
		//     [i_ts] => 2019-01-10 12:32:42
		//     [u_ts] => 2019-01-10 12:32:42
		//     [tipo] => producto
		// )


		$producto = static::producto((object)[
			"item_id"=>$p->item_id,
			"lock"=>$lock
		]);

		
		$tmp1 = explode("\n",$producto->attrs->inventario->data[0]);
		
		// $tmp2 = explode("\n",$producto->attrs->inventario_ingles->data[0]);
		

		
		// resultado de $tmp
		// Array
		// (
		//     [0] => L:Blanco:10
		//     [1] => M:rojo:10
		//     [2] => S:azul:10
		// )

		foreach ($tmp1 as $aux) {
			if( empty($aux) ) continue;
			$aux = explode(":",trim($aux));

			if(strtolower($p->color) == strtolower($aux[1]) && strtolower($p->talla) == strtolower($aux[0])){
				return $aux[2]; //unidades del color
			}

		}

		// foreach ($tmp2 as $aux) {
		// 	if( empty($aux) ) continue;
		// 	$aux = explode(":",trim($aux));

		// 	if(strtolower($p->color) == strtolower($aux[1]) && strtolower($p->talla) == strtolower($aux[0])){
		// 		return $aux[2]; //unidades del color
		// 	}

		// }

		return 0;
	}




	public static function ajustarUnidades(stdClass $p){

		$item = Models\Runtime\EavModel::load($p->item_id);
		$inv = static::__procesarUnidades($item->inventario);
		// $inv_ing = static::__procesarUnidades($item->inventario_ingles);

		$tmp = "";
		$tmp_ing = "";


		//Cuando hay talla
		// foreach($inv as $r){
		// 	$tmp.="{$r->talla}:{$r->color}:". ( $r->unidades - $p->unidades )."\n";
		// }

		//cuando solo hay color
		// foreach($inv as $r){
		// 	$tmp.=($p->color == $r->color)?"{$r->color}:".($r->unidades-$p->unidades)."\n":"{$r->color}:{$r->unidades}\n";
		// }

		//cuando hay talla
		foreach($inv as $r){
			if($p->color !== $r->color){
				$tmp.="{$r->talla}:{$r->color}:{$r->unidades}\n";
			} else {
				if($p->talla == $r->talla){
					$tmp.="{$r->talla}:{$r->color}:".($r->unidades-$p->unidades)."\n";
				} else{
					$tmp.="{$r->talla}:{$r->color}:{$r->unidades}\n";
				}
			}
		}

		// foreach($inv_ing as $r){
		// 	if($p->color !== $r->color){
		// 		$tmp_ing.="{$r->talla}:{$r->color}:{$r->unidades}\n";
		// 	} else {
		// 		if($p->talla == $r->talla){
		// 			$tmp_ing.="{$r->talla}:{$r->color}:".($r->unidades-$p->unidades)."\n";
		// 		} else{
		// 			$tmp_ing.="{$r->talla}:{$r->color}:{$r->unidades}\n";
		// 		}
		// 	}
		// }
		

		$item->inventario = $tmp;
		// $item->inventario_ingles = $tmp_ing;


		EavModel::save($item);

	}


	public static function historicoCompras($id_cliente){
		global $db;
		$ca=new Cm\DbQuery($db);

		$sql="
		select
			e.id_carro,
			e.id_pedido,
			e.fechahora,
			e.fechahora_pago,
			e.estado,
			e.nombre_forma_pago,
			d.descripcion,
			d.item_id,
			d.descripcion,
			d.referencia,
			d.variacion,
			d.por_impuesto,
			d.precio,
			format(d.precio,2) as precio_fmt,
			e.subtotal,
			format(e.subtotal,2) as subtotal_fmt,
			e.total,
			e.total_usd,
			format(e.total,2) as total_fmt,
			e.total_transporte,
			format(e.total_transporte,2) as total_transporte_fmt,
			d.unidades


		from pedidos_d d
		join pedidos_e e on (e.plat_id=d.plat_id and e.id_carro=d.id_carro)
		where
			e.id_cliente = :id_cliente
			and e.estado<>'carro'

		order by
			e.fechahora desc

		";


		$ca->prepare($sql);
		$ca->bindValue(":id_cliente",$id_cliente);
		$ca->exec();
		$res = $ca->fetchAll();

/*		foreach($res as &$r){
			$tmp = static::producto( (object)["item_id"=>$r->item_id] );
			$r->attrs = ($tmp->inventario)?$tmp->attrs:$tmp;
			//$r->attrs = $tmp->attrs;
		}*/

		return $res;
	}


}

?>

<?php
if( in_array("tienda",$cfg["modules"]) ){
	require_once "{$cfg["appPath"]}/public/modelos/Pedido.php";
	require_once "{$cfg["appPath"]}/public/modelos/Tienda.php";
}




$exports[]="Tienda_PedidosE";
class Tienda_PedidosE {

	public function ers(stdClass $p=null){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();

		$res->estados = [
			["data"=>"reserva","label"=>"Reserva"],
			["data"=>"pagado","label"=>"Pagado"],
			["data"=>"anulado","label"=>"Anulado"]
		];

		return $res;
	}


	public function load(stdClass $p){
		global $cfg;
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();
		$res->ers = $this->ers($p);


		$sql="
		select
			id_carro,
			id_pedido,
			fechahora,
			fechahora_pago,
			nombre_forma_pago,

			format(total,2) as total,
			format(total_usd,2) as total_usd,
			format(total_iva,2) as total_iva,
			format(total_transporte,2) as total_transporte,
			total_unidades,
			estado,

			com_nombres,
			com_apellidos,
			com_direccion,
			com_ciudad,
			com_telefono_fijo,
			com_telefono_celular,
			com_correo_electronico,

			ent_nombres,
			ent_apellidos,
			ent_direccion,
			ent_ciudad,
			ent_telefono,
			ent_telefono_celular,

			notas

		from pedidos_e
		where
			plat_id=:plat_id
			and id_carro=:id_carro

		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":id_carro",$p->id_carro);
		$ca->exec();
		$res->enc = $ca->fetch();


		$sql="
		select
			item_id,
			descripcion,
			variacion,
			color,
			talla,
			unidades,
			por_impuesto,
			precio,
			format(precio,2) as precio_fmt,
			format(precio_usd,2) as precio_usd_fmt,
			subtotal,
			format(subtotal,2) as subtotal_fmt

		from pedidos_d
		where
			id_carro=:id_carro

		order by
			descripcion
		";

		$ca->prepare($sql);
		$ca->bindValue(":id_carro",$res->enc->id_carro);
		$ca->exec();
		$res->det = $ca->fetchAll();

		foreach($res->det as &$r){
			$tmp = Models\Runtime\EavModel::item( (object)[
				"filter"=>"items.item_id=:item_id",
				"params"=>[
					":item_id"=>$r->item_id
				]
			]);



			$r->attrs = $tmp->attrs;
			$r->imagen = "http://{$cfg["appHost"]}{$cfg["siteRoot"]}/imagenes/imagen.php?imagenid=".$r->attrs->imagen_1->data[0];
		}

		return $res;
	}

	public function save(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		$db->transaction();
		$pedido=new PedidoMdl($p->id_pedido);
		$pedido->cambiarEstado( $p );
		$db->commit();

		return $res;
	}

	public function eliminar(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();



		$db->transaction();
		$pedido=new PedidoMdl($p->id_pedido);
		$pedido->eliminar( $p );
		$db->commit();

		return $res;
	}
}

?>

<?php
if( in_array("tienda",$cfg["modules"]) ){
	require_once "{$cfg["appPath"]}/public/modelos/Pedido.php";
	require_once "{$cfg["appPath"]}/public/modelos/Tienda.php";
}

$exports[]="PedidosL";
class PedidosL {

	public function page($p){
		$si=App::session($p);
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sqlFilter=["1=1"];

		if( !empty($p->filter) ){
			$sqlFilter[]="and (";

			$sqlFilter[]="lower(id_pedido) like lower(:q)";
			$sqlFilter[]="or";
			$sqlFilter[]="lower(com_nombres) like lower(:q)";
			$sqlFilter[]="or";
			$sqlFilter[]="lower(com_apellidos) like :q";
			$sqlFilter[]="or";
			$sqlFilter[]="lower(com_correo_electronico) like lower(:q)";
			$sqlFilter[]="or";
			$sqlFilter[]="lower(com_direccion) like lower(:q)";
			$sqlFilter[]="or";
			$sqlFilter[]="lower(com_telefono_fijo) like lower(:q)";
			$sqlFilter[]="or";
			$sqlFilter[]="lower(com_telefono_celular) like lower(:q)";


			$sqlFilter[]=")";

		}

		$sqlFilter = implode(" ",$sqlFilter);

		$sql="
		select
			id_carro,
			id_pedido,
			fechahora,
			fechahora_pago,
			nombre_forma_pago,
			estado,
			com_nombres,
			com_apellidos,
			com_correo_electronico,
			com_ciudad,
			com_direccion,
			com_telefono_fijo,
			com_telefono_celular,
			total,
			total_usd,
			total_iva,
			total_unidades,
			format(total,2) as total_fmt,
			format(total_usd,2) as total_usd_fmt

		from pedidos_e
		where
			plat_id=:plat_id
			and estado<>'carro'
			and {$sqlFilter}

		order by
			id_pedido desc

		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":q",'%'.$p->filter.'%');

		return $ca->execPage($p);
	}

	/*
	public function delete(stdClass $p){
		$si=App::session($p);
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$db->transaction();

		$ca->prepareTable('table');
		$ca->bindWhere(':plat_id',$si->plat_id);
		$ca->bindWhere(':id',$p->id);
		$ca->execDelete();

		$db->commit();

		return;
	}
	*/

}


?>

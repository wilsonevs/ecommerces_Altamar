<?php

class TarjetaRegaloMdl {



	public static function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public static function generar($idPedido,$idTr,$unidades){
		global $db;
		$ca=new Cm\DbQuery($db);

		$sql="select * from tarjetaregalo where plat_id=:plat_id and id_pedido=:id_pedido and id_tr=:id_tr";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":id_pedido",$idPedido);
		$ca->bindValue(":id_tr",$idTr);
		$ca->exec();

		if( $ca->size() > 0 ){

			$ca->prepareTable("tarjetaregalo");
			$ca->bindValue(":estado","activo");

			$ca->bindWhere(":plat_id",1);
			$ca->bindWhere(":id_pedido",$idPedido);
			$ca->execUpdate();

			return 'update';
		}


		for($i=0;$i<$unidades;$i++){
			$tr = Models\Runtime\EavModel::item( (object)[
				"filter"=>"items.item_id = {$idTr}"
			]);

			$valor = $tr->attrs->valor_tarjeta_regalo->data[0];
			if( empty($valor) ){
				throw new Cm\PublicException("Valor de Tarjeta Regalo Invalido");
			}

			$referencia = static::generateRandomString(10);
			$secreto = static::generateRandomString(8);

			$ca->prepareTable("tarjetaregalo");
			$ca->bindValue(":plat_id",1);
			$ca->bindValue(":id_pedido",$idPedido);
			$ca->bindValue(":id_tr",$idTr);
			$ca->bindValue(":fechahora","current_timestamp",false);
			$ca->bindValue(":referencia",$referencia);
			$ca->bindValue(":secreto",$secreto);
			$ca->bindValue(":valor",$valor);
			$ca->bindValue(":saldo",$valor);
			$ca->execInsert();
		}

		return 'insert';
	}

	public static function anularDesdePedido($idPedido){

		global $db;
		$ca=new Cm\DbQuery($db);

		$ca->prepareTable("tarjetaregalo");
		$ca->bindValue(":estado","anulado");

		$ca->bindWhere(":plat_id",1);
		$ca->bindWhere(":id_pedido",$idPedido);
		$ca->execUpdate();

		return true;
	}

	public static function consultar($referencia,$secreto = null){
		global $db;
		$ca=new Cm\DbQuery($db);

		$sql="select * from tarjetaregalo where plat_id=:plat_id and referencia=:referencia";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":referencia",$referencia);
		$ca->exec();

		if( $ca->size() ==  0 ){
			throw new Cm\PublicException("Tarjeta Regalo Invalida",404);
		}

		$r = $ca->fetch();


		if( $secreto!==null && $r->secreto != $secreto ){
			throw new Cm\PublicException("Tarjeta Regalo Invalida",401);
		}

		return $r;
	}
}


?>

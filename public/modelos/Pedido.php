<?php
require_once __DIR__.'/Carro.php';

class PedidoMdl extends CarroMdl {

	public function __construct($idPedido=null){
		parent::__construct(null,$idPedido);
	}

	public function cambiarEstado(stdClass $p){
		global $db;
		$ca=new Cm\DbQuery($db);

		$p->notas = coalesce_blank($p->notas);

		$enc=$this->enc();
		$det=$this->det();


		$fp = TiendaMdl::formaPago($enc->id_forma_pago);

		//de anulado a reserva,pagado
		if( $enc->estado=='anulado' && in_array($p->estado,['reserva','pagado']) ){
			$this->reservarInventario();
		}

		//de reverva,pagado a anulado
		if(in_array($enc->estado,['reserva','pagado']) && $p->estado=='anulado' ){
			$this->liberarInventario();
		}

		//tarjetas regalo vendidas
		// if( $p->estado=='pagado' ){
		// 	foreach($det as $r){
		// 		if( $r->tipo =="tarjetaregalo" ){
		// 			TarjetaRegaloMdl::generar($enc->id_pedido,$r->item_id,$r->unidades);
		// 		}
		// 	}
		// }
		// else {
		// 	TarjetaRegaloMdl::anularDesdePedido($enc->id_pedido);
		// }


		// $fechaHoraPago = $p->estado == 'pagado' ? "current_timestamp":"";

		if($p->estado == 'pagado'){
			$query=",fechahora_pago=:fechahora_pago";
		} else{
			$query=null;
		}

		$sql="
		update pedidos_e
		set
			estado='".$p->estado."',
			notas='".$p->notas."'
		".$query."
		where
			plat_id=1
			and id_pedido=".$p->id_pedido;

		$ca->prepare($sql);
		$ca->bindValue(":fechahora_pago","current_timestamp",false);
		$ca->exec();


		//tarjetas regalo utilizadas para pagar

	}


	public function eliminar(stdClass $p){
		global $db;
		$ca=new Cm\DbQuery($db);

		$this->liberarInventario();

		$sql = "
			delete from pedidos_e where plat_id = :plat_id and id_carro = :id_carro
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":id_carro",$p->id_carro);
		$ca->exec();


		$sql = "
			delete from pedidos_d where plat_id = :plat_id and id_carro = :id_carro
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":id_carro",$p->id_carro);
		$ca->exec();



	}



}
?>

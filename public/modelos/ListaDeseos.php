<?php


class ListaDeseosMdl {

	public function __construct($idCliente){
		global $db;
		$ca=new Cm\DbQuery($db);

		$this->id_cliente = $idCliente;

		$sql="select * from listadeseos_e where plat_id=:plat_id and id_cliente = :id_cliente";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":id_cliente",$idCliente);
		$ca->exec();

		if( $ca->size() > 0 ){
			$tmp = $ca->fetch();

			$this->id_lista = $tmp->id_lista;
			return;
		}


		$this->id_lista=App::nextval("listadeseos_e_id_lista");

		$ca->prepareTable("listadeseos_e");
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":id_lista",$this->id_lista);
		$ca->bindValue(":id_cliente",$this->id_cliente);
		$ca->bindValue(":nombre","Lista por Defecto");

		$ca->execInsert();
	}


	public function det(){
		global $db;
		$ca=new Cm\DbQuery($db);
		$res = [];

		$sql="select * from listadeseos_d where plat_id=:plat_id and id_lista=:id_lista";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":id_lista",$this->id_lista);
		$ca->exec();

		foreach( $ca->fetchAll() as $r){
			$res[] = Models\Runtime\EavModel::item( (object)[
				"filter"=>"item_id = {$r->item_id}"
			]);
		}

		return $res;
	}


	public function agregarItem(stdClass $p){
		global $db;
		$ca=new Cm\DbQuery($db);


		$sql="select * from listadeseos_d where plat_id=:plat_id and id_lista=:id_lista and item_id=:item_id";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":id_lista",$this->id_lista);
		$ca->bindValue(":item_id",$p->item_id);
		$ca->exec();

		if($ca->size() > 0 ) {
			return;
		}

		$ca->prepareTable("listadeseos_d");
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":id_lista",$this->id_lista);
		$ca->bindValue(":item_id",$p->item_id,false);
		$ca->bindValue(":i_ts","current_timestamp",false);
		//$ca->bindValue(":u_ts","current_timestamp",false);
		$ca->execInsert();
	}

	public function removerItem(stdClass $p){
		global $db;
		$ca=new Cm\DbQuery($db);
		//$enc=$this->enc();

		$ca->prepareTable("listadeseos_d");
		$ca->bindWhere(":plat_id",1);
		$ca->bindWhere(":id_lista",$this->id_lista);
		$ca->bindWhere(":item_id",$p->item_id);
		$ca->execDelete();
	}

}
?>

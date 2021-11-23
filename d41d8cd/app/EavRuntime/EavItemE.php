<?php
require_once "{$cfg["modelsPath"]}/runtime/EavModel.php";
require_once "{$cfg["modelsPath"]}/runtime/EavItems.php";
require_once "{$cfg["modelsPath"]}/runtime/EavCategories.php";

use Models\Runtime\EavModel;


$exports[]="EavRuntime_EavItemE";
class EavRuntime_EavItemE {

	public function ers(stdClass $p=null){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();

		$res->options =[
			['data'=>'1','label'=>'Uno'],
		];


		//eav struct
		$res->eav = new stdClass();
		$res->eav->st = Models\Runtime\EavModel::struct( (object)["category_id"=>$p->category_id] );
		$res->eav->st->category = Models\Runtime\EavCategories::categoryById($p->category_id);

		$res->eav->item = (object)["attrs"=>[]];


		return $res;
	}


	public function load(stdClass $p){
		global $cfg;
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();
		$res->ers = $this->ers($p);

		$res->ers->eav->item = Models\Runtime\EavModel::item((object)[
			"filter"=>"items.item_id=:item_id",
			"params"=>[
				":item_id"=>$p->item_id
			]
		]);


		//load resources
		foreach( $res->ers->eav->item->attrs as $field=>$r){

			if( $r->type=="imagefield" && !empty($r->data[0]) ){

				$rsId=$r->data[0];
				$gfs=new Cm\GridFsDirect($db);
				$res->ers->eav->item->attrs->{$field}->extra = $gfs->loadById( $rsId );
				$res->ers->eav->item->attrs->{$field}->extra->src = "http://{$cfg["appHost"]}{$cfg["siteRoot"]}/imagenes/imagen.php?imagenid={$rsId}";
			}


			if( $r->type=="filefield" && !empty($r->data[0]) ){

				$rsId=$r->data[0];
				$gfs=new Cm\GridFsDirect($db);
				$res->ers->eav->item->attrs->{$field}->extra = $gfs->loadById( $rsId );
				$res->ers->eav->item->attrs->{$field}->extra->src = "http://{$cfg["appHost"]}{$cfg["siteRoot"]}/imagenes/imagen.php?imagenid={$rsId}";
			}

		}

		return $res;
	}


	public function save(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$res=new stdClass();

		$p->item_id = is_numeric($p->item_id) ? $p->item_id : '';


		foreach($p as $k0=>$v0){
			if( !is_array($v0) ) continue;

			$value=[];
			foreach($v0 as $k1=>$v1){
				$value[] = $v1->data;
			}

			$p->{$k0} = $value;
		}

		//App::log($p);return 1;

		$db->transaction();
		$res=Models\Runtime\EavModel::save($p, (object)["context"=>"admin"]);
		$db->commit();
		return $res;
	}

	public function remove(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$res=new stdClass();

		$p->item_id = is_numeric($p->item_id) ? $p->item_id : '';

		$db->transaction();
		EavModel::delete($si,$p->item_id);
		$db->commit();
		return true;
	}

}

?>

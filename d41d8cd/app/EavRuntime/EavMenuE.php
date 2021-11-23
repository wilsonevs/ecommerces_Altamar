<?php

$exports[]="EavRuntime_MenuE";
class EavRuntime_MenuE {

	public function ers(stdClass $p=null){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();

		$res->targets = [
			["data"=>"_self","label"=>"Misma Ventana"],
			["data"=>"_blank","label"=>"Nueva Ventana"]
		];

		return $res;
	}


	public function load(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();
		$res->ers = $this->ers($p);

		if( $p->category_id==-1 ){
			throw new Cm\PublicException("La categoria principal no es editable");
		}

		$sql="
		select
			category_id,
			category_name,
			url,
			target,
			events

		from eav_categories
		where
			plat_id=:plat_id
			and category_id=:category_id
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":category_id",$p->category_id);
		$ca->exec();
		$res = object_merge($res,$ca->fetch());

		$res->events = json_decode($res->events);
		return $res;
	}

	public function save(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		$p->url = coalesce_blank($p->url) ?: '';
		$p->target = coalesce_blank($p->target) ?:'_self';

		$p->events = coalesce_blank($p->events);
		$p->events = json_encode($p->events);

		$ca->prepareTable('eav_categories');

		$ca->bindValue(":category_name",$p->category_name);
		$ca->bindValue(":url",$p->url);
		$ca->bindValue(":target",$p->target);
		$ca->bindValue(":events",$p->events);

		$ca->bindWhere(':plat_id',$si->plat_id);
		$ca->bindWhere(':category_id',$p->category_id);
		$ca->execUpdate();

		return $res;
	}
}

?>

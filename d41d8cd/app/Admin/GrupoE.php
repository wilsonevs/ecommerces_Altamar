<?php

$exports[]="Admin_GrupoE";
class Admin_GrupoE {

	public function ers(stdClass $p=null){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();

		$res->user_types =[
			['data'=>'architect','label'=>'architect'],
			['data'=>'admin','label'=>'admin'],
			['data'=>'guest','label'=>'guest']
		];

		return $res;
	}


	public function load(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();
		$res->ers = $this->ers($p);

		$sql="
		select
			group_id,
			group_name

		from plat_groups
		where
			plat_id=:plat_id
			and group_id=:group_id
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":group_id",$p->group_id);
		$ca->exec();

		$res = object_merge($res,$ca->fetch());
		return $res;
	}


	public function save(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();



		$ca->prepareTable('plat_groups');

		$ca->bindValue(':plat_id',$si->plat_id);

		$ca->bindValue(':group_name',$p->group_name);
		$ca->bindValue(':notes',$p->notes);



		if( empty($p->group_id) ){
			$groupId=App::nextval("plat_groups_group_id");
			$ca->bindValue(":group_id",$groupId);

			$ca->execInsert();
		}
		else {
			$groupId=$p->group_id;

			$ca->bindWhere(':plat_id',$si->plat_id);
			$ca->bindWhere(':group_id',$groupId);
			$ca->execUpdate();
		}

		$res->group_id = $groupId;
		return $res;
	}

}


?>

<?php

$exports[]="Admin_GrupoL";
class Admin_GrupoL {

	public function page($p){
		$si=App::session($p);
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sqlFilter=[];
		$fields = "field1"; //field1,field2
		$sqlFilter[] = $ca->sqlFieldsFilters($fields, $p->filter);
		$sqlFilter=implode(" and ",$sqlFilter);

		$sql="
		select
			group_id,
			group_name

		from plat_groups

		where
			plat_id=:plat_id

		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);

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

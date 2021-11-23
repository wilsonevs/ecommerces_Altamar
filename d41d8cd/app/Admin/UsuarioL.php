<?php

$exports[]="Admin_UsuarioL";
class Admin_UsuarioL {

	public static function page($p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sqlFilter=[];
		$fields = "user_name,user_type";
		$sqlFilter[] = $ca->sqlFieldsFilters($fields, $p->filter);
		$sqlFilter=implode(" and ",$sqlFilter);

		$sql="
		select
			user_id,
			user_type,
			login,
			user_name

		from plat_users
		where
			plat_id=:plat_id
			and {$sqlFilter}

		order by
			login
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);

		return $ca->execPage($p);
	}

	/*
	public function delete(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$db->transaction();

		$ca->prepareTable('plat_users');
		$ca->bindWhere(':plat_id',$si->plat_id);
		$ca->bindWhere(':user_id',$p->user_id);
		$ca->execDelete();

		$db->commit();

		return;
	}
	*/

}


?>

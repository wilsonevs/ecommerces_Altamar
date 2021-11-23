<?php

$exports[]="EavTypeL";
class EavTypeL {

	public static function page($p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sqlFilter=[];
		$fields = "type_name,notes";
		$sqlFilter[] = $ca->sqlFieldsFilters($fields, $p->filter);
		$sqlFilter=implode(" and ",$sqlFilter);


		$sql="
		select
			type_id,
			type_name,
			notes

		from eav_types
		where
			plat_id=:plat_id
			and type_id > 0
			and {$sqlFilter}

		order by
			notes
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);

		return $ca->execPage($p);
	}

}


?>

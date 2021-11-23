<?php

$exports[]="EavRuntime_EavMenuView";
class EavRuntime_EavMenuView {

	public function load(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="
		select
			a.parent_id,
			a.category_id,
			a.category_name,
			a.category_order,
			a.category_path,
			a.type_id,
			case
				when a.type_id=-1 then '-Expandible-'
				when a.type_id=-2 then '-Enlace-'
				else coalesce(b.type_name,'')
			end as type_name,
			true as collapsed,

			a.target,
			a.url

		from eav_categories a
		left join eav_types b on (a.plat_id=b.plat_id and a.type_id=b.type_id)
		where
			a.plat_id=:plat_id
			and a.category_id<>-1

		order by
			parent_id,
			category_order,
			category_name
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->exec();

		return $ca->fetchAll();
	}
}

?>

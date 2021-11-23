<?php
require_once "{$cfg["modelsPath"]}/runtime/EavModel.php";
require_once "{$cfg["modelsPath"]}/runtime/EavItems.php";

$exports[]="Eav";
class Eav {

	public function acAttrDs(stdClass $p){
		$si=App::session();
		return Models\Runtime\EavModel::acAttrDs($si,$p);
	}


	/*
	public function completeRelAttrs(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=[];

		$dsFilters=[];

		$sql="
		select
			d.attr_id,
			a.attr_name,
			d.filter_attr_id,
			d.ds_type_id,
			d.ds_filter_attr_id

		from eav_attr_deps d
		join eav_attrs a on (
			d.plat_id=a.plat_id
			and d.type_id=a.type_id
			and d.filter_attr_id=a.attr_id
		)
		where
			d.plat_id=:plat_id
			and d.type_id=:type_id
			and d.attr_id=:attr_id
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":type_id",$p->type_id);
		$ca->bindValue(":attr_id",$p->attr_id);
		$ca->exec();

		foreach($ca->fetchAll() as $k=>$r){
			$dsValue="";


			//App::log($r);
			//tokenfield
			$dsValue = $p->dr->{$r->attr_name}[0]->data;

			$a="f{$k}";

			$dsFilters[]="
			join eav_values {$a} on (
				{$a}.plat_id=r.plat_id
				and {$a}.item_id=r.item_id
				and {$a}.type_id={$r->ds_type_id}
				and {$a}.attr_value='{$dsValue}'
			)
			";
		}
		$dsFilters=implode(" ",$dsFilters);

		$sql="
		select
			r.data,
			r.label
		from view_rels_data r
		{$dsFilters}

		where
			r.plat_id=:plat_id
			and r.type_id=:type_id
			and r.attr_id=:attr_id
			and (
				lower(r.label) like lower('%:filter%')
				or
				lower(r.label) like lower('%:filter%')
			)
		order
			by r.label
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":type_id",$p->type_id);
		$ca->bindValue(":attr_id",$p->attr_id);
		$ca->bindValue(":filter",$p->filter,false);
		//App::log($ca->preparedQuery());
		$ca->exec();



		$res=$ca->fetchAll();

		return $res;
	}
	*/

}
?>

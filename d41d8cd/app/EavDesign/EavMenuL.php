<?php

$exports[]="EavDesign_EavMenuL";
class EavDesign_EavMenuL {

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

	public function reorder(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		//App::log($p);

		$fn = function($items,$parentId) use ($si,&$fn,&$ca) {

			$index = 0;
			foreach($items as $r){
				$ca->prepareTable("eav_categories");
				$ca->bindValue(":category_order",$index++);
				$ca->bindValue(":parent_id",$parentId);

				$ca->bindWhere(":plat_id",$si->plat_id);
				$ca->bindWhere(":category_id",$r->category_id);
				$ca->execUpdate();

				if( !empty($r->nodes) ){
					$fn($r->nodes,$r->category_id);
				}
			}
		};

		$db->transaction();
		$fn($p->items,-1);
		$db->commit();


		//validar que solo se pueda modificar un parent_id al tiempo
		/*
		$db->transaction();
		foreach($p->items as $r){
			$ca->prepareTable("eav_categories");
			$ca->bindValue(":category_order",$r->order);
			$ca->bindValue(":parent_id",$r->parent_id);

			$ca->bindWhere(":plat_id",$si->plat_id);
			$ca->bindWhere(":category_id",$r->id);
			$ca->execUpdate();
		}
		$db->commit();
		*/




		return true;
	}

	public function delete(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		if( $p->category_id == -1 ){
			throw new Cm\PublicException("No se puede eliminar la categoria raíz");
		}


		$db->transaction();

		$sql="select * from eav_categories where plat_id=:plat_id and parent_id=:parent_id";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":parent_id",$p->category_id);
		$ca->exec();

		if( $ca->size() > 0 ){
			throw new Cm\PublicException("No se puede eliminar una categoria, con categorias hijas");
		}

		$ca->prepareTable("eav_categories");
		$ca->bindWhere(":plat_id",$si->plat_id);
		$ca->bindWhere(":category_id",$p->category_id);
		$ca->execDelete();

		$sql="
		delete from eav_values
		where
			plat_id=:plat_id
			and item_id in (
				select item_id from eav_items where plat_id=:plat_id and category_id=:category_id

			)
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":category_id",$p->category_id);
		$ca->exec();

		$sql="
		delete from eav_items
		where
			plat_id=:plat_id
			and category_id=:category_id
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":category_id",$p->category_id);
		$ca->exec();

		$db->commit();

		return true;
	}
}

?>

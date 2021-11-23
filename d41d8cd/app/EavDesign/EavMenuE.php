<?php

$exports[]="EavDesign_EavMenuE";
class EavDesign_EavMenuE {

	public function ers(stdClass $p=null){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();

		$res->targets = [
			["data"=>"_self","label"=>"Misma Ventana"],
			["data"=>"_blank","label"=>"Nueva Ventana"]
		];

		/*
		$sql="
		select
			category_id as data,
			category_path as label

		from eav_categories
		where
			plat_id=:plat_id
		order by
			category_path
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->exec();
		$res->parents=$ca->fetchAll();
		*/


		/*
		$sql="
		select
			type_id as data,
			type_name as label
		from eav_types
		where
			plat_id=:plat_id
		order by
			type_name
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->exec();
		$res->types=$ca->fetchAll();

		array_unshift($res->types,["data"=>'-3',"label"=>"-- Navegacion --"]);
		array_unshift($res->types,["data"=>'-2',"label"=>"-- Enlace --"]);
		array_unshift($res->types,["data"=>'-1',"label"=>"-- Expandible --"]);
		*/


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
			parent_id,
			category_name,
			category_path,
			category_order,
			type_id,
			slug,
			url,
			target,
			events,
			custom_export,
			notes

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

		$res->type_id = $this->acTypes((object)[],"type_id={$res->type_id}");

		$res->events = json_decode($res->events);
		return $res;
	}


	public function save(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		$p->automatic = coalesce_false($p->automatic);


		$p->category_id = coalesce_blank($p->category_id);
		$p->category_name = coalesce_blank($p->category_name) ?: "Nueva Categoria";
		$p->parent_id = coalesce_blank($p->parent_id) ?: -1;
		$p->type_id = coalesce_blank($p->type_id) ?: -1;
		$p->category_order = coalesce_zero($p->category_order);
		$p->custom_export = coalesce_blank($p->custom_export);
		$p->url = coalesce_blank($p->url);
		$p->target = coalesce_blank($p->target);

		$p->events = coalesce_blank($p->events);
		$p->events = json_encode($p->events);
		$p->notes = coalesce_blank($p->notes);

		if( !$p->automatic ){
			$p->slug = coalesce_blank($p->slug) ?: Cm\WebTools::normalizeUrl($p->category_name);
		}
		else {
			$p->category_order = $this->__minCategoryOrden($p->parent_id);
			$p->slug = '';
		}

		if( $p->category_id==-1){
			throw new Cm\PublicException("No se puede editar la categoria raíz");
		}

		$db->transaction();

		$ca->prepareTable("eav_categories");

		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":category_name",$p->category_name);
		$ca->bindValue(":parent_id",$p->parent_id);
		$ca->bindValue(":category_path","");
		$ca->bindValue(":type_id",$p->type_id);
		$ca->bindValue(":category_order",$p->category_order?:0);
		$ca->bindValue(":category_level",0);
		$ca->bindValue(":slug",$p->slug);
		$ca->bindValue(":slug_path","");

		$ca->bindValue(":url",$p->url?:"");
		$ca->bindValue(":target",$p->target?:"_self");

		$ca->bindValue(":events",$p->events);
		$ca->bindValue(":custom_export",$p->custom_export,true);
		$ca->bindValue(":notes",$p->notes);

		if( empty($p->category_id) ){
			$categoryId=App::nextval("eav_categories_category_id");
			$ca->bindValue(":category_id",$categoryId);

			$ca->execInsert();
		}
		else {
			$categoryId=$p->category_id;

			$ca->bindWhere(":category_id",$categoryId);
			$ca->execUpdate();
		}


		self::updateTree();

		$db->commit();

		$res->category_id = $categoryId;
		return $res;
	}

	private static function updateTree(){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="
		select
			category_id,
			parent_id,
			category_name,
			category_path,
			slug

		from eav_categories
		order by
			category_id
		";
		$ca->prepare($sql);
		$ca->exec();
		$rl=$ca->fetchAll();

		$recursive=function($r,$rl,$level) use (&$recursive){
			if( $r->category_id==-1 ) return (object)["path"=>"/","level"=>0];
			if( $r->parent_id==-1) return (object)["path"=>"/{$r->category_name}","level"=>1];


			foreach($rl as $v ){
				if( $r->parent_id==$v->category_id ){
					$tmp =$recursive($v,$rl,$level);

					return (object)[
						"path"=>"{$tmp->path}/{$r->slug}",
						"level"=>$tmp->level+1
					];

					//return $recursive($v,$rl)."/{$r->category_name}";
				}
			}


			throw new Cm\PublicException("Categoria huerfana, id = {$r->category_id} parent_id = {$r->parent_id}");
			//return "**Menu Invalid**";
		};


		foreach($rl as $r){
			if( $r->category_id == -1 ) continue;

			$tmp=$recursive($r,$rl,0);

			if(!is_object($tmp) || !isset($tmp->path) ){
				throw new Cm\PublicException($tmp);
			}


			$sql="
			update eav_categories
			set
				category_path=:category_path,
				category_level=:category_level,
				slug_path=:slug_path

			where
				plat_id=:plat_id
				and category_id=:category_id
			";

			$ca->prepare($sql);
			$ca->bindValue(":plat_id",$si->plat_id);
			$ca->bindValue(":category_path",$tmp->path);
			//$ca->bindValue(":slug",Cm\WebTools::normalizeUrl($r->category_name) );
			$ca->bindValue(":slug_path",Cm\WebTools::normalizeUrl($tmp->path) );
			$ca->bindValue(":category_level",$tmp->level);

			$ca->bindValue(":category_id",$r->category_id);
			$ca->exec();
		}
	}



	//refresca category_path
	private static function __updateTree(){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="
		select
			category_id,
			parent_id,
			category_name,
			category_path
		from eav_categories
		order by
			category_id
		";
		$ca->prepare($sql);
		$ca->exec();
		$rl=$ca->fetchAll();

		$recursive=function($r,$rl) use (&$recursive){
			if( $r->category_id==-1 ) return "/";
			if( $r->parent_id==-1) return "/{$r->category_name}";


			foreach($rl as $v ){

				if( $r->parent_id==$v->category_id ){
					return $recursive($v,$rl)."/{$r->category_name}";
				}
			}

			return "**Menu Invalid**";
		};


		foreach($rl as $r){
			if( $r->category_id == -1 ) continue;

			$menuPath = $recursive($r,$rl);
			$sql="
			update eav_categories
			set
				category_path=:category_path

			where
				category_id=:category_id
			";
			$ca->prepare($sql);
			$ca->bindValue(":category_path",$menuPath);
			$ca->bindValue(":category_id",$r->category_id);
			$ca->exec();
		}
	}

	private function __minCategoryOrden($parentId){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="
		select
			min(category_order) as category_order

		from eav_categories
		where
			plat_id=:plat_id
			and parent_id=:parent_id
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":parent_id",$parentId);
		$ca->exec();

		if( $ca->size() == 0 ) return 0;
		return $ca->fetch()->category_order - 1;
	}

	public function acTypes(stdClass $p,$staticFilter='1=1'){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$p->filer = coalesce_blank($p->filter);

		$sqlFilter=[];
		$fields = "type_name,notes";
		$sqlFilter[] = $ca->sqlFieldsFilters($fields, $p->filter);
		$sqlFilter=implode(" and ",$sqlFilter);


		$sql="
		select
			type_id as data,
			type_name as label
		from eav_types
		where
			plat_id=:plat_id
			and {$sqlFilter}
			and {$staticFilter}

		order by
			case when type_id < 0 then 0 else 1 end,
			type_name
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->exec();
		$res=$ca->fetchAll();

		/*
		array_unshift($res,["data"=>'-3',"label"=>"-- Navegacion --"]);
		array_unshift($res,["data"=>'-2',"label"=>"-- Enlace --"]);
		array_unshift($res,["data"=>'-1',"label"=>"-- Expandible --"]);
		*/

		return $res;
	}
}

?>

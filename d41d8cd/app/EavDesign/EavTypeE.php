<?php

$exports[]="EavTypeE";
class EavTypeE {

	public function types(){
		return [
			//["data"=>"boolean","label"=>"boolean","ds"=>false],
			["data"=>"checkbox","label"=>"checkbox","ds"=>true],
			["data"=>"datefield","label"=>"datefield","ds"=>false],
			["data"=>"imagefield","label"=>"imagefield","ds"=>false],
			["data"=>"richeditor","label"=>"richeditor","ds"=>false],
			["data"=>"selectbox","label"=>"selectbox","ds"=>true],
			["data"=>"textfield","label"=>"textfield","ds"=>false],
			["data"=>"textarea","label"=>"textarea","ds"=>false],
			["data"=>"tokenfield","label"=>"tokenfield","ds"=>true]

		];
	}

	public function ers(stdClass $p=null){
		global $cfg;
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();


		$res->sections=[];
		$res->sections=array_unshift($res->sections,(object)['section_id'=>-1,'section_title'=>'Sin Grupo','section_order'=>-1]);

		$res->opts_ds_types =[
			["data"=>"eav","label"=>"Items"],
			["data"=>"ads","label"=>"Atributo"]
		];

		$res->opts_ds_multiple = [
			["data"=>"0","label"=>"No"],
			["data"=>"1","label"=>"Si"]
		];

		$res->types = static::types();
		$res->view_fields = [
			["data"=>"","label"=>"Oculto"],
			["data"=>"title","label"=>"Como Titulo"],
			["data"=>"description","label"=>"Como Descripción"],
			["data"=>"image","label"=>"Como Imagen"]
		];

		/*
		$res->column_withs = [
			["data"=>"2","label"=>"2"],
			["data"=>"4","label"=>"4"],
			["data"=>"6","label"=>"6"],
			["data"=>"8","label"=>"8"],
			["data"=>"10","label"=>"10"],
			["data"=>"12","label"=>"12"],
			["data"=>"14","label"=>"14"],
			["data"=>"16","label"=>"16"]
			["data"=>"18","label"=>"18"]
			["data"=>"20","label"=>"20"]
			["data"=>"22","label"=>"22"]
			["data"=>"24","label"=>"24"]
		];
		*/

		$res->column_withs = [];
		for($i=2;$i<=$cfg["gridColumnCount"];$i++){
			$res->column_withs[]=[
				"data"=>$i,
				"label"=>$i
			];
		}

		/*
		$sql="
		select
			'eav' as rel_source,
			concat('eav_',type_id) as data,
			type_name as label
		from eav_types
		where
			plat_id=:plat_id

		union all

		select
			'ds' as rel_source,
			concat('ds_',ds_id) as data,
			concat('DS: ',ds_name) as label
		from eav_ds
		where
			plat_id=:plat_id

		order by
			rel_source desc,
			label asc
		";
		*/

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
		$res->rel_types=$ca->fetchAll();
		//array_unshift($res->rel_types,["data"=>"","label"=>"Seleccione:"]);


		//triggers

		$res->opts_tg_context = [
			["data"=>"admin","label"=>"Admin"],
			["data"=>"other","label"=>"Other"],
			["data"=>"all","label"=>"All"]
		];

		$res->opts_tg_layer = [
			["data"=>"front","label"=>"Front (js)"],
			["data"=>"back","label"=>"Back (php)"]
		];


		$res->opts_tg_when = [
			["data"=>"before","label"=>"Before"],
			["data"=>"after","label"=>"After"]
		];

		$res->opts_tg_op = [
			["data"=>"insert","label"=>"Insert"],
			["data"=>"update","label"=>"Update"],
			["data"=>"delete","label"=>"Delete"]
		];


		return $res;
	}


	public function load(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		//tipo de dato
		$sql="
		select
			type_id,
			type_name,
			type_title,
			type_source,
			type_sql,
			slug_attrs,
			notes
		from eav_types
		where
			plat_id=:plat_id
			and type_id=:type_id
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":type_id",$p->type_id);
		$ca->exec();
		$res=$ca->fetch();

		//secciones de campos
		$sql="
		select
			section_id,
			section_title,
			section_order

		from eav_sections
		where
			plat_id=:plat_id
			and type_id=:type_id

		order by
			section_order asc
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":type_id",$p->type_id);
		$ca->exec();
		$res->sections = $ca->fetchAll();
		array_unshift($res->sections,(object)['section_id'=>-1,'section_title'=>'Sin Grupo','section_order'=>-1]);


		//attributes
		$sql="
		select
			section_id,
			attr_id,
			attr_name,
			attr_label,
			attr_type,
			attr_type as attr_type_label,
			attr_eorder,
			attr_ewidth,
			attr_eheight,
			attr_vfield,
			attr_vorder,
			attr_vwidth,

			ds_type,
			ds_multiple,
			ds_content,
			notes

		from eav_attrs
		where
			plat_id=:plat_id
			and type_id=:type_id

		order by
			attr_eorder,
			attr_name
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":type_id",$p->type_id);
		$ca->exec();
		$res->attrs=$ca->fetchAll();

		foreach($res->attrs as $k=>$r){
			if( $r->ds_type != "eav" ) continue;

			$res->attrs[$k]->rel_type_id = [];
			$res->attrs[$k]->rel_attr_ids = [];

			//relaciones con otros tipos de datos
			$sql="
			select
				a.rel_id,
				a.rel_type_id,
				t.type_name as rel_type_name,
				a.rel_attr_id,
				b.attr_name

			from eav_attr_rels a
			join eav_attrs b on (a.plat_id=b.plat_id and a.rel_attr_id=b.attr_id)
			left join eav_types t on (a.plat_id=t.plat_id and a.rel_type_id=t.type_id)
			where
				a.plat_id=:plat_id
				and a.type_id=:type_id
				and a.attr_id=:attr_id

			order by
				a.rel_order
			";

			$ca->prepare($sql);
			$ca->bindValue(":plat_id",$si->plat_id);
			$ca->bindValue(":type_id",$p->type_id);
			$ca->bindValue(":attr_id",$r->attr_id);
			$ca->exec();

			if( $ca->size()==0 ) continue;

			$fields=[];
			foreach($ca->fetchAll() as $k0=>$v){
				$fields[]=["data"=>$v->rel_attr_id,"label"=>$v->attr_name];
			}

			$res->attrs[$k]->rel_id = $v->rel_id;
			$res->attrs[$k]->rel_type_id[] = ["data"=>$v->rel_type_id,"label"=>$v->rel_type_name];
			$res->attrs[$k]->rel_attr_ids = $fields; //json_encode($fields);


			//dependencias
			$sql="
			select
				a.attr_id,
				a.filter_attr_id,
				a.ds_filter_attr_id,
				b.attr_name

			from eav_attr_deps a
			join eav_attrs b on (a.plat_id=b.plat_id and a.ds_filter_attr_id=b.attr_id)
			where
				a.plat_id=:plat_id
				and a.type_id=:type_id
				and a.attr_id=:attr_id
			";

			$ca->prepare($sql);
			$ca->bindValue(":plat_id",$si->plat_id);
			$ca->bindValue(":type_id",$p->type_id);
			$ca->bindValue(":attr_id",$r->attr_id);
			$ca->exec();

			if( $ca->size() == 0 ) continue;
			$tmp = $ca->fetch();

			//$res->attrs[$k]->filter_attr_id = ["data"=>$tmp->filter_attr_id,"label"=>$tmp->attr_name]; //tokenfield
			$res->attrs[$k]->filter_attr_id = $tmp->filter_attr_id; //selectbox
			$res->attrs[$k]->ds_filter_attr_id = [[ "data"=>$tmp->ds_filter_attr_id, "label"=>$tmp->attr_name ]];

		}


		//triggers
		$sql="
		select
			tg_id,
			tg_name,
			tg_enabled,
			tg_order,
			tg_when,
			tg_op,
			tg_context,
			tg_layer,
			tg_code

		from eav_triggers
		where
			plat_id=:plat_id
			and type_id=:type_id
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":type_id",$p->type_id);
		$ca->exec();
		$res->triggers=$ca->fetchAll();


		return $res;
	}

	public function save(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		$p->triggers = coalesce_array($p->triggers);

		//App::log($p->triggers);

		if( empty($p->attrs) ) {
			throw new Cm\PublicException("Debe agregar al menos un atributo");
		}


		$p->type_title=coalesce_blank($p->type_title);
		$p->type_source=coalesce_blank($p->type_source) ?: "items";
		$p->type_sql=coalesce_blank($p->type_sql);



		$db->transaction();

		$ca->prepareTable("eav_types");
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":type_name",$p->type_name);
		$ca->bindValue(":type_title",$p->type_title);
		$ca->bindValue(":type_source",$p->type_source);
		$ca->bindValue(":type_sql",$p->type_sql);
		$ca->bindValue(":slug_attrs",$p->slug_attrs);
		$ca->bindValue(":notes",$p->notes);

		if(empty($p->type_id)){
			$typeId=App::nextval("eav_types_type_id");
			$ca->bindValue(":type_id",$typeId);

			$ca->execInsert();
		}
		else {
			$typeId=$p->type_id;

			$ca->bindWhere(":type_id",$typeId);
			$ca->execUpdate();
		}


		$ca->prepareTable("eav_attrs");
		$ca->bindWhere(":plat_id",$si->plat_id);
		$ca->bindWhere(":type_id",$typeId);
		$ca->execDelete();

		$index = 0;
		foreach($p->attrs as $r){



			$r->attr_vfield = coalesce_blank($r->attr_vfield);
			$r->attr_vorder = coalesce_null($r->attr_vorder)?:-1;
			$r->attr_vwidth = coalesce_null($r->attr_vwidth)?:-1;
			$r->attr_eheight = coalesce_blank($r->attr_eheight);
			$r->ds_type = coalesce_blank($r->ds_type);
			$r->ds_multiple = coalesce_null($r->ds_multiple)?:0;
			$r->ds_content = coalesce_blank($r->ds_content);
			$r->notes = coalesce_blank($r->notes);


			$attrId= (float) $r->attr_id > 0 ? $r->attr_id : App::nextval("eav_attrs_attr_id");

			$ca->prepareTable("eav_attrs");
			$ca->bindValue(":plat_id",$si->plat_id);
			$ca->bindValue(":type_id",$typeId);
			$ca->bindValue(":section_id",$r->section_id);
			$ca->bindValue(":attr_id",$attrId);
			$ca->bindValue(":attr_name",$r->attr_name);
			$ca->bindValue(":attr_label",$r->attr_label);
			$ca->bindValue(":attr_type",$r->attr_type);

			$ca->bindValue(":attr_eorder",$index);
			$ca->bindValue(":attr_ewidth",$r->attr_ewidth);
			$ca->bindValue(":attr_eheight",$r->attr_eheight?:'',true);
			$ca->bindValue(":attr_vorder",$r->attr_vorder);
			$ca->bindValue(":attr_vfield",$r->attr_vfield);
			$ca->bindValue(":attr_vwidth",$r->attr_vwidth);
			$ca->bindValue(":ds_type",$r->ds_type?:"");
			$ca->bindValue(":ds_multiple",$r->ds_multiple?:0);
			$ca->bindValue(":ds_content",$r->ds_content?:"");
			$ca->bindValue(":notes",$r->notes);

			$ca->execInsert();


			if( !empty($r->rel_type_id) ){

				//$relId = !empty($r->rel_id) ? $r->rel_id : App::nextval("eav_attr_rels_rel_id");
				$ca->prepareTable("eav_attr_rels");
				$ca->bindWhere(":plat_id",$si->plat_id);
				$ca->bindWhere(":type_id",$typeId);
				$ca->bindWhere(":attr_id",$attrId);
				$ca->execDelete();

				//foreach(json_decode($r->rel_attr_ids) as $k0=>$r0){
				foreach($r->rel_attr_ids as $k0=>$r0){

					//$relId=App::nextval("eav_attr_rels_rel_id");

					$ca->prepareTable("eav_attr_rels");
					$ca->bindValue(":plat_id",$si->plat_id);
					//$ca->bindValue(":rel_id",$relId);
					$ca->bindValue(":type_id",$typeId);
					$ca->bindValue(":attr_id",$attrId);

					$ca->bindValue(":rel_type_id",$r->rel_type_id[0]->data);
					$ca->bindValue(":rel_attr_id",$r0->data);
					$ca->bindValue(":rel_order",$k0);

					//App::log( $ca->preparedInsert() );
					$ca->execInsert();
				}
			}

			if( !empty($r->filter_attr_id) ){
				$ca->prepareTable("eav_attr_deps");
				$ca->bindWhere(":plat_id",$si->plat_id);
				$ca->bindWhere(":type_id",$typeId);
				$ca->bindWhere(":attr_id",$attrId);
				$ca->execDelete();

				$depId = App::nextval("eav_attrs_deps_id");

				$ca->prepareTable("eav_attr_deps");
				$ca->bindValue(":plat_id",$si->plat_id);
				$ca->bindValue(":dep_id",$depId);
				$ca->bindValue(":type_id",$typeId);
				$ca->bindValue(":attr_id",$attrId);
				$ca->bindValue(":filter_attr_id",$r->filter_attr_id);

				$ca->bindValue(":ds_type_id",$r->rel_type_id);
				$ca->bindValue(":ds_filter_attr_id",$r->ds_filter_attr_id[0]->data);
				$ca->execInsert();
			}

			$index++;
		}

		//triggers
		$ca->prepareTable("eav_triggers");
		$ca->bindWhere(":plat_id",$si->plat_id);
		$ca->bindWhere(":type_id",$typeId);
		$ca->execDelete();

		foreach($p->triggers as $r){

			$r->tg_order=0;

			$ca->prepareTable("eav_triggers");
			$ca->bindValue(":plat_id",$si->plat_id);
			$ca->bindValue(":type_id",$typeId);

			$ca->bindValue(":tg_id",$r->tg_id);
			$ca->bindValue(":tg_name",$r->tg_name);
			$ca->bindValue(":tg_enabled",$r->tg_enabled);
			$ca->bindValue(":tg_order",$r->tg_order);
			$ca->bindValue(":tg_when",$r->tg_when);
			$ca->bindValue(":tg_op",$r->tg_op);
			$ca->bindValue(":tg_context",$r->tg_context);
			$ca->bindValue(":tg_layer",$r->tg_layer);
			$ca->bindValue(":tg_code",$r->tg_code,true);
			$ca->execInsert();

		}

		$db->commit();

		return (object)["type_id"=>$typeId];
	}
}

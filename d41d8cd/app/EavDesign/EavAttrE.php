<?php

$exports[]="EavAttrE";
class EavAttrE {

	public function types(){
		return [
			//["data"=>"boolean","label"=>"boolean","ds"=>false],
			//["data"=>"checkbox","label"=>"checkbox","ds"=>true],
			["data"=>"datefield","label"=>"datefield","ds"=>false],
			["data"=>"filefield","label"=>"filefield","ds"=>false],
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

		$res->ds_types =[
			["data"=>"eav","label"=>"Items"],
			["data"=>"ads","label"=>"Atributo"]
		];

		$res->ds_multiple = [
			["data"=>"0","label"=>"No"],
			["data"=>"1","label"=>"Si"]
		];

		$res->attr_types = static::types();

		$res->view_fields = [
			["data"=>"","label"=>"Oculto"],
			["data"=>"title","label"=>"Como Titulo"],
			["data"=>"description","label"=>"Como Descripción"],
			["data"=>"image","label"=>"Como Imagen"]
		];

		$res->column_withs = [];
		for($i=2;$i<=$cfg["gridColumnCount"];$i++){
			$res->column_withs[]=[
				"data"=>(string) $i,
				"label"=>(string) $i
			];
		}

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

	public function acRelAttrs($p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sqlFilter=[];
		$fields = "attr_name,attr_label";
		$sqlFilter[] = $ca->sqlFieldsFilters($fields, $p->filter);
		$sqlFilter=implode(" and ",$sqlFilter);

		$sql="select
			attr_id as data,
			attr_name as label,
			attr_label

		from eav_attrs
		where
			plat_id=:plat_id
			and type_id=:type_id
			and {$sqlFilter}

		order
			by attr_name
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":type_id",$p->type_id);
		$ca->bindValue(":filter",$p->filter,false);
		$ca->exec();
		App::log( $ca->preparedQuery() );

		return $ca->fetchAll();
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

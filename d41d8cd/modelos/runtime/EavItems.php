<?php
namespace Models\Runtime;
use App;
use stdClass;
use Cm;


class EavItems {
	public static function typeIdFromCategoryId($categoryId){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="
		select
			type_id
		from eav_categories
		where
			plat_id=:plat_id
			and category_id=:category_id
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":category_id",$categoryId);
		$ca->exec();
		$tmp=$ca->fetch();
		return $tmp->type_id;
	}

	public static function categoryById($categoryId){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="
		select
			category_id,
			category_name,
			type_id,
			slug_path,
			slug

		from eav_categories
		where
			plat_id=:plat_id
			and category_id=:category_id
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":category_id",$categoryId);
		$ca->exec();
		return $ca->fetch();
	}



	public static function typeView(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$typeId = isset($p->type_id) ? $p->type_id:"-1";
		$typeName = isset($p->type_name) ? $p->type_name:"";

		$mode=isset($p->mode) ? $p->mode :"plain";

		if( !in_array($mode,["plain","json","export"]) ){
			throw new Cm\PublicException("Invalid view mode '{$mode}'");
		}


		$fields=isset($p->fields) ? $p->fields : ["label"];

		if( array_intersect($fields,["data","label"]) != $fields ){
			throw new Cm\PublicException("Invalid view fields ".implode(",",$fields));
		}



		if( isset($p->category_id) ){
			$sql="
			select
				type_id
			from eav_categories
			where
				category_id=:category_id
			";
			$ca->prepare($sql);
			$ca->bindValue(":category_id",$p->category_id);
			$ca->exec();
			$tmp=$ca->fetch();
			$typeId=$tmp->type_id;
		}




		$sql="
		select
			a.type_id,
			a.attr_id,
			a.attr_name,
			a.attr_label,
			a.attr_type,

			rel.rel_type_id,
			rel.rel_type_source,
			case
				when rel.rel_type_source='sql' then
					rel.rel_type_sql
				else
					concat('select plat_id,data,label from view_rels_data where plat_id=',t.plat_id,' and type_id=',t.type_id,' and attr_id=',a.attr_id)
			end as rel_sql



		from eav_types t
		join eav_attrs a
			on (t.plat_id=a.plat_id and t.type_id=a.type_id )

		left join view_attr_rels_group rel
			on (t.plat_id=rel.plat_id and t.type_id=rel.type_id and a.attr_id=rel.attr_id)

		where
			t.plat_id=:plat_id
			and ( t.type_id=:type_id or t.type_name=:type_name )

		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":type_id",$typeId);
		$ca->bindValue(":type_name",$typeName,true);


		//App::log($ca->preparedQuery() );

		$ca->exec();
		$rl=$ca->fetchAll();
		$typeId=$rl[0]->type_id;


		$sqlTplViewAttrValues="
		select
			a.plat_id,
			a.item_id,
			a.type_id,
			a.attr_id,
			b.attr_name,
			group_concat(a.attr_value separator ',') as attr_values

		from eav_values a
		join eav_attrs b on (a.plat_id=b.plat_id and a.type_id=b.type_id and a.attr_id=b.attr_id)

		where
			{filter}

		group by
			a.plat_id,
			a.item_id,
			a.type_id,
			a.attr_id,
			b.attr_name
		";


		$subAttrs=[];

		foreach($rl as $r){

			if( $mode=="plain" ){

				if( in_array($r->attr_type,["selectbox","tokenfield"]) ){

					if( in_array("data",$fields) ){

						$subAttrs[]="(
						select attr_values from (
							select
								item_id,
								attr_values
							from view_attr_values
							where
								plat_id={$si->plat_id}
								and type_id={$typeId}
								and attr_id={$r->attr_id}

						) tmp_{$r->attr_name}
						where
							item_id=i.item_id
						) as {$r->attr_name}_data
						";
					}

					if( in_array("label",$fields) ){



						if( $r->rel_type_id ){
							$subAttrs[]="(
							select
							".(
								$db->driver()==$db::PGSQL ?
								"string_agg(ds.label,',')" :
								"group_concat(ds.label separator ',')"
							)."

							from eav_values v
							join ( {$r->rel_sql} ) ds on ( v.plat_id=ds.plat_id and v.attr_value = ds.data )
							where
								v.type_id={$typeId}
								and v.attr_id={$r->attr_id}
								and v.item_id=i.item_id

							) as {$r->attr_name}_label
							";

						}
						else {
							$subAttrs[]="(
							select
								attr_values
							from view_attr_values
							where
								type_id={$typeId}
								and attr_id={$r->attr_id}
								and item_id=i.item_id
							) as {$r->attr_name}_label
							";
						}
					}

				}

				//campos que no son de seleccion
				else {

					if( in_array("data",$fields) ){
						/*
						$subAttrs[]="(
							select attr_values from (
								select
									item_id,
									attr_values
								from view_attr_values
								where
									plat_id={$si->plat_id}
									and type_id={$typeId}
									and attr_id={$r->attr_id}
							) tmp_{$r->attr_name}
							where item_id=i.item_id

						) as {$r->attr_name}_data
						";
						*/

						$tmp = str_replace("{filter}","

							a.plat_id={$si->plat_id}
							and a.type_id={$typeId}
							and a.attr_id={$r->attr_id}
							-- and a.item_id=i.item_id

						",$sqlTplViewAttrValues);

						$subAttrs[]="
						( select attr_values from ( {$tmp} ) tmp_{$r->attr_name} where item_id=i.item_id ) as {$r->attr_name}_data
						";
					}

					if( in_array("label",$fields) ){
						/*
						$subAttrs[]="(
							select attr_values from (
								select
									item_id,
									attr_values
								from view_attr_values
								where
									plat_id={$si->plat_id}
									and type_id={$typeId}
									and attr_id={$r->attr_id}
							) tmp_{$r->attr_name}
							where item_id=i.item_id

						) as {$r->attr_name}_label
						";
						*/

						$tmp = str_replace("{filter}","

							a.plat_id={$si->plat_id}
							and a.type_id={$typeId}
							and a.attr_id={$r->attr_id}
							-- and a.item_id=i.item_id

						",$sqlTplViewAttrValues);

						$subAttrs[]="
						( select attr_values from ( {$tmp} ) tmp_{$r->attr_name} where item_id=i.item_id ) as {$r->attr_name}_label
						";


					}
				}
			}


			if( $mode=="json" ){
				throw new Cm\PublicException("Todo");
			}

		}



		$subAttrs = implode("\n,",$subAttrs);

		$sql="
		select
			i.plat_id,
			i.type_id,
			i.item_id,
			i.item_order,
			i.category_id,
			i.i_ts,
			i.u_ts,

			{$subAttrs}

		from eav_items i

		where
			i.plat_id={$si->plat_id}
			and i.type_id={$typeId}

		";

		//App::log($sql);

		return $sql;
	}



	public static function load($p){
		global $cfg;
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="
		select
			type_id
		from eav_items
		where
			plat_id=:plat_id
			and item_id=:item_id
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":item_id",$p->item_id);
		$ca->exec();
		$rItem=$ca->fetch();

		$typeView=self::typeView((object)[
			"type_id"=>$rItem->type_id,
			"fields"=>["data","label"],
			"mode"=>"plain"
		]);


		$sql="select * from ({$typeView}) t where t.plat_id=:plat_id and t.item_id=:item_id";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":item_id",$p->item_id);
		$ca->exec();
		$res=$ca->fetch();


		$st=self::struct( (object)["type_id"=>$res->type_id] );


		foreach( $st->attrs as $r){
			if( $r->attr_type=="imagefield"){
				$dataField="{$r->attr_name}_data";

				if( !empty($res->{$dataField}) ){

					$rsId=$res->{$dataField};

					$gfs=new Cm\GridFsDirect($db);
					$res->{$dataField} = $gfs->loadById( $rsId );

					//$res->{$r->attr_name}->source = "{$cfg["appRoot"]}/imagenes/imagen.php?imagenid={$rsId}";
					$res->{$dataField}->source = "{$cfg["appRoot"]}/imagenes/imagen.php?imagenid={$rsId}";

				}
			}
		}


		return $res;
	}

	public static function triggers($typeId,$tgContext,$tgLayer,$tgWhen,$tgOp){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="
		select
			tg_id,
			tg_name,
			tg_order,
			tg_when,
			tg_op,
			tg_context,
			tg_code

		from eav_triggers
		where
			plat_id=:plat_id
			and type_id=:type_id
			and tg_context=:tg_context
			and tg_layer=:tg_layer
			and tg_enabled=1
			and tg_when=:tg_when
			and find_in_set(:tg_op, tg_op)

		order by
			tg_order
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":type_id",$typeId);
		$ca->bindValue(":tg_context",$tgContext);
		$ca->bindValue(":tg_layer",$tgLayer);
		$ca->bindValue(":tg_when",$tgWhen);
		$ca->bindValue(":tg_op",$tgOp);
		$ca->exec();

		return $ca->fetchAll();
	}




}

?>

<?php
namespace Models\Runtime;
use App;
use stdClass;
use Cm;


class EavModel {
	public static $gfsPath="/userfiles/gfs";

	public static function rsFolder($rsId){
		global $cfg;
		return $cfg["appPath"] . static::$gfsPath.'/'. ceil( $rsId / 4096);
	}

	public static function generateAlias($prefix="t"){
		return $prefix."_".rand(0,10).date("su");
	}


	public static function categoryById($categoryId){
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="select * from eav_categories where category_id=:category_id";
		$ca->prepare($sql);
		$ca->bindValue(":category_id",$categoryId);
		$ca->exec();

		if( $ca->size()==0 ){
			throw new Cm\PublicException("Category not found, category_id = {$categoryId}");
		}

		return $ca->fetch();
	}

	public static function categoryByName($categoryName){
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="select * from eav_categories where category_name=:category_name";
		$ca->prepare($sql);
		$ca->bindValue(":category_id",$categoryName);
		$ca->exec();

		if( $ca->size()==0 ){
			throw new Cm\PublicException("Category not found, category_name = {$categoryName}");
		}

		return $ca->fetch();
	}


	public static function categoriesByParentId($parentId){
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="select * from eav_categories where parent_id=:parent_id order by category_order asc";
		$ca->prepare($sql);
		$ca->bindValue(":parent_id",$parentId);
		$ca->exec();

		if( $ca->size()==0 ){
			throw new Cm\PublicException("Category not found, parent_id = {$parentId}");
		}

		return $ca->fetchAll();
	}



	public static function attrInfo($typeId,$attrId){
		global $db;
		$ca=new Cm\DbQuery($db);

		$sql="
		select
			attr_id,
			attr_type,
			attr_name,
			attr_label

		from eav_attrs
		where
			plat_id=1
			and type_id=:type_id
			and attr_id=:attr_id
		";

		$ca->prepare($sql);
		$ca->bindValue(":type_id",$typeId);
		$ca->bindValue(":attr_id",$attrId);
		$ca->exec();

		return $ca->fetch();
	}

	public static function attrLabel($typeId,$attrId,$parentAlias='a',$parentField='item_id'){
		//todo


	}


	public static function items($p){
		global $db;
		$p=(object)$p;

		$ca=new Cm\DbQuery($db);

		$p=object_merge( (object)[
			"pager"=>false,
			"fetch"=>true,
			"order"=>["1"],
			"page"=>1,
			"count"=>1000,
			"params"=>[],
			"debug"=>false,
			"lock"=>false
		],$p);

		//$p->filter = coalesce_array($p->filter);
		$p->filter = is_array($p->filter) ? $p->filter : [$p->filter];
		$p->order = is_array($p->order) ? $p->order : [$p->order];


		//filter
		$sqlFilters=[];
		$pattern="/\{([a-zA-Z0-9_]+)(\\.[a-z]+)?\}/";

		foreach($p->filter as $tmp){
			$m=null;
			preg_match_all($pattern,$tmp,$m);
			//echo "{$tmp}\n";print_r($m);

			if( empty($m[0]) ){
				$sqlFilters[]=$tmp;
				continue;
			}


			$attrName=$m[1][0];
			$attrField=$m[2][0]; //data,label
			$search = !empty($attrField) ?  "{{$attrName}{$attrField}}": "{{$attrName}}";

			$tmpFilter = str_replace($search,"a.attr_value",$tmp);

			$sqlFilters[]="
			1 in (
				select
					1

				from eav_values a
					join eav_items i on (a.plat_id=i.plat_id and a.item_id=i.item_id)
					join eav_attrs b on (a.plat_id=b.plat_id and i.type_id=b.type_id and a.attr_id=b.attr_id)
				where
					a.plat_id=items.plat_id
					and a.item_id=items.item_id
					and b.attr_name='{$attrName}'
					and {$tmpFilter}
			)
			";
		}

		$sqlFilters=implode(" ",$sqlFilters);

		//order
		$sqlOrderFrom=[];
		$sqlOrderBy=[];

		foreach($p->order as $tmp){
			$m=null;
			preg_match_all($pattern,$tmp,$m);
			//echo "{$tmp}\n";print_r($m);

			//basic order by
			if( empty($m[0]) ){
				$sqlOrderBy[]=$tmp;
				continue;
			}

			//attr order by

			$attrName=$m[1][0];
			$attrField=$m[2][0]; //data,label
			$search = !empty($attrField) ?  "{{$attrName}{$attrField}}": "{{$attrName}}";

			$a=static::generateAlias($attrName);
			$tmpOrder = str_replace($search,"{$a}.attr_value",$tmp);

			$sqlOrderFrom[]="
			left join (
				select
					a.plat_id,
					a.item_id,
					a.attr_id,
					b.attr_name,
					a.attr_value

				from eav_values a
					join eav_items i on (a.plat_id=i.plat_id and a.item_id=i.item_id)
					join eav_attrs b on (a.plat_id=b.plat_id and i.type_id=b.type_id and a.attr_id=b.attr_id)
				where
					b.attr_name='{$attrName}'
			) {$a} on (
				items.plat_id={$a}.plat_id
				and items.item_id={$a}.item_id
				and {$a}.attr_name='{$attrName}'
			)
			";


			$tmpOrder = preg_replace("/as\s+integer/i","as signed",$tmpOrder);
			$sqlOrderBy[]=$tmpOrder;
		}


		$sqlOrderFrom=implode("\n",$sqlOrderFrom);
		$sqlOrderBy=implode(",",$sqlOrderBy);

		$order=explode(" ",$sqlOrderBy);
		$campo=explode(".",$sqlOrderBy);

		$add_query = "";
		if($p->pager){
			$add_query = ",{$order[0]} as {$campo[0]}";
		}

		$sql="
		select
			items.item_id,
			items.slug,
			items.category_id,
			category.category_name,
			category.slug as category_slug
			{$add_query}

		from eav_items items
		left join eav_categories category on (items.plat_id=category.plat_id and items.category_id=category.category_id)
			{$sqlOrderFrom}

		where
			1=1 and {$sqlFilters}

		order by
			{$sqlOrderBy}

		";

		//en mysql primero va limit y despues for update
		if( !$p->pager ){
			$sql.=" limit {$p->count}";
		}

		if( $p->lock ){
			$sql.=" for update";
		}



		$ca->prepare($sql);

		foreach($p->params as $k=>$v){

			if( is_array($v) ){
				$ca->bindValue($k,$v[0],$v[1]);
			}
			else {
				$ca->bindValue($k,$v);
			}

		}

		if( $p->debug ){
			\App::log( $ca->preparedQuery() );
		}


		if(!$p->pager){
			$ca->exec();
			$res=$ca->fetchAll();



			if( $p->fetch ){
				foreach($res as $k=>$r){
					$res[$k]=static::fetch($r->item_id,$r,  (object)["lock"=>$p->lock]);
				}
			}
			return $res;
		}

		try {
			
			$res = $ca->execPage((object)[
				"page"=>$p->page,
				"count"=>$p->count,
				"sort" => "{$campo[0]} {$order[1]}"
			]);
		}
		catch(Exception $e){
			echo $ca->preparedQuery();exit;
		}


		if( $p->fetch ){
			foreach($res->records as $k=>$r){
				$res->records[$k]=static::fetch($r->item_id,$r, (object)["lock"=>$p->lock] );
			}
		}


		return $res;

	}

	public static function item($p){
		$p=(object)$p;

		$p->pager=false;
		$p->page=1;
		$p->count=1;

		$tmp=static::items($p);
		if( empty($tmp) ) return null;


		return $tmp[0];
	}

	public static function page($p){
		$p->pager=true;
		return static::items($p);
	}



	public static function fetch($itemId,$itemInfo=null){
		return static::fetchExtended($itemId,$itemInfo);
	}

	public static function fetchExtended($itemId,$itemInfo=null,$extra=null){
		$si=App::session();

		global $db;

		$extra = is_object($extra) ? $extra : (object)[];
		$extra=object_merge( (object)[
			"lock"=>false
		],$extra);


		$ca=new Cm\DbQuery($db);

		if( $db->driver()==$db::PGSQL ){
			$sqlTplViewAttrRelValues="
			select
			av.plat_id,
			av.item_id,
			r.type_id,
			av.attr_id,
			string_agg(case when r.data is not null then r.data else av.attr_value end , ',') as data,
			string_agg(case when r.label is not null then r.label else av.attr_value end , ',') as label,
			( '[' || string_agg( '{\"data\":\"'||r.data||'\", \"label\":\"'||r.label||'\"}' , ',' ) || ']' ) as json_values

			from eav_values av
			left join view_rels_data r on (av.plat_id=r.plat_id and av.type_id=r.type_id and av.attr_id=r.attr_id and av.attr_value=r.data)
			where
				av.item_id = :item_id

			group by
				av.plat_id,
				av.item_id,
				av.attr_id,
				r.type_id
			";
		}
		else {
			$sqlTplViewAttrRelValues="
			select
			av.plat_id,
			av.item_id,
			r.type_id,
			av.attr_id,
			group_concat(case when r.data is not null then r.data else av.attr_value end separator ',') as data,
			group_concat(case when r.label is not null then r.label else av.attr_value end separator ',') as label,
			concat('[',group_concat( '{\"data\":\"',r.data,'\", \"label\":\"',r.label,'\"}' separator ',' ),']') as json_values

			from eav_values av
			left join view_rels_data r on (av.plat_id=r.plat_id and av.type_id=r.type_id and av.attr_id=r.attr_id and av.attr_value=r.data)
			where
				av.item_id = :item_id

			group by
				av.plat_id,
				av.item_id,
				av.attr_id,
				r.type_id
			";
		}




		$sql="

		select
			a.type_id,
			a.attr_id,
			a.attr_type,
			a.attr_name,
			a.attr_label,
			a.ds_type,
			-- v.attr_values as attr_value,
			rv.data as attr_value,
			rv.json_values

		from eav_items i
		join eav_attrs a on (
			i.plat_id=a.plat_id
			and i.type_id=a.type_id
		)

		/*
		left join view_attr_values v on (
			i.plat_id=v.plat_id
			and i.item_id=v.item_id
			and a.attr_id=v.attr_id
		)
		*/

		/*
		left join view_attr_rel_values rv on (
			i.plat_id=rv.plat_id
			and i.item_id=rv.item_id
			and a.attr_id=rv.attr_id
			and rv.item_id=:item_id
		)
		*/

		left join ( {$sqlTplViewAttrRelValues} ) rv on (
			i.plat_id=rv.plat_id
			and i.item_id=rv.item_id
			and a.attr_id=rv.attr_id
			and rv.item_id=:item_id
		)

		where
			i.plat_id=:plat_id
			and i.item_id=:item_id

		order by
			a.attr_eorder asc

		";

		if( $extra->lock ){
			$sql.=" for update";
		}

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":item_id",$itemId);

		//echo $ca->preparedQuery();exit;
		//App::log($ca->preparedQuery());
		$ca->exec();

		$res=(object)[
			"item_id"=>$itemId,
			"slug"=>$itemInfo->slug,
			"category_id"=>$itemInfo->category_id,
			"category_name"=>$itemInfo->category_name,
			"category_slug"=>$itemInfo->category_slug,
			"attrs"=>(object)[]
		];

		//print_r($ca->fetchAll());

		foreach($ca->fetchAll() as $r){
			//echo json_encode($r)."\n";

			if( !isset( $res->attrs->{$r->attr_name} ) ){
				$res->attrs->{$r->attr_name}=(object)[
					"attr_name"=>$r->attr_name,
					"attr_label"=>$r->attr_label,
					"attr_type"=>$r->attr_type,
					"type"=>$r->attr_type, //deprecated 20160929
					"data"=>[],
					"label"=>[]
				];
			}

			/*
			if( $r->ds_type=='ads' && in_array($r->attr_type,["tokenfield","selectbox"]) ){
				continue;
			}

			if( $r->ds_type=='eav' && in_array($r->attr_type,["tokenfield","selectbox"]) ){ // && empty($r->json_values) ){

				continue;
			}
			*/

			if( in_array($r->attr_type,["tokenfield","selectbox"]) ){
				if( $r->ds_type=='eav'){
					$tmp=json_decode($r->json_values,false);
					if( is_array($tmp) ){
						foreach($tmp as $v){
							$res->attrs->{$r->attr_name}->data[] = $v->data;
							$res->attrs->{$r->attr_name}->label[] = $v->label;
						}
					}
				}

				if( $r->ds_type=='ads'){
					$tmp = static::acAttrDs($si,$r,explode(",",$r->attr_value));
					//App::log($tmp);
					$res->attrs->{$r->attr_name}->data = \array_columnx($tmp,'data');
					$res->attrs->{$r->attr_name}->label = \array_columnx($tmp,'label');
				}
				continue;
			}


			if( empty($r->json_values) ){
				$res->attrs->{$r->attr_name}->data[] = $r->attr_value;
				$res->attrs->{$r->attr_name}->label[] = $r->attr_value;
				continue;
			}




		}

		//print_r($res);exit;
		return $res;
	}

	public static function fetchSimple($itemId,$itemInfo=null){
		global $db;
		$ca=new Cm\DbQuery($db);

		$sql="

		select
			i.category_id,
			a.attr_type,
			a.attr_name,
			a.attr_label,
			v.attr_value,
			rv.json_values

		from eav_items i
		left join eav_attrs a on (
			i.plat_id=a.plat_id
			and i.type_id=a.type_id
		)
		left join eav_values v on (
			i.plat_id=v.plat_id
			and i.item_id=v.item_id
			and a.attr_id=v.attr_id
		)
		left join view_attr_rel_values rv on (
			i.plat_id=rv.plat_id
			and i.item_id=rv.item_id
			and a.attr_id=rv.attr_id
		)

		where
			i.item_id={$itemId}

		order by
			a.attr_eorder asc

		";

		$ca->prepare($sql);
		$ca->exec();

		$res=(object)[
			"item_id"=>$itemId,
			"category_id"=>$itemInfo->category_id,
			"slug"=>$itemInfo->slug
		];

		foreach($ca->fetchAll() as $k=>$r){
			if( $k==0 ){
				$res->category_id=$r->category_id;
			}

			if( !isset($res->{$r->attr_name}) ){
				$res->{$r->attr_name} = $r->attr_value;
				continue;
			}

			if( !is_array($res->{$r->attr_name}) ){
				$res->{$r->attr_name} = [ $res->{$r->attr_name} ];
			}

			$res->{$r->attr_name}[] = $r->attr_value;
		}

		return $res;
	}


	public static function load($itemId){
		global $db;
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		$sql="
		select
			'plain' as format,
			i.plat_id,
			i.item_id,
			i.type_id,
			i.category_id,
			a.attr_id,
			a.attr_name,
			a.ds_multiple,
			-- group_concat(v.attr_value separator ',') as attr_value
			group_concat(v.attr_value SEPARATOR 0x3) as attr_value

		from eav_items i
		left join eav_attrs a on (i.plat_id=a.plat_id and i.type_id=a.type_id)
		left join eav_values v on (i.plat_id=v.plat_id and a.attr_id=v.attr_id and i.item_id=v.item_id  )

		where
			i.plat_id=:plat_id
			and i.item_id = :item_id

		group by
			i.plat_id,
			i.item_id,
			i.type_id,
			a.attr_id,
			a.attr_name
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":item_id",$itemId);
		$ca->exec();

		foreach($ca->fetchAll() as $k=>$r){
			if($k==0){
				$res->item_id = $r->item_id;
				$res->category_id = $r->category_id;
				$res->type_id = $r->type_id;
			}

			$isSet = isset($res->{$r->attr_name});


			if( !isset($res->{$r->attr_name}) ){
				if( $r->ds_multiple ){
					$res->{$r->attr_name} = [];
				}
				else {
					$res->{$r->attr_name} = "";
				}
			}

			if( $r->ds_multiple ){
				$res->{$r->attr_name} = explode("\x3", $r->attr_value);
			}
			else {
				$res->{$r->attr_name} = $r->attr_value;
			}
		}


		return $res;
	}


	public static function save(stdClass $p,$extra=[]){
		global $cfg;
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		$extra = (object)$extra;
		$extra = object_merge([
			'context'=>'other'
		],$extra);


		//echo "p=";print_r($p);

		if( empty($p->category_id) ){
			throw new Cm\PublicException("Invalid category_id");
		}


		$category=static::categoryById($p->category_id);
		$typeId = $category->type_id;

		$st=self::struct((object)["category_id"=>$p->category_id]);
		$slugAttrs=explode(",",$st->slug_attrs);

		$_old = null;

		if( empty($p->item_id) ){
			$insert = true;

			$itemId=App::nextval("eav_items_item_id");
			//static::execTrigger($typeId,$extra->context,"before","insert",$p,$_old);
		}
		else {
			$insert=false;

			$itemId=$p->item_id;
			//static::execTrigger($typeId,$extra->context,"before","update",$p,$_old);
		}


		$ca->prepareTable("eav_items");
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":item_id",$itemId);
		$ca->bindValue(":type_id",$st->type_id);
		$ca->bindValue(":category_id",$p->category_id);
		$ca->bindValue(":u_ts","current_timestamp");

		$slug=[];
		foreach($slugAttrs as $attr){
			if( empty($attr) ) continue;
			$slug[]=Cm\WebTools::normalizeUrl( $p->{$attr} );
		}

		$slug=implode("-",$slug);
		$ca->bindValue(":slug",$slug);
		$ca->bindValue(":slug_path","{$category->slug_path}/{$slug}");


		if( empty($p->item_id) ){
			$ca->bindValue(":i_ts","current_timestamp");
			$ca->execInsert();
		}
		else {
			$ca->bindWhere(":item_id",$itemId);
			$ca->execUpdate();
		}

		$ca->prepareTable("eav_values");
		$ca->bindWhere(":plat_id",$si->plat_id);
		$ca->bindWhere(":item_id",$itemId);
		$ca->execDelete();

		//throw new Cm\PublicException($st);
		foreach($st->attrs as $r){

			if( in_array($r->attr_type,["imagefield","filefield"]) && !is_numeric($p->{$r->attr_name})  ){
				$file = $p->{$r->attr_name};

				$value="";
				if( !empty($file->tmp_name) ){
					$gfs=new Cm\GridFsDirect($db);

					$v=new stdClass();
					$v->relname="eav_items";
					$v->relkey="item_id";
					$v->relfield="{$r->attr_name}";
					$v->relvalue=$itemId;

					$v->rsname = $file->rsname;
					$v->rstype = $file->rstype;
					$v->rssize = $file->rssize;


					$filename="{$cfg["appPath"]}/tmp/{$file->tmp_name}";
					//$value=$gfs->storeFile($filename,$v);


					$value=$gfs->storeBytes('',$v);


					$folder = static::rsFolder( $value );
					$target="{$folder}/{$value}";
					rename($filename,$target);
				}

				if( !empty($file->rsid) ){
					$value=$file->rsid;
				}

			}
			else {
				//no empty because 0
				$value = isset( $p->{$r->attr_name} ) ? $p->{$r->attr_name} : "";
			}

			if( in_array($r->attr_type,['tokenfield','selectbox']) && !is_array($value) && trim($value)=="" ){
				//\App::log($r);
				//\App::log("value=",$value);
				continue;
			}


			if( !is_array($value) ){
				$value = [$value];
			}

			foreach($value as $val){
				$valueId=App::nextval("eav_values_value_id");

				if( !empty($val->data) ){
					$val = $val->data;
				}

				$ca->prepareTable("eav_values");
				$ca->bindValue(":plat_id",$si->plat_id);
				$ca->bindValue(":item_id",$itemId);
				$ca->bindValue(":type_id",$st->type_id);

				$ca->bindValue(":value_id",$valueId);
				$ca->bindValue(":attr_id",$r->attr_id);
				$ca->bindValue(":attr_value",$val,true);

				try {
					$ca->execInsert();
				}
				catch(Exception $e){
					throw new Cm\PublicException($e->getMessage()." on attr {$r->attr_name}");
				}

			}
		}


		//static::execTrigger($typeId,$extra->context,"after",$insert ? "insert":"update",$p,$_old);

		static::execTrigger($typeId,$extra->context,(object)[
			'tg_layer'=>'back',
			'tg_when'=>'after',
			'tg_op'=>$insert ? 'insert':'update',
			'_new'=>$p,
			'_old'=>$_old,
			'_res'=>&$res
		]);


		$res->item_id = $itemId;
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


	public static function execTrigger($typeId,$tgContext,$ctx){
		//App::log( func_get_args() );
		global $cfg;
		$tgs = static::triggers($typeId,$tgContext,'back',$ctx->tg_when,$ctx->tg_op);

		$siteRoot = $cfg["siteRoot"];

		foreach($tgs as $tg){

			$tgWhen=$ctx->tg_when;
			$tgOp=$ctx->tg_op;
			$_new = $ctx->_new;
			//$_old = $ctx->_old;
			$_old = null;
			$_res = &$ctx->_res;

			eval($tg->tg_code);
		}
	}


	public static function delete(stdClass $si,$itemId){
		global $db;
		global $cfg;
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		$item = static::item( (object)[
			"filter"=>"items.item_id = :item_id",
			"params"=>[
				":item_id"=>$itemId
			]
		]);

		foreach( $item->attrs as $field=>$r){

			if( in_array($r->type,["imagefield","filefield"]) && !empty($r->data[0]) ){

				$folder = static::rsFolder($r->data[0]);
				$target="{$folder}/{$r->data[0]}";
				@ unlink($target);
			}
		}


		$sql="delete from eav_items where plat_id=:plat_id and item_id = :item_id";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":item_id",$itemId);
		$ca->exec();

		$sql="delete from eav_values where plat_id=:plat_id and item_id = :item_id";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":item_id",$itemId);
		$ca->exec();

		return true;
	}


	public static function updateAttr($itemId,$attrName,$attrValue){

	}


	public static function struct(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		$typeId = isset($p->type_id) ? $p->type_id:"";

		if( isset($p->category_id) ){
			$category=static::categoryById($p->category_id);
			$typeId = $category->type_id;
		}


		$sql="
		select
			type_id,
			slug_attrs

		from eav_types
		where
			plat_id=:plat_id
			and type_id=:type_id
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":type_id",$typeId);
		$ca->exec();
		if( $ca->size() == 0 ){
			throw new Cm\PublicException("Type with id = {$typeId} not found");
		}
		$res=$ca->fetch();


		$sql="
		select
			a.type_id,
			a.attr_id,
			a.attr_name,
			a.attr_label,
			a.attr_type,
			a.attr_eorder,
			a.attr_ewidth,
			a.attr_vfield,
			a.attr_vorder,

			a.ds_type,
			a.ds_multiple,
			rel.rel_type_id


		from eav_types t
		join eav_attrs a on (t.plat_id=a.plat_id and t.type_id=a.type_id )
		left join view_attr_rels_group rel on (t.plat_id=rel.plat_id and t.type_id=rel.type_id and a.attr_id=rel.attr_id)
		where
			t.plat_id=:plat_id
			and t.type_id=:type_id

		order by
			attr_eorder,
			attr_name

		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":type_id",$typeId);
		$ca->exec();


		$res->attrs=$ca->fetchAll();
		foreach($res->attrs as $k=>$attr){
			//$res->attrs[$k]->ds_options = null;
			$res->attrs[$k]->ds_options = static::acAttrDs($si,$attr);
		}

		return $res;
	}

	/*
	public static function acAttrDsOld($si,$attr){
		if( is_numeric($attr) || (!is_object($attr) || empty($attr->attr_id) ) ){
			throw new Cm\PublicException("Invalid arguments in acAttrDs");
		}

		$attrId = is_numeric($attr) ? $attr : $attr->attr_id;

		if(!is_numeric($attr)){
			throw new Cm\PublicException("Unimplemented load attr from id");
		}


		//el token field siempre busca datos por rpc
		if( $attr->attr_type=="tokenfield"){
			return null;
		}



		//fuente de datos desde items
		if( $attr->ds_type=="eav"){
			return $this->acAttrDsFromItems($si,(object)[
				"type_id"=>$attr->type_id,
				"attr_id"=>$attr->attr_id,
				"filter"=>"",
				"count"=>10000 //maximo de 10.000 items para selectbox
			]);
		}

		//fuente de datos estatica del atributo
		if( $attr->ds_type=="ads"){
			$options = [];

			$tmp =explode("\n",$attr->ds_content);
			foreach($tmp as $aux){
				if( empty($aux) ) continue;

				$aux = explode(";",$aux);
				$aux[1] = isset($aux[1]) ? $aux[1]:$aux[0];
				$options[] = ["data"=>trim($aux[0]),"label"=>trim($aux[1]) ];
			}

			return $options;
		}

		return null;
	}
	*/

	//recibe al attr_id a un record de attr
	public static function acAttrDs($si,$attr,$items=[]){
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);


		if( is_numeric($attr) || (!is_object($attr) || empty($attr->attr_id) ) ){
			throw new Cm\PublicException("Invalid arguments in acAttrDs");
		}

		//$attrId = is_numeric($attr) ? $attr : $attr->attr_id;

		if(!is_numeric($attr)){
			$attrId = $attr->attr_id;
			$typeId = $attr->type_id;

			$sql="
			select
				a.type_id,
				a.attr_id,
				a.attr_name,
				a.attr_label,
				a.attr_type,
				a.ds_type,
				a.ds_content

			from eav_attrs a
			where
				a.plat_id=:plat_id
				and a.type_id=:type_id
				and a.attr_id=:attr_id
			";

			$ca->prepare($sql);
			$ca->bindValue(":plat_id",$si->plat_id);
			$ca->bindValue(":type_id",$typeId);
			$ca->bindValue(":attr_id",$attrId);

			$ca->exec();
			if( $ca->size() == 0 ){
				throw new Cm\PublicException("Attributo not found, id:{$attrId}");
			}
			$attr = $ca->fetch();
		}



		if( !in_array($attr->attr_type,['tokenfield','selectbox']) ){
			return null;
		}


		if( $attr->ds_type=='eav'){
			$attr->filter = coalesce_blank($attr->filter);
			return static::acAttrDsFromItems($si,$attr,(object)["filter"=>$attr->filter]);
		}

		if( $attr->ds_type=='ads'){
			return static::acAttrDsFromAttr($si,$attr,$items);
		}

		throw new Cm\PublicException("Invalid attribute datasource type");
	}


	public static function acAttrDsFromAttr($si,$attr,$items=[]){
		$options = [];

		$tmp =explode("\n",$attr->ds_content);
		foreach($tmp as $aux){
			if( empty($aux) ) continue;

			$aux = explode(";",$aux);
			$aux[1] = isset($aux[1]) ? $aux[1]:$aux[0];
			$aux[0] = trim($aux[0]);
			$aux[1] = trim($aux[1]);

			if( !empty($items) && in_array($aux[0],$items) ){
				$options[] = ["data"=>$aux[0],"label"=>$aux[1] ];
			}

			if( empty($items) ){
				$options[] = ["data"=>$aux[0],"label"=>$aux[1] ];
			}

		}

		return $options;
	}


	public static function acAttrDsFromItems($si,$attr,$p){
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
		$ca->bindValue(":type_id",$attr->type_id);
		$ca->bindValue(":attr_id",$attr->attr_id);
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
		$ca->bindValue(":type_id",$attr->type_id);
		$ca->bindValue(":attr_id",$attr->attr_id);
		$ca->bindValue(":filter",$p->filter,false);
		//App::log($ca->preparedQuery());
		$ca->exec();

		return $ca->fetchAll();
	}

	public static function importacionExcel($p){

        $si = App::session();
        $db = Cm\Database::database();
        $ca = new Cm\DbQuery($db);
        $item = new stdClass();
        global $cfg;

		if (empty($p)) {
			return;
		}

		$db->transaction();

		//Recorremos los items del excel para insertarlos

		foreach ($p as $r) {

			if(empty($r["category_id"])){
				continue;
			}

			foreach ($r as $key => $value) {

				if (empty($key)) {
					continue;
				}
				if ($key == "id") {
					$tmp = static::item( (object)[
						"filter"=>"{".$value[0]."} = :value",
						"params"=>[
							":value"=>$value[1]
						]
					]);
					$item->item_id = coalesce_blank($tmp)? $tmp->item_id : "";
				}

                $item->{$key} = $value;
            }

			$st = self::struct((object)["category_id"=>$r["category_id"]]);

            foreach($st->attrs as $attrs){
            	if( in_array($attrs->attr_type,["imagefield","filefield"])){

					//throw new Cm\Exception($tmp->attrs->{$attrs->attr_name}->data[0]);

                    $file = $item->{$attrs->attr_name};


					//Validacion para eliminar una imagen
					if (strtolower($file) === 'eliminar') {
						continue;
					}

					//Validacion para un registro nuevo y que no tenga imagen
					if (empty($file) && empty($tmp)) {
						continue;
					}

					$gfs = new Cm\GridFsDirect($db);
					$url_img = "{$cfg["appPath"]}/tmp/productos/importacion/imagenes/{$file}";
					$item->{$attrs->attr_name} = new stdClass;
					$item->{$attrs->attr_name}->rsid = 0;
					$item->{$attrs->attr_name}->rsname = basename("{$url_img}");
					$item->{$attrs->attr_name}->rstype = mime_content_type("{$url_img}");
					$item->{$attrs->attr_name}->rssize = filesize("{$url_img}");
					$item->{$attrs->attr_name}->tmp_name = "/productos/importacion/imagenes/{$file}";
					$item->{$attrs->attr_name}->url = true;

					$value = $gfs->storeBytes('',$item->{$attrs->attr_name});

					//Validacion para editar un item con la imagen vacia y que no la elimine
					if (empty($file)) {
						if (!empty($tmp)) {
							$value = $tmp->attrs->{$attrs->attr_name}->data[0];
							$item->{$attrs->attr_name} = $value;
						}
					}

					$folder = static::rsFolder($value);

                    //Validamos que la carpeta dinamica que me entregó la función anterior ya exista, en caso de que no la crearemos
                    if(!file_exists("{$folder}")){
                        mkdir("{$folder}");
                    }

                }
            }

			//throw new Cm\Exception($item);

			$res = static::save($item);

		}

		$db->commit();

    }
}





/*
$filter=[];
$filter[]="items.type_id=1";
$filter[]="and";
$filter[]="{collection}=2";
$filter[]="and";
$filter[]="{category}=5";


$order=[];
$order[]="items.item_id asc";
$order[]="{category.data} asc";
$order[]="{item_name.data} asc";


$q = EavQuery::items((object)[
	"filter"=>$filter,
	"order"=>$order,
	"page"=>1,
	"count"=>1000,
	"fetch"=>true
]);


print_r($q->records[0]);
*/

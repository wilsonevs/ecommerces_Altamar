<?php
require_once "{$cfg["modelsPath"]}/runtime/EavItems.php";
require_once "{$cfg["modelsPath"]}/runtime/EavModel.php";

use Models\Runtime\EavModel;

$exports[]="EavRuntime_EavItemL";
class EavRuntime_EavItemL {

	public function page(stdClass $p=null){
		global $cfg;
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		$p->filter = trim($p->filter);

		//$p->category_id = coalesce_null($p->category_id) ?: -1;

		$sql="
		select
			category_path
		from eav_categories
		where
			plat_id=:plat_id
			and category_id=:category_id
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":category_id",$p->category_id);
		$ca->exec();
		$res->category=$ca->fetch();


		$st=Models\Runtime\EavModel::struct((object)["category_id"=>$p->category_id] );


		$filter=[];
		$filter[]="items.category_id = :category_id and status=:status";
		$filter[]="and ( ";

		//positivo para todos cuando el filtro este vacio
		$filter[]= empty($p->filter) ? "1=1":"1=2";


		$titleFields = [];
		$descriptionFields = [];
		$imageField = '';

		foreach($st->attrs as $attr){
			if( empty( $attr->attr_vfield) ) continue;

			if( $attr->attr_vfield=='title'){
				$titleFields[ (integer) $attr->attr_vorder ] = $attr->attr_name;
			}

			if( $attr->attr_vfield=='description'){
				$descriptionFields[ (integer) $attr->attr_vorder ] = $attr->attr_name;
			}

			if( $attr->attr_vfield=='image'){
				$imageField = $attr->attr_name;
			}

			//$sqlFilter[] = "lower({$attr->attr_name}_label) like lower('%{$p->filter}%')";
			$filter[]="or";
			$filter[]="lower( {{$attr->attr_name}} ) like lower('%{$p->filter}%')";
		}

		$filter[]=")";

		//App::log($filter);
		//App::log($st);

		$status = $p->archived ? 'archived':'active';

		$page = Models\Runtime\EavModel::page((object)[
			'filter'=>$filter,
			'params'=>[
				':category_id'=>$p->category_id,
				':status'=>$status
			],
			'order'=>'items.item_order desc',
			'count'=>$p->count,
			'page'=>$p->page,
			'debug'=>true
		]);

		$tmp = $page->records;
		$page->records = [];


		ksort($titleFields);
		ksort($descriptionFields);

		foreach($tmp as $r){
			$title = [];
			foreach($titleFields as $field){
				$title[]=$r->attrs->{$field}->label[0];
			}
			$title=implode(" ",$title);


			$description = [];
			foreach($descriptionFields as $field){
				if( !isset($r->attrs->{$field}->label[0]) ) continue;

				if( count($r->attrs->{$field}->label) ==  1 ){
					$description[]="<b>{$field}:</b> ".trim( $r->attrs->{$field}->label[0] );
				}
				else {
					$description[]="<b>{$field}</b>:<br/>".trim( implode("<br/>",$r->attrs->{$field}->label) );
				}

			}

			$description = array_filter($description, 'strlen');
			$description=implode("<br/>",$description);

			//$image = 'http://placehold.it/80x80?text=Sin+Imagen&txtsize=20';
			//$image='https://placeholdit.imgix.net/~text?w=80&h=80&txtsize=20&txt=Sin+Imagen';
			$image = 'img/noimage.png';
			if( isset($r->attrs->{$imageField}->data[0]) ){
				//$image = $r->attrs->{$imageField}->data[0];
				$rsId = $r->attrs->{$imageField}->data[0];
				if( !empty($rsId) ){
					$image = "http://{$cfg["appHost"]}{$cfg["siteRoot"]}/imagenes/imagen.php?imagenid={$rsId}";
				}

			}


			$page->records[] = [
				'category_id'=>$r->category_id,
				'category_path'=>$res->category->category_path,
				'item_id'=>$r->item_id,
				'title'=>$title,
				'description'=>$description,
				'image'=>$image,
				'has_image'=> !empty($imageField)
			];

		}

		$res=object_merge($res,$page);

		return $res;
	}


	public function delete(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$db->transaction();
		foreach($p->items as $itemId){
			EavModel::delete($si,$itemId);
		}
		$db->commit();

		return count($p->items);
	}

	public function archive(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$db->transaction();
		foreach($p->items as $itemId){
			$item = EavModel::load($si,$itemId);
			$item->status = 'archived';
			EavModel::save($si,$itemId);
		}
		$db->commit();

		return count($p->items);
	}

	public function reorder(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		//App::log($p);

		if( empty($p->items) ) return true;


		$db->transaction();

		//$itemIds = Cm\DbQuery::arrayColumnToSqlIn($p->items,"item_id");
		$itemIds = implode(",",$p->items);
		//throw new Cm\PublicException($p);


		//App::log("itemIds",$itemIds);

		//lock items
		$sql="
		select
			item_id
		from eav_items
		where
			plat_id=:plat_id
			and item_id in (:items)
		for update
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":items",$itemIds,false);
		$ca->exec();


		$sql="
		select
			max(item_order) as max_order
		from eav_items
		where
			item_id in (:items)
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":items",$itemIds,false);
		$ca->exec();
		$tmp = $ca->fetch();

		$tmp->max_order = $tmp->max_order ?: count($p->items);

		$order = $tmp->max_order;

		foreach($p->items as $itemId){

			$ca->prepareTable("eav_items");
			$ca->bindValue(":item_order",$order);

			$ca->bindWhere(":plat_id",$si->plat_id);
			$ca->bindWhere(":item_id",$itemId);
			$ca->execUpdate();

			$order--;
		}

		$db->commit();
		return true;
	}
}


?>

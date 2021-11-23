<?php
namespace Cm;


class EavQuery { 
	public static function byType($platId,$typeId){
		$db = Database::database();
		$ca = new DbQuery($db);
		
		$sql="
		select 
			attr_id,
			attr_name,
			attr_label
		from eav_attrs
		where 
			plat_id=:plat_id
			and type_id=:type_id
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$platId);
		$ca->bindValue(":type_id",$typeId);
		$ca->exec();
		
		$index=0;
		$subAttrs=array();
		
		foreach($ca->fetchAll() as $r){
			
			$subAttrs[]="
			(
				select attr_value
				from view_eav_attr_string_values 
				where plat_id='{$platId}' and item_id=eav_items.item_id and attr_id='{$r->attr_id}'
			) as {$r->attr_name}_value
			";
			
			$subAttrs[]="
			(
				select attr_label
				from view_eav_attr_string_values 
				where plat_id='{$platId}' and item_id=eav_items.item_id and attr_id='{$r->attr_id}'
			) as {$r->attr_name}
			";
		}
		
		
		$sql="
		select
			eav_items.item_id,
			eav_items.ts_insert,
			eav_items.ts_update,
			plat_users.login,
			plat_users.user_name,
			".implode(",",$subAttrs)."
		from 
			eav_items
			left join plat_users on (eav_items.plat_id=plat_users.plat_id and eav_items.user_id=plat_users.user_id)
		where 
			eav_items.plat_id='{$platId}'
			and eav_items.type_id='{$typeId}'
		";
		
		return $sql;
	}
}
	
?>

<?php
class Cache {


	public static function set($key,$value,$ttl = 2592000){ //1 mes de cache

		$value = json_decode($value);
		$expire = time() + $ttl;

		$ca=new Cm\DbQuery($db);
		$ca->prepareTable("mod_cache");
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":key",$key);
		$ca->bindValue(":value",$value);
		$ca->bindValue(":expire",$expire);
		$ca->execInsert();

	}

	public static function get(){
		$sql="
		select
			*
		from mod_cache
		where
			plat_id=:plat_id
			and key=:key
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":key",$key);
		$ca->exec();

		if( $ca->size() == 0 ){
			return null;
		}

		$tmp=$ca->fetch();

		$time = time();
		if( $time > $tmp->expire ){
			return null;
		}

		return json_decode($tmp->value);
	}
}

?>

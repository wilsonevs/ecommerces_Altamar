<?php


class Application  {

	public static function nextval($sequenceName){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="select * from plat_sequences where plat_id=:plat_id and sequence_name=:sequence_name for update";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":sequence_name",$sequenceName);
		$ca->exec();
		$tmp=$ca->fetch();
		$value = $tmp->sequence_value + 1;

		$ca->prepareTable("plat_sequences");
		$ca->bindValue(":sequence_value",$value);
		$ca->bindWhere(":plat_id",$si->plat_id);
		$ca->bindWhere(":sequence_name",$sequenceName);
		$ca->execUpdate();

		return $value;
	}


	public static function session(){
		return (object)[
			"plat_id"=>1
		];
	}

	public static function log($label,$message=''){
		$backtrace=debug_backtrace();
		$file=$backtrace[0]["file"];
		$line=$backtrace[0]["line"];

		if( empty($message) ){
			$message=$label;
			$label='';
		}

		if( !is_string($message) ){
			$message=print_r($message,1);
		}

		$message=str_replace("|","?",$message);

		\FB::log("\nfile:{$file}\nline:{$line}\n{$message}",$label);
		//\PC::debug("\nfile:{$file}\nline:{$line}\n{$message}",$label);
	}

}

class App extends Application {}
?>

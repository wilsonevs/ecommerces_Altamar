<?php
namespace Cm;

class Application {

	public static $appId=1;
	public static $appVersion=0.01;

	public static $appSessionException=1;
	public static $appVersionException=2;
	public static $appValidationException=3;
	public static $appAclException=4;

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
		
		if( class_exists('\PC') ){
			\FB::log("\nfile:{$file}\nline:{$line}\n{$message}",$label);
			//\PC::debug("\nfile:{$file}\nline:{$line}\n{$message}",$label);
		}
	}

	public static function signout($p=array()) {
		
		if( isset($_SESSION["info"]) ){
			unset($_SESSION["info"]);
		}
		
		if( session_id() ){
			session_destroy();
		}
		
		
		return;
	}

	public static function checkVersion(){
		$v = isset($_GET["v"]) ? $_GET["v"]:"";
		if( (float)$v != (float) static::$appVersion ){
			self::signout();

			$message="La Aplicacion ha sido actualizada a la version ".static::$appVersion.", su version es la {$v}";
			$message.="<br/>Presione la tecla F5 durante 5 segundos ó recargue la pagina e ingrese nuevamente";

			throw new PublicException($message,self::$appVersionException);
		}
	}

	public static function uuid(){
		return Database::uuid4();
	}

	public static function nextval($sequenceName){
		if( empty($_SESSION["info"]) ){
			throw new PublicException("La sesion ha expirado",static::$appSessionException);
		}

		$si=$_SESSION["info"];

		$db=Database::database();
        $ca = new DbQuery($db);

        $sql="
		select
			sequence_value+1 as sequence_value
		from plat_sequences
		where
			plat_id=:plat_id
			and sequence_name='{$sequenceName}' for update";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->exec();

        if( $ca->size() == 0) {
            throw new DbException("Sequence not found {$sequenceName}");
        }

        $ra = $ca->fetch();
        $sequenceValue = $ra->sequence_value;

        $sql="
		update plat_sequences
			set sequence_value={$sequenceValue}
		where
			plat_id=:plat_id
			and sequence_name='{$sequenceName}'";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->exec();

        return $sequenceValue;
	}

}


class QxApplication extends Application {};
?>

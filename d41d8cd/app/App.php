<?php
Cm\Bootstrap::requireOnce("qx/application.inc.php");
//require_once __DIR__."/password.php";


$exports[]="App";
class App extends Cm\Application {

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

	public function signin(stdClass $p){
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		unset($_SESSION["si"]);

		$sql="
		select
			plat_id,
			login,
			user_name,
			user_type,
			password

		from plat_users
		where
			plat_id=:plat_id
			and login=:login
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":login",$p->login);
		$ca->exec();

		if( $ca->size() == 0 ){
			throw new Cm\PublicException("Usuario o clave invalidos");
		}

		$tmp = $ca->fetch();

		if( !password_verify($p->password,$tmp->password) ){
			throw new Cm\PublicException("Usuario o clave invalidos");
		}

		unset($tmp->password);//security unset
		$_SESSION["si"]=$tmp;

		return (object)[
			"account"=>$tmp,
			"menu"=>static::menu()
		];
	}

	public function checkSession(){
		if( !isset($_SESSION["si"]) ){
			return (object)[];
		}

		return (object)[
			"account"=>$_SESSION["si"],
			"menu"=>static::menu()
		];
	}


	public static function session(){
		return (object)[
			"plat_id"=>1
		];

		if( !isset($_SESSION["si"]) ){
			return null;
		}

		return $_SESSION["si"];
	}

	/*
	public static function nextval($sequenceName){
		$si=static::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="select plat_nextval(:plat_id,:sequence_name) as nextval";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":sequence_name",$sequenceName);
		$ca->exec();

		$tmp=$ca->fetch();
		return $tmp->nextval;
	}
	*/


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


	public static function menu(){
		global $cfg;
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$baseUrl="{$cfg["adminRoot"]}";

		$tmp=[];
		$userType=$_SESSION["si"]->user_type;

		$menuAdmin=[];

		if($userType=="architect"){

			$menuAdmin=[
				(object)[
					"menu_id"=>"admin",
					"menu_name"=>"Diseño",
					"parent_id"=>-1,
				],
				(object)[
					"menu_id"=>"design_types",
					"menu_name"=>"Tipos de Datos",
					"url"=>"/design/types",
					"parent_id"=>"admin"

				],
				(object)[
					"menu_id"=>"design_menu",
					"menu_name"=>"Menú",
					"url"=>"/design/menu",
					"parent_id"=>"admin"
				],
				(object)[
					"menu_id"=>"users",
					"menu_name"=>"Usuarios",
					"url"=>"/design/users",
					"parent_id"=>"admin"

				]
			];
		}

		$tmp=array_merge($tmp,$menuAdmin);

		$sql="
		select
			category_id as menu_id,
			parent_id,
			category_name as menu_name,

			case when type_id<>-1 then
				concat('/category/',category_id,'/1')
			else
				''
			end as url
		from eav_categories
		where
			category_id > 0
		order by
			parent_id,
			category_order,
			category_name
		";
		$ca->prepare($sql);
		$ca->exec();
		$rl=$ca->fetchAll();
		$tmp=array_merge($tmp,$rl);

		$menuModules=[];
		if( !empty($cfg["modules"]) ){
			$menuModules[]=	(object)[
				"menu_id"=>"modules",
				"menu_name"=>"Modulos",
				"parent_id"=>-1,
			];

			if( isset($cfg["modules"]["pedidos"])  && $cfg["modules"]["pedidos"] ){
				$menuModules[]=(object)[
					"menu_id"=>"pedidos",
					"menu_name"=>"Pedidos",
					"url"=>"/module/pedidos",
					"parent_id"=>"modules"
				];
			}
		}


		$tmp=array_merge($tmp,$menuModules);

		$menu=[];

		$buildTree=function(&$elements, $parentId = -1) use (&$buildTree){
			$branch = array();
			foreach ($elements as $k=>$element) {
				if ($element->parent_id == $parentId) {
					$children = $buildTree($elements, $element->menu_id);
					if ($children) {
						$element->children = $children;
					}
					//$branch[$element->menu_id] = $element;
					$branch[] = $element;
				}
			}
			return $branch;
		};

		$menu=$buildTree($tmp);
		return $menu;

	}


	public static function uploadBytes($p){
		global $cfg;
		static::session();
		$tmp_name = uniqid("upload");

		//$data=file_get_contents($p->bytes);//read stream data://text/plain;base64,SSBsb3ZlIFBIUAo=
		$data = explode(",",$p->bytes);
		$data = $data[ count($data)-1 ];
		$data=base64_decode($data);

		file_put_contents("{$cfg["appPath"]}/tmp/{$tmp_name}",$data);
		return array("tmp_name"=>$tmp_name);
	}

}


?>

<?php
require_once __DIR__."/__init__.php";

$uri = $_SERVER["REQUEST_URI"];
if( strpos($uri,"index.php") !== false ){
	$uri = str_replace("/public/index.php","",$uri);
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: {$uri}");
	exit;
}


$db=Cm\Database::database();
$ca=new Cm\DbQuery($db);

$idioma = isset($_SESSION["idioma"]) ? $_SESSION["idioma"] : "espanol";


if ($idioma == 'espanol') {
	$menuSuperior=Models\Runtime\EavCategories::menu(50,(object)["root_element"=>false]);
	$menuInferior=Models\Runtime\EavCategories::menu(50,(object)["root_element"=>false]);
}


require_once __DIR__."/router.php";

$path = realpath(__DIR__.'/controllers');
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
foreach($objects as $filename => $object){
	$filename=realpath($filename);
	if( is_file($filename) && preg_match("/^.*\.(php)$/i",$filename)){
		require_once $filename;
	}
}

$app->error(function ( Exception $e ) use ($app) {
    echo "error : " . $e;
});

$app->post("{$cfg["siteRoot"]}/rpc",function() use($app) {
	$app->config('debug',false);

	$tmp=explode(".",$_POST["method"]);
	//$res = call_user_func( array($tmp[0],$tmp[1]), (object)$_POST["params"] );

	try {
		$res = call_user_func( array($tmp[0],$tmp[1]), (object)$_POST["params"] );
	}
	catch(Cm\PublicException $e){
		echo json_encode([
			"error"=>[
				"code"=>0,
				"message"=>$e->getMessage()
			]
		]);
		exit;
	}
	catch(Exception $e){
		echo json_encode([
			"error"=>[
				"code"=>0,
				"message"=>"Fallo interno, comuniquese con soporte por favor.".$e->getMessage()
			]
		]);
		exit;
	}

	//$res = RpcController::{$_POST["method"]}( (object)$_POST["params"] );
	echo json_encode([
		"result"=>$res,
		"error"=>null
	]);
	exit;
});


foreach(array_merge($menuSuperior->items,$menuInferior->items) as $entry){
	$app->get("{$cfg["siteRoot"]}{$entry->route}",function() use($app,$entry) {
		$req = $app->request;
		$entry->root_uri= $req->getRootUri();
		$entry->resource_uri=$req->getResourceUri();

		Router\resolve( (object) $entry );
	});

}

$app->get("{$cfg["siteRoot"]}/",function() use ($cfg){
	header("Location: {$cfg["siteRoot"]}/inicio",TRUE,301);
	exit;
});

$app->get(".*",function() use($app){
	$req = $app->request;
	$rootUri = $req->getRootUri();
	$resourceUri = $req->getResourceUri();

	$entry=[
		"category_id"=>-1,
		"type_id"=>-1,
		"root_uri"=>$rootUri,
		"resource_uri"=>$resourceUri
	];

	Router\resolve( (object) $entry );
});

$app->run();
?>

<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');


if( $_SERVER['REQUEST_METHOD'] == "OPTIONS" ){
	exit;
}

if( $_SERVER["HTTP_HOST"]=="localhost"){

}

require_once __DIR__.'/__init__.php';

if( $_SERVER["HTTP_HOST"]=="localhost"){
	Cm\Bootstrap::requireOnce("debug/phpconsole.inc.php");
	Cm\Bootstrap::requireOnce("debug/firephp.inc.php");
	Cm\PhpConsoleInit();
}

session_set_cookie_params( 3600 * 24 * 7 );
session_name($cfg["sessionName"]);
if (!session_id()) session_start();


Cm\Bootstrap::requireOnce("core/lang.inc.php");
Cm\Bootstrap::requireOnce("rpc/rpcserver.inc.php");
Cm\Bootstrap::requireOnce("database/database.inc.php");
Cm\Bootstrap::requireOnce("utils/validation.inc.php");
Cm\Bootstrap::requireOnce("utils/utils.inc.php");
Cm\Bootstrap::requireOnce("debug/firephp.inc.php");
Cm\Bootstrap::requireOnce("qx/application.inc.php");
Cm\Bootstrap::requireOnce("gridfs/gridfs.inc.php");
Cm\Bootstrap::requireOnce("web/webtools.inc.php");


$db = new Cm\Database($cfg["dbDriver"]);


$db->setHostName($cfg["dbHost"]);
$db->setPort($cfg["dbPort"]);
$db->setDatabaseName($cfg["dbName"]);
$db->setUserName($cfg["dbUser"]);
$db->setPassword($cfg["dbPassword"]);
try {
	$db->open_();
}
catch(Exception $e){
	echo 'No se pudo conectar a la base de datos';
	exit;
}

$exports = array();

$controllersPaths=[
	__DIR__.'/../../app', //produccion
	__DIR__.'/../../www/app' //desarrollo
];

foreach($controllersPaths as $path){
	if( is_dir($path) ){
		$controllersPath = $path;
		break;
	}
}

if( empty($controllersPath) ){
	throw new Exception("Invalid controllers path");
}

$path = realpath($controllersPath);
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
foreach($objects as $filename => $object){
	$filename=realpath($filename);
	if( is_file($filename) && preg_match("/^.*\.(php)$/i",$filename)){
		require_once $filename;
	}
}

$server = Cm\RpcServer::create(Cm\RpcServer::MODE_JSON);
$server->setClasses($exports);
$server->handle();
exit;
?>

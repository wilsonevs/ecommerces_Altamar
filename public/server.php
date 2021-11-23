<?php
require_once __DIR__.'/../config.php';
require_once __DIR__."/__app__.php";

Cm\Bootstrap::requireOnce("debug/phpconsole.inc.php");
// Cm\Bootstrap::requireOnce("debug/firephp.inc.php");
Cm\PhpConsoleInit();

Cm\Bootstrap::requireOnce("core/lang.inc.php");
Cm\Bootstrap::requireOnce("database/database.inc.php");
Cm\Bootstrap::requireOnce("rpc/rpcserver.inc.php");
Cm\Bootstrap::requireOnce("qx/application.inc.php");
Cm\Bootstrap::requireOnce("web/webtools.inc.php");
Cm\Bootstrap::requireOnce("gridfs/gridfs.inc.php");


session_set_cookie_params( 3600 * 24 * 7 );
session_name($cfg["sessionName"]);
if (!session_id()) session_start();


$db = new Cm\Database($cfg["dbDriver"]);
$db->setHostName($cfg["dbHost"]);
$db->setPort(isset($cfg["dbPort"]) ? $cfg["dbPort"]:3306);
$db->setDatabaseName($cfg["dbName"]);
$db->setUserName($cfg["dbUser"]);
$db->setPassword($cfg["dbPassword"]);
$db->open_();


require_once __DIR__."/../d41d8cd/modelos/runtime/EavItems.php";
require_once __DIR__."/../d41d8cd/modelos/runtime/EavModel.php";
require_once __DIR__."/../d41d8cd/modelos/runtime/EavCategories.php";
require_once __DIR__."/../d41d8cd/public/Captcha.php";


class SessionException extends Exception {}
class InformationException extends Exception {}
class CriticalException extends Exception {}
class ValidationException extends Exception {}


$exports = array();
//autoload controllers
$path = realpath(__DIR__.'/controllers');
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
foreach($objects as $filename => $object){
	$filename=realpath($filename);
	if( is_file($filename) && preg_match("/^.*\.data\.(php)$/i",$filename)){

		require_once $filename;
	}
}


$server = Cm\RpcServer::create(Cm\RpcServer::MODE_JSON);
$server->setClasses($exports);
$server->handle();
?>

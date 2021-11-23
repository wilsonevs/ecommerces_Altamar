<?php
require_once __DIR__."/scssphp-0.5.1/scss.inc.php";
use Leafo\ScssPhp\Server;
use Leafo\ScssPhp\Compiler;

//print_r($_SERVER["PATH_INFO"]);
$_SERVER["PATH_INFO"] = strtr($_SERVER["PATH_INFO"],[".css"=>".scss"]);
if( !strpos($_SERVER["PATH_INFO"],".scss") ){
	$_SERVER["PATH_INFO"].=".scss";
}

$server = new Server(__DIR__);
$server->scss->setVariables(['test' => '1']);
$server->serve();
?>

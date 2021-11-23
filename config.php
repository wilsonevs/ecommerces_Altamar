<?php
date_default_timezone_set("America/Bogota");
ini_set("error_reporting", E_STRICT | E_ALL | E_NOTICE);
ini_set("display_errors", 1);
ini_set("html_errors",0);

$cfg["sessionName"]="d41d8cd";
$cfg["dbDriver"] = "MYSQL";
$cfg["dbPort"]=3306;

$cfg["gridColumnCount"]=12;
$cfg["modules"]=["tienda"];

if( file_exists(__DIR__."/config.www") ){
	$cfg["env"]="www";
	$cfg['sandbox']=false;

	//Ejemplo
	// $cfg["appHost"] = "zaecko.com.co";
	$cfg["appHost"] = "";
	$cfg["appRoot"] = "";
	$cfg["appPath"] = __DIR__;
	$cfg["libRoot"] = "{$cfg["appRoot"]}/d41d8cd/rpc/cm/php";
	$cfg["libPath"] = __DIR__."/d41d8cd/cm/php";
	$cfg["siteRoot"]="{$cfg["appRoot"]}";
	$cfg["adminRoot"]=$cfg["appRoot"];
	$cfg["modelsPath"] = __DIR__."/d41d8cd/modelos";

	$cfg["dbHost"] = "localhost";
	$cfg["dbName"] = "";
	$cfg["dbUser"] = "";
	$cfg["dbPassword"] = "";

	//Ejemplo
	// $cfg["smtp"]=[
	// 	"host"=>'smtp.gmail.com',
	// 	"username"=>'infozaecko@gmail.com',
	// 	"password"=>'Zaecko2020.',
 // 		"from"=>'zaecko@gmail.com',
	// 	"from_name"=>'zaecko.com',
	// 	'port' => 465,
	// 	'secure' => 'ssl'
	// ];


} elseif( file_exists(__DIR__."/config.int") ){
	$cfg["env"]="int";
	$cfg['sandbox']=true;

	$cfg["appHost"] = "localhost";
	$cfg["appRoot"] = "/altamareco";
	//$cfg["appRoot"] = "";
	$cfg["appPath"] = __DIR__;
	$cfg["libRoot"] = "{$cfg["appRoot"]}/d41d8cd/rpc/cm/php";
	$cfg["libPath"] = __DIR__."/d41d8cd/cm/php";


	$cfg["siteRoot"]="{$cfg["appRoot"]}";
	$cfg["adminRoot"]=$cfg["appRoot"];

	$cfg["modelsPath"] = __DIR__."/d41d8cd/modelos";

	$cfg["dbHost"] = "5.181.218.1";
	$cfg["dbName"] = "u394573475_altamar";
	$cfg["dbUser"] = "u394573475_altamar";
	$cfg["dbPassword"] = "Altamar2022.";

		
	$cfg["smtp"]=[
		"host"=>'smtp.gmail.com',
		"username"=>'camiloproyects@gmail.com',
		"password"=>'gunsandroses702',
		"from"=>'info@altamar.com',
		"from_name"=>'Altamar Importaciones'
	];

} elseif( file_exists(__DIR__."/config.dev") ){
	// $cfg["env"]="int";
	// $cfg['sandbox']=true;

	// $cfg["appHost"] = "mundoshop.com.co";
	// $cfg["appRoot"] = "/tiendaconvariacion";
	// //$cfg["appRoot"] = "";
	// $cfg["appPath"] = __DIR__;
	// $cfg["libRoot"] = "{$cfg["appRoot"]}/d41d8cd/rpc/cm/php";
	// $cfg["libPath"] = __DIR__."/d41d8cd/cm/php";


	// $cfg["siteRoot"]="{$cfg["appRoot"]}";
	// $cfg["adminRoot"]=$cfg["appRoot"];

	// $cfg["modelsPath"] = __DIR__."/d41d8cd/modelos";

	// $cfg["dbHost"] = "localhost";
	// $cfg["dbName"] = "u394573475_tiendaCon";
	// $cfg["dbUser"] = "u394573475_tiendaCon";
	// $cfg["dbPassword"] = "Camilo702.";

	// $cfg["smtp"]=[
	// 	"host"=>'mx1.hostinger.co',
	// 	//"port"=>'',
	// 	"username"=>'info@mundoshop.com.co',
	// 	"password"=>'Camilo702.',
	// 	"port"=>587,
	// 	"from"=>'info@mundoshop.com.co',
	// 	"from_name"=>'Tienda Con Variacion'
	// ];
}

else {
	throw new Exception("Ambiente no configurado");
}

//print_r($cfg);

require_once "{$cfg["libPath"]}/bootstrap.inc.php";
?>

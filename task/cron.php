<?php
require_once __DIR__.'/__init__.php';

$url_prefix = "http://{$_SERVER["HTTP_HOST"]}{$cfg["appRoot"]}/task/mail";



//CRON REGISTRO
$filter=[];
$filter[]="items.category_id = 39";
$filter[]="and";
$filter[]="{enviado}<>1";

$registro = Models\Runtime\EavModel::items( (object)[
	"filter"=>$filter
]);

foreach($registro as $r){
	$url="{$url_prefix}/correo_registro.php?item_id={$r->item_id}";
	file_get_contents($url);
	echo $url."<br/><hr/>\n";
}


//CRON CONTACTENOS
$filter=[];
$filter[]="items.category_id = 8";
$filter[]="and";
$filter[]="{enviado}<>1";

$contactenos = Models\Runtime\EavModel::items( (object)[
	"filter"=>$filter
]);

foreach($contactenos as $r){
	$url="{$url_prefix}/correo_contactenos.php?item_id={$r->item_id}";
	file_get_contents($url);
	echo $url."<br/><hr/>\n";
}


//CRON RECUPERAR CONTRASEÑA
$filter=[];
$filter[]="items.category_id = 60";
$filter[]="and";
$filter[]="{enviado}<>1";

$recuperar = Models\Runtime\EavModel::items( (object)[
	"filter"=>$filter
]);

foreach($recuperar as $r){
	$url="{$url_prefix}/correo_recuperar_contrasena.php?item_id={$r->item_id}";
	file_get_contents($url);
	echo $url."<br/><hr/>\n";
}


//CRON SUSCRIBE
$filter=[];
$filter[]="items.category_id = 67";
$filter[]="and";
$filter[]="{enviado}<>1";

$suscribe = Models\Runtime\EavModel::items( (object)[
	"filter"=>$filter
]);

foreach($suscribe as $r){
	$url="{$url_prefix}/correo_suscribe.php?item_id={$r->item_id}";
	file_get_contents($url);
	echo $url."<br/><hr/>\n";
}



?>

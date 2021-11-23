<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__.'/index.html';

$cfgPath = __DIR__.'/../config.php';
if( !file_exists($cfgPath) ){
	$cfgPath='/../../config.php';
}
require_once $cfgPath;

$jsCfg = [
	"appHost"=>$cfg["appHost"],
	"appRoot"=>$cfg["appRoot"],
	"modules"=>$cfg["modules"]
];

?>
<script>
window.cmCfg = <?=json_encode($jsCfg);?>;
</script>

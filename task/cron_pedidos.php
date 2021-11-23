<?php
require_once __DIR__.'/__init__.php';

$url_prefix = "http://{$_SERVER["HTTP_HOST"]}{$cfg["appRoot"]}/task/mail";

$ca = new Cm\DbQuery($db);


$sql="
	select
		*
	from
		pedidos_e
	where
		notificado < 1
		and estado = 'pagado'
";
$ca->prepare($sql);
$ca->exec();


//CRON NOTIFICACION RESERVA
foreach($ca->fetchAll() as $r){

	//$params =json_decode($r->params);
	$url="{$url_prefix}/correo_pedido.php?id_pedido={$r->id_pedido}";

	echo json_encode($r)."<br/>\n";
	echo $url."<br/>\n";

	$res = file_get_contents($url);
	echo $res."<br/><hr/>\n";

}

echo "<br/>END";

?>

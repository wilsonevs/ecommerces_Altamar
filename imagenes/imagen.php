<?php
require_once dirname(__FILE__).'/../config.php';


$rsId = filter_input(INPUT_GET, 'imagenid', FILTER_VALIDATE_INT);
if(!$rsId){
	echo "Invalid resource id";
	exit;
}


Cm\Bootstrap::requireOnce("database/database.inc.php");

$db = new Cm\Database($cfg["dbDriver"]);
$db->setHostName($cfg["dbHost"]);
$db->setDatabaseName($cfg["dbName"]);
$db->setUserName($cfg["dbUser"]);
$db->setPassword($cfg["dbPassword"]);
$db->open_();

$ca=new Cm\DbQuery($db);

$sql="
select
	a.rsname,
	a.rstype,
	b.data
from gfs_rs a
left join gfs_chunks b on (a.rsid=b.rsid)
where
	a.rsid=:rsid
order by
	b.segment asc
";

$ca->prepare($sql);
$ca->bindValue(":rsid",$rsId,false);

try {
	$ca->exec();
}
catch(Exception $e){
	echo "Invalid resource";
	exit;
}

if( $ca->size() == 0 ){
	echo "Resource not found";
	exit;
}

//echo "chks=".$ca->size()."<br/>";
foreach($ca->fetchAll() as $tmp){
	if( empty($r) ){
		$r = $tmp;
		continue;
	}

	$r->data.= $tmp->data;
}

$filepath = "{$cfg["appPath"]}/userfiles/gfs/1/{$rsId}";
if( !file_exists($filepath) ){
	header("HTTP/1.0 404 Not Found");
	exit;
}

$etag = md5( $r->data );
$maxAge = 3600*24*30;

header('Pragma: public');
header('Cache-Control: max-age='.$maxAge);
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + $maxAge));
header('Etag: '.$etag);
header('Content-Type: '.$r->rstype);
echo file_get_contents($filepath);
exit;
?>

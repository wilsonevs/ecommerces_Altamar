<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../config.php';


session_set_cookie_params( 3600 * 24 * 7 );
session_name($cfg["sessionName"]);
if (!session_id()) session_start();


Cm\Bootstrap::requireOnce("core/lang.inc.php");
Cm\Bootstrap::requireOnce("rpc/rpcserver.inc.php");
Cm\Bootstrap::requireOnce("database/database.inc.php");
Cm\Bootstrap::requireOnce("utils/validation.inc.php");
Cm\Bootstrap::requireOnce("utils/utils.inc.php");
// Cm\Bootstrap::requireOnce("debug/firephp.inc.php");
Cm\Bootstrap::requireOnce("gridfs/gridfs.inc.php");
Cm\Bootstrap::requireOnce("fileformats/phpexcel.inc.php");
Cm\Bootstrap::requireOnce("qx/application.inc.php");
Cm\Bootstrap::requireOnce("web/webtools.inc.php");
Cm\Bootstrap::requireOnce("mail/mail.inc.php");


$db = new Cm\Database($cfg["dbDriver"]);
$db->setHostName($cfg["dbHost"]);
$db->setPort($cfg["dbPort"]);
$db->setDatabaseName($cfg["dbName"]);
$db->setUserName($cfg["dbUser"]);
$db->setPassword($cfg["dbPassword"]);
$db->open_();

require_once __DIR__."/../d41d8cd/modelos/runtime/EavItems.php";
require_once __DIR__."/../d41d8cd/modelos/runtime/EavModel.php";
require_once __DIR__."/../d41d8cd/modelos/runtime/EavCategories.php";
require_once __DIR__."/../d41d8cd/public/Tools.php";
require_once __DIR__."/../d41d8cd/public/Controller2.php";
require_once __DIR__."/../d41d8cd/public/TwigView.php";
require_once __DIR__."/../d41d8cd/public/Forms.php";
require_once __DIR__."/../d41d8cd/public/Mail.php";
require_once __DIR__."/../d41d8cd/public/Captcha.php";



$app = new \Slim\Slim(array(
    'view' => new Cm\TwigView()
));

$app->config(array(
    'debug' => true,
    'template.path' => __DIR__.'/paginas',
	'template.url'=>"{$cfg["appRoot"]}/public/paginas",
	'site.url'=>"{$cfg["siteRoot"]}"
));


require_once __DIR__."/__app__.php";
?>

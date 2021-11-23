<?php
require_once __DIR__."/../config.php";

//require_once __DIR__."/vendor/autoload.php";
/*
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem(__DIR__);
$twig = new Twig_Environment($loader, array());
*/

Cm\Bootstrap::requireOnce("core/lang.inc.php");
Cm\Bootstrap::requireOnce("database/database.inc.php");
Cm\Bootstrap::requireOnce("mail/mail.inc.php");
Cm\Bootstrap::requireOnce("utils/utils.inc.php");
Cm\Bootstrap::requireOnce("web/webtools.inc.php");

require_once __DIR__."/../d41d8cd/modelos/runtime/EavModel.php";
require_once __DIR__."/../d41d8cd/modelos/runtime/EavItems.php";


require_once __DIR__."/../d41d8cd/public/Mail.php";
require_once __DIR__.'/../public/__app__.php';


$db = new Cm\Database($cfg["dbDriver"]);
$db->setHostName($cfg["dbHost"]);
$db->setPort($cfg["dbPort"]);
$db->setDatabaseName($cfg["dbName"]);
$db->setUserName($cfg["dbUser"]);
$db->setPassword($cfg["dbPassword"]);
$db->setFnTextSearch('fn_text_search');
$db->open_();

?>

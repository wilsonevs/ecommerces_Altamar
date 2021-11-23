<?php
require_once __DIR__."/../__init__.php";

Cm\Bootstrap::requireOnce("mail/mailinliner.inc.php");
Cm\Bootstrap::requireOnce("utils/date.inc.php");

$mail_root = "http://{$_SERVER["HTTP_HOST"]}{$cfg["appRoot"]}/task/mail";

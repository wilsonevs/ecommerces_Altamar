<?php
namespace Cm;
require_once('phar://'.__DIR__.'/PhpConsole.phar');

function PhpConsoleInit(){
	$handler = \PhpConsole\Handler::getInstance();
	$handler->start();

	\PhpConsole\Helper::register();

	//PC::debug
}

?>

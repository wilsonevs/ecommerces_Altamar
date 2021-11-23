<?php

if( file_exists("../jlib2.phar") ){
	unlink("../jlib2.phar");
}

$phar = new Phar('../jlib2.phar', 0, 'jlib2.phar');
//$phar=new PharData("../jlib2.tar");
$phar->buildFromDirectory(dirname(__FILE__) . '/../jlib2', '/.*/');

//$stub=$phar->createDefaultStub('/2.1/bootstrap.inc.php', '2.1/bootstrap.inc.php');
//$phar->setStub($stub);
?>
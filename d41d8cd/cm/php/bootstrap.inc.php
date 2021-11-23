<?php
namespace Cm;

class Bootstrap {
	public static function requireOnce($module){
		require_once dirname(__FILE__)."/{$module}";
	}
}

require_once __DIR__."/core/lang.inc.php";

?>

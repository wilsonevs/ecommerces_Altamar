<?php
namespace Cm;

class JQuery {

	public static function import($p = array()) {
		global $cfg;
		
		$jqueryVersionRoot = "{$cfg["libRoot"]}/jquery/jquery-ui-1.8.21.custom";
		$jqueryTheme="ui-lightness";
		$jqueryUiVersion="1.8.21";
		$jqueryVersion="1.7.2";
		
		$src = '';
		$src .= Html::importCss("{$jqueryVersionRoot}/css/{$jqueryTheme}/jquery-ui-{$jqueryUiVersion}.custom.css");
		$src .= Html::importJavascript("{$jqueryVersionRoot}/js/jquery-{$jqueryVersion}.min.js");
		$src .= Html::importJavascript("{$jqueryVersionRoot}/js/jquery-ui-{$jqueryUiVersion}.custom.min.js");

		return $src;
	}
	
	public static function importPlugin($file){
		global $cfg;
		$pluginRoot = "{$cfg["libRoot"]}/jquery/plugins";
		$src = Html::importJavascript("{$pluginRoot}/$file");
		return $src;
		
	}
	

}
?>

angular.module('cm.directives', ['ionic-datepicker']);
angular.module('cm.service', []);



function currentScriptPath(){
	var scripts = document.getElementsByTagName("script")
	var currentScriptPath = scripts[scripts.length - 1].src;
	return currentScriptPath;
}


var cmLang = {
	coalesceBool:function(value,defaultValue){
		if( value === null || value === undefined ) return defaultValue;
		if( value == "0" || value == "" || value===false || value===0 ) return false;
		return true;
	},

	coalesceArray:function(value,defaultValue){
		if( value === null || value === undefined || value==="" ) return defaultValue || [];
		return value;
	}
}

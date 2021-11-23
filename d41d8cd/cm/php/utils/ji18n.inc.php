<?php

class ji18n {

	public static function tr($message,$params){
		return vprintf($message,$params);
	}
}



function _tr($string,$arg1='',$arg2='',$arg3=''){
    if(func_num_args ()==1){
        return $string;
    }

    $args = func_get_args();
    $cmd = "\$text = sprintf('".implode("','",$args)."');";
    $text="";
    eval($cmd);
    return $text;
}



$es_LA = array(
    'Missing input var %1$s'=>'Falta variable de entrada %1$s'
)
?>
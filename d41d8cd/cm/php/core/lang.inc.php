<?php

//deprecated
function coalesce_blank(&$v1,&$v2=null,&$v3=null){
	if(!empty($v1)) return $v1;
	if(!empty($v2)) return $v2;
	if(!empty($v3)) return $v3;
	return "";
}



function coalesce_empty(&$v1,&$v2=null,&$v3=null){
	if(!empty($v1)) return $v1;
	if(!empty($v2)) return $v2;
	if(!empty($v3)) return $v3;
	return "";
}


function coalesce_null(&$v1,&$v2=null,&$v3=null){
	if(!empty($v1)) return $v1;
	if(!empty($v2)) return $v2;
	if(!empty($v3)) return $v3;
	return null;
}

function coalesce_zero(&$v1,&$v2=null,&$v3=null){
	if(!empty($v1)) return $v1;
	if(!empty($v2)) return $v2;
	if(!empty($v3)) return $v3;
	return 0;
}


function coalesce_false(&$v1,&$v2=null,&$v3=null){
	if(!empty($v1)) return $v1;
	if(!empty($v2)) return $v2;
	if(!empty($v3)) return $v3;
	return false;
}

function coalesce_array(&$v1,&$v2=null,&$v3=null){
	if(!empty($v1) && is_array($v1) ) return $v1;
	if(!empty($v2) && is_array($v2) ) return $v2;
	if(!empty($v3) && is_array($v3) ) return $v2;
	return array();
}

function coalesce_object(&$v1,&$v2=null,&$v3=null){
	if(!empty($v1) && is_object($v1) ) return $v1;
	if(!empty($v2) && is_object($v2) ) return $v2;
	if(!empty($v3) && is_object($v3) ) return $v2;
	return new stdClass();
}



if( !function_exists("array_column") ){

	function array_column($array,$column_key){
		$result=array();
		foreach($array as $k=>$v){
			if( is_array($v) ){
				$result[] = $v[$column_key];
			}
		}

		return $result;
	}
}


//support object and array items
function array_columnx($array,$column_key){
	$result=array();
	foreach($array as $k=>$v){
		$v=(array)$v;
		$result[] = $v[$column_key];
	}
	return $result;
}

if( !function_exists("object_merge") ){

	function object_merge($obj1,$obj2){
		return (object) array_merge((array) $obj1, (array) $obj2);
	}
}



if( !function_exists('array_concat') ){
	function array_concat($arr1,$arr2){
		foreach($arr2 as $item){
			$arr1[] = $item;
		}

		return $arr1;
	}
}


function deep_clone($obj){
	return unserialize( serialize($obj) );
}


//network
function cidr_match($ip, $cidr){

	if( $ip=="::1" ){
		return true;
	}


	if(!is_array($cidr)){
		$cidr = [ $cidr ];
	}

	foreach($cidr as $tmp){
		$tmp = trim($tmp);
		if( empty($tmp) ) continue;

		if( strpos($tmp,"/") === false ){

			if( $tmp[0]=="0.0.0.0" ){
				$tmp.="/0";
			}
			else {
				$tmp.="/32";
			}
		}

		list($subnet, $mask) = explode('/', $tmp);
		if ((ip2long($ip) & ~((1 << (32 - $mask)) - 1) ) == ip2long($subnet)){
			return true;
		}
	}

	return false;
}


function curl_file_get_contents($url,$method="post",$fields=[]){
	$fields_string = http_build_query($fields);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);


	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}



function array_group_by($arr, $key)
{
	if (!is_array($arr)) {
		trigger_error('array_group_by(): The first argument should be an array', E_USER_ERROR);
	}
	if (!is_string($key) && !is_int($key) && !is_float($key)) {
		trigger_error('array_group_by(): The key should be a string or an integer', E_USER_ERROR);
	}

	// Load the new array, splitting by the target key
	$grouped = [];

	foreach ($arr as $value) {

		if (is_object($value)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$value = get_object_vars($value);
		}

		$grouped[ $value[$key] ][] = $value;

	}

	// Recursively build a nested grouping if more parameters are supplied
	// Each grouped array value is grouped according to the next sequential key
	if (func_num_args() > 2) {
		$args = func_get_args();

		foreach ($grouped as $key => $value) {
			$parms = array_merge([$value], array_slice($args, 2, func_num_args()));
			$grouped[$key] = call_user_func_array('array_group_by', $parms);
		}
	}

	return $grouped;
}




?>

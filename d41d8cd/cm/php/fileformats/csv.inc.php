<?php
namespace Cm;

class Csv {
	public static function exportAsString($data,$headers=null,$delimiter = ",",$enclosure = '"'){
		if( !is_array($data) || count($data)==0 ) return "";
		$output=array();
		$output[]=implode($delimiter,array_keys( (array) $data[0]));


		foreach($data as $r0){
			$output[] = implode($delimiter,array_values( (array) $r0));
		}

		return implode("\n",$output);
	}
}
?>

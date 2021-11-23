<?php
namespace Cm;
//require_once __DIR__."/PHPExcel_1.7.9/Classes/PHPExcel.php";
//require_once __DIR__."/PHPExcel_1.7.9/Classes/PHPExcel/Writer/Excel2007.php";

require_once __DIR__."/PHPExcel_1.8.0/Classes/PHPExcel.php";
require_once __DIR__."/PHPExcel_1.8.0/Classes/PHPExcel/Writer/Excel2007.php";


class PHPExcel extends \PHPExcel {

	public static function exportFromQuery($ca,$p,$filename='',$output='file'){
		ini_set("memory_limit","1024M");
		global $cfg;


		if( isset($p->filename) ){
			$filename = $p->filename;
		}

		if( empty($filename) ){
			throw new PublicException("Invalid xls filename");
		}

		/*
		if( empty($filename) ){
			$filename = "{$cfg["appPath"]}/tmp/{$p->output_prefix}.xlsx";

		}
		*/

		$uri = "{$cfg["appRoot"]}/tmp/{$filename}.xlsx";
		$filepath = "{$cfg["appPath"]}/tmp/{$filename}.xlsx";


		$xls = new \PHPExcel();
		$xls->setActiveSheetIndex(0);

		$p->page = 1;
		$p->count = 100000*10;
		$page = $ca->execPage($p);
		$p->column_types = isset($p->column_types) ? (object)$p->column_types:new \stdClass();
		$p->exclude_fields = isset($p->exclude_fields) ? $p->exclude_fields : "";


		//elimina un texto al final de todas las columnas
		$p->strip_suffix = coalesce_blank($p->strip_suffix);

		$excludeFields = explode(",",$p->exclude_fields);

		if( $page->recordCount > 0 ){
			$rh = array_keys( (array) $page->records[0] );
			$columnCount = count($rh);

			$colIndex = 0 ;

			foreach($rh as $k0=>$v0){

				if( in_array($v0,$excludeFields) ) continue;

				if( $p->strip_suffix ){
					$v0 = preg_replace("/{$p->strip_suffix}$/","",$v0);
				}

				$col = \PHPExcel_Cell::stringFromColumnIndex($colIndex++);
				$xls->getActiveSheet()->SetCellValue($col."1", $v0);
			}
		}


		foreach($page->records as $i=>$r){
			//$columnIndex = PHPExcel_Cell::columnIndexFromString($column);

			//foreach($r as $k0=>$v0){

			$colIndex=0;

			foreach($rh as $k0=>$v0){

				if( in_array($v0,$excludeFields) ) continue;

				$col = \PHPExcel_Cell::stringFromColumnIndex($colIndex++);
				$fieldType=isset($p->column_types->{$v0}) ? $p->column_types->{$v0} : "string";


				if( $fieldType!="string" ){
					$xls->getActiveSheet()->SetCellValue($col.($i+2), $r->{$v0});
					continue;
				}

				$xls->getActiveSheet()->setCellValueExplicit($col.($i+2), $r->{$v0}, \PHPExcel_Cell_DataType::TYPE_STRING);

				/*
				if( $fieldType=="string" || gettype($r->{$v0})=="string"){
					continue;
				}
				*/
			}
		}


		if( $output == 'browser' ){
			$filename = !empty($filename) ? $filename :"document.xls";

			header('Content-Type: application/vnd.ms-excel');

			$inline = false;
			if( $inline ){
				header('Content-Disposition: inline;filename="'.$filename.'"');
			}
			else {
				header('Content-Disposition: attachment;filename="'.$filename.'"');
			}

			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
			$objWriter->save('php://output');
			exit;
		}


		$objWriter = new \PHPExcel_Writer_Excel2007($xls);
		$objWriter->save($filepath);
		return $uri;
	}


	public static function exportFromArray($data,$p,$filename='',$output='file'){
		ini_set("memory_limit","1024M");
		global $cfg;

		if( isset($p->filename) ){
			$filename = $p->filename;
		}

		if( empty($filename) ){
			throw new PublicException("Invalid xls filename");
		}

		$uri = "{$cfg["appRoot"]}/tmp/{$filename}.xlsx";
		$filepath = "{$cfg["appPath"]}/tmp/{$filename}.xlsx";

		$xls = new \PHPExcel();
		$xls->setActiveSheetIndex(0);

		$p->page = 1;
		$p->count = 100000*10;
		$p->column_types = isset($p->column_types) ? (object)$p->column_types:new \stdClass();
		$p->exclude_fields = isset($p->exclude_fields) ? $p->exclude_fields : "";

		//elimina un texto al final de todas las columnas
		$p->strip_suffix = coalesce_blank($p->strip_suffix);

		$excludeFields = explode(",",$p->exclude_fields);

		$rh = array();

		if( count($data["data"]) > 0 ){

			$rh = $data["headers"];

			$columnCount = count($rh);

			$colIndex = 0 ;

			foreach($rh as $k0=>$v0){
				$col = \PHPExcel_Cell::stringFromColumnIndex($colIndex++);
				$xls->getActiveSheet()->SetCellValue($col."1", $v0);
			}

			$columnIndex = 2;
			foreach($data["data"] as $r){
				$colIndex=0;

				foreach($data["headers"] as $key => $value){
					$col = \PHPExcel_Cell::stringFromColumnIndex($colIndex++);
					$xls->getActiveSheet()->setCellValueExplicit($col.$columnIndex, $r[$key], \PHPExcel_Cell_DataType::TYPE_STRING);
				}

				$columnIndex++;
			}
		}



		if( $output == 'browser' ){
			$filename = !empty($filename) ? $filename :"document.xls";

			header('Content-Type: application/vnd.ms-excel');

			$inline = false;
			if( $inline ){
				header('Content-Disposition: inline;filename="'.$filename.'"');
			}
			else {
				header('Content-Disposition: attachment;filename="'.$filename.'"');
			}

			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
			$objWriter->save('php://output');
			exit;
		}

		$objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
		$objWriter->save($filepath);
		return $uri;
	}

	public function outputBrowserHtml(){
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'HTML');
		$objWriter->save('php://output');
		exit;
	}

	public function outputBrowserXls($filename='',$inline=false){
		$filename = !empty($filename) ? $filename :"document.xls";

		header('Content-Type: application/vnd.ms-excel');

		if( $inline ){
			header('Content-Disposition: inline;filename="'.$filename.'"');
		}
		else {
			header('Content-Disposition: attachment;filename="'.$filename.'"');
		}

		header('Cache-Control: max-age=0');
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}

	public function outputFileXls($filename){
		$objWriter = new \PHPExcel_Writer_Excel2007($this);
		$objWriter->save($filename);
	}

}
?>

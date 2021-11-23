<?php
namespace Cm;
require_once __DIR__.'/fpdf17/code128.php';



class FPDF_Encoding extends \PDF_Code128 {
	public $encoding;

	
	public function setEncoding($encoding){
		$this->encoding = $encoding;
	}
	
	public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link=''){
		@ $txt = iconv($this->encoding, 'ISO-8859-2//TRANSLIT', $txt);
		parent::Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
	}
	
	public function CellTruncate($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link=''){
		
		$tmp = $txt;
		while( $this->GetStringWidth($tmp) > $w ){
			$tmp = substr($tmp,0,-1);
		}
		
		if( $tmp!=$txt){
			$tmp=substr($tmp,0,-3)."...";
		}
		
		@ $txt = iconv($this->encoding, 'ISO-8859-2//TRANSLIT', $tmp);
		parent::Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
	}
	/*
	public function GetStringWidth($s){
		// Get width of a string in the current font
		$s = (string)$s;
		$cw = &$this->CurrentFont['cw'];
		$w = 0;
		$l = strlen($s);
		for($i=0;$i<$l;$i++)
			$w += $cw[$s[$i]];
		return $w*$this->FontSize/1000;
	}
	*/
	
	public function GetFontSize(){
		return $this->FontSize;
	}
	
	public function wordWrap($s,$width){
		$lines=array();
		$chars =str_split($s);
		$l=array();
		
		while( count($chars) > 0 ){
			$c = $chars[0];
			
			if( $c!="\n" && $this->GetStringWidth(implode('',$l).$c) < $width){
				$l[]=$c;
				\array_shift($chars);
				continue;
			}
			
			if( $c=="\n"){
				\array_shift($chars);
			}
			else {
				if( $l[ count($l)-1 ] !=" " && $c!=" "){
					
					//search previous space
					if( in_array(" ",$l) ){
						while( $l[count($l)-1]!=" "){
							$c = \array_pop($l);
							\array_unshift($chars,$c);
						}
					}
					
					//cut
					if( count($l) > $width ){
						while( count($l) > $width){
							$c = \array_pop($l);
							\array_unshift($chars,$c);
						}
					}
				}
			}
			
			$lines[]=implode('',$l);
			$l=array();
			
		}
		if( count($l) > 0 ){
			$lines[] = implode('',$l);
		}
		
		return $lines;
	}
	
}

class FPDF_Final extends FPDF_Encoding{

}

class JNcReport extends FPDF_Final {
	private $doc = null;
	private $op;
	private $m_xmlfile=null;
	private $m_initc=array();
	public $m_ds=array();
	public $m_variables=array();
	
	public $m_pageHeaderHeight=0;
	public $m_pageFooterHeight=0;
	
	function __construct(){
	}
	
	
	public function resourcePath(){
		return dirname($this->m_xmlfile);
	}
	
	
	public function configPage($margins){
		$options = $this->doc->getElementsByTagName("options")->item(0);
		$this->options=array();
		foreach($options->getElementsByTagName("*") as $r){
			$this->options[ $r->tagName ] = $r->textContent;
		}
		
		$this->options["leftmargin"] = $margins[0];
		$this->options["topmargin"] = $margins[1];
		$this->options["rightmargin"] = $margins[2];
		$this->options["bottommargin"] = $margins[3];
	}
	
	public static function hex2rgb($hexStr, $returnAsString = false, $seperator = ',') {
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
		$rgbArray = array();
		if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
			$colorVal = hexdec($hexStr);
			$rgbArray['r'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['g'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['b'] = 0xFF & $colorVal;
		} elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
			$rgbArray['r'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['g'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['b'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		} else {
			return false; //Invalid hex color code
		}
		return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
	}
	
	public function drawRectangle($x,$y,$el){
		$posX = $el->getAttribute("posX");
		$posY = $el->getAttribute("posY");
		$width = $el->getAttribute("width");
		$height = $el->getAttribute("height");
		
		$fillStyle = $el->getAttribute("fillStyle");
		
		$fillColor = $el->getAttribute("fillColor");
		$fillColor = self::hex2rgb($fillColor);
		
		$lineStyle = $el->getAttribute("lineStyle");
		$lineWidth = $el->getAttribute("lineWidth");
		
		$this->SetLineWidth( $lineWidth );
		
		$this->setXY($x,$y);
		
		if( $fillStyle!="no"){
			$this->SetFillColor($fillColor["r"],$fillColor["g"],$fillColor["b"]);
		}
		else {
			$this->SetFillColor(255,255,255);
		}
		
		$this->Cell($width,$height,'',1,0,'',true);
	
		return;
	}
	
	public function drawLabel($x,$y,$el,$content){
		$fontName = $el->getAttribute("fontName");
		if( strpos(strtolower($fontName),"arial,courier,helvetica,symbol,times,zapfdingbats") === false ){
			$fontName="Arial";
		}
		
		
		
		
		$fontSize = $el->getAttribute("fontSize");
		$fontWeight = $el->getAttribute("fontWeight");
		$fontWeight = $fontWeight=="bold"?"B":"";
		
		$width = $el->getAttribute("width");
		$height = $el->getAttribute("height");
		
		$alignment = $el->getAttribute("alignment");
		$wordbreak = $el->getAttribute("wordbreak");
		
		switch($alignment){
			case 34:
				$alignment='R';
				break;
				
			case 4:
			case 36:
				$alignment='C';
				break;
				
			case 1:
				$alignment='L';
				break;
				
			case 2:
				$alignment='R';
				break;
		}
		
		
        //<field id="1" zValue="1" resource="static" posX="168.010" posY="17.462" width="26.458" height="5.027" 
		//fontName="Arial" fontSize="10" alignment="2" forecolor="#000000" type="num" ftype="var" 
		//formatting="true" numwidth="0" format="f" precision="0" fillchar=" " localized="true" arg="">total_val_flete_pesos</field>
		
		$type=$el->getAttribute("type");
		$formatting=$el->getAttribute("formatting");
		$format=$el->getAttribute("format");
		$fillchar=$el->getAttribute("fillchar");
		$localized=$el->getAttribute("localized");
		$precision=$el->getAttribute("precision");
		
		
		if( $type=="num" && $formatting=="true" && is_numeric($content) ){
			$content = number_format($content,$precision);
		}
		
		$this->setFont($fontName,$fontWeight,(integer)$fontSize);
		
		if($wordbreak=="true"){
			$text = $this->wordWrap($content,$width);

		}
		else {
			$text = explode("\n",$content);
		}
		
		if( $el->tagName=="text"){
			$text = array("Text Element....");
		}
		
		$offsetY = $y;
		
		$lineHeight = floor($height/count($text));
		$lineHeight = $this->GetFontSize()+.4;
		
		
		foreach($text as $k=>$textLine){
			//if( empty($textLine) ) continue;
			 
			$this->setXY($x,$offsetY );
			$this->CellTruncate($width, $lineHeight ,$textLine,0,0,$alignment);
			
			$offsetY+=$lineHeight;
		}
	}
	
	
	
	
	public function drawLine($x,$y,$el){
	
		$fromX = $el->getAttribute("fromX");
		$fromY = $el->getAttribute("fromY");
		
		$toX = $el->getAttribute("toX");
		$toY = $el->getAttribute("toY");
	
		$lineWidth = $el->getAttribute("lineWidth");
		
		$fillStyle = $el->getAttribute("fillStyle");
		
		$fillColor = $el->getAttribute("fillColor");
		$fillColor = self::hex2rgb($fillColor);
		
		$this->setXY($x,$y);
		
		$this->SetLineWidth($lineWidth);
		
		if( $fillStyle!="no"){
			$this->SetFillColor($fillColor["r"],$fillColor["g"],$fillColor["b"]);
		}
		else {
			$this->SetFillColor(255,255,255);
		}
		
		$this->Line($x+$fromX,$y+$fromY,$x+$toX,$y+$toY);
	
		return;
	}
	
	public function drawImage($x,$y,$el){
		//<image id="1869" zValue="13" posX="106.362" posY="0.000" width="60.325" height="11.112" 
		//resource="file" aspectRatio="keep" format="0">:/img/CoordinadoraUSA17a.jpg</image>
		$posX = $el->getAttribute("posX");
		$posY = $el->getAttribute("posY");
		$width = $el->getAttribute("width");
		$height = $el->getAttribute("height");
		
		$image = $el->textContent;
		if( strpos($image,":/")!==-1){
			$image = substr($el->textContent,2);
		}
		
		$this->Image( $this->resourcePath()."/{$image}",$x,$y,$width,$height );
		return;
	}
	
	public function drawBarcode($x,$y,$el,$content){
		$barcodeType = $el->getAttribute("barcodeType");
		$width = $el->getAttribute("width");
		$height = $el->getAttribute("height");
		
		//EAN 128
		if( $barcodeType==8){
			$this->Code128($x, $y, $content, $width, $height) ;
		}
		
		return;
	}
	
	public function processSection($x,$y,$section){
		$sectionHeight = $section->getAttribute("height");
		
		foreach( $section->getElementsByTagName("*") as $el){
			$posX = $x+ $el->getAttribute("posX") ;
			$posY = $y+ $el->getAttribute("posY");
		
			if( $el->tagName=="rectangle"){
				$this->drawRectangle($posX,$posY,$el);
				continue;
			}
			
			if( $el->tagName=="label" || $el->tagName=="field" || $el->tagName=="text"){
				
				$content = $el->textContent;
				if( $el->tagName=="field"){
					$content = self::fieldValue( $el->getAttribute("ftype"),$el->textContent );
				}
			
				$this->drawLabel($posX,$posY,$el,$content);
				continue;
			}
			
			if( $el->tagName=="line"){
				$this->drawLine($posX,$posY,$el);
				continue;
			}
			
			
			if( $el->tagName=="image"){
				$this->drawImage($posX,$posY,$el);
				continue;
			}
			
			
			if( $el->tagName=="barcode"){
				$content = $el->textContent;
				if( $el->getAttribute("resource") != "static" ){
					$content = self::fieldValue( $el->getAttribute("resource"),$el->textContent );
				}
				
				$this->drawBarcode($posX,$posY,$el,$content);
				continue;
			}
		}
		
	}

	public function processReportHeader(){
		$op=&$this->options;
		$x = $op["leftmargin"];
		$y = $this->getY();
		
		$section = $this->doc->getElementsByTagName("reportheader")->item(0);
		
		if( empty($section) ){
			return;
		}
		
		$sectionHeight = $section->getAttribute("height");
		$this->processSection($x,$y,$section);
		$this->SetY(  $y+$sectionHeight );
	}
	
	public function processReportFooter(){
		$op=&$this->options;
		$x = $op["leftmargin"];
		$y = $this->getY();
		
		$section = $this->doc->getElementsByTagName("reportfooter")->item(0);
		
		if( empty($section) ){
			return;
		}
		
		$sectionHeight = $section->getAttribute("height");
		$this->checkAddPage($y+$sectionHeight);
		$y = $this->GetY();
		$this->processSection($x,$y,$section);
	}
	
	public function processPageHeader(){
		$op=&$this->options;
		
		$x = $op["leftmargin"];
		$y = $op["topmargin"];
		
		
		$header = $this->doc->getElementsByTagName("pageheader")->item(0);
		
		if( empty($header) ){
			return;
		}
		
		
		$sectionHeight = $header->getAttribute("height");
		
		$this->processSection($x,$y,$header);
		$this->SetY(  $y+$sectionHeight );
	}
	
	public function processPageFooter(){
		$op=&$this->options;
		
		$x = $op["leftmargin"];
		
		
		
		$section = $this->doc->getElementsByTagName("pagefooter")->item(0);
		if( empty($section) ){
			return;
		}
		
		
		$sectionHeight = $section->getAttribute("height");

		$this->SetY( -1*$sectionHeight);
		$y=$this->GetY();
		
		$this->processSection($x,$y,$section);
		$this->setY(  $y+$sectionHeight );
	}
	

	
	public function processDetails(){
		$op=&$this->options;
		$details = $this->doc->getElementsByTagName("details")->item(0);
		
		$x=$op["leftmargin"];
		//$y = $this->GetY();
		
		
		
		foreach( $details->getElementsByTagName("detail") as $detail){
			$y = $this->GetY();
		
			$sectionHeight = $detail->getAttribute("height");
			$this->processDetail($x,$y,$detail);
		}
	}
	public function processDetail($x,$y,$detail){
		$op=&$this->options;
		
		$x = $op["leftmargin"];
		//$y = $this->getY();
		
		
		
		$sectionHeight = (float) $detail->getAttribute("height");
		$groupFooterHeight=0;
		
		
		$groups = $detail->getElementsByTagName("groups")->item(0);
		//<group id="Group0" groupExp="guia.id_guia" resetVariables="" reprintHeader="true" startsOnNewPage="true">
		
		$groupHeader="";
		$groupFooter="";
		$groupExp="";
		
		if( !empty($groups) ){
			
			$group = $groups->getElementsByTagName("group")->item(0);
			$groupExp=$group->getAttribute("groupExp");
			$groupExpValue=null;
			$groupResetVariables = $group->getAttribute("resetVariables");
			$groupResetVariables = explode(",",$groupResetVariables);
			
			$groupReprintHeader = $group->getAttribute("reprintHeader");
			$groupStartsInNewPage = $group->getAttribute("startsOnNewPage");
			$groupHeader = $group->getElementsByTagName("groupheader")->item(0);
			$groupHeaderHeight=0;
			if(!empty($groupHeader) ){
				$groupHeaderHeight = $groupHeader->getAttribute("height");
			}
			
			
			$groupFooter = $group->getElementsByTagName("groupfooter")->item(0);
			$groupFooterHeight=0;
			if(!empty($groupFooter) ){
				$groupFooterHeight = $groupFooter->getAttribute("height");
			}

		}
		
		$items = $detail->getElementsByTagName("items")->item(0);
		$detailHeight = $detail->getAttribute("height");
		$dsId = $detail->getAttribute("datasource");
		
		
		$groupExpValue=$this->fieldValue("ds",$groupExp);
		
		for($i=0;$i<$this->m_ds[$dsId]["query"]->size();$i++){
			$groupHeaderPainted=false;
			$groupBreak=false;
			
			if( $this->checkAddPage($y+$sectionHeight) ){
				$y = $this->GetY();
			}
			
			if( !$groupHeaderPainted && $groupHeader && $i==0 ){
				$groupHeaderPainted=true;
				
				$this->processSection($x,$y,$groupHeader);
				$y +=$groupHeaderHeight;
			}
			
			$this->processSection( $x,$y,$items);
			$y+=$sectionHeight;

			
			if( !$groupHeaderPainted && $groupHeader && $groupExpValue != $this->fieldValue("ds",$groupExp,true) ){
				$groupHeaderPainted=true;
				
				if( $groupFooter ){
					$this->processSection($x,$y,$groupFooter);
					$y+= $groupFooterHeight;
				}
				
				$this->resetVariables($groupResetVariables);
				
				if( $groupStartsInNewPage=="true" && $i!=0){
					$this->addPage();
					$y=$this->GetY();
				}
				
				$groupExpValue = $this->fieldValue("ds",$groupExp,true);
				
				$this->processSection($x,$y,$groupHeader);
				$y +=$groupHeaderHeight;
			}
			
			
			$this->nextDsRecord($dsId);
		}
		
		
		if( $groupFooter && $this->m_ds[$dsId]["query"]->size() > 0 ){
			$this->processSection($x,$y,$groupFooter);
		}
		
		$this->SetY( $y );
		//$this->SetY( $y + $detailHeight + $groupFooterHeight );
		
	}
	
	
	//deprecated
	function Header(){
		//$this->processPageHeader();
	}
	
	//deprecated
	function Footer(){
		//$this->processPageFooter();
	}
	
	
	public function nextDsRecord($dsId){
		$query = &$this->m_ds[$dsId]["query"];
		$r=false;
		$eof=true;
		
		/*if( ($query->at()+1) < $query->size() ){
			$eof=false;
			$r=$this->m_ds[$dsId]["record"]=$this->m_ds[$dsId]["query"]->fetch();
		}*/
		
		$index=$this->m_ds[$dsId]["index"];
		
		if( ( $index+1 ) < $this->m_ds[$dsId]["size"] ){
			$eof=false;
			$r=$this->m_ds[$dsId]["record"]=$this->m_ds[$dsId]["records"][$index+1];
			$this->m_ds[$dsId]["index"] = $index+1;
		}
		
			
		if(!$eof){
			foreach($this->m_variables as $k=>$v){
			
				//avoid if not current data source
				list($varDsId,$field)=explode(".",$v["content"] );
				if( $varDsId != $dsId ) continue;
				
				
				switch($v["funcType"]){
					case "sum":
						$this->m_variables[$k]["value"]= (float)$v["value"] + (float)$this->fieldValue("ds",$v["content"] );
						break;
						
					case "count":
						$this->m_variables[$k]["value"]= (float)$v["value"] + 1;
						break;
				}
			}
		}
		
		//return $r===false ? false:true;
		return !$eof;
	}
	
	/*public function nextDsRecordNotAdvance($dsId){
	
	}*/
	
	
	public function resetVariables($variables){
		
		foreach($variables as $name){
			
			//fixme
			//revisar con este reporte de cye
			//http://localhost/dropboxwww/cye/reports/report.php?xml=factura.xml&id_despacho=400&id_guia=-1
			
			if(!isset($this->m_variables[ $name ]["initValue"]) ){
				continue;
			}
			
			$this->m_variables[ $name ]["value"] = $this->m_variables[ $name ]["initValue"];
		}
	}
	
	
	public function fieldValue($ftype,$field,$nextRecord=false){
		
		if( ( $ftype=="ds" || $ftype=="datasource") && strpos($field,".") !== false ){
			list($dsId,$field)=explode(".",$field);
			
			//if( $this->m_ds[$dsId]["query"]->at()==-1){
				//$this->m_ds[$dsId]["record"]=$this->m_ds[$dsId]["query"]->fetch();
				//$this->nextDsRecord($dsId);
			//}
			
			
			if( !isset( $this->m_ds[$dsId] ) ){
				throw new PublicException("Invalid data source '{$dsId}'");
			}
			
			
			if( $nextRecord==false){
				$r = isset( $this->m_ds[$dsId]["record"] ) ? $this->m_ds[$dsId]["record"]:(new \stdClass()) ;
			}
			else {
				$index = $this->m_ds[$dsId]["index"]+1;
				$index = $index < $this->m_ds[$dsId]["size"] ? $index: $index-1;
				$r = $this->m_ds[$dsId]["records"][$index];
			}
			
			return isset($r->{$field})?$r->{$field}:"";
		}
		
		if( $ftype=="var" && !empty( $this->m_variables[ $field ] ) ){
			return $this->m_variables[ $field ]["value"];
		}
		
		
		return "unknown field";
	}
	
	public function checkAddPage($y){
		$op=&$this->options;
		
		if( ($y+$this->m_pageFooterHeight) >= ( $this->CurPageSize[1]-$op["bottommargin"] ) ){
			$this->addPage();
			return true;
		}
		
		return false;
	}
	
	public function addPage($orientation = '', $size = ''){
		$op=$this->options;
		
		
		if( $this->page>0){
			$this->processPageFooter();
		}
		
		parent::AddPage($orientation,$size);
		$this->SetXY($op["leftmargin"],$op["topmargin"]);
		$this->SetFont('Arial','',8);
		
		$this->processPageHeader();
	}
	
	public function runReport(&$db,$xmlfile,$p=array(),$orientation='P', $unit='mm', $size='Letter') {
		$p=(array) $p;
		
		if( !empty($p["topmargin"]) ){
			$p["margins"]="{$p["topmargin"]},{$p["leftmargin"]}";
		}
		
		
		$p["margins"] = isset($p["margins"]) ? $p["margins"]:"0,0,0,0";
		$p["margins"] = explode(",",$p["margins"]);
		while( count($p["margins"]) < 4){
			$p["margins"][]=0;
		}
			
		$this->m_xmlfile = $xmlfile;
		$this->doc  = new \DomDocument('1.0');
		$doc = &$this->doc;
		$doc->loadXML( file_get_contents($this->m_xmlfile) );
		
		
		$variables = $doc->getElementsByTagName("variables")->item(0);
		foreach($variables->getElementsByTagName("variable") as $el){
			//<variable id="v_peso_kg" type="num" funcType="sum" scope="report" initValue="0">peso</variable>
			//<variable id="v_total_declarado" type="num" funcType="sum" scope="report" initValue="0">valor_total</variable>
		
			$varId = $el->getAttribute("id");
			$this->m_variables[ $varId ] = array(
				"type"=>$el->getAttribute("type"),
				"funcType"=>$el->getAttribute("funcType"),
				"scope"=>$el->getAttribute("scope"),
				"initValue"=>$el->getAttribute("initValue"),
				"content"=>$el->textContent,
				"value"=>$el->getAttribute("initValue")
			);
		}
		

		
		
		
		$ds = array();
		$datasources=$doc->getElementsByTagName("datasources")->item(0);
		foreach( $datasources->getElementsByTagName("datasource") as $d){
			$id = $d->getAttribute("id");
			$content = trim( (string )$d->textContent );
			
			//empty datasource
			if( $content=="" ) continue;

			$ca = new DbQuery($db);
			$query = $content;
			foreach($p as $k=>$v){
				if( is_string($v) || is_numeric($v) ){
					$query = str_replace("\$P{{$k}}",$v,$query);
				}
			}
			
			$ca->prepare($query);
			$ca->exec();
			
			
			$this->m_ds[$id] = array(
				"type"=>$d->getAttribute("type"),
				"content"=>$content,
				"query"=>$ca,
				"records"=>$ca->fetchAll(),
				"index"=>-1,
				"size"=>$ca->size()
			);
			
			//print_r( array_keys($this->m_ds) );
			
			$this->nextDsRecord($id);
			
		}
		
		
		
		


		
		
		$pageHeader = $doc->getElementsByTagName("pageheader");
		if( $pageHeader->length > 0 ){
			$this->m_pageHeaderHeight =$pageHeader->item(0)->getAttribute("height");
		}
		
		$pageFooter = $doc->getElementsByTagName("pagefooter");
		if( $pageFooter->length > 0 ){
			$this->m_pageFooterHeight =$pageFooter->item(0)->getAttribute("height");
		}
		
		
		//<orientation>landscape</orientation>
		//<pagesize width="99.000" height="61.000">CUSTOM</pagesize>
		
		$options = $this->doc->getElementsByTagName("options")->item(0);
		$orientation = $options->getElementsByTagName("orientation")->item(0);
		$orientation = $orientation->textContent;
		$orientation = $orientation=="landscape" ?"L":"P";
		
		$size = $options->getElementsByTagName("pagesize")->item(0);
		$size = array(  $size->getAttribute("width"), $size->getAttribute("height") );
		
		
		parent::__construct($orientation,$unit,$size);
		$this->setEncoding("UTF-8");
		$this->SetAutoPageBreak(false);
		$this->SetMargins(0,0,0,0);
		
		$this->configPage($p["margins"]);
		$this->addPage();
		
		
		$this->processReportHeader();
		$this->processDetails();
		$this->processReportFooter();
		$this->processPageFooter();
		$this->Output();
	}
}

?>

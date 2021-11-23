<?php
namespace Cm;
//require_once __DIR__.'/fpdf17/fpdf_ext.inc.php';
//require_once __DIR__.'/tfpdf/tfpdf.php';
require_once __DIR__.'/code128.php';


class PDF_Rotate extends \PDF_Code128 {
	var $angle=0;

	function Rotate($angle,$x=-1,$y=-1)
	{
		if($x==-1)
			$x=$this->x;
		if($y==-1)
			$y=$this->y;
		if($this->angle!=0)
			$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0)
		{
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}

	function RotatedText($x,$y,$txt,$angle)
	{
		//Text rotated around its origin
		$this->Rotate($angle,$x,$y);
		$this->Text($x,$y,$txt);
		$this->Rotate(0);
	}

	function RotatedImage($file,$x,$y,$w,$h,$angle)
	{
		//Image rotated around its upper-left corner
		$this->Rotate($angle,$x,$y);
		$this->Image($file,$x,$y,$w,$h);
		$this->Rotate(0);
	}


	function _endpage()
	{
		if($this->angle!=0)
		{
			$this->angle=0;
			$this->_out('Q');
		}
		parent::_endpage();
	}
}




class FPDF_Encoding extends PDF_Rotate { //\tFPDF { //\FPDF_Ext {
	public $encoding;


	/*
	function __construct($orientation='P', $unit='mm', $format='Letter'){
		parent::__construct($orientation,$unit,$format);
	}
	*/

	public function pageHeader(){

	}

	public function header(){
		$this->pageHeader();
	}

	public function pageFooter(){

	}

	public function footer(){
		$this->pageFooter();
	}


	public function setEncoding($encoding){
		$this->encoding = $encoding;
	}

	public function cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='',$radius=0,$corners=''){
		//@ $txt = iconv($this->encoding, 'ISO-8859-1//TRANSLIT', $txt);

		if( $radius== 0 ){
			parent::cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
			return;
		}

		$x=$this->getX();
		$y=$this->getY();
		$style=($border=1 ? "D":"").($fill ?"F":"");

		$this->RoundedRect($x, $y,$w,$h, $radius, $corners,$style);
		parent::cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
	}

	/*
	public function multiCell($w, $h=0, $txt='', $border=0, $align='', $fill=false){
		//throw new \Exception("multiCell not supported");
		//@ $txt = iconv($this->encoding, 'ISO-8859-1//TRANSLIT', $txt);

		$x=$this->getX();
		$lines=$this->wordWrap($txt,$w);
		foreach($lines as $l){
			$this->cell($w,$h,$l);
			$this->ln();
			$this->setX($x);
		}

		//parent::multiCell($w,$h,$txt,$border,$align,$fill);
	}
	*/

	public function cellTruncate($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link=''){

		$tmp = $txt;
		while( $this->GetStringWidth($tmp) > $w ){
			$tmp = substr($tmp,0,-1);
		}

		if( $tmp!=$txt){
			$tmp=substr($tmp,0,-3)."...";
		}

		//@ $txt = iconv($this->encoding, 'ISO-8859-1//TRANSLIT', $tmp);
		parent::Cell($w,$h,$tmp,$border,$ln,$align,$fill,$link);
	}

	public function __review__GetStringWidth($s){
		// Get width of a string in the current font
		$s = (string)$s;
		$cw = &$this->CurrentFont['cw'];
		$w = 0;
		$l = strlen($s);
		for($i=0;$i<$l;$i++)
			$w += $cw[$s[$i]];
		return $w*$this->FontSize/1000;
	}


	public function getFontSize(){
		return $this->FontSize;
	}

	function WordWrap($text, $maxwidth){
		$text = trim($text);
		$text=preg_replace("/\s+/"," ",$text);

		if ($text==='')
			return [];

		$space = $this->GetStringWidth(' ');
		$lines = explode("\n", $text);
		$text = '';
		$count = 0;

		foreach ($lines as $line)
		{
			$words = preg_split('/ +/', $line);
			$width = 0;

			foreach ($words as $word)
			{
				$wordwidth = $this->GetStringWidth($word);
				if ($wordwidth > $maxwidth)
				{
					// Word is too long, we cut it
					for($i=0; $i<strlen($word); $i++)
					{
						$wordwidth = $this->GetStringWidth(substr($word, $i, 1));
						if($width + $wordwidth <= $maxwidth)
						{
							$width += $wordwidth;
							$text .= substr($word, $i, 1);
						}
						else
						{
							$width = $wordwidth;
							$text = rtrim($text)."\n".substr($word, $i, 1);
							$count++;
						}
					}
				}
				elseif($width + $wordwidth <= $maxwidth)
				{
					$width += $wordwidth + $space;
					$text .= $word.' ';
				}
				else
				{
					$width = $wordwidth + $space;
					$text = rtrim($text)."\n".$word.' ';
					$count++;
				}
			}
			$text = rtrim($text)."\n";
			$count++;
		}
		$text = rtrim($text);
		return explode("\n",$text);
	}

	public function wordWrapOld($s,$width){
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


	public function transaction(){
		$this->_transaction = clone $this;
	}

	public function commit(){
		$this->_transaction=null;
	}

	public function rollback(){
		foreach($this->_transaction as $k=>$v){
			$this->{$k}=$v;
		}

		$this->_transaction=null;
	}


	public function sectionHeight($section){


		//$this->transaction();
		$pdf=clone $this;
		$pdf->setAutoPageBreak(false);

		$y1=$pdf->getY();
		$pdf->{$section}();
		$y2=$pdf->getY();

		$height=$y2-$y1;

		//$this->rollback();
		return $height;
	}




    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = ''){
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

        $xc = $x+$w-$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
        if (strpos($corners, '2')===false)
            $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
        else
            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

        $xc = $x+$w-$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        if (strpos($corners, '3')===false)
            $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x+$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        if (strpos($corners, '4')===false)
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        if (strpos($corners, '1')===false)
        {
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
            $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
        }
        else
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3){
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }

	/*
	public function cellRound($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='',$r=0,$corners='',$style='D'){
		$x=$this->getX();
		$y=$this->getY();
		$this->RoundedRect($x, $y,$w,$h, $r, $corners,$style);
		$this->cell($w,$h,$txt,0,$ln,$align,$fill,$link);

	}
	*/

	function SetDash($black=null, $white=null){
        if($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }

    function DashedRect($x1, $y1, $x2, $y2, $width=1, $nb=15){
        $this->SetLineWidth($width);
        $longueur=abs($x1-$x2);
        $hauteur=abs($y1-$y2);
        if($longueur>$hauteur) {
            $Pointilles=($longueur/$nb)/2; // length of dashes
        }
        else {
            $Pointilles=($hauteur/$nb)/2;
        }
        for($i=$x1;$i<=$x2;$i+=$Pointilles+$Pointilles) {
            for($j=$i;$j<=($i+$Pointilles);$j++) {
                if($j<=($x2-1)) {
                    $this->Line($j,$y1,$j+1,$y1); // upper dashes
                    $this->Line($j,$y2,$j+1,$y2); // lower dashes
                }
            }
        }
        for($i=$y1;$i<=$y2;$i+=$Pointilles+$Pointilles) {
            for($j=$i;$j<=($i+$Pointilles);$j++) {
                if($j<=($y2-1)) {
                    $this->Line($x1,$j,$x1,$j+1); // left dashes
                    $this->Line($x2,$j,$x2,$j+1); // right dashes
                }
            }
        }
    }


}


class FPDF extends FPDF_Encoding {

	public $leftmargin=10;
	public $topmargin=10;
	public $rightmargin=10;
	public $bottommargin=10;

	public $pageWidth=215;
	public $pageHeight=279;

	public $reportWidth=195;
	public $detailHeight=190;

	public function config($p){
		$p=(object)$p;

		$this->setEncoding("UTF-8");
		$this->AliasNbPages();


		$fields=["topmargin","rightmargin","bottommargin","leftmargin"];
		foreach($fields as $field){
			//$this->{$field} = $p->{$field};
		}
	}


	public function setReportFooterOffset(){

		$startY=$this->getY();
		$height = $this->sectionHeight('reportFooter');
		$offset=$this->pageHeight-$this->bottommargin-$height;

		//echo "$offset+$height > $this->pageHeight - $this->bottommargin ";exit;

		if( $startY+$height > $this->pageHeight - $this->bottommargin ){
			$this->addPage();
			return;
		}


		$this->setY($offset);
	}

	/*
	public function setReportFooterOffset(){
		$startY=$this->getY();
		$height = $this->sectionHeight('reportFooter');
		$offset=$this->pageHeight-$this->bottommargin-$height;

		if( $offset+$height > $this->pageHeight - $this->bottommargin ){
			$this->addPage();
			return;
		}


		$this->setY($offset);
	}
	*/


}
?>

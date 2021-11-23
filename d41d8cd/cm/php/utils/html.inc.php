<?php
namespace Cm;

class Html {

    public static function br($count=1){
        $html ='';
        for($i=0;$i<$count;$i++){
            $html.="<br/>";
        }
        return $html;
    }

    public static function attrString($attrs){
        $html="";
        foreach($attrs as $k=>$v){
            $html.="{$k}=\"{$v}\" ";
        }
        return $html;
    }


    public static function img($src,$alt="...",$op=array()){
        return "<img src=\"{$src}\" alt=\"{$alt}\" />";
    }

    public static function aMark($name){
        return "<a name=\"{$name}\"></a>";
    }

    public static function aHref($href,$label,$attr=array()){
        $attrStr = self::attrString($attr);
        return "<a href=\"{$href}\" {$attrStr} >{$label}</a>";
    }


    public static function meta($name,$content){
        return "<meta name=\"{$name}\" content=\"{$content}\" />\n";
    }
    public static function metaDescription($content){
        return self::meta("description",$content);
    }

    public static function metaKeyworkds($content){
        return self::meta("keywords",$content);
    }

    public static function metaTitle($content){
        return self::meta("title",$content);
    }

    public static function title($title){
        return "<title>{$title}</title>\n";
    }

    public static function divClearing(){
        echo "<div style=\"clear:both;\"></div>";
    }


    public static function embedCssFile($scriptPath){
        $code = file_get_contents($scriptPath);
        return "<style type=\"text/css\">{$code}</style>";
    }
	
	public static function importCss($url){
		return '<link type="text/css" href="'.$url.'" rel="stylesheet" />'."\n";
	}
	
	
    public static function scriptStart(){
        return '<script type="text/javascript">';    
    }
    public static function scriptEnd(){
        return '</script>';
    }
	
    public static function embedJavaScriptSource($code){
        return self::scriptStart()."\n".$code."\n".self::scriptEnd();
    }
	
    public static function embedJavaScriptFile($scriptPath){
        $code = file_get_contents($scriptPath);
        return self::scriptStart()."\n".$code."\n".self::scriptEnd();
    }
	
	public static function importJavascript($url){
		return '<script type="text/javascript" src="'.$url.'"></script>';
	}
    

}

?>

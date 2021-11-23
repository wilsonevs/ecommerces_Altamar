<?php
namespace Cm;
use stdClass;

require_once __DIR__."/../vendor/autoload.php";
require __DIR__."/../vendor/scssphp-0.6.6/scss.inc.php";


class MailInliner {

	public static function inline($source,$d,stdClass $extra){

		$path = dirname($source);
		$extra->root = coalesce_empty($extra->root);

		//twig
		$loader = new \Twig_Loader_Filesystem($path);
		$twig = new \Twig_Environment($loader, array());
		$twig->getExtension('core')->setTimezone("America/Bogota");

		//scss

		$base = basename($source);
		$base = str_replace(".php","",$base);

		$php_file = $source;
		$html_file = basename( str_replace(".php",".html",$source) );
		$scss_file = basename( str_replace(".php",".scss",$source) );


		$filter=new \Twig_SimpleFilter('image', function($image){
			return "{$imagen}";
		});
		$twig->addFilter($filter);

		$function = new \Twig_SimpleFunction('scss_compile', function ($filename) use ($extra,$path) {
			$scss = new \Leafo\ScssPhp\Compiler();
			$scss->setImportPaths($path);
			$css = $scss->compile('@import "'.$filename.'";');
			//$css=str_replace("rs/","{$extra->root}/rs/",$css);
			return $css;

		},["is_safe"=>["html"]]);
		$twig->addFunction($function);

		$function = new \Twig_SimpleFunction('get_template_name', function () use ($base) {
			return $base;
		});
		$twig->addFunction($function);

		$scss = new \Leafo\ScssPhp\Compiler();
		$scss->setImportPaths($path);
		$css = $scss->compile('@import "'.$scss_file.'";');
		$css=str_replace("rs/","{$extra->root}/rs/",$css);

		//html
		$template = $twig->loadTemplate($html_file);
		$html= $template->render( (array) $d);
		$html=str_replace("rs/","{$extra->root}/rs/",$html);


		$emogrifier = new \Pelago\Emogrifier();
		$emogrifier->disableStyleBlocksParsing();
		$emogrifier->setCss($css);
		$emogrifier->setHtml($html);

		$html = $emogrifier->emogrify();

		if( isset($_GET["preview"]) && $_GET["preview"]==1 ){
			echo $html;
			exit;
		}

		return $html;
	}

}

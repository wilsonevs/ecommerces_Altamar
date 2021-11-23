<?php
namespace Cm;

class TwigView extends \Slim\View {

    public function appendData($data)
    {
        if (!is_array($data) && !is_object($data) ) {
            throw new \InvalidArgumentException('Cannot append view data. Expected array argument.');
        }
        $this->data->replace($data);
    }

    public function render($template,$data=null) {
		$app=\Slim\Slim::getInstance();

		$data=$this->getData();

		$data["GET"]=$_GET;
		$data['site_url']=$app->config("site.url");
		$data['template_url']=$app->config("template.url");

		$data['site.url']=$app->config("site.url");
		$data['template.url']=$app->config("template.url");

		$templatePath=$app->config('template.path');
		$loader = new \Twig_Loader_Filesystem($templatePath);
		$twig = new \Twig_Environment($loader, array(
			'debug' => true,
			'cache' => false
		));

		$twig->addExtension(new \Twig_Extension_Debug());
		$twig->addExtension(new \Twig_Extensions_Extension_Text());
		//$twig->addExtension(new Twig_Extension_StringLoader());


		$function = new \Twig_SimpleFunction('in_array', function ($needle,$haystack) {
			return in_array($needle,$haystack);
		});
		$twig->addFunction($function);


		$filterImage=new \Twig_SimpleFilter('image', function($rsId,$p=null){
			$p=!empty($p) ? $p:(object)[];

			$tmp=is_array($rsId) ? $rsId[0]:$rsId;

			global $cfg;
			return "{$cfg["appRoot"]}/imagenes/imagen.php?imagenid={$rsId}";

		});
		$twig->addFilter($filterImage);


		$filterCmFileDownload=new \Twig_SimpleFilter('cmFileDownload', function($rsId,$p=null){
			$p=!empty($p) ? $p:(object)[];

			$tmp=is_array($rsId) ? $rsId[0]:$rsId;

			global $cfg;
			return "{$cfg["appRoot"]}/imagenes/imagen.php?imagenid={$rsId}&disposition=attachment";
		});
		$twig->addFilter($filterCmFileDownload);

		$filterCmFileInline=new \Twig_SimpleFilter('cmFileInline', function($rsId,$p=null){
			$p=!empty($p) ? $p:(object)[];

			$tmp=is_array($rsId) ? $rsId[0]:$rsId;

			global $cfg;
			return "{$cfg["appRoot"]}/imagenes/imagen.php?imagenid={$rsId}&disposition=inline";
		});
		$twig->addFilter($filterCmFileInline);



		$twig->addFilter(new \Twig_SimpleFilter('normalizeUrl',function($url){

			return \Cm\WebTools::normalizeUrl($url);
		}));

		$twig->addFunction(new \Twig_SimpleFunction('getTemplateName', function () use ($template) {
			$cssName = str_replace(".php","",$template);
			return $cssName;
		}));


		echo $twig->render($template,$data);
		exit;


    }
}

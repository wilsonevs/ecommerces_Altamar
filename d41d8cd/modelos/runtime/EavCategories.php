<?php
namespace Models\Runtime;
use Application;
use stdClass;
use Cm;

class EavCategories {

	public static function categoryById($categoryId){
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="select * from eav_categories where category_id=:category_id";
		$ca->prepare($sql);
		$ca->bindValue(":category_id",$categoryId);
		$ca->exec();

		if( $ca->size()==0 ){
			throw new Cm\PublicException("Category not found, category_id = {$categoryId}");
		}

		return $ca->fetch();
	}

	public static function categoryByName($categoryName){
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="select * from eav_categories where category_name=:category_name";
		$ca->prepare($sql);
		$ca->bindValue(":category_id",$categoryName);
		$ca->exec();

		if( $ca->size()==0 ){
			throw new Cm\PublicException("Category not found, category_name = {$categoryName}");
		}

		return $ca->fetch();
	}


	public static function categoryBySlug($categorySlug){
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$sql="select * from eav_categories where slug=:slug";
		$ca->prepare($sql);
		$ca->bindValue(":slug",$categorySlug);
		$ca->exec();

		if( $ca->size()==0 ){
			throw new Cm\PublicException("Category not found, slug = {$categorySlug}");
		}

		return $ca->fetch();
	}


	public static function menu($rootId,stdClass $p=null, $classli='', $classa='',$urlestatico=false,$icono=''){
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		$p=coalesce_object($p);

		$p->exclude = coalesce_blank($p->exclude)?:-1;
		//$p->root_element = coalesce_false($p->root_element);
		$p->root_element = isset($p->root_element) ? $p->root_element : true;
		$p->prefix = coalesce_blank($p->prefix);
		$p->li_class = coalesce_blank($p->li_class);


		$sql="
		select
			category_name,
			category_path
		from eav_categories
		where
			category_id=:category_id";
		$ca->prepare($sql);
		$ca->bindValue(":category_id",$rootId);
		$ca->exec();
		$root=$ca->fetch();


		$sql="
		select
			category_id,
			parent_id,
			category_name,
			slug,
			type_id,
			replace(category_path,'{$root->category_path}','') as route,
			case when type_id = -2 then
				url
			else
				replace(category_path,'{$root->category_path}','')
			end as url,
			target,
			events

		from eav_categories
		where
			category_path like '{$root->category_path}/%'
			and category_id not in ( {$p->exclude} )
		order by
			parent_id,
			category_order,
			category_name
		";
		$ca->prepare($sql);
		$ca->exec();
		$res->items=$ca->fetchAll();

		foreach($res->items as $k=>$r){
			$res->items[$k]->route = Cm\WebTools::normalizeUrl($r->route);

			//solo normalizo si no es enlace
			if( $r->type_id != -2 ){
				$res->items[$k]->url = Cm\WebTools::normalizeUrl($r->url);
			}

		}

		$menu=[];

		$buildTree=function($elements, $parentId = -1) use (&$buildTree){
			$branch = array();

			foreach ($elements as $k=>$element) {
				//fix php object reference
				$element = (object) (array) $element;

				if ($element->parent_id == $parentId) {
					$children = $buildTree($elements, $element->category_id);
					if ($children) {
						$element->children = $children;
					}
					$branch[$element->category_id] = $element;
				}
			}

			return $branch;

		};

		$res->tree=$buildTree($res->items,$rootId);
		$res->html=static::htmlMenu($rootId,$res->tree,$p,true,$classli,$classa,$urlestatico,$icono);
		return $res;
	}

	public static function htmlMenu($rootId,$menu=null,$opt=[],$firstLevel=true,$classli='',$classa='',$urlestatico=false,$icono=''){
		global $cfg;
		if( $menu===null ){
			$menu=self::menu($rootId);
		}

		$opt=(object)$opt;

		//$opt->exclude = isset($opt->exclude) ? $opt->exclude:[];
		//$opt->exclude = is_array($opt->exclude) ? $opt->exclude:[ $opt->exclude ];

		$html='';

		$dropdown=$firstLevel ? "":"menu submenu vertical";


		if( ($firstLevel && $opt->root_element) || !$firstLevel ){
			$html.='<ul class="'.$dropdown.'">';
		}

		foreach($menu as $r){


			$liClass=$classli;
			// if( $firstLevel ){
			// 	$liClass=isset($opt->{"li_1_class"}) ? $opt->{"li_1_class"}:"";
			// }

			$events=json_decode($r->events);
			$events=coalesce_object($events);
			$eventAttrs=[];

			foreach($events as $k=>$v){
				$eventAttrs[]="{$k}=\"{$v}\"";
			}
			$eventAttrs = implode(" ",$eventAttrs);

			//if( in_array($r->category_id,$opt->exclude) ) continue;

			$url = !empty($r->url) ? "{$cfg["siteRoot"]}{$r->url}":"javascript:;";
			$url = $r->type_id!=-1 ? $url : "javascript:;";

			if( $r->type_id > 0 ){
				$url = "{$cfg["siteRoot"]}{$opt->prefix}{$r->url}";
			}

			if($urlestatico){
				$url = "{$r->url}";
			}

			$hasDropdown=!empty($r->children) ? "has-dropdown":"";

			$html.='<li class="'.$liClass.' '.$r->slug.'" url="'.$r->slug.'">';
			$html.=$icono;
			$html.='<a class="'.$classa.'" href="'.$url.'" target="'.$r->target.'" url="'.$r->slug.'" '.$eventAttrs.'>'.$r->category_name.'</a>';

			if( !empty($r->children) ){
				//$html.='<ul>';
				$html.=static::htmlMenu($r->category_id,$r->children,$opt,false);
				//$html.='</ul>';
			}


			$html.='</li>';
		}


		if( ($firstLevel && $opt->root_element) || !$firstLevel ){
			$html.='</ul>';
		}


		return $html;
	}

}

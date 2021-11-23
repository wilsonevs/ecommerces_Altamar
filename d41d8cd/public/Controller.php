<?php
class Controller {

	public static function get(){
		return new static();
	}

	public static function data(){
		global $app;
		global $menu;

		$res=new stdClass();
		$res->root_uri=$app->request->getRootUri();
		$res->resource_uri=$app->request->getResourceUri();
		$res->current_uri = "{$res->root_uri}{$res->resource_uri}";
		$res->menu=$menu;

		return $res;
	}


	protected function baseData(){
		return (object)[];
	}

	public function render($tempalte,$data=null){
		global $app;
		$tmp=static::data();
		$tmp=object_merge($tmp,$this->baseData());
		$data=object_merge($tmp,$data);

		$data->_GET = $_GET;
		$app->render($tempalte,$data);
		exit;
	}

	public static function getParams(){
		global $app;
		return $app->request->get();
	}


	public static function pager(&$page,$params=[]){
		$prevPage=$page->currentPage>1 ? $page->currentPage-1:1;
		$nextPage=$page->currentPage < $page->pageCount ? $page->currentPage+1 : $page->pageCount;


		$html=[];

		$html[]='<ul class="pagination">';

		$vars = http_build_query( array_merge($params,["page"=>$prevPage]) );
		$html[]='<li class="arrow unavailable"><a href="?'.$vars.'">&laquo;</a></li>';

		//$params["page"] = $page->currentPage;
		//$params=http_build_query($params);

		$current='';
		for($i=1;$i<=$page->pageCount;$i++){
			$current=$page->currentPage==$i ? 'current':'';
			$vars = http_build_query( array_merge($params,["page"=>$i]) );

			$html[]='<li class="'.$current.'"><a href="?'.$vars.'">'.$i.'</a></li>';
		}

		$vars = http_build_query( array_merge($params,["page"=>$nextPage]) );
		$html[]='<li class="arrow"><a href="?'.$vars.'">&raquo;</a></li>';
		$html[]='</ul>';
		return implode("",$html);
	}
}

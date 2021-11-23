<?php

class Tools {

	public static function autoRoute(&$app,&$data){
		global $app;
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		foreach($data->menu->menu_list as $entry){


			$app->get($entry->url,function() use($app,$data,$entry) {
				$req = $app->request;
				$entry->root_uri= $req->getRootUri();
				$entry->resource_uri=$req->getResourceUri();

				SiteController::router( (object) $entry );

				//$tmp =SiteController::{$r->controller}((object)$entry);
			});
		}

		$app->get(".*",function() use($app){
			$req = $app->request;
			$rootUri = $req->getRootUri();
			$resourceUri = $req->getResourceUri();

			$p=(object)[
				"category_id"=>-1,
				"type_id"=>-1,
				"root_uri"=>$rootUri,
				"resource_uri"=>$resourceUri
			];
			SiteController::router($p);
		});
	}

	public static function xxx(){

	}


}

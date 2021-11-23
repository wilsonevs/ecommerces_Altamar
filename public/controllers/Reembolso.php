<?php
require_once __DIR__.'/Base.php';

use Models\Runtime\EavModel;
use Models\Runtime\EavCategories;

class Reembolso extends Base {

	public function index(stdClass $p){
		$res=new stdClass();

		$this->render("reembolso.php",$res);
	}




}

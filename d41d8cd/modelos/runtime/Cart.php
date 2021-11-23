<?php

class Cart {

	public static function header($cartId){
	
	}
	
	public static function detail($cartId){
	
	}
	
	
	public static function addItem($cartId,stdClass $p){
		$si=Application::session();
		
		$p->cart_id=1;
		$detailId=Application::nextval("cart_h_detail_id");
		
		$subtotal=round($p->price * $p->qty,2);
		
		$ca->prepareTable("cart_d");
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":cart_id",$p->cart_id);
		$ca->bindValue(":detail_id",$detailId);
		$ca->bindValue(":item_id",$p->item_id);
		$ca->bindValue(":item_name",$p->item_name);
		$ca->bindValue(":item_price",$p->price);
		$ca->bindValue(":item_tax",$p->per_tax);
		$ca->bindValue(":qty",$p->qty);
		$ca->bindValue(":subtotal",$subtotal);
		$ca->execInsert();
		
	}

}

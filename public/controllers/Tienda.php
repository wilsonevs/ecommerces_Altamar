<?php
require_once __DIR__."/Base.php";
require_once __DIR__."/../modelos/Region.php";
require_once __DIR__."/../modelos/Carro.php";
require_once __DIR__."/../modelos/Tienda.php";

class Tienda extends Base {

	public function carro(stdClass $p){
		global $db;
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();

		$c=CarroMdl::get();
		$res->enc = $c->enc();
		$res->det = $c->det();

		$db->transaction();

		//actualizo el id_cliente si es diferente
		if( isset($this->si->usuario) && $this->pedido_enc->id_cliente != $this->si->usuario->item_id ){
			$this->pedido->setCliente( $this->si->usuario->item_id );
		}

		// $this->pedido->totalizar();
		$db->commit();

		// $res->tarjetas_regalo = $this->pedido->tarjetasRegalo();

		$res->paises = RegionMdl::paises();
		$res->departamentos = RegionMdl::departamentos( (object)[
			"country_id"=>$res->enc->ent_id_pais
		]);

		$res->ciudades = RegionMdl::ciudades( (object)[
			"country_id"=>$res->enc->ent_id_pais,
			"state_id"=>$res->enc->ent_id_departamento
		]);


		foreach($res->det as $r){

			$tmp = Models\Runtime\EavModel::item( (object)[
				"filter"=>"items.item_id = :item_id",
				"params"=>[
					":item_id"=>$r->item_id
				]
			]);

			$r->attrs = $tmp->attrs;
			$r->slug = $tmp->slug;

			// $r->unidades_disponibles = TiendaMdl::unidadesDisponibles( (object)[
			// 	'item_id'=>$r->item_id,
			// 	'variacion'=>$r->variacion
			// ]);

			// $r->unidades_disponibles -= $r->unidades;

		}


		// for ($i=0; $i < count($res->det); $i++) {
		//
		// 	/*TALLA*/
		// 	$aux = explode(":",trim($res->det[$i]->variacion));
		// 	$res->det[$i]->talla = coalesce_blank($aux[0]);
		//
		// 	/*IMAGEN*/
		// 	$aux2 = explode("_",trim($res->det[$i]->nom_inventario));
		// 	$res->det[$i]->imagen = coalesce_blank($aux2[1]);
		//
		// }
		
		$this->render("carro.php",$res);
	}

	public function pedido(stdClass $p){
		global $cfg;
		$db=Cm\Database::database();

		if(!isset($this->si->usuario) ){
			header("Location: {$cfg["siteRoot"]}/account?e26ee7b57a3d7f953de0818fae0b795=0");
			exit;
		}

		$db->transaction();
		$this->pedido->totalizar();
		$db->commit();

		$res=new stdClass();
		$c=CarroMdl::get();
		$res->pedido_enc = $c->enc();
		$res->det = $c->det();

		foreach($res->det as $r){

			$tmp = Models\Runtime\EavModel::item( (object)[
				"filter"=>"items.item_id = :item_id",
				"params"=>[
					":item_id"=>$r->item_id
				]
			]);

			$r->attrs = $tmp->attrs;
			$r->slug = $tmp->slug;

		}


		// $res->tipos_identificacion = CuentaMdl::tiposIdentificacion();

		$res->paises = RegionMdl::paises();
		$res->departamentos = RegionMdl::departamentos( (object)[
			"country_id"=>$this->si->usuario->attrs->pais->data[0]
		]);

		$res->ciudades = RegionMdl::ciudades( (object)[
			"country_id"=>$this->si->usuario->attrs->pais->data[0],
			"state_id"=>$this->si->usuario->attrs->departamento->data[0],
		]);


		$res->formas_pago = TiendaMdl::formaspago( (object)[
			"moneda"=>$this->pedido_enc->moneda
		]);

		//INFORMACION DE ENVIO
		$filter=[];
		$filter[]="items.category_id = 90";

		$res->envio = Models\Runtime\EavModel::item( (object)[
			"filter"=>$filter
		]);

		//print_r($res->pedido_enc);
		//exit;
		$this->render("pago.php",$res);
	}

	public function pedidoFinalizado(stdClass $p){
		$res=new stdClass();
		global $cfg;

		$id_cart = filter_input(INPUT_GET,'cart',FILTER_SANITIZE_NUMBER_INT);
		$ped = CarroMdl::get($id_cart);
		$enc = $ped->enc();
		$res->forma_pago = TiendaMdl::formaPago($enc->id_forma_pago);
		$res->web = $cfg['appHost'];
		$res->enc = $enc;


		$this->render("pedido_finalizado.php",$res);
	}

	// public function pagoPayU(stdClass $p){
	// 	global $cfg;
	// 	$res=new stdClass();

	// 	$id_pedido = filter_input(INPUT_GET,'numero_pedido',FILTER_SANITIZE_NUMBER_INT);
	// 	$ped = CarroMdl::get($id_pedido);
	// 	$this->pedido_enc = $ped->enc();

	// 	$res->forma_pago = TiendaMdl::formaPago($this->pedido_enc->id_forma_pago);
	// 	$res->test = 0;

	// 	//VARIABLES EN PRODUCCIÓN
	// 	$apiKey="";//api key de tienda
	// 	$res->accountId=0; //id de la cuenta tienda
	// 	$res->merchantId=0; //merchant id de tienda

	// 	$res->url="https://checkout.payulatam.com/ppp-web-gateway-payu/";

	// 	$res->description="Compra realizada en {$cfg["appHost"]}";
	// 	$res->referenceCode=$this->pedido_enc->id_pedido;
	// 	$res->amount=$this->pedido_enc->total;
	// 	$res->tax=$this->pedido_enc->total_iva;
	// 	$taxReturnBase = $this->pedido_enc->total_iva == 0 ? 0 : $this->pedido_enc->total - $this->pedido_enc->total_iva;
	// 	$res->taxReturnBase= $taxReturnBase;
	// 	$res->currency=strtoupper($this->pedido_enc->moneda);
	// 	$res->buyerFullName=$this->si->usuario->attrs->nombres->data[0].' '.$this->si->usuario->attrs->nombres->data[0];
	// 	$res->buyerEmail=$this->si->usuario->attrs->correo_electronico->data[0];
	// 	$res->responseUrl="http://{$cfg["appHost"]}{$cfg["appRoot"]}/respuesta-pago";
	// 	$res->confirmationUrl="http://{$cfg["appHost"]}{$cfg["appRoot"]}/payuConfirm/confirmacion_pago.php";
	// 	$res->text=0;


	// 	//VARIABLES EN PRUEBA SACADO DE LA URL http://developers.payulatam.com/es/web_checkout/sandbox.html
	// 	if ($cfg["sandbox"]) {
	// 		$apiKey='4Vj8eK4rloUd272L48hsrarnUA';
	// 		$res->merchantId=508029;
	// 		$res->accountId=512321; //cuenta de prueba colombiana
	// 		$res->description="Compra realizada en {$cfg["appHost"]} de prueba";
	// 		$res->tax=0;
	// 		$res->taxReturnBase=0;
	// 		$res->buyerEmail='dontcry-702@hotmail.com';
	// 		$res->text=1;
	// 		$res->confirmationUrl="https://www.{$cfg["appHost"]}/tiendaconvariacion/payuConfirm/confirmacion_pago.php";
	// 		$res->url="https://sandbox.checkout.payulatam.com/ppp-web-gateway-payu";

	// 	}

	// 	$res->signature = "{$apiKey}~{$res->merchantId}~{$res->referenceCode}~{$res->amount}~{$res->currency}";
	// 	$res->signature = md5($res->signature);

	// 	//“ApiKey~merchantId~referenceCode~amount~currency”.
	// 	//"6u39nqhq8ftd0hlvnjfs66eh8c~500238~TestPayU~3~USD"


	// 	$this->render("pedido_finalizado_payu.php",$res);
	// }


	// public function respuestaPayu(stdClass $p){
	// 	$res = new stdClass();
		
	// 	RESPUESTA


	// 			Array
	// 	(
	// 	    [merchantId] => 508029
	// 	    [merchant_name] => Test PayU Test comercio
	// 	    [merchant_address] => Av 123 Calle 12
	// 	    [telephone] => 7512354
	// 	    [merchant_url] => http://pruebaslapv.xtrweb.com
	// 	    [transactionState] => 4
	// 	    [lapTransactionState] => APPROVED
	// 	    [message] => APPROVED
	// 	    [referenceCode] => TestPayU
	// 	    [reference_pol] => 845483499
	// 	    [transactionId] => 1a0be298-d676-4537-9a4d-66ab5456180a
	// 	    [description] => Compra realizada en localhost de prueba
	// 	    [trazabilityCode] => 00000000
	// 	    [cus] => 00000000
	// 	    [orderLanguage] => es
	// 	    [extra1] =>
	// 	    [extra2] =>
	// 	    [extra3] =>
	// 	    [polTransactionState] => 4
	// 	    [signature] => 591a9f50641c9f9ac15fb6bfeb775636
	// 	    [polResponseCode] => 1
	// 	    [lapResponseCode] => APPROVED
	// 	    [risk] =>
	// 	    [polPaymentMethod] => 10
	// 	    [lapPaymentMethod] => VISA
	// 	    [polPaymentMethodType] => 2
	// 	    [lapPaymentMethodType] => CREDIT_CARD
	// 	    [installmentsNumber] => 1
	// 	    [TX_VALUE] => 2249900.00
	// 	    [TX_TAX] => .00
	// 	    [currency] => COP
	// 	    [lng] => es
	// 	    [pseCycle] =>
	// 	    [buyerEmail] => dontcry-702@hotmail.com
	// 	    [pseBank] =>
	// 	    [pseReference1] =>
	// 	    [pseReference2] =>
	// 	    [pseReference3] =>
	// 	    [authorizationCode] => 00000000
	// 	    [processingDate] => 2019-04-01
	// 	)

	// 	if($_REQUEST['transactionState'] == 4){
	// 		$res->estado ="Transaccion Aprobada";
	// 		$res->img = 1;
	// 	}
	// 	else if($_REQUEST['transactionState'] == 5){
	// 		$res->estado ="Transaccion Cancelada";
	// 		$res->img = 2;
	// 	}
	// 	else if($_REQUEST['transactionState'] == 6){
	// 		$res->estado ="Transaccion Rechazada";
	// 		$res->img = 3;
	// 	}
	// 	else if($_REQUEST['transactionState'] == 7){
	// 		$res->estado ="Transaccion Pendiente";
	// 		$res->img = 4;
	// 	}
	// 	// $res->extra1 = $_REQUEST['extra1'];
	// 	$res->referenced_code = $_REQUEST['referenceCode'];


	// 	$this->render("respuesta_payu.php", $res);
	// }



	// public function pagoPaypal(stdClass $p){
	// 	global $cfg;
	// 	$res=new stdClass();

	// 	$id_pedido = filter_input(INPUT_GET,'numero_pedido',FILTER_SANITIZE_NUMBER_INT);
	// 	$ped = CarroMdl::get($id_pedido);
	// 	$this->pedido_enc = $ped->enc();


	// 	$res->url = "https://www.paypal.com/cgi-bin/webscr";
	// 	$res->correo_paypal = ""; //Correo cuenta paypal
	// 	$res->forma_pago = TiendaMdl::formaPago($this->pedido_enc->id_forma_pago);
	// 	$res->currency = 'USD';//strtoupper($this->pedido_enc->moneda);
	// 	$res->shopping_url = ""; //Url de la tienda
	// 	$res->responseUrl = "http://{$cfg["appHost"]}{$cfg["appRoot"]}/respuesta-pago-paypal";
	// 	$res->confirmationUrl = "http://{$cfg["appHost"]}{$cfg["appRoot"]}/paypalConfirm/confirmacion_pago.php";
	// 	$res->description = "Compra realizada en {$cfg["appHost"]}";
	// 	$res->referenceCode = $this->pedido_enc->id_pedido;
	// 	$res->amount = $this->pedido_enc->total_usd;

	// 	//VARIABLES EN PRUEBA
	// 	if ($cfg["sandbox"]) {
	// 		$res->correo_paypal = "sb-h4747oh1895468@business.example.com";
	// 		$res->url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	// 	}

	// 	$this->render("pedido_finalizado_paypal.php",$res);
	// }


	// public function respuestaPaypal(stdClass $p){
	// 	$res = new stdClass();


	// 	if($_REQUEST['status'] == 'Completed'){
	// 		$res->estado ="Transaccion Aprobada";
	// 		$res->img = 1;
	// 	}
	// 	else if($_REQUEST['status'] == 'Canceled'){
	// 		$res->estado ="Transaccion Cancelada";
	// 		$res->img = 2;
	// 	}
	// 	else if($_REQUEST['status'] == 'Revelsarerror' || $_REQUEST['status'] == 'ERROR' || $_REQUEST['status'] == 'Incomplete'){
	// 		$res->estado ="Transaccion Rechazada";
	// 		$res->img = 3;
	// 	}
	// 	else if($_REQUEST['status'] == 'Create' || $_REQUEST['status'] == 'Pending' || $_REQUEST['status'] == 'Proccesing'){
	// 		$res->estado = "Transaccion Pendiente";
	// 		$res->img = 4;
	// 	}

	// 	$res->referenced_code = $_REQUEST['referenceCode'];

	

	// 	$this->render("respuesta_paypal.php", $res);
	// }

	// function consignacion(stdClass $p){
	// 	global $cfg;
	// 	$res=new stdClass();
	//
	// 	$id_pedido = $_GET["numero_pedido"];
	// 	$ped = CarroMdl::get($id_pedido);
	//
	// 	//sobre escribo el pedido por el recibido en la url
	// 	$this->pedido_enc = $ped->enc();
	// 	$res->forma_pago = TiendaMdl::formaPago($this->pedido_enc->id_forma_pago);
	//
	// 	$res->business='thenewangelweb@gmail.com';
	// 	$res->item_name = "Tu compra en {$cfg["appHost"]}";
	// 	$res->invoice = $this->pedido_enc->id_pedido;
	// 	$res->currency_code=strtoupper($this->pedido_enc->moneda);
	// 	$res->amount=$this->pedido_enc->total;
	//
	// 	//$res->notify_url='https://host/payments/paypal/paypal_ipn.php';
	// 	$res->cancel_return='https://host';
	// 	$res->return='https://host';
	//
	// 	$this->render("pago_paypal.php",$res);
	// }

}

<?php
require_once __DIR__."/Base.data.php";
require_once __DIR__."/../modelos/Region.php";
require_once __DIR__."/../modelos/Carro.php";
require_once __DIR__."/../modelos/Tienda.php";

use Models\Runtime\EavModel;
use Models\Runtime\EavCategories;


$exports[]="TiendaData";
class TiendaData extends BaseData {
	//Compra mínima
	public static $cat_configuracion = 64;

	public function departamentos(stdClass $p){
		return RegionMdl::departamentos($p);
	}

	public function ciudades(stdClass $p){
		return RegionMdl::ciudades($p);
	}

	public static function agregarItem(stdClass $p){
		global $db;

		/*
			stdClass Object (
				[item_id] => 274
				[color] => Rojo
				[talla] => M
				[variacion] => 40:9
				[nom_inventario] => inventario_color1
				[imagen] => color1_imagen1
			)
		 */

		// $p->unidades = isset($p->unidades) ? $p->unidades : 1;

		// $unidades = TiendaMdl::unidadesDisponibles($p);
		// if( $unidades < $p->unidades ){
		// 	throw new Cm\PublicException("Lo sentimos, no tenemos unidades disponibles del producto");
		// }

		$db->transaction();
		$c = CarroMdl::get();
		$c->agregarItem($p);
		$db->commit();

		return;
	}

	public static function removerItem(stdClass $p){
		global $db;

		$db->transaction();

		$c = CarroMdl::get();
		$c->removerItem($p);
		$db->commit();

		return;
	}

	public static function actualizarUnidades(stdClass $p){
		global $db;

		$c = CarroMdl::get();
		$det = $c->det();

		if ($p->unidades == 0 || $p->unidades < 0) {
			self::removerItem($p);
			return;
		}



		/* INVENTARIO */
		foreach($det as $r){
			$unidades = TiendaMdl::unidadesDisponibles($r);


			if( $unidades < $p->unidades ){
				if($_SESSION['idioma'] == 'espanol'){
					throw new Cm\PublicException("Lo sentimos, solo tenemos {$unidades} unidades de '{$r->descripcion}', Ajusta las unidades en tu carrito y vuelva a intentarlo.");
				} else {
					throw new Cm\PublicException("Sorry we just have {$unidades} units of '{$r->descripcion}', Please adjust the units in your cart and try again.");
				}
			}
		}


		$db->transaction();
		$c = CarroMdl::get();
		$c->actualizarUnidades($p);
		$db->commit();

		return;
	}

	public function setDestino(stdClass $p){
		global $db;

		$db->transaction();
		$c = CarroMdl::get();
		$c->setDestino($p);
		$db->commit();

		$tmp = new CarroMdl();
		$det = $tmp->enc();

		return $det;
	}

	public function verificarPedido(){
		global $db;
		$res=new stdClass();

		$c = CarroMdl::get();
		$enc=$c->enc();

		$field_compra_minima = "compra_minima_{$enc->moneda}";

		// if($enc->ent_id_ciudad === null){
		// 	throw new Cm\PublicException("Debe seleccionar una ciudad para la entrega.");
		// }

		$filter=[];
		$filter[]="items.category_id = :category_id";

		$tienda_cfg =  Models\Runtime\EavModel::item((object)[
			"filter"=>$filter,
			"params"=>[
				":category_id"=>static::$cat_configuracion
			]
		]);

		if((integer)$enc->total < (integer)$tienda_cfg->attrs->{$field_compra_minima}->data[0]){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("La compra mínima de la tienda es de $" . number_format($tienda_cfg->attrs->{$field_compra_minima}->data[0]));
			} else {
				throw new Cm\PublicException("The minimum purchase of the store is $" . number_format($tienda_cfg->attrs->{$field_compra_minima}->data[0]));
			}
		}

		return;
	}

	public function finalizarCompra(stdClass $p){
		global $db;
		$res=new stdClass();
		$c = CarroMdl::get();
		$enc = $c->enc();

		if( $enc->total_pendiente > 0 && !is_numeric($p->id_forma_pago) ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Debe seleccionar una forma de pago.");
			} else {
				throw new Cm\PublicException("You must select a payment method.");
			}
		}


		if(empty($p->com_nombres)){
			throw new Cm\PublicException("El nombre del comprador no puede estar vacio.");
		}
		if(empty($p->com_apellidos)){
			throw new Cm\PublicException("El apellido del comprador no puede estar vacio.");
		}
		if(empty($p->tipo_identificacion)){
			throw new Cm\PublicException("El tipo de identificación del comprador no puede estar vacio.");
		}
		if(empty($p->com_identificacion)){
			throw new Cm\PublicException("La identificación del comprador no puede estar vacia.");
		}
		if(empty($p->com_telefono_celular)){
			throw new Cm\PublicException("El teléfono celular del comprador no puede estar vacio.");
		}
		if(empty($p->com_id_pais)){
			throw new Cm\PublicException("Debes seleccionar el pais del comprador.");
		}
		if(empty($p->com_id_departamento)){
			throw new Cm\PublicException("Debes seleccionar el departamento del comprador.");
		}
		if(empty($p->com_id_ciudad)){
			throw new Cm\PublicException("Debes seleccionar la ciudad del comprador.");
		}
		if(empty($p->com_direccion)){
			throw new Cm\PublicException("La dirección del comprador no puede estar vacia.");
		}

		
		if(empty($p->ent_nombres)){
			throw new Cm\PublicException("El nombre de la persona a quien se le enviara el pedido no puede estar vacio.");
		}

		if(empty($p->ent_apellidos)){
			throw new Cm\PublicException("El apellido de la persona a quien se le enviara el pedido no puede estar vacio.");
		}

		if(empty($p->ent_identificacion)){
			throw new Cm\PublicException("La identificación de la persona a quien se le enviara el pedido no puede estar vacia.");
		}

		if(empty($p->ent_telefono_celular)){
			throw new Cm\PublicException("El teléfono celular de la persona a quien se le enviara el pedido no puede estar vacio.");
		}

		if(empty($p->ent_id_pais)){
			throw new Cm\PublicException("Debes seleccionar el pais de la persona a quien se le enviara el pedido.");
		}

		if(empty($p->ent_id_departamento)){
			throw new Cm\PublicException("Debes seleccionar el departamento de la persona a quien se le enviara el pedido.");
		}

		if(empty($p->ent_id_ciudad)){
			throw new Cm\PublicException("Debes seleccionar la ciudad de la persona a quien se le enviara el pedido.");
		}

		if(empty($p->ent_direccion)){
			throw new Cm\PublicException("La dirección de la persona a quien se le enviara el pedido no puede estar vacia.");
		}



		$p->com_ciudad = RegionMdl::paisDepartamentoCiudad($p->com_id_ciudad);
		$p->ent_ciudad =  RegionMdl::paisDepartamentoCiudad($p->ent_id_ciudad);
		$p->com_correo_electronico =  $this->si->usuario->attrs->correo_electronico->data[0];
		$p->id_cliente =  $this->si->usuario->item_id;


		$db->transaction();

			$c->setDatosCompra($p);
			$c->reservarInventario();
			$id_carro = $c->idCarro();
			$forma_pago = TiendaMdl::formaPago($c->enc()->id_forma_pago);

		$db->commit();

		switch($forma_pago->attrs->codigo->data[0]){
			case "consignacion":
				$res->url = "/pago-realizado?cart={$id_carro}";
			break;
			case "contraentrega":
				$res->url = "/pago-realizado?cart={$id_carro}";
			break;
			// case "payu":
			// 	$res->url = "/redireccion-banco?numero_pedido={$id_carro}";
			// break;
			// case "paypal":
			// 	$res->url = "/redireccion-paypal?numero_pedido={$id_carro}";
			// break;
		}
		return $res;
	}

	// public function agregarTarjetaRegalo(stdClass $p){
	// 	global $db;
	// 	$res=new stdClass();

	// 	if( empty($p->referencia) ){
	// 		throw new Cm\PublicException("Referencia Invalida");
	// 	}

	// 	if( empty($p->secreto) ){
	// 		throw new Cm\PublicException("Secreto Invalido");
	// 	}

	// 	$db->transaction();
	// 	$this->pedido->agregarTarjetaRegalo($p);
	// 	$db->commit();

	// 	return $res;
	// }

	// public function removerTarjetaRegalo(stdClass $p){
	// 	global $db;
	// 	$res=new stdClass();

	// 	if( empty($p->referencia) ){
	// 		throw new Cm\PublicException("Referencia Invalida");
	// 	}

	// 	$db->transaction();
	// 	$this->pedido->removerTarjetaRegalo($p);
	// 	$db->commit();

	// 	return $res;
	// }

	public static function tallas(stdClass $p){
		$res=[];

		$filter=[];
		$filter[]="items.category_id=35";
		$filter[]="and";
		$filter[]="items.slug = :slug";

		
		$tmp = EavModel::item([
			"filter"=>$filter,
			"params"=>[
				":slug"=>$p->slug
			]
		]);

		if ($_SESSION['idioma'] == 'espanol') {
			$tmp = explode("\n",trim($tmp->attrs->inventario->data[0]));
		} else{
			$tmp = explode("\n",trim($tmp->attrs->inventario_ingles->data[0]));
		}


		foreach ($tmp as $aux) {
			if( empty($aux) ) continue;
			$aux = explode(":",trim($aux));
			$tmp2[] = (object)[
				"color"=>coalesce_blank($aux[1]),
				"unidades"=>coalesce_blank($aux[2]),
				"talla"=>coalesce_blank($aux[0])
			];
		}	


		$res = [];

		foreach ($tmp2 as $key => $value) {
			if ($value->color == $p->color) {
				array_push($res, ["data"=>coalesce_blank($value->talla),"label"=>strtoupper(coalesce_blank($value->talla))]);
			}
		}


		array_unshift($res,["data"=>"","label"=>"Seleccione"]);
		return $res;
	}
}


?>

<?php
require_once __DIR__.'/Transporte.php';
require_once __DIR__.'/Tienda.php';


class CarroMdl {

	private $id_carro = null;
	private $_enc = null;
	private $_det = null;
	private $moneda = 'cop';
	private $campo_precio = 'precio';
	private $campo_precio_usd = 'precio_usd';
	private $campo_impuesto = 'por_impuesto_cop';


	public function __construct($id=null,$id_pedido = null){
		global $db;
		$ca=new Cm\DbQuery($db);


		if(!empty($id_pedido) ){
			$sqlFilter = "id_pedido = :id_pedido";
		}
		elseif(!empty($id)){
			$sqlFilter = "id_carro = :id_carro";
		}
		else {
			$sqlFilter = "session_id=:session_id and estado=:estado";
		}

		$sql="
		select
			id_carro,
			id_pedido,
			session_id,
			moneda

		from pedidos_e
		where
			{$sqlFilter}
		";

		$ca->prepare($sql);

		if(empty($id) && empty($id_pedido)){
			$ca->bindValue(":session_id",session_id(),true);
			$ca->bindValue(":estado","carro");
		}
		else {
			$ca->bindValue(":id_carro",$id);
			$ca->bindValue(":id_pedido",$id_pedido);
		}

		$ca->exec();
		if( $ca->size() > 0 ){
			$tmp = $ca->fetch();
			$this->id_carro = $tmp->id_carro;
			$this->moneda = $tmp->moneda;
			$this->campo_precio = "precio";
			$this->campo_precio_usd = "precio_usd";
			$this->campo_impuesto = "por_impuesto_{$this->moneda}";
			return;
		}

		if(!empty($id)){
			if($_SESSION['idioma']=='espanol'){
				throw new Cm\PublicException("Id de pedido invalido");
			} else {
				throw new Cm\PublicException("Invalid order id");
			}

		}


		$id_carro = App::nextval("pedidos_e_id_carro");

		$ca->prepareTable("pedidos_e");
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":id_carro",$id_carro);
		$ca->bindValue(":id_pedido",$id_carro * -1); //id de carro negativo para garantizar unicidad
		$ca->bindValue(":moneda","cop");
		$ca->bindValue(":session_id",session_id(),true);
		$ca->bindValue(":direccion_ip",$_SERVER["REMOTE_ADDR"]);
		$ca->bindValue(":fechahora","current_timestamp",false);
		$ca->bindValue(":estado","carro");
		$ca->bindValue(":i_ts","current_timestamp",false);
		$ca->bindValue(":u_ts","current_timestamp",false);
		$ca->bindValue(":total",0);
		$ca->bindValue(":total_usd",0);
		$ca->execInsert();

		$this->id_carro = $id_carro;
		$this->moneda = "cop";
		$this->campo_precio = "precio";
		$this->campo_precio_usd = "precio_usd";
		$this->campo_impuesto = "por_impuesto_{$this->moneda}";

	}

	public static function get($id_carro = null){
		return new static($id_carro);
	}

	public function idCarro(){
		return $this->id_carro;
	}

	public function enc(){
		global $db;
		$ca=new Cm\DbQuery($db);

		$sql="
		select
			id_carro,
			id_pedido,
			estado,
			session_id,
			moneda,


			total_unidades,
			subtotal,
			total_iva,
			total_transporte,
			total,
			total_usd,
			total_pendiente,
			total_descuento,
			total_tr,

			id_forma_pago,

			id_cliente,
			com_nombres,
			com_apellidos,
			com_tipo_identificacion,
			com_identificacion,
			com_correo_electronico,
			com_direccion,
			com_telefono_fijo,
			com_telefono_celular,
			com_ciudad,

			ent_id_pais,
			ent_id_departamento,
			ent_id_ciudad,
			ent_nombres,
			ent_apellidos,
			ent_identificacion,
			ent_direccion,
			ent_telefono,
			ent_telefono_celular,
			ent_ciudad,

			nombre_forma_pago,

			i_ts,
			u_ts

		from pedidos_e
		where
			id_carro = :id_carro
		";

		$ca->prepare($sql);
		$ca->bindValue(":id_carro", $this->id_carro);
		$ca->exec();

		if($ca->size() == 0 ) return null;
		return $ca->fetch();
	}


	public function det(){
		global $db;
		$ca=new Cm\DbQuery($db);
		$sql="
			select *
			from
				pedidos_d
			where
				id_carro = :id_carro
			order by
				descripcion,
				referencia
		";
		$ca->prepare($sql);
		$ca->bindValue(":id_carro", $this->id_carro);
		$ca->exec();
		return $ca->fetchAll();
	}


	public function setCliente($id_cliente){
		global $db;
		$ca=new Cm\DbQuery($db);

		$ca->prepareTable("pedidos_e");
		$ca->bindValue(":id_cliente",$id_cliente);
		$ca->bindWhere(":id_carro",$this->id_carro);
		$ca->execUpdate();
	}

	public function setMoneda($moneda){
		global $db;
		$ca=new Cm\DbQuery($db);

		if( !in_array($moneda,["cop","usd"]) ){
			throw new Cm\PublicException("Moneda Invalida");
		}

		$this->moneda = $moneda;
		$this->campo_precio = "precio";
		$this->campo_precio_usd = "precio_usd";
		$this->campo_impuesto = "por_impuesto_{$moneda}";

		$ca->prepareTable("pedidos_e");
		$ca->bindValue(":moneda",$_GET["moneda"]);
		$ca->bindWhere(":plat_id",1);
		$ca->bindWhere(":id_carro",$this->id_carro);
		$ca->execUpdate();


		/*
		$ca->prepareTable("pedidos_d");
		$ca->bindWhere(":plat_id",1);
		$ca->bindWhere(":id_carro",$this->id_carro);
		$ca->execDelete();
		*/

		foreach($this->det() as $r){

			$filter=[];
			$filter[]="items.item_id=:item_id";
			$r = Models\Runtime\EavModel::item((object)[
				"filter"=>$filter,
				"params"=>[":item_id"=>$r->item_id]
			]);

			$ca->prepareTable("pedidos_d");
			$ca->bindValue(":precio",$r->attrs->{$this->campo_precio}->data[0]);
			$ca->bindValue(":precio_usd",$r->attrs->{$this->campo_precio_usd}->data[0]);
			$ca->bindValue(":por_impuesto",$r->attrs->{$this->campo_impuesto}->data[0]);

			$ca->bindWhere(":plat_id",1);
			$ca->bindWhere(":id_carro",$this->id_carro);
			$ca->bindWhere(":item_id",$r->item_id);
			$ca->execUpdate();
		}



		$this->totalizar();
	}

	public function agregarItem(stdClass $p){
		global $db;
		$ca=new Cm\DbQuery($db);

		//si tiene variacion
		// if (isset($p->variacion)) {
		// 	$p->variacion = coalesce_blank($p->variacion);
		// 	if (empty($p->variacion)) {
		// 		throw new Cm\PublicException('Debes seleccionar el tipo de celular.');
		// 	}
		// }
		//
		//si tiene color
		$p->color = coalesce_blank($p->color);


		if (empty($p->color)) {
			if ($_SESSION['idioma'] == 'espanol') {
				throw new Cm\PublicException('Debes seleccionar un color.');
			} else{
				throw new Cm\PublicException('You must select a color.');
			}
		}

		//si tiene talla
		$p->talla = coalesce_blank($p->talla);
		if (empty($p->talla)) {
			if ($_SESSION['idioma'] == 'espanol') {
				throw new Cm\PublicException('Debes seleccionar una talla.');
			} else{
				throw new Cm\PublicException('You must select a size.');
			}
		}

		$enc = $this->enc();

		$filter=[];
		$filter[]="items.item_id=:item_id";
		$r = Models\Runtime\EavModel::item((object)[
			"filter"=>$filter,
			"params"=>[":item_id"=>$p->item_id]
		]);

		$sql="
			select
				a.*
			from pedidos_d a
			where
				a.item_id = :item_id
				and a.id_carro = :id_carro
				and a.color = :color
				and a.talla = :talla
		";

		$ca->prepare($sql);
		$ca->bindValue(":item_id", $p->item_id);
		$ca->bindValue(":id_carro", $enc->id_carro);
		$ca->bindValue(":color", $p->color);
		$ca->bindValue(":talla", $p->talla);
		// $ca->bindValue(":variacion",$p->variacion);
		$ca->exec();

		if($ca->size() > 0 ){
			if ($_SESSION['idioma'] == 'espanol') {
				throw new Cm\PublicException("Este producto ya se encuentra en tu carrito.");
			} else{
				throw new Cm\PublicException("This product is already in your cart.");
			}
		}
		 


		//$ca->fetch();

		$tipo = coalesce_blank($r->attrs->tipo->data[0]) ?:'producto';
		if( empty($tipo) ){
			if ($_SESSION['idioma'] == 'espanol') {
				throw new Cm\PublicException("Tipo de producto invalido.");
			} else{
				throw new Cm\PublicException("Invalid product type.");
			}
		}

		$ca->prepareTable("pedidos_d");
		$ca->bindValue(":plat_id",1);
		$ca->bindValue(":id_carro",$enc->id_carro);
		$ca->bindValue(":item_id",$p->item_id,false);
		$ca->bindValue(":tipo",$tipo);
		$ca->bindValue(":referencia",$r->attrs->referencia->data[0]);
		$ca->bindValue(":descripcion",$r->attrs->titulo->data[0]);
		$ca->bindValue(":variacion","{$p->talla}:{$p->color}:1");
		$ca->bindValue(":color",$p->color);
		$ca->bindValue(":talla",$p->talla);
		$ca->bindValue(":unidades",1);
		// $ca->bindValue(":nom_inventario",$p->nom_inventario);
		$ca->bindValue(":precio",$r->attrs->{$this->campo_precio}->data[0]);
		$ca->bindValue(":precio_venta",$r->attrs->{$this->campo_precio}->data[0]);
		$ca->bindValue(":precio_usd", 0);
		$ca->bindValue(":por_impuesto",$r->attrs->{$this->campo_impuesto}->data[0]);
		$ca->bindValue(":subtotal",0);
		$ca->bindValue(":i_ts","current_timestamp",false);
		$ca->bindValue(":u_ts","current_timestamp",false);
		$ca->execInsert();

		$this->totalizar();
	}

	public function removerItem(stdClass $p){
		global $db;
		$ca=new Cm\DbQuery($db);
		$enc=$this->enc();

		$ca->prepareTable("pedidos_d");
		$ca->bindWhere(":item_id",$p->item_id);
		$ca->bindWhere(":id_carro",$enc->id_carro);
		$ca->bindWhere(":color",$p->color);
		$ca->bindWhere(":talla",$p->talla);
		$ca->bindWhere(":plat_id",1);
		$ca->execDelete();

		$this->totalizar();
	}

	public function vaciar(){
		$det = $this->det();
		foreach($det as $r){
			$this->removerItem((object)["item_id"=>$r->item_id]);
		}
	}

	public function actualizarUnidades(stdClass $p){
		global $db;
		$ca=new Cm\DbQuery($db);
		$enc=$this->enc();

		$sql = "
		update
			pedidos_d set unidades = {$p->unidades}
		where
			variacion = :variacion
			and item_id = {$p->item_id}
			and id_carro = {$enc->id_carro}
			and plat_id = 1
		";

		$ca->prepare($sql);
		$ca->bindValue(":variacion", $p->variacion);
		$ca->exec();


		// $ca->prepareTable("pedidos_d");
		// $ca->bindValue(":unidades", $p->unidades);
		// $ca->bindWhere(":variacion",$p->variacion);
		// $ca->bindWhere(":item_id",$p->item_id);
		// $ca->bindWhere(":id_carro",$enc->id_carro);
		// $ca->bindWhere(":plat_id",1);
		// $ca->execUpdate();

		$this->totalizar();

	}

	public function setDestino(stdClass $p){
		global $db;
		$ca=new Cm\DbQuery($db);


		$ca->prepareTable("pedidos_e");
		$ca->bindValue(":ent_id_pais",$p->id_pais);
		$ca->bindValue(":ent_id_departamento",$p->id_departamento);
		$ca->bindValue(":ent_id_ciudad",$p->id_ciudad);

		$ca->bindValue(":direccion_ip",$_SERVER["REMOTE_ADDR"]);
		$ca->bindValue(":u_ts","current_timestamp",false);

		$ca->bindWhere(":id_carro",$this->id_carro);
		//throw new Cm\PublicException($ca->preparedUpdate());

		$ca->execUpdate();

		$this->totalizar();
	}


	public function setDatosCompra(stdClass $p){
		global $db;
		$ca=new Cm\DbQuery($db);
		$p->id_forma_pago = coalesce_null($p->id_forma_pago) ?: -1;

		$enc = $this->enc();

		$forma_pago = TiendaMdl::formaPago($p->id_forma_pago);
		if( !$forma_pago ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Forma de pago invalida.");
			} else{
				throw new Cm\PublicException("Invalid form of payment.");	
			}
		}

		$nombre_forma_pago = $forma_pago->attrs->nombre->label[0];
		//throw new Cm\PublicException($enc);

		$id_pedido = App::nextval("pedidos_e_id_pedido");

		$ca->prepareTable("pedidos_e");
		$ca->bindValue(":id_pedido",$id_pedido);

		$ca->bindValue(":id_cliente",$p->id_cliente);
		$ca->bindValue(":com_correo_electronico",$p->com_correo_electronico);
		$ca->bindValue(":id_forma_pago",$p->id_forma_pago);
		$ca->bindValue(":nombre_forma_pago", $nombre_forma_pago);

		$ca->bindValue(":com_nombres",$p->com_nombres);
		$ca->bindValue(":com_apellidos",$p->com_apellidos);
		$ca->bindValue(":com_identificacion",$p->com_identificacion);
		$ca->bindValue(":com_direccion",$p->com_direccion);
		$ca->bindValue(":com_telefono_fijo",$p->com_telefono_fijo);
		$ca->bindValue(":com_telefono_celular",$p->com_telefono_celular);
		$ca->bindValue(":com_ciudad",$p->com_ciudad);


		$ca->bindValue(":ent_nombres",$p->ent_nombres);
		$ca->bindValue(":ent_apellidos",$p->ent_apellidos);
		$ca->bindValue(":ent_identificacion",$p->ent_identificacion);
		$ca->bindValue(":ent_direccion",$p->ent_direccion);
		$ca->bindValue(":ent_telefono",$p->ent_telefono);
		$ca->bindValue(":ent_telefono_celular",$p->ent_telefono_celular);
		$ca->bindValue(":ent_id_pais",$p->ent_id_pais);
		$ca->bindValue(":ent_id_departamento",$p->ent_id_departamento);
		$ca->bindValue(":ent_id_ciudad",$p->ent_id_ciudad);
		$ca->bindValue(":ent_ciudad",$p->ent_ciudad);
		$ca->bindValue(":estado","reserva");

		$ca->bindValue(":direccion_ip",$_SERVER["REMOTE_ADDR"]);
		$ca->bindValue(":u_ts","current_timestamp",false);


		$ca->bindWhere(":id_carro",$this->id_carro);
		$ca->bindWhere(":plat_id",1);
		$ca->execUpdate();

		$this->totalizar();

	}

	public function totalizar(){
		global $db;
		$ca=new Cm\DbQuery($db);

		$enc=$this->enc();
		$det=$this->det();

		$subtotal = 0;
		$subtotal_usd = 0;
		$total = 0;
		$total_iva = 0;
		$total_unidades=0;
		$total_usd = 0;
		$tmp_dolar = 0;

		foreach($det as $r){

			// $tmp1 = $r->precio * $r->unidades;
			// $tmp2 = $tmp1 -  ( $tmp1 / ( 1 + ($r->por_impuesto/100.0) ) );
			//
			//
			// $subtotal +=$tmp1+$tmp2;
			// $total_iva +=$tmp2;
			//
			// $total_unidades += $r->unidades;


			$tmp1 = $r->precio * $r->unidades;
			// $tmp_dolar = $r->precio_usd * $r->unidades;
			//$tmp2 = $tmp1 -  ( $tmp1 / ( 1 + ($r->por_impuesto/100.0) ) );
			$tmp2 = ($tmp1 * 19) / 100;

			$subtotal +=$tmp1;
			$total_iva +=$tmp2;
			$subtotal_usd += $tmp_dolar;

			$total_unidades += $r->unidades;


			$ca->prepareTable("pedidos_d");
			$ca->bindValue(":subtotal",$tmp1);
			$ca->bindValue(":subtotal_usd", 0);

			$ca->bindWhere(":id_detalle",$r->id_detalle);
			$ca->bindWhere(":id_carro",$enc->id_carro);
			$ca->bindWhere(":item_id",$r->item_id);
			$ca->execUpdate();
		}

		$total_transporte = TransporteMdl::calcular($enc,$det);

		$total = $subtotal + $total_transporte;
		$total_usd = $subtotal_usd;

		$ca->prepareTable("pedidos_e");
		$ca->bindValue(":subtotal",$subtotal);
		$ca->bindValue(":total",$total);
		$ca->bindValue(":total_iva",$total_iva);
		$ca->bindValue(":total_unidades",$total_unidades);
		$ca->bindValue(":total_transporte",$total_transporte);
		$ca->bindValue(":total_usd",$total_usd);
		$ca->bindWhere(":plat_id",1);
		$ca->bindWhere(":id_carro",$enc->id_carro);
		$ca->execUpdate();

	}


	public function reservarInventario(){

		$det=$this->det();


		foreach($det as $r){
			$unidades = TiendaMdl::unidadesDisponibles($r,true);//lock


			if( $unidades == 0){
				if($_SESSION['idioma']=='espanol'){
					throw new Cm\PublicException("Lo sentimos, ya no tenemos unidades disponibles de '{$r->descripcion}'");
				}else{
					throw new Cm\PublicException("Sorry, we no longer have units available from '{$r->descripcion}'");
				}
			}

			if( $unidades < $r->unidades ){
				if($_SESSION['idioma']=='espanol'){
					throw new Cm\PublicException("Lo sentimos, solo tenemos {$unidades} unidades de '{$r->descripcion}', Ajusta las unidades en tu carrito y vuelva a intentarlo");
				}else{
					throw new Cm\PublicException("Sorry we just have {$unidades} units of '{$r->descripcion}', Please adjust the units in your cart and try again");
				}
			}
			

			/*
			stdClass Object (
				[plat_id] => 1
				[id_carro] => 563
				[id_detalle] => 52
				[item_id] => 274
				[referencia] => 0001234
				[descripcion] => Zapato Café Casual N. 1
				[variacion] => 39:2
				[precio] => 67000.00
				[por_impuesto] => 19.00
				[subtotal] => 67000.00
				[i_ts] => 2017-07-01 10:38:00
				[u_ts] => 2017-07-01 10:38:00
				[por_descuento] => 0.00
				[tipo] => producto )
			*/


			TiendaMdl::ajustarUnidades($r);


		}


	}

	public function liberarInventario(){
		//Cuando no tiene variacion
		// $det=$this->det();
		//
		// foreach($det as $r){
		// 	//$r->unidades = $r->unidades * -1;
		// 	//TiendaMdl::ajustarUnidades($r);
		// }

		$det=$this->det();

		foreach($det as $r){
			$r->unidades = $r->unidades * -1;
			TiendaMdl::ajustarUnidades($r);
		}

	}





}


?>

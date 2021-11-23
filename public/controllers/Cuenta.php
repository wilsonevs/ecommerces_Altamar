<?php
require_once __DIR__."/../modelos/Region.php";
require_once __DIR__."/../modelos/Cuenta.php";
require_once __DIR__."/Cuenta.data.php";

use Models\Runtime\EavModel;
use Models\Runtime\EavCategories;

class Cuenta extends Base {

	//Función para consultar la información que se muestra encima del formulario
	//Paginas: registro.php
	public function registro(){
		unset($_SESSION["si"]);

		$res=new stdClass();

		//TERMINOS
		$filter=[];
		$filter[]="items.category_id = 43";

		$res->terminos = EavModel::item( (object)[
			"filter"=>$filter
		]);

		// $res->tipos_identificacion = CuentaMdl::tiposIdentificacion();
		$res->paises = RegionMdl::paises();


		// $res->contenido_registro = Models\Runtime\EavModel::item( (object)[
		// 	"filter"=>"items.category_id=:category_id",
		// 	"params"=>[
		// 		":category_id"=>"34"
		// 	]
		// ]);


		$this->render("registro.php",$res);
	}


	public function account(){
		$res=new stdClass();


		if( !empty($_SESSION["si"]) && !empty($_SESSION["si"]->usuario) ){
			static::perfil();
			return;
		}

		$res->ref = '/account/perfil';

		if (isset($_REQUEST['e26ee7b57a3d7f953de0818fae0b795'])) {
			$res->ref = '/checkout?e26ee7b57a3d7f953de0818fae0b79=0';
		}

		$this->render("login.php",$res);
	}


	public function perfil(){
		$res=new stdClass();

		// $res->tipos_identificacion = CuentaMdl::tiposIdentificacion();

		$res->paises = RegionMdl::paises();
		$res->departamentos = [];
		$res->ciudades = [];

		if( !empty($this->si->usuario->attrs->pais->data) ){
			$res->departamentos = RegionMdl::departamentos( (object)[
				"country_id"=>$this->si->usuario->attrs->pais->data[0]
			]);

			if( !empty($this->si->usuario->attrs->departamento->data) ){
				$res->ciudades = RegionMdl::ciudades( (object)[
					"country_id"=>$this->si->usuario->attrs->pais->data[0],
					"state_id"=>$this->si->usuario->attrs->departamento->data[0],
				]);

			}
		}



		$this->render("perfil_datos.php",$res);
	}

	public function compras(){
		$res=new stdClass();

		$res->historico_compras = TiendaMdl::historicoCompras($this->si->usuario->item_id);


		for ($i = 0; $i < count($res->historico_compras); $i++) {
			//Producto
			$filter=[];
			$filter[]="items.item_id = :item_id";

			$res->historico_compras[$i]->item = EavModel::item( (object)[
				"filter"=>$filter,
				"params"=>[
					":item_id"=>$res->historico_compras[$i]->item_id
				]
			]);

		}

		// print_r($res->historico_compras);
		// exit;

		$this->render("perfil_historico_compra.php",$res);
	}

	// public function listaDeseos(){
	// 	$res=new stdClass();

	// 	//$res->historicoCompras = TiendaMdl::historicoCompras($this->si->usuario->item_id);

	// 	$lista = new ListaDeseosMdl($this->si->usuario->item_id);
	// 	$res->productos = $lista->det();

	// 	//print_r($res);

	// 	$this->render("perfil_lista_deseos.php",$res);
	// }


	public function cambioClave(){
		global $db;
		$res=new stdClass();
		$fecha=date("Y-m-d");

		$tokenCorreo = filter_input(INPUT_GET,"token");
		$correo_electronico = filter_input(INPUT_GET,"correo_electronico",FILTER_VALIDATE_EMAIL,FILTER_NULL_ON_FAILURE);

		if( !empty($correo_electronico) ){
			$filter=[];
			$filter[]="items.category_id = 39";
			$filter[]="and";
			$filter[]="{correo_electronico}='{$correo_electronico}'";

			//print_r($filter);
			//exit;

			$registro = EavModel::item( (object)[
				"filter"=>$filter
			]);

			$registro = EavModel::load($registro->item_id);

			$token = hash('sha256', $registro->correo_electronico . $fecha . "tulicorera");

			if($tokenCorreo != $token){
				//throw new Cm\PublicException("Token invalido o expirado, por favor realize la solicitud de recuperación nuevamente");
				Error::get()->index(['mensaje'=>'Token invalido o expirado, por favor realize la solicitud de recuperación nuevamente.']);
			}

			$registro->recuperar_clave = true;

			CuentaData::get()->iniciarSesion($registro);
		}

		$this->render("perfil_clave.php",$res);
	}

	public function cerrarSesion(stdClass $p){
		unset($_SESSION["si"]);
		Router\dispatch("Inicio@index",$p);
	}


	public function recuperarClave(){
		$res=new stdClass();

		$this->render("recuperar_clave.php",$res);
	}


}

?>

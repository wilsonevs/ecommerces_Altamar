<?php
use Models\Runtime\EavModel;
require_once __DIR__."/Base.data.php";
require_once __DIR__."/../modelos/Region.php";
require_once __DIR__."/../modelos/Cuenta.php";

$exports[]="CuentaData";
class CuentaData extends BaseData {

	public function departamentos(stdClass $p){
		return RegionMdl::departamentos($p);
	}

	public function ciudades(stdClass $p){
		return RegionMdl::ciudades($p);
	}

	public function registrar(stdClass $p){
		global $db;
		global $cfg;
		$res=new stdClass();

		//DATOS DE SESION
		if(!filter_var($p->correo_electronico, FILTER_VALIDATE_EMAIL)){
			throw new Cm\PublicException("Ingresa un correo electrónico válido por favor");
		}

		if( empty($p->contrasena) ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Ingresa una contraseña por favor.");
			}else{
				throw new Cm\PublicException("Please enter a password.");
			}
		}

		if( $p->contrasena !== $p->repite_contrasena ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Las contraseñas no coinciden.");
			}else{
				throw new Cm\PublicException("Passwords do not match.");
			}
		}

		unset($p->repite_contrasena);
		$p->contrasena = password_hash($p->contrasena, PASSWORD_DEFAULT);


		$cuenta = CuentaMdl::item($p->correo_electronico);
		if( $cuenta ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new  Cm\PublicException("Ya existe una cuenta con el correo electrónico que ingresó");
			}else{
				throw new Cm\PublicException("There is already an account with the email you entered");
			}
		}

		// Captcha::verify("6LczjyIUAAAAADUDCrkJi096zTwFmS4ofez3POSv",$p);

		// La categoría que se pone es la de almacena los usuarios registrados.

		$p->category_id = 39;
		$p->item_id = "";
		$p->enviado = 2;
		$p->fecha_de_registro = date("d/m/Y");
		$p->ip = $_SERVER["REMOTE_ADDR"];
		$p->ips = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
		$p->acepto_terminos = 1;


		$db->transaction();
		$res->item_id = CuentaMdl::insert($p);
		$db->commit();

		if($_SESSION['idioma'] == 'espanol'){
			$res->message="Tu Registro ha sido exitoso.";
		}else{
			$res->message = "Your registration has been successful.";
		}


		$url_prefix = "http://{$_SERVER["HTTP_HOST"]}{$cfg["appRoot"]}/task/mail";
		$url="{$url_prefix}/correo_registro.php?item_id={$res->item_id}";
		file_get_contents($url);

		return $res;
	}

	public function actualizar(stdClass $p){
		$this->checkSession();

		global $db;
		$res=new stdClass();

		$this->validarDatosCompartidos($p);

		$tmp = CuentaMdl::load( $this->si->usuario->item_id );
		$p = object_merge($tmp,$p);

		$db->transaction();
		CuentaMdl::update($p);
		$db->commit();

		$cuenta = CuentaMdl::item($p->correo_electronico);
		unset($cuenta->attrs->clave);

		$_SESSION["si"]->usuario = $cuenta;
		if($_SESSION['idioma'] == 'espanol'){
			$res->message = "Datos actualizados correctamente.";
		}else{
			$res->message = "Data updated correctly.";
		}
		
		return $res;
	}


	public function validarDatosCompartidos(stdClass $p)
	{
		if( $p->tipo_identificacion == '' ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Selecciona un el tipo de identificación por favor.");
			}else{
				throw new Cm\PublicException("Select a type of identification please.");
			}
		}

		if( empty($p->identificacion) ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Ingresa un número de identificación por favor.");
			}else{
				throw new Cm\PublicException("Please enter an identification number.");
			}
		}

		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $p->identificacion)){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("El número de identificación no debe contener caracteres especiales.");
			}else{
				throw new Cm\PublicException("The identification number must not contain special characters.");
			}
		}

		if( empty($p->nombres) ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Ingresa un nombre por favor.");
			}else{
				throw new Cm\PublicException("Please enter a name.");
			}
		}

		if( strlen($p->nombres) < 3 ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("El nombre debe contener más de 3 letras.");
			}else{
				throw new Cm\PublicException("The name must contain more than 3 letters.");
			}
		}

		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $p->nombres)){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("El nombre no debe contener caracteres especiales.");
			}else{
				throw new Cm\PublicException("The name must not contain special characters.");
			}
		}

		if (strcspn($p->nombres, '0123456789') != strlen($p->nombres)){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("El nombre no debe contener números.");
			}else{
				throw new Cm\PublicException("The name must not contain numbers.");
			}
		}

		if( empty($p->apellidos) ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Ingresa un apellido por favor.");
			}else{
				throw new Cm\PublicException("Please enter a last name.");
			}
		}

		if( strlen($p->apellidos) < 3 ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("El apellido debe contener más de 3 letras.");
			}else{
				throw new Cm\PublicException("The last name must contain more than 3 letters.");
			}
		}

		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $p->apellidos)){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("El apellido no debe contener caracteres especiales.");
			}else{
				throw new Cm\PublicException("The last name must not contain special characters.");
			}
		}

		if (strcspn($p->apellidos, '0123456789') != strlen($p->apellidos)){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("El apellido no debe contener números.");
			}else{
				throw new Cm\PublicException("The last name must not contain numbers.");
			}
		}

		if( empty($p->telefono_celular) ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Ingresa un celular por favor.");
			}else{
				throw new Cm\PublicException("Enter a cell phone please.");
			}
		}

		if( empty($p->direccion) ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Ingresa una dirección por favor.");
			}else{
				throw new Cm\PublicException("Enter an address please.");
			}
		}


		//DATOS DE SESION
		if( empty($p->correo_electronico) ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Ingresa un correo electrónico por favor.");
			}else{
				throw new Cm\PublicException("Please enter an email.");
			}
		}

		if(!filter_var($p->correo_electronico, FILTER_VALIDATE_EMAIL)){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Ingresa un correo electrónico válido por favor");
			}else{
				throw new Cm\PublicException("Please enter a valid email address");
			}
		}

		if($_SESSION["si"]->usuario->attrs->correo_electronico->data[0] !== $p->correo_electronico){
			$cuenta = CuentaMdl::item($p->correo_electronico);
			if( $cuenta ){
				if($_SESSION['idioma'] == 'espanol'){
					throw new  Cm\PublicException("Ya existe una cuenta con el correo electrónico que ingresó");
				}else{
					throw new Cm\PublicException("There is already an account with the email you entered");
				}
			}
		}
		

		return;
	}

	public function cambiarClave(stdClass $p){
		$this->checkSession();

		global $db;
		global $cfg;
		$res=new stdClass();

		if(!password_verify($p->contrasena_ant, $_SESSION["si"]->usuario->attrs->contrasena->data[0])){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("La contraseña anterior no coincide.");
			}else{
				throw new Cm\PublicException("The old password does not match.");
			}
		}

		if( empty($p->contrasena) ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Contraseña invalida.");
			}else{
				throw new Cm\PublicException("Invalid password.");
			}
		}

		if( $p->contrasena != $p->contrasena2 ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Las contraseñas no coinciden.");
			}else{
				throw new Cm\PublicException("Passwords do not match.");
			}
		}

		unset($p->contrasena2);
		$p->contrasena = password_hash($p->contrasena, PASSWORD_DEFAULT);

		$tmp = CuentaMdl::load( $this->si->usuario->item_id );
		$p = object_merge($tmp,$p);

		$db->transaction();
		CuentaMdl::update($p);
		$db->commit();

		$_SESSION["si"]->usuario->attrs->contrasena->data[0] = $p->contrasena;
		if($_SESSION['idioma'] == 'espanol'){
			$res->message = "Datos actualizados correctamente.";
		}else{
			$res->message = "Data updated correctly.";
		}
		
		return $res;
	}

	public function iniciarSesion(stdClass $p){

		if( empty($p->correo_electronico) ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Usuario invalido.");
			}else{
				throw new Cm\PublicException("Invalid user.");
			}
		}

		if( empty($p->contrasena) ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Contraseña invalida.");
			}else{
				throw new Cm\PublicException("Invalid password.");
			}
		}

		$cuenta = CuentaMdl::item($p->correo_electronico);
		if(!$cuenta){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("Usuario o contraseña invalidos.");
			}else{
				throw new Cm\PublicException("Invalid username or password.");
			}
		}

		if(!isset($p->recuperar_contrasena)){
			if (!password_verify($p->contrasena, $cuenta->attrs->contrasena->data[0])) {
			    if($_SESSION['idioma'] == 'espanol'){
			    	throw new Cm\PublicException("Usuario o contraseña invalidos.");
			    }else{
			    	throw new Cm\PublicException("Invalid username or password.");
			    }
			}
		}

		$_SESSION["si"] = new stdClass();
		$_SESSION["si"]->usuario = $cuenta;

		return true;
	}

	public function cerrarSesion(stdClass $p){
		unset($_SESSION["si"]);
	}

	public function recuperarClave(stdClass $p){
		global $db;
		global $cfg;

		$cuenta = CuentaMdl::item($p->correo_electronico);

		if( is_null($cuenta) ){
			if($_SESSION['idioma'] == 'espanol'){
				throw new Cm\PublicException("No existe una cuenta registrada con el correo que escribiste.");
			}else{
				throw new Cm\PublicException("There is no registered account with the email you wrote.");
			}
		}
		//Aca se coloca la categoria donde se guarda cada vez que alguien solicita clave (categoria: Recuperaciones de clave (registros almacenados))
		$p->category_id=60;
		$p->fecha=date("Y-m-d");
		$p->enviado=0;

		$db->transaction();
		$tmp = Models\Runtime\EavModel::save($p);
		$db->commit();

		if($_SESSION['idioma']=='espanol'){
			$message = "La clave ha sido enviada al correo ( {$p->correo_electronico} ) .";
		} else{
			$message = "The key has been sent to the mail ( {$p->correo_electronico} ) .";
		}

		$url_prefix = "http://{$_SERVER["HTTP_HOST"]}{$cfg["appRoot"]}/task/mail";
		$url="{$url_prefix}/correo_recuperar_contrasena.php?item_id={$tmp->item_id}";
		file_get_contents($url);
		
		return [
			"message"=>$message,
			"url" => $url
		];
	}

	// public function suscribe(stdClass $p){
	// 	global $db;
	// 	global $cfg;

	// 	//DATOS DE SESION
	// 	if( empty($p->correo_electronico) ){
	// 		if($_SESSION['idioma'] == 'espanol'){
	// 			throw new Cm\PublicException("Ingresa un correo electrónico por favor.");
	// 		}else{
	// 			throw new Cm\PublicException("Please enter an email.");
	// 		}
	// 	}

	// 	if(!filter_var($p->correo_electronico, FILTER_VALIDATE_EMAIL)){
	// 		if($_SESSION['idioma'] == 'espanol'){
	// 			throw new Cm\PublicException("Ingresa un correo electrónico válido por favor");
	// 		}else{
	// 			throw new Cm\PublicException("Please enter a valid email address");
	// 		}
	// 	}

	// 	//PLANTILLA DEL SITIO
	// 	$suscribe = EavModel::item([
	// 		"filter"=>"items.category_id=67 and {correo_electronico} = '{$p->correo_electronico}'"
	// 	]);

	// 	if ($suscribe) {
	// 		if($_SESSION['idioma'] == 'espanol'){
	// 			throw new Cm\PublicException("Elcorreo electrónico ya se encuentra suscrito.");
	// 		}else{
	// 			throw new Cm\PublicException("The email is already subscribed.");
	// 		}
	// 	}

	// 	//Aca se coloca la categoria donde se guarda cada vez que alguien solicita clave (categoria: Recuperaciones de clave (registros almacenados))
	// 	$p->category_id = 67;
	// 	$p->fecha = date("Y-m-d");
	// 	$p->unsubscribe = 0;
	// 	$p->enviado = 0;

	// 	$db->transaction();
	// 	$tmp = Models\Runtime\EavModel::save($p);
	// 	$db->commit();

	// 	if($_SESSION['idioma']=='espanol'){
	// 		$message = "Te has suscrito correctamente a {$cfg["appHost"]}";
	// 	} else{
	// 		$message = "You have successfully subscribed to {$cfg["appHost"]}";
	// 	}
		
	// 	return [
	// 		"message"=>$message
	// 	];
	// }



}


?>

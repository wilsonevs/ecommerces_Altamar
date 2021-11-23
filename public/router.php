<?php
namespace Router;
use Cm;

function dispatch($controller,$arguments){
	$tmp = explode("@",$controller);
	$ctrl = new $tmp[0]();
	$ctrl->{$tmp[1]}($arguments);
	//call_user_func($controller,$arguments);
}


function resolve($p){

	//MENU SUPERIOR-------------------------------------

	//INICIO
	$m=null;
	$pattern="/\\/inicio/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Inicio@index",$p);
	}

	//CATALOGO
	$m=null;
	$pattern="/\\/tienda/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Catalogo@index",$p);
	}


	//CONTACTENOS
	$m=null;
	$pattern="/\\/contactenos/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Contactenos@index",$p);
	}


	//MENU INFERIOR-------------------------------------

	//Habes Data
	$m=null;
	$pattern="/\\/habes-data/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("HabesData@index",$p);
	}

	//TERMINOS
	$m=null;
	$pattern="/\\/terminos-y-condiciones/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Terminos@index",$p);
	}

	//Reembolso
	$m=null;
	$pattern="/\\/reembolso/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Reembolso@index",$p);
	}





	//-----------------------------------------------------

	//AUTENTICACIÓN
	// LOGIN
	// Paginas: login.php
	$m=null;
	$pattern="/\\/account$/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Cuenta@account",$p);
	}

	// REGISTRO
	//Paginas: registro.php
	$m=null;
	$pattern="/\\/registro/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Cuenta@registro",$p);
	}


	//RECUPERAR CLAVE
	$m=null;
	$pattern="/\\/recuperar-contrasena/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Cuenta@recuperarClave",$p);
	}


	// CAMBIAR CLAVE
	//Páginas: perfil_clave.php
	$m=null;
	$pattern="/\\/account\\/cambiar-clave/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Cuenta@cambioClave",$p);
	}

	//CERRAR SESIÓN
	//Paginas: inicio.php
	$m=null;
	$pattern="/\\/cerrarsesion/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Cuenta@cerrarSesion",$p);
	}


	//PERFIL
	//Paginas: perfil_datos.php
	$m=null;
	$pattern="/\\/account\\/perfil/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Cuenta@perfil",$p);
	}

	//HISTÓRICO DE COMPRAS
	//Pagina: perfil_historico_compra.php
	$m=null;
	$pattern="/\\/account\\/compras/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Cuenta@compras",$p);
	}



	//-----------------------------------------------------


	//AMPLIACION ITEM
	$m=null;
	$pattern="/\\/catalogo\\/(.*)/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		$p->catalogo_slug=$m[1];
		dispatch("Catalogo@ampliacion",$p);
	}

	




	//---------------------------------------------------------


	//ESTADO DEL PEDIDO - PRODUCTO AÑADIDO
	//Page: carro.php
	$m=null;
	$pattern="/\\/carro/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Tienda@carro",$p);
	}


	//TOTAL A PAGAR Y DATOS DE ENTREGA
	//Page: pago.php
	$m=null;
	$pattern="/\\/checkout$/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Tienda@pedido",$p);
	}

	//PEDIDO FINALIZADO - FORMAS DE PAGO
	//Page: pedido_finalizado.php
	$m=null;
	$pattern="/\\/pago-realizado/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Tienda@pedidoFinalizado",$p);
	}

	//PEDIDO FINALIZADO - PAYU
	//Page: pago_payu.php
	$m=null;
	$pattern="/\\/redireccion-banco/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Tienda@pagoPayU",$p);
	}

	//RESPUESTA PAGO PAYU
	//Page: respuesta_payu.php
	$m=null;
	$pattern="/\\/respuesta-pago/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Tienda@respuestaPayu",$p);
	}

	//PEDIDO FINALIZADO - PAYPAL
	//Page: pago_paypal.php
	$m=null;
	$pattern="/\\/redireccion-paypal/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Tienda@pagoPaypal",$p);
	}

	//RESPUESTA PAGO PAYPAL
	//Page: respuesta_paypal.php
	$m=null;
	$pattern="/\\/respuesta-pago-paypal/";
	if( preg_match($pattern,$p->resource_uri,$m) ){
		dispatch("Tienda@respuestaPaypal",$p);
	}


	dispatch("PaginaError@index",$p);
	exit;


	throw new Cm\PublicException("Page not found");
	return;

}


?>

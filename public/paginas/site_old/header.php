<!DOCTYPE HTML>
<html class="no-js" lang="es" >
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="{{plantilla.attrs.meta_description.data[0]}}">
	<title>{{plantilla.attrs.titulo.data[0]}}</title>
	{% if plantilla.attrs.icono.data[0] %}
	<link rel="shortcut icon" href="{{plantilla.attrs.icono.data[0]|image}}">
	{% else %}
	<link rel="shortcut icon" href="https://via.placeholder.com/15">
	{% endif %}
	<link rel="stylesheet" href="{{template_url}}/libreria/foundation6-custom/css/foundation.css">
	<link rel="stylesheet" href="{{template_url}}/estilos.php/{{getTemplateName()}}.css">
	<!-- <link rel="stylesheet" href="{{template_url}}/styles.css?page={{getTemplateName()}}"> -->
	<link rel="stylesheet" href="{{template_url}}/libreria/owl-carousel/owl.carousel.css" />
	<script src="{{template_url}}/libreria/foundation6-custom/js/vendor/jquery.js"></script>
	<script src="{{template_url}}/libreria/foundation6-custom/js/vendor/what-input.js"></script>
	<script src="{{template_url}}/libreria/foundation6-custom/js/vendor/foundation.js"></script>
	<script src="{{template_url}}/libreria/owl-carousel/owl.carousel.min.js"></script>

	<script>
	window.site_url = '{{site_url}}';
	window.rpcUrl = '{{site_url}}/public/server.php';
	{{ source('predeterminado.js') }}
	</script>
	<script>{{ source('header.js') }}</script>
	{{plantilla.attrs.javascript_header.data[0]|raw}}
</head>
<body class="{{getTemplateName()}} bodyloader">
	{% include 'carro_lateral.php' %}
	<div class="off-canvas-wrapper">
		<div class="off-canvas-wrapper-inner" data-off-canvas-wrapper>
			<div class="off-canvas position-left" id="offCanvas" data-off-canvas>
				{% include 'menu_small.php' %}
				{% if si.usuario %}
				<div class="hide-for-large">
					{% include 'menu_interno.php' %}
				</div>
				{% endif %}
				<ul class="vertical menu">
					<li>
						
						{% if getTemplateName() == 'inicio' %}
						<a>
							{% include 'idioma_small.php' %}
						</a>
						<a>
							{% include 'part_moneda_small.php' %}
						</a>
						{% endif %}
						
						
					</li>
				</ul>
				
			</div>
			<div class="off-canvas-content" data-off-canvas-content id="header" data-magellan-target="header">

				<!-- HEADER FOR LARGE -->
				<div class="header show-for-large">

					<div class="" data-sticky-container>
						<div class="sticky" data-sticky data-options="marginTop:0;" data-top-anchor="header">
							<div class="content-gris">

								<div class="row">
									<div class="column small-24 medium-12">
										<div style="display: inline-block;">
										{% if getTemplateName() == 'inicio' %}
						                	{% include 'idioma.php' %}
										{% endif %}
										</div>
										<div style="display: inline-block;">
										{% if getTemplateName() == 'inicio' %}
						                	{% include 'part_moneda.php' %}
						                {% endif %}
										</div>
									</div>
									<div class="column small-24 medium-12 text-right pd-top7 pd-bot7">
										{% if si.usuario %}
										<a href="{{site_url}}/account/perfil" class="inicio-sesion negro font-9 no-hover-negro bold" title="">
											{{si.usuario.attrs.correo_electronico.data[0]}}</a>&nbsp;&nbsp;&nbsp;
										{% endif %}
										{% if not si.usuario %}
										<a href="{{site_url}}/account" class="inicio-sesion negro font-9 no-hover-negro bold" title="">
											{% if idioma=='espanol' %}
											Iniciar sesión
											{% else %}
											Login
											{% endif %}
										</a>
										<span class="negro font-8">/</span>
										<a href="{{site_url}}/registro" class="inicio-sesion negro font-9 no-hover-negro bold" title="">
											{% if idioma=='espanol' %}
											Registrarme
											{% else %}
											Sign Up
											{% endif %}
										</a>
										{% endif %}
										
									</div>
								</div>
							</div>
							<div class="content-negro">
								<div class="row">
									<div class="column small-24 medium-7 relative">
										<!-- <div class="icon-menu">
											<a data-toggle="offCanvas" title="iono menu"><img src="{{template_url}}/img/icon-menu.png" title="iono menu" alt="iono menu"/></a>
										</div> -->
										<div class="logo">
											<a href="{{site_url}}/inicio">
											{% if plantilla.attrs.logo.data[0] %}
											<img src="{{plantilla.attrs.logo.data[0]|image}}" alt="{{plantilla.attrs.titulo.data[0]}}" title="{{plantilla.attrs.titulo.data[0]}}" width="225" />
											{% else %}
											<img src="https://via.placeholder.com/250x50"/>
											{% endif %}
											</a>
										</div>
									</div>
									<div class="column small-24 medium-14 padding-none">
										<!-- <form class="form buscador" id="form-search" action="{{site_url}}/catalogo" method="get">
											<input type="text" name="p" value=""/>
											<a onclick="document.getElementById('form-search').submit();" class="icon-search pointer" title="buscar accesorios"><img src="{{template_url}}/img/icon-search.png" title="buscar accesorios" alt="buscar accesorios"></a>
										</form> -->
										{% include 'menu.php' %}
									</div>
									<div class="column small-24 medium-3 text-right">
										<div class="menu-aut-car">
											<div class="content-carro">
												{% if num_items == 0 %}
													<a id="carro-lateral" class="blanco font-10 no-hover-blanco" title="{{plantilla.attrs.titulo.data[0]}}"><img src="{{template_url}}/img/bolsablanca.png" width="30" title="{{plantilla.attrs.titulo.data[0]}}" alt="{{plantilla.attrs.titulo.data[0]}}" /></a>
												{% else %}
													<a id="carro-lateral" class="blanco font-10 no-hover-blanco" title="{{plantilla.attrs.titulo.data[0]}}"><img src="{{template_url}}/img/bolsaverde.png" width="30" title="{{plantilla.attrs.titulo.data[0]}}" alt="{{plantilla.attrs.titulo.data[0]}}" /></a>
												{% endif %}
												
												<div class="num-items">
													{{num_items}}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- HEADER FOR SMALL -->
				<div class="header hide-for-large">
					<div class="content-negro">
						<div class="row">
							<div class="column small-24 text-center">
								<div class="icon-menu">
									<a data-toggle="offCanvas" title="icon menu"><img src="{{template_url}}/img/icon-menu.png" title="icon menu" alt="icon menu"/></a>
								</div>
								<div class="logo">
									<a href="{{site_url}}/inicio" title="logo">
										{% if plantilla.attrs.logo.data[0] %}
										<img width="200" src="{{plantilla.attrs.logo.data[0]|image}}" alt="" title="{{plantilla.attrs.titulo.data[0]}}"/>
										{% else %}
										<img width="200" src="https://via.placeholder.com/250x50"/>
										{% endif %}
									</a>
								</div>
								<div class="carro-small">
									<div class="content-carro">
										<!-- <a id="carro-lateral" href="{{site_url}}/carro" class="blanco font-10 no-hover-blanco" title="{{plantilla.attrs.titulo.data[0]}}"><img src="{{template_url}}/img/icon-carro.png" width="25" title="{{plantilla.attrs.titulo.data[0]}}" alt="{{plantilla.attrs.titulo.data[0]}}" /></a> -->

										{% if num_items == 0 %}
											<a id="carro-lateral" href="{{site_url}}/carro" class="blanco font-10 no-hover-blanco" title="{{plantilla.attrs.titulo.data[0]}}"><img src="{{template_url}}/img/bolsablanca.png" width="30" title="{{plantilla.attrs.titulo.data[0]}}" alt="{{plantilla.attrs.titulo.data[0]}}" /></a>
										{% else %}
											<a id="carro-lateral" href="{{site_url}}/carro" class="blanco font-10 no-hover-blanco" title="{{plantilla.attrs.titulo.data[0]}}"><img src="{{template_url}}/img/bolsaverde.png" width="30" title="{{plantilla.attrs.titulo.data[0]}}" alt="{{plantilla.attrs.titulo.data[0]}}" /></a>
										{% endif %}

										<div class="num-items">
											{{num_items}}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

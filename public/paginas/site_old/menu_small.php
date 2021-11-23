<div class="menu_small">
	<div class="autenticacion">
		{% if not si.usuario %}
		<a href="{{site_url}}/account">
			{% if idioma=='espanol' %}
				Iniciar sesión
            {% else %}
                Log in
            {% endif %}
		</a> <span class="verde">/</span> <a href="{{site_url}}/registro">
			{% if idioma=='espanol' %}
				Registrarme
            {% else %}
               	Sign up
            {% endif %}
		</a>
		{% endif %}
		{% if si.usuario %}
		<a href="{{site_url}}/account">{{si.usuario.attrs.correo_electronico.data[0]}}</a>
		{% endif %}
	</div>
	<ul class="vertical menu accordion-menu" data-accordion-menu>
		{{menu_superior.html|raw}}
		<!-- <li><a href="{{site_url}}/inicio.php">Inicio</a></li>
		<li><a href="{{site_url}}/catalogo.php">Catalogo</a></li>
		<li><a href="{{site_url}}/carro.php"><img class="carrito" src="{{template_url}}/img/icon-carro.png" width="20" alt="carrito">Carrito</a></li>
		<li><a href="{{site_url}}/contactenos.php">Contáctenos</a></li> -->
	</ul>
</div>

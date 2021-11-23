<script>{{ source('carro_lateral.js') }}</script>
<div class="item_producto">
	<div class="card">
		{% if r.attrs.descuento.data[0] %}
		<div class="descuento">
			{{r.attrs.descuento.data[0]}}
		</div>
		{% endif %}
		<div class="relative border">
			<a href="{{site_url}}/catalogo/{{r.slug}}" alt="Abrir ampliación" title="Abrir ampliación" >
				{% if r.attrs.imagen_1.data[0] %}
				<img src="{{r.attrs.imagen_1.data[0]|image}}"/>
				{% else %}
				<img src="https://via.placeholder.com/640x400"/>
				{% endif %}
			</a>
			<!-- <a onclick="Carro.agregarItem({{r.item_id}});" alt="Añadir al carrito" title="Añadir al carrito" class="anadir-carro">
				<img class="icon-carrito" src="{{template_url}}/img/icon-carrito-item.png" alt=""> &nbsp;Añadir al Carrito
			</a> -->
		</div>
		<div class="card-section">
			<a href="{{site_url}}/catalogo/{{r.slug}}" alt="{{r.attrs.titulo.data[0]}}" title="{{r.attrs.titulo.data[0]}}" >
				<div class="gris bold uppercase font-9 show-for-medium hover-underline">
					{% if idioma=='espanol' %}
					{{r.attrs.titulo.data[0]|slice(0,20) ~ '...'}}
					{% else %}
					{{r.attrs.titulo_ingles.data[0]|slice(0,20) ~ '...'}}
					{% endif %}
				</div>
				<div class="gris bold uppercase font-8 hide-for-medium hover-underline">
					{% if idioma=='espanol' %}
					{{r.attrs.titulo.data[0]|slice(0,19) ~ '...'}}
					{% else %}
					{{r.attrs.titulo_ingles.data[0]|slice(0,19) ~ '...'}}
					{% endif %}
				</div>
			</a>
			{% if moneda=='cop' %}
				<span class="font-12 bold verde">COP ${{r.attrs.precio.data[0]|number_format(0, ',', '.')}} </span>
			{% else %}
				<span class="font-12 bold verde">USD ${{r.attrs.precio_usd.data[0]|number_format(2, ',', '.')}} </span><br/>
			{% endif %}
			{% if moneda=='cop' %}
				{% if r.attrs.precio_anterior.data[0] > 0 %}
					<span class="font-8 gris line-through">
						COP ${{r.attrs.precio_anterior.data[0]|number_format(0, ',', '.')}}
					</span>&nbsp;&nbsp;
				{% endif %}
			{% else %}
				{% if r.attrs.precio_anterior_usd.data[0] > 0 %}
					<span class="font-8 gris line-through">USD ${{r.attrs.precio_anterior_usd.data[0]|number_format(2, ',', '.')}}
					</span>
				{% endif %}
			{% endif %}
			<!-- <img class="icon-amp" src="{{template_url}}/img/icon-amp-item.png" alt="" /> -->
		</div>
		<!-- <div class="text-center card-section">
			<a onclick="Carro.agregarItem({{r.item_id}});" alt="Añadir al carrito pointer" class="rojo underline no-hover-rojo font-9">Añadir al carrito</a>
		</div> -->
	</div>
</div>

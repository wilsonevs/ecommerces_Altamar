<div class="carro_lateral">
    <script>{{ source('carro_lateral.js') }}</script>
    <div class="row">
        <div class="column small-24">
            <div class="cerrar-carro">
                <a id="cerrar-carro" title="Cerrar Carro"><img src="{{template_url}}/img/icon-cerrar-carro.png" alt="Cerrar Carro" /></a>
            </div>
        </div>
    </div>
    {% if det %}
	{% for r in det %}
    <div class="row item">
        <div class="column small-5 medium-5 large-5 padding-right-none">
            <a href="{{site_url}}/catalogo/{{r.slug}}">
                {% if r.attrs.imagen_1.data[0] %}
                <img src="{{attribute(r.attrs, 'imagen_1').data[0]|image}}" alt="{{r.attrs.titulo.data[0]}}" />
                {% else %}
                <img src="https://via.placeholder.com/640x400"/>
                {% endif %}
            </a>
        </div>
        <div class="column small-8 medium-8 large-9 padding-right-none">
            <a href="#" class="nombre-producto">
                <h1 class="blanco font-9 bold margin-none">
                    {% if idioma=='espanol' %}
                      {{r.attrs.titulo.data[0]|slice(0,15) ~ '...'}}
                    {% else %}
                      {{r.attrs.titulo_ingles.data[0]|slice(0,15) ~ '...'}}
                    {% endif %}
                </h1>
            </a>
            <div class="referencia">
                <span class="blanco font-8 bold">Ref: </span>
                <span class="blanco font-9">{{r.attrs.referencia.data[0]}}</span>
            </div>
            <div class="color">
                <span class="blanco font-8 bold">
                    {% if idioma=='espanol' %}
                      Color:
                    {% else %}
                      Colour:
                    {% endif %}
                </span>
                <span class="blanco font-9">{{r.color}}</span>
            </div>
            <div class="color">
                <span class="blanco font-8 bold">
                    {% if idioma=='espanol' %}
                      Talla:
                    {% else %}
                      Size:
                    {% endif %}
                </span>
                <span class="blanco font-9 upper">{{r.talla}}</span>
            </div>
        </div>
        <div class="column small-8 medium-8 large-7">
            <div class="precio-articulo-unitario text-right">
                <div class="titulo-articulo">
                    <span class="blanco bold font-8">
                        {% if idioma=='espanol' %}
                          Val Und
                        {% else %}
                          Price Unit
                        {% endif %}
                    </span>
                </div>
                {% if moneda=='cop' %}
                <span class="font-9 blanco">${{r.precio|number_format()}} cop</span>
                {% else %}
                <span class="font-9 blanco">${{r.precio_usd|number_format(2, ',', '.')}} usd</span>
                {% endif %}
                {% if r.attrs.descuento.data[0] %}
                <div class="titulo-articulo">
                    <span class="blanco bold font-8">
                        {% if idioma=='espanol' %}
                            Descuento
                        {% else %}
                            Sale
                        {% endif %}
                    </span>
                </div>
                <span class="font-9 blanco">{{r.attrs.descuento.data[0]}}</span>
                {% endif %}
            </div>
        </div>
        <div class="column small-3 text-center">
            <div class="eliminar">
                <a onclick="CarroLateral.removerItem({{r.item_id}},'{{r.color}}','{{r.talla}}');" title="Eliminar"><img src="{{template_url}}/img/icon-eliminar.png" alt="Eliminar"/> </a>
            </div>
        </div>
    </div>
    {% endfor %}
    <div class="row">
		<div class="valores">
          <div class="row">
            <div class="column small-24 text-right">
                <span class="font-9 blanco bold">
                    {% if idioma=='espanol' %}
                        * El valor del envio se calcula automaticamente
                    {% else %}
                        * The shipping value is calculated automatically
                    {% endif %}
                </span>
            </div>
          </div>
            {% if moneda=='cop' %}
			<div class="row">
				<div class="column large-offset-8 medium-offset-7 medium-7 small-12">
					<span class="label-total bold blanco">Total</span>
				</div>
				<div class="column medium-9 small-12 text-right">
					<span class="content-total bold blanco">${{enc.total|number_format()}} COP</span>
				</div>
			</div>
            {% else %}
            <div class="row">
                <div class="column large-offset-8 medium-offset-7 medium-7 small-12">
                    <span class="label-total bold blanco">Total USD</span>
                </div>
                <div class="column medium-9 small-12 text-right">
                    <span class="content-total bold blanco">${{enc.total_usd|number_format(2, ',', '.')}} USD</span>
                </div>
            </div>
            {% endif %}
		</div>
	</div>
    <div class="row">
        <div class="column small-24">
            <button onclick="location='{{site_url}}/carro';" type="button" name="button" class="button expanded">
            {% if idioma=='espanol' %}
                Ir al Carrito
            {% else %}          
                Go to Cart
            {% endif %}
            </button>
        </div>
    </div>
    {% else %}
	<div class="row">
		<div class="column small-24 text-center">
			<h1 class="font-12 blanco bold margin-top-2">
                {% if idioma=='espanol' %}
                    No hay productos en el carrito.
                {% else %}          
                    There are no products in the cart.
                {% endif %}
            </h1>
		</div>
	</div>
	{% endif %}
</div>

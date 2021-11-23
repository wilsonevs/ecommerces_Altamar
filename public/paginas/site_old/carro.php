{% include 'header.php' %}
<script>{{ source('carro_lateral.js') }}</script>
<script>{{ source('carro.js') }}</script>
<div class="content">

	<div class="row">
        <div class="column small-24">
            <h2 class="font-14 primary bold">
            	{% if idioma=='espanol' %}
                    Carro:
                {% else %}
                    Shopping Cart:
                {% endif %}
            </h2>
        </div>
        <div class="column small-24">
            <hr/>
        </div>
    </div>

	{% if det %}
	{% for r in det %}
	<div class="row">
		<div class="column large-24 medium-24 small-24 items">
			<div class="row">
				<div class="column large-12 medium-9 small-24">
					<div class="row">
						<div class="column large-7 medium-8 small-24 padding-left-none padding-right-none">
							<a href="{{site_url}}/catalogo/{{r.slug}}" title="{{r.attrs.titulo.data[0]}}">
								{% if r.attrs.imagen_1.data[0] %}
				                <img src="{{attribute(r.attrs, 'imagen_1').data[0]|image}}" alt="{{r.attrs.titulo.data[0]}}" title="{{r.attrs.titulo.data[0]}}" />
				                {% else %}
				                <img src="https://via.placeholder.com/640x400"/>
				                {% endif %}
							</a>
						</div>
						<div class="column large-17 medium-16 small-12 padding-small-none">
							<div class="descripcion">
								<div class="titulo-articulo">
									<h1 class="bold font-14 negro margin-bottom-none">{{r.attrs.titulo.data[0]|slice(0,20)~'...'}}</h1>
								</div>
								<div class="referencia">
									<span class="gris font-9 bold show-for-large">
									{% if idioma=='espanol' %}
					                    Referencia:
					                {% else %}
					                    Reference:
					                {% endif %} &nbsp;</span>
									<span class="gris font-9 bold hide-for-large">Ref: &nbsp;</span>
									<span class="referencia-contenido font-9 negro">{{r.attrs.referencia.data[0]}}</span>
								</div>
								<div class="color">
									<span class="gris font-9 bold">
									{% if idioma=='espanol' %}
					                    Color:
					                {% else %}
					                    Colour:
					                {% endif %} &nbsp;</span>
									<span class="referencia-contenido font-9 negro">{{r.color}}</span>
								</div>
								<div class="color">
									<span class="gris font-9 bold">
									{% if idioma=='espanol' %}
					                    Talla:
					                {% else %}
					                    Size:
					                {% endif %} &nbsp;</span>
									<span class="referencia-contenido font-9 negro upper">{{r.talla}}</span>
								</div>
								{% if r.attrs.descuento.data[0] %}
								<div class="color">
									<span class="gris font-9 bold">
									{% if idioma=='espanol' %}
					                    Descuento:
					                {% else %}
					                    Sale:
					                {% endif %} &nbsp;</span>
									<span class="referencia-contenido font-9 negro upper">{{r.attrs.descuento.data[0]}}</span>
								</div>
								{% endif %}
								<!-- <div class="descripcion-articulo show-for-medium">
									<p class="negro font-9">
										{{r.attrs.description_corta.data[0] | raw}}
									</p>
								</div> -->
							</div>
						</div>
						<!-- CANTIDAD EN SMALL -->
						<div class="column small-12 padding-right-small-none hide-for-medium">
							<div class="categoria">
								<div class="tag">
									<div class="titulo-articulo">
										<span class="negro bold font-10">
											{% if idioma=='espanol' %}
							                    Cantidad:
							                {% else %}
							                    Quantity:
							                {% endif %}
					            		</span>
									</div>
									<div class="input">
										<input disabled type="text" placeholder="10" value="{{r.unidades}}" class="unidades_{{r.item_id}}_{{r.color}}_{{r.talla}}" style="display:inline;"
										onkeypress="CarroLateral.modificarUnidades({item_id:{{r.item_id}},variacion:'{{r.variacion}}', unidades:$('.unidades_{{r.item_id}}_{{r.color}}_{{r.talla}}').val(), color:'{{r.color}}', talla: '{{r.talla}}' });"
										/>
										<div class="botones">
											<div class="mas">
												<a onclick="CarroLateral.masUnidades({item_id:{{r.item_id}}, variacion:'{{r.variacion}}', unidades:$('.unidades_{{r.item_id}}_{{r.color}}_{{r.talla}}').val(), color:'{{r.color}}', talla: '{{r.talla}}' });" ><img src="{{template_url}}/img/boton-mas.png" alt="" title="" width="24" /></a>
											</div>
											<div class="menos">
												<a onclick="CarroLateral.menosUnidades({item_id:{{r.item_id}},  variacion:'{{r.variacion}}', unidades:$('.unidades_{{r.item_id}}_{{r.color}}_{{r.talla}}').val(), color:'{{r.color}}', talla:'{{r.talla}}' });"><img src="{{template_url}}/img/boton-menos.png" alt="" title="" width="24" /></a>
											</div>
										</div>
									</div>
									<!-- <span class="mensaje show-for-large">Para más productos digite la cantidad y haga clic en actualizar</span> -->
								</div>
							</div>
						</div>
						<!-- FIN CANTIDAD EN SMALL -->
					</div>
				</div>


				<!-- CANTIDAD EN LARGE -->
				<div class="column large-4 medium-5 show-for-medium">
					<div class="categoria">
						<div class="tag">
							<div class="titulo-articulo">
								<span class="negro bold font-10">
									{% if idioma=='espanol' %}
							            Cantidad:
							        {% else %}
							            Quantity:
							        {% endif %}
								</span>
							</div>
							<div class="input">
								<input disabled type="text" placeholder="10" value="{{r.unidades}}" class="unidades_{{r.item_id}}_{{r.color}}_{{r.talla}}" style="display:inline;"
								onkeypress="CarroLateral.modificarUnidades({item_id:{{r.item_id}}, variacion:'{{r.variacion}}', unidades:$('.unidades_{{r.item_id}}_{{r.color}}_{{r.talla}}').val(), color:'{{r.color}}', talla: '{{r.talla}}' });"
								/>
								<div class="botones">
									<div class="mas">
										<a onclick="CarroLateral.masUnidades({item_id:{{r.item_id}}, variacion:'{{r.variacion}}', unidades:$('.unidades_{{r.item_id}}_{{r.color}}_{{r.talla}}').val(), color:'{{r.color}}', talla: '{{r.talla}}' });" ><img src="{{template_url}}/img/boton-mas.png" alt="" title="" width="24" /></a>
									</div>
									<div class="menos">
										<a onclick="CarroLateral.menosUnidades({item_id:{{r.item_id}},  variacion:'{{r.variacion}}', unidades:$('.unidades_{{r.item_id}}_{{r.color}}_{{r.talla}}').val(), color:'{{r.color}}', talla: '{{r.talla}}' });"><img src="{{template_url}}/img/boton-menos.png" alt="" title="" width="24" /></a>
									</div>
								</div>

							</div>
							<!-- <span class="mensaje show-for-large">Para más productos digite la cantidad y haga clic en actualizar</span> -->
						</div>
					</div>
				</div>
				<!-- FIN CANTIDAD EN LARGE -->

				<div class="column large-8 medium-10 small-24">
					<div class="row">
						<div class="column large-19 medium-18 small-12 text-right padding-left-small-none">
							<div class="precio-articulo-unitario ">
								<div class="titulo-articulo">
									<span class="negro bold font-10">
										{% if idioma=='espanol' %}
							                Precio Und:
							            {% else %}
							                Price Unt:
							            {% endif %}
									</span>
								</div>
								{% if moneda=='cop' %}
								<span class="font-10 negro">${{r.precio|number_format()}} COP</span><br/>
								{% else %}
								<span class="font-10 negro">${{r.precio_usd|number_format(2, ',', '.')}} USD</span>
								{% endif %}
							</div>
						</div>
						<div class="column large-5 medium-6 small-12 text-right padding-right-none padding-right-small-none">
							<!-- <div class="precio-articulo">
								<div class="titulo-articulo">
									<span class="negro bold font-10">Total</span>
								</div>
								<span class="font-10 negro">${{r.subtotal|number_format()}} COP</span><br/>
								<span class="font-10 negro">${{r.total_usd|number_format()}} USD</span>
							</div> -->
							<div class="text-center text-medium-right margin-top-small-1">
								<a onclick="CarroLateral.removerItem({{r.item_id}},'{{r.color}}', '{{r.talla}}');"  title="Eliminar producto">
									<img src="{{template_url}}/img/papelera.png" alt="" width="20">
								</a>
							</div>
						</div>
					</div>

					<!-- <div class="row">
						<div class="column large-24 medium-24 small-24">
							<div class="inventario-articulo">
								<span>Sólo quedan 6 unidad(es)</span>
								<br>
								Éste producto se puede agotar si otro usuario
								lo compra antes.
							</div>
						</div>
					</div> -->
				</div>
			</div>
			<!-- <div class="row">
				<div class="column large-24 medium-24 small-24">
					<div class="text-center text-medium-right margin-top-small-1">
						<a onclick="CarroLateral.removerItem({{r.item_id}},'{{r.color}}', '{{r.talla}}');" class="button bg-rojo"  title="Eliminar producto">
							{% if idioma=='espanol' %}
							    Eliminar
							{% else %}
							    Delete
							{% endif %}
						</a>
					</div>
					<br class="hide-for-medium"/>
				</div>
			</div> -->
		</div>
	</div>

	{% endfor %}
	<br/>
	<div class="row">
		<div class="column small-24">
				<h2 class="font-14 primary bold">
					{% if idioma=='espanol' %}
						Total Carrito
					{% else %}
						Total Cart
					{% endif %}
				</h2>
		</div>
		<div class="column small-24">
				<hr/>
		</div>
  </div>

	<div class="row">

		<div class="column small-24 medium-offset-10 large-offset-12 medium-14 large-12 padding-none">
			<div class="row">
				<div class="valores">
					<div class="row">
						<div class="column ">
							<div class="text-right small-centered">
								<span class="font-10 primary bold">
									{% if idioma=='espanol' %}
				                        * El valor del envio se calcula automaticamente
				                    {% else %}
				                        * The shipping value is calculated automatically
				                    {% endif %}
								</span>
							</div>
						</div>
					</div>
					<!-- <div class="row">
						<div class="column large-offset-7 medium-offset-7 medium-7 small-12 text-right">
							<span class="label-subtotal bold gris">Envío</span>
						</div>
						<div class="column medium-9 small-12 text-right">
							<span class="content-subtotal bold">${{enc.total_transporte|number_format()}} COP</span>
						</div>
					</div> -->
					<!-- <div class="row">
						<div class="column large-offset-7 medium-offset-7 medium-7 small-12 text-right">
							<span class="label-subtotal bold gris">Subtotal</span>
						</div>
						<div class="column medium-9 small-12 text-right">
							<span class="content-subtotal bold">${{enc.subtotal|number_format()}} COP</span>
						</div>
					</div> -->
					{% if moneda=='cop' %}
					<div class="row">
						
						<div class="column large-offset-7 large-8 medium-offset-5 medium-8 small-12 text-right">
							<span class="label-total bold gris">Total</span>
						</div>
						<div class="column large-9 medium-10 small-12 text-right">
							<span class="content-total bold">${{enc.total|number_format()}} COP</span>
						</div>
					</div>
					{% else %}
					<div class="row">
						<div class="column large-offset-7 large-8 medium-offset-5 medium-8 small-12 text-right">
							<span class="label-total bold gris">Total USD</span>
						</div>
						<div class="column large-9 medium-10 small-12 text-right">
							<span class="content-total bold">${{enc.total_usd|number_format(2, ',', '.')}} USD</span>
						</div>
					</div>
					{% endif %}
				</div>
			</div>

			<div class="row continuar-comprando">
				<div class="column small-24">
					<div class="text-right small-centered">
						{% if idioma=='espanol' %}
							<a class="negro underline no-hover-negro" onclick="location='{{site_url}}/catalogo';" type="" name="agregar-codigo">Continuar comprando</a>&nbsp;<br/><br/>
				        	<button class="button medium" onclick="Carro.onFinalizar();" type="" name="agregar-codigo">Realizar el pago</button>
				        {% else %}
				            <a class="negro underline no-hover-negro" onclick="location='{{site_url}}/catalogo';" type="" name="agregar-codigo">Continue buying</a>&nbsp;<br/><br/>
				        	<button class="button medium" onclick="Carro.onFinalizar();" type="" name="agregar-codigo">Make the payment</button>
				        {% endif %}
						
					</div>
				</div>
			</div>
		</div>
	</div>


	{% else %}
	<div class="row">
		<div class="column small-24 text-center">
			<img class="margin-top-5" src="{{template_url}}/img/icon-carro-grande.png" title="No hay accesorios en el carro de compras" alt="No hay accesorios en el carro de compras"/>
			<h1 class="font-12 gris margin-bottom-5 margin-top-1">No hay productos en el carrito.</h1>
		</div>
	</div>
	{% endif %}

	<br/>

</div>
{% include 'footer.php' %}

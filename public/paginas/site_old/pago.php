{% include 'header.php' %}
<script>{{ source('predeterminado.js') }}</script>
<script>{{ source('pago.js') }}</script>
<div class="content">
    <div class="row">
        <div class="column small-24">
            <h2 class="font-14 primary bold">Checkout</h2>
        </div>
        <div class="column small-24">
            <hr/>
        </div>
    </div>

    <form id="form-compra" action="" class="form" onsubmit="return false;">
        <div class="row">
            <div class="column small-24 large-12 padding-none">
              <div class="row">
                  <div class="column small-24">
                    <label for="font-9 negro bold">
                      {% if idioma=='espanol' %}
                      Elige una forma de pago
                      {% else %} 
                      Choose a payment method   
                      {% endif %}
                    
                    </label>
                    <select name="id_forma_pago">
                      {% for r in formas_pago %}
                      <option value="{{r.data}}">{{r.label}}</option>
                      {% endfor %}
                    </select>
                  </div>
              </div>
              <div class="row margin-top-1">
                  <div class="column small-24">
                      <h2 class="font-14 primary bold">
                        {% if idioma=='espanol' %}
                        Detalle de facturación
                        {% else %} 
                        Billing detail  
                        {% endif %}
                      </h2>
                  </div>
                  <div class="column small-24">
                      <hr/>
                  </div>
              </div>
              <div class="row">
                  {% if si.usuario.attrs.empresa.data[0] %}
                  <div class="column small-24">
                      <label class="font-9 negro bold">
                        {% if idioma=='espanol' %}
                        Empresa
                        {% else %} 
                        Company   
                        {% endif %}
                      </label>
                      <input type="text" name="com_empresa" value="{{si.usuario.attrs.empresa.data[0]}}"/>
                  </div>
                  {% endif %}
                  <div class="column small-24 large-12">
                      <label class="font-9 negro bold">
                        {% if idioma=='espanol' %}
                        Nombres
                        {% else %} 
                        Names   
                        {% endif %}
                      </label>
                      <input type="text" name="com_nombres" value="{{si.usuario.attrs.nombres.data[0]}}"/>
                  </div>
                  <div class="column small-24 large-12">
                      <label class="font-9 negro bold">
                        {% if idioma=='espanol' %}
                        Apellidos
                        {% else %} 
                        Surnames   
                        {% endif %}
                      </label>
                      <input type="text" name="com_apellidos" value="{{si.usuario.attrs.apellidos.data[0]}}"/>
                  </div>
              </div>
              <div class="row">
                  <div class="column small-24 large-12">
                      <label class="font-9 negro bold">
                        {% if idioma=='espanol' %}
                        Tipo de documento
                        {% else %} 
                        Document type   
                        {% endif %}
                      </label>
                      <select class="" name="tipo_identificacion">
                          <option value="-1">Seleccione</option>
                          <option value="cc" selected>Cedula de Ciudadania</option>
                      </select>
                  </div>
                  <div class="column small-24 large-12">
                      <label class="font-9 negro bold">
                        {% if idioma=='espanol' %}
                        Número de documento
                        {% else %} 
                        Document number   
                        {% endif %}
                      </label>
                      <input type="text" name="com_identificacion" value="{{si.usuario.attrs.identificacion.data[0]}}"/>
                  </div>
              </div>
              <div class="row">
                  <div class="column small-24 large-12">
                      <label class="font-9 negro bold">
                        {% if idioma=='espanol' %}
                        Número celular
                        {% else %} 
                        Cell phone number   
                        {% endif %}
                      </label>
                      <input type="text" name="com_telefono_celular" value="{{si.usuario.attrs.telefono_celular.data[0]}}"/>
                  </div>
                  <div class="column small-24 large-12">
                      <label class="font-9 negro bold">
                        {% if idioma=='espanol' %}
                        Teléfono
                        {% else %} 
                        Phone   
                        {% endif %}
                      </label>
                      <input type="text" name="com_telefono_fijo" value="{{si.usuario.attrs.telefono_fijo.data[0]}}">
                  </div>
              </div>
              <div class="row">
                  <div class="column small-24 large-8">
                      <label class="font-9 negro bold">
                        {% if idioma=='espanol' %}
                        Pais
                        {% else %} 
                        Country   
                        {% endif %}
                      </label>
                      <select name="com_id_pais">
                          {% for r in paises %}
                          <option value="{{r.data}}" {{ r.data==si.usuario.attrs.pais.data[0] ? "selected":"" }} >{{r.label}}</option>
                          {% endfor %}
                      </select>
                  </div>
                  <div class="column small-24 large-8">
                      <label class="font-9 negro bold">
                        {% if idioma=='espanol' %}
                        Departamento
                        {% else %} 
                        Department   
                        {% endif %}
                      </label>
                      <select name="com_id_departamento">
                          {% for r in departamentos %}
                          <option value="{{r.data}}" {{ r.data==si.usuario.attrs.departamento.data[0] ? "selected":"" }}>{{r.label}}</option>
                          {% endfor %}
                      </select>
                  </div>
                  <div class="column small-24 large-8">
                      <label class="font-9 negro bold">
                        {% if idioma=='espanol' %}
                        Ciudad
                        {% else %} 
                        City   
                        {% endif %}
                      </label>
                      <select name="com_id_ciudad">
                          {% for r in ciudades %}
                          <option value="{{r.data}}" {{ r.data==si.usuario.attrs.ciudad.data[0] ? "selected":"" }}>{{r.label}}</option>
                          {% endfor %}
                      </select>
                  </div>
              </div>
              <div class="row">
                  <div class="column small-24">
                      <label class="font-9 negro bold">
                        {% if idioma=='espanol' %}
                        Dirección
                        {% else %} 
                        Address   
                        {% endif %}
                      </label>
                      <input type="text" name="com_direccion" value="{{si.usuario.attrs.direccion.data[0]}}">
                  </div>
              </div>
              <div class="row">
                  <div class="column small-24">
                      <label class="font-9 negro bold">
                        &nbsp;
                      </label>
                      <label for="datos-entrega">
                        <span class="font-13 gris bold">
                          {% if idioma=='espanol' %}
                          ¿Enviar a la misma dirección?
                          {% else %} 
                          Send to the same address   
                          {% endif %}
                        </span>&nbsp;&nbsp;
                        <input type="checkbox" id="datos-entrega" onclick="Pago.mismaDireccion(this);">
                      </label>
                  </div>
              </div>

                <div class="row">
                    <div class="column small-24">
                        <h2 class="font-14 primary bold detalle-entrega">
                          {% if idioma=='espanol' %}
                          Detalle de Entrega
                          {% else %} 
                          Delivery Detail   
                          {% endif %}
                        </h2>
                    </div>
                    <div class="column small-24">
                        <hr/>
                    </div>
                </div>
                <div class="row">
                    <div class="column small-24 large-24">
                        <label class="font-9 negro bold">
                          {% if idioma=='espanol' %}
                          Identificacion
                          {% else %} 
                          ID   
                          {% endif %}
                        </label>
                        <input type="text" name="ent_identificacion" value=""/>
                    </div>
                </div>
                <div class="row">
                    <div class="column small-24 large-12">
                        <label class="font-9 negro bold">
                          {% if idioma=='espanol' %}
                          Nombres
                          {% else %} 
                          Names   
                          {% endif %}
                        </label>
                        <input type="text" name="ent_nombres" value=""/>
                    </div>
                    <div class="column small-24 large-12">
                        <label class="font-9 negro bold">
                          {% if idioma=='espanol' %}
                          Apellidos
                          {% else %} 
                          Surnames   
                          {% endif %}
                        </label>
                        <input type="text" name="ent_apellidos" value=""/>
                    </div>
                </div>
                <div class="row">
                    <div class="column small-24 large-8">
                        <label class="font-9 negro bold">
                          {% if idioma=='espanol' %}
                          Pais
                          {% else %} 
                          Country   
                          {% endif %}
                        </label>
                        <select name="ent_id_pais" onchange="Pago.pais();">
                            {% for r in paises %}
                            <option value="{{r.data}}" >{{r.label}}</option>
                            {% endfor %}
                        </select>
                    </div>
                    <div class="column small-24 large-8">
                        <label class="font-9 negro bold">
                          {% if idioma=='espanol' %}
                          Departamento
                          {% else %} 
                          Department   
                          {% endif %}
                        </label>
                        <select name="ent_id_departamento" onchange="Pago.departamento();">
                            {% for r in departamentos %}
                            <option value="{{r.data}}">{{r.label}}</option>
                            {% endfor %}
                        </select>
                    </div>
                    <div class="column small-24 large-8">
                        <label class="font-9 negro bold">
                          {% if idioma=='espanol' %}
                          Ciudad
                          {% else %} 
                          City   
                          {% endif %}
                        </label>
                        <select name="ent_id_ciudad" onchange="Pago.ciudad();">
                            {% for r in ciudades %}
                            <option value="{{r.data}}">{{r.label}}</option>
                            {% endfor %}
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="column small-24 large-12">
                        <label class="font-9 negro bold">
                          {% if idioma=='espanol' %}
                          Teléfono
                          {% else %} 
                          Phone   
                          {% endif %}
                        </label>
                        <input type="text" name="ent_telefono_celular" value=""/>
                        <input type="hidden" name="ent_telefono" value=""/>
                    </div>
                    <div class="column small-24 large-12">
                        <label class="font-9 negro bold">
                          {% if idioma=='espanol' %}
                          Dirección
                          {% else %} 
                          Address   
                          {% endif %}
                        </label>
                        <input type="text" name="ent_direccion" value=""/>
                    </div>
                </div>
            </div>
            <div class="column small-24 large-12 padding-none">

              <div class="row">
                <div class="column small-24">
                  {% if idioma=='espanol' %}
                    {{envio.attrs.contenido.data[0]|raw}}
                  {% else %} 
                    {{envio.attrs.contenido_ingles.data[0]|raw}}  
                  {% endif %}
                </div>
              </div>
              
            </div>
        </div>
        <div class="row">

          <div class="column small-24 medium-offset-10 large-offset-12 medium-14 large-12 padding-none">
      			<div class="row">
      				<div class="valores">
      					<!-- <div class="row">
      						<div class="column large-offset-16 medium-offset-16 medium-4 small-12">
      							<span class="label-subtotal">Iva</span>
      						</div>
      						<div class="column medium-4 small-12 text-right">
      							<span class="content-subtotal">${{enc.total_iva|number_format()}}</span>
      						</div>
      					</div> -->
                {% if pedido_enc.total_descuento > 0 %}
                <!-- <div class="row">
      						<div class="column large-offset-9 medium-offset-9 medium-6 small-12">
      							<span class="label-subtotal gris bold">Descuento</span>
      						</div>
      						<div class="column medium-8 small-12 text-right">
      							<span class="content-subtotal bold">${{pedido_enc.total_descuento|number_format(0)}} COP</span>
      						</div>
      					</div> -->
                {% endif %}
                {% if moneda=='cop' %}
                <div class="row">
                  <div class="column large-offset-9 medium-offset-9 medium-6 small-12">
                    <span class="label-subtotal gris bold">
                        Envío
                    </span>
                  </div>
                  <div class="column medium-8 small-12 text-right">
                    <span class="content-subtotal bold" id="envio">${{pedido_enc.total_transporte|number_format(0)}} COP</span>
                  </div>
                </div>
                {% endif %}
      					<!-- <div class="row">
      						<div class="column large-offset-9 medium-offset-9 medium-6 small-12">
      							<span class="label-subtotal gris bold">Subtotal</span>
      						</div>
      						<div class="column medium-8 small-12 text-right">
      							<span class="content-subtotal bold" id="subtotal">${{pedido_enc.subtotal|number_format(0)}} COP</span>
      						</div>
      					</div> -->
                {% if moneda=='cop' %}
      					<div class="row">
      						<div class="column large-offset-9 medium-offset-9 medium-6 small-12">
      							<span class="label-total gris bold">Total</span>
      						</div>
      						<div class="column medium-8 small-12 text-right">
      							<span class="content-total bold" id="total">${{pedido_enc.total|number_format(0)}} COP</span>
      						</div>
      					</div>
                {% else %} 
                <div class="row">
                  <div class="column large-offset-9 medium-offset-9 medium-6 small-12">
                    <span class="label-total gris bold">Total USD</span>
                  </div>
                  <div class="column medium-8 small-12 text-right">
                    <span class="content-total bold" id="total_usd">${{pedido_enc.total_usd|number_format(2, ',', '.')}} USD</span>
                  </div>
                </div>
                {% endif %}
                <div class="row">
                    <div class="column large-24">
                      <div class="text-right small-centered">
                        <button class="button" onclick="Pago.finalizar();">
                          {% if idioma=='espanol' %}
                            Ir A Pagar
                          {% else %} 
                            Go Pay   
                          {% endif %}
                        </button>
                      </div>
                    </div>
                </div>
      				</div>
      			</div>
          </div>

        </div>
    </form>

</div>
{% include 'footer.php' %}

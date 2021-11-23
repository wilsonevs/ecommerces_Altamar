{% include 'altamar/src/app/includes/head.php' %}
{% include 'altamar/src/app/includes/header.php' %}
<script>{{ source('/altamar/src/public/js/carro_lateral.js') }}</script>
<script>{{ source('/altamar/src/public/js/carro.js') }}</script>
    <!-- Cart Items -->
    {% if det %}
    <div class="container cart">
      <table>
        <tr>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Precio</th>
        </tr>

        {% for r in det %}
        <tr>
          <td>
            <div class="cart-info">
              <img src="{{r.attrs.imagen_1.data[0]|image}}" alt="{{r.attrs.titulo.data[0]}}" />
              <div>
                <p>{{r.attrs.titulo.data[0]|slice(0,20)~'...'}}</p>
                <span>Referencia: {{r.attrs.referencia.data[0]}}</span>
                <br />
                <a onclick="CarroLateral.removerItem({{r.item_id}},'{{r.color}}', '{{r.talla}}');"  title="Eliminar producto"><b>Eliminar</b></a>
              </div>
            </div>
          </td>
          <td>
            <div class="input">
              
              <div class="botones">
                
                <div class="menos" style="display:inline;">
                  <a onclick="CarroLateral.menosUnidades({item_id:{{r.item_id}},  variacion:'{{r.variacion}}', unidades:$('.unidades_{{r.item_id}}_{{r.color}}_{{r.talla}}').val(), color:'{{r.color}}', talla:'{{r.talla}}' });"><img src="{{template_url}}/altamar/src/public/img/boton-menos.png" class="icon-cart"/></a>
                </div>
                <input id="input-cart" disabled type="text" placeholder="10" value="{{r.unidades}}" class="unidades_{{r.item_id}}_{{r.color}}_{{r.talla}}"
                onkeypress="CarroLateral.modificarUnidades({item_id:{{r.item_id}},variacion:'{{r.variacion}}', unidades:$('.unidades_{{r.item_id}}_{{r.color}}_{{r.talla}}').val(), color:'{{r.color}}', talla: '{{r.talla}}' });"
                />
                <div class="mas" style="display:inline;">
                  <a onclick="CarroLateral.masUnidades({item_id:{{r.item_id}}, variacion:'{{r.variacion}}', unidades:$('.unidades_{{r.item_id}}_{{r.color}}_{{r.talla}}').val(), color:'{{r.color}}', talla: '{{r.talla}}' });" ><img src="{{template_url}}/altamar/src/public/img/boton-mas.png" class="icon-cart"/></a>
                </div>

              </div>
            </div>

          </td>
          <td>${{r.precio|number_format()}}</td>
        </tr>
        {% endfor %}

      </table>


<!-- 
======================
tabla
====================== 
-->

      <div class="total-price">
        <table>
          <tr>
            <td><b>Subtotal</b></td>
            <td>${{enc.subtotal|number_format()}}</td>
          </tr>
          <tr>
            <td><b>Valor Total</b></td>
            <td>${{enc.total|number_format()}}</td>
          </tr>
        </table>
        <a href="#" onclick="Carro.onFinalizar();" class="checkout btn">Confirmar pedido</a>
      </div>
    </div>

    {% else %}

    <div class="section">
      <div class="login-form">
        <h1 class="title">No tienes productos en el carrito todavia.</h1>
        <div class="buttons">
          <button type="button" class="signupbtn" onclick="window.location.href = '{{site_url}}/tienda';">Ir a Comprar</button>
        </div>
      </div>
    </div>

    {% endif %}


{% include 'altamar/src/app/includes/footer.php' %}
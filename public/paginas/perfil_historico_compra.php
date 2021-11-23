{% include 'altamar/src/app/includes/head.php' %}
{% include 'altamar/src/app/includes/header.php' %}



<!-- Cart Items -->
<div class="container cart">

  {% if historico_compras | length == 0 %}
  
  <div class="section">
    <div class="login-form">
      <h1 class="title">No tienes compras todavia.</h1>
      <div class="buttons">
        <button type="button" class="signupbtn" onclick="window.location.href = '{{site_url}}/tienda';">Ir a Comprar</button>
      </div>
    </div>
  </div>
  
  {% else %}

  <table>
    <tr>
      <th>Producto</th>
      <th>Total</th>
    </tr>

    {% for r in historico_compras %}
    <tr>
      <td>
        <div class="cart-info">
          <a href="{{site_url}}/pago-realizado?cart={{r.id_carro}}"><img src="{{r.item.attrs.imagen_1.data[0]|image}}" alt="" class="cart-info__img" /></a>
          <div>
            <p><b>{{r.item.attrs.titulo.data[0]|slice(0,20)~'...'}}</b></p>
            <span><b>Pedido #:</b> {{r.id_pedido}}</span>
            <br />
            <span><b>Fecha de compra:</b> {{r.fechahora}}</span>
            <br />
            <span><b>Forma de pago:</b> {{r.nombre_forma_pago}}</span>
            <br />
            <span><b>Precio x Unidad:</b> COP ${{r.precio|number_format()}}</span>
            <br />
            <span><b>Unidades:</b> {{r.unidades}}</span>
            <br />
            <span><b>Estado:</b> {{r.estado}}</span>
            <br />
          </div>
        </div>
      </td>
      <td><b>$</b>{{r.total|number_format()}}</td>
    </tr>
    {% endfor %}
  
  </table>

  {% endif %}

</div>

{% include 'altamar/src/app/includes/footer.php' %}
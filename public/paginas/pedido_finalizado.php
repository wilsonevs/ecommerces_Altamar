{% include 'altamar/src/app/includes/head.php' %}
{% include 'altamar/src/app/includes/header.php' %}


<!-- 
======================
FORMULARIO
====================== 
-->


<section>

  <div class="container cart">
    <div class="inner contact">

      <h1>Pago Realizado</h1>
      <div class="linea_larga"></div>
      <div style="height: 55px;"></div>

      {% if forma_pago.attrs.codigo.data[0] == 'contraentrega'  %}
      <div class="informacion_entrega">
        <h1 class="informacion_entrega__titulo">Contra Entrega</h1>
        <p class="informacion_entrega__parrafo">Gracias por comprar, recuerda que el tiempo estimado de entrega es de 1 día habil.</p>
        <div style="height: 55px;"></div>
        <h2 class="h2_pedido">Pedido</h2>
        <h3>{{enc.id_pedido}}</h3>
      </div>
      {% else %}


      <div class="informacion_entrega">
        <img src="src/public/img/altamar_Qr.png"class="informacion_entrega__titulo img_qr" alt="">
        <h2 class="informacion_entrega__ahorro">Ahorros Bancolombia</h2>
        <p class="informacion_entrega_cuenta"><b>{{forma_pago.attrs.numero_de_cuenta.data[0]}}</b></p>
        <p class="informacion_entrega__parrafo">Realiza tu transferencia y envíanos el soporte de pago con el número del pedido a nuestro Whatsapp  
        <a href="https://wa.me/{{plantilla.attrs.whatsapp.data[0]}}" target="_blank" class="informacion_entrega__a">
          <span><i class="fab fa-whatsapp"></i></span> <b>{{plantilla.attrs.whatsapp.data[0]}}</b>
        </a>
        ó a nuestro correo electrónico 
        <a href="mailto:{{plantilla.attrs.email.data[0]}}" class="informacion_entrega__a">
              <span><i class="far fa-envelope"></i></span><b>{{plantilla.attrs.email.data[0]}}</b>
        </a>
      </p>
        <div style="height: 55px;"></div>
        <h2 class="h2_pedido">Pedido</h2>
        <h3>{{enc.id_pedido}}</h3>
      </div>
      {% endif %}


      <div class="linea_larga"></div>
      <div class="informacion_cliente">
        <div class="informacion_cliente__info">
          <h2>Facturar a:</h2>
          <p>{{enc.com_nombres}} {{enc.com_apellidos}}</p>
          <p>{{enc.com_identificacion}}</p>
          <p>{{enc.com_telefono_fijo}} / {{enc.com_telefono_celular}}</p>
          <p>{{enc.com_ciudad}}</p>
          <p>{{enc.com_direccion}}</p>
        </div>
        <div class="informacion_cliente__info">
          <h2>Enviar a:</h2>
          <p>{{enc.ent_nombres}} {{enc.ent_apellidos}}</p>
          <p>{{enc.ent_identificacion}}</p>
          <p>{{enc.ent_telefono}} / {{enc.ent_telefono_celular}}</p>
          <p>{{enc.ent_ciudad}}</p>
          <p>{{enc.ent_direccion}}</p>
        </div>
        <div class="informacion_cliente__info">
          <h2>Fecha Compra:</h2>
          <p>{{enc.i_ts}}</p>
        </div>
      </div>

      <!-- 
      ======================
      TABLA VALOR
      ====================== 
      -->

      <div class="total-price">
        <table>
          <tr>
            <td><b>Envío</b></td>
            <td><b>$</b>{{enc.envio|number_format()}}</td>
          </tr>
          <tr>
            <td><b>Subtotal</b></td>
            <td><b>$</b>{{enc.subtotal|number_format()}}</td>
          </tr>
          <tr>
            <td><b>Valor Total</b></td>
            <td><b>$</b>{{enc.total|number_format()}}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  </div>
</section>


{% include 'altamar/src/app/includes/footer.php' %}
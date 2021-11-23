{% include 'altamar/src/app/includes/head.php' %}
{% include 'altamar/src/app/includes/header.php' %}
<script>{{ source('/altamar/src/public/js/pago.js') }}</script>

<!-- 
======================
FORMULARIO
====================== 
-->

<div class="container cart">

  <div class="inner contact">
    <!-- Form Area -->
    <div class="contact-form"></div>
    <!-- Form -->
    <form id="form-compra" action="" onsubmit="return false;">

      <h1>Verificar</h1>
      <div class="linea_larga"></div>
      <div class="distancia_alto"></div>
      <div class="contenido_uno">

        <div class="wrapper">

          <div class="wrapper_form_uno">

            <h2>Detalle de facturación</h2>
            <h2></h2>

            <select name="id_forma_pago" id="id_forma_pago" class="color_select">
              <option value="-1" selected>Forma de pago</option>
              {% for r in formas_pago %}
                <option value="{{r.data}}">{{r.label}}</option>
              {% endfor %}
            </select>

            <input type="text" name="com_nombres" id="com_nombres" class="form text_negro" placeholder="Nombre" value="{{si.usuario.attrs.nombres.data[0]}}"/>

            <input type="text" name="com_apellidos" id="com_apellidos" class="form text_negro" placeholder="Apellidos" value="{{si.usuario.attrs.apellidos.data[0]}}" />

            <select name="tipo_identificacion" id="tipo_identificacion" class="color_select">
              <option value="" selected>Tipo de documento</option>
              <option value="cc" {{ "cc"==si.usuario.attrs.tipo_identificacion.data[0] ? "selected":"" }}>Cedula de Ciudadania</option>
              <option value="tx" {{ "tx"==si.usuario.attrs.tipo_identificacion.data[0] ? "selected":"" }}>Tarjeta de extranjería </option>
              <option value="ce" {{ "ce"==si.usuario.attrs.tipo_identificacion.data[0] ? "selected":"" }}>Cédula de extranjería</option>
              <option value="nit" {{ "nit"==si.usuario.attrs.tipo_identificacion.data[0] ? "selected":"" }}>NIT</option>
              <option value="pass" {{ "pass"==si.usuario.attrs.tipo_identificacion.data[0] ? "selected":"" }}>Pasaporte</option>
            </select>

            <input type="text" name="com_identificacion" id="com_identificacion" class="form text_negro" placeholder="Número de documento" value="{{si.usuario.attrs.identificacion.data[0]}}"/>

            <input type="text" name="com_telefono_celular" id="com_telefono_celular" class="form text_negro" placeholder="Número Celular" value="{{si.usuario.attrs.telefono_celular.data[0]}}"/>

            <input type="text" name="com_telefono_fijo" class="form text_negro" placeholder="Número Fijo" value="{{si.usuario.attrs.telefono_fijo.data[0]}}">

            <select name="com_id_pais" id="com_id_pais" class="color_select">
              {% for r in paises %}
                <option value="{{r.data}}" {{ r.data==si.usuario.attrs.pais.data[0] ? "selected":"" }} >{{r.label}}</option>
              {% endfor %}
            </select>

            <select name="com_id_departamento" id="com_id_departamento" class="color_select">
              {% for r in departamentos %}
                <option value="{{r.data}}" {{ r.data==si.usuario.attrs.departamento.data[0] ? "selected":"" }}>{{r.label}}</option>
              {% endfor %}
            </select>

            <select name="com_id_ciudad" id="com_id_ciudad" class="color_select">
              {% for r in ciudades %}
                <option value="{{r.data}}" {{ r.data==si.usuario.attrs.ciudad.data[0] ? "selected":"" }}>{{r.label}}</option>
              {% endfor %}
            </select>

            <input type="text" name="com_direccion" id="com_direccion" class="form text_negro" placeholder="Dirección" value="{{si.usuario.attrs.direccion.data[0]}}"/>
            <br>

            <label>¿Enviar a la misma dirección? Sí <input type="checkbox" id="datos-entrega" onclick="Pago.mismaDireccion(this);"> </label><br>
            <h2>Dirección de entrega</h2>
            <h2></h2>

            <input type="text" name="ent_nombres" id="ent_nombres" class="form text_negro" placeholder="Nombre" />

            <input type="text" name="ent_apellidos" id="ent_apellidos" class="form text_negro" placeholder="Apellidos" />

            <input type="text" name="ent_identificacion" id="ent_identificacion" class="form text_negro" placeholder="Número de documento" />

            <input type="text" name="ent_telefono_celular" id="ent_telefono_celular" class="form text_negro" placeholder="Número Celular" />

            <input type="text" name="ent_telefono" id="ent_telefono" class="form text_negro" placeholder="Número Fijo" />

            <select class="color_select" name="ent_id_pais" onchange="Pago.pais();">
              {% for r in paises %}
                <option value="{{r.data}}" >{{r.label}}</option>
              {% endfor %}
            </select>

            <select name="ent_id_departamento" onchange="Pago.departamento();" class="color_select">
              {% for r in departamentos %}
                <option value="{{r.data}}">{{r.label}}</option>
              {% endfor %}
            </select>

            <select name="ent_id_ciudad" onchange="Pago.ciudad();" class="color_select">
              {% for r in ciudades %}
                <option value="{{r.data}}">{{r.label}}</option>
              {% endfor %}
            </select>

            <input type="text" name="ent_direccion" id="ent_direccion" class="form text_negro" placeholder="Dirección" />


          </div>

          <div class="wrapper_form_dos">
            <p>
            <ul>
              <ol><b>1.</b>Tiempo de entrega máximo 24 horas en el Valle de Aburrá a partir del momento de confirmación de orden de compra y confirmación de pago.</ol>
              <ol><b>2.</b>Domingos y feriados, no cuentan dentro del tiempo de entrega.</ol>
              <ol><b>3.</b>Devoluciones por garantía gratis.</ol>
              <ol><b>4.</b>Por compras iguales o superiores a $50.000 COP, el domicilio no tiene costo en la ciudad de Medellín, por compras inferiores a $50,000 COP, el envío tendrá un valor de domicilio de 5,000 pesos adicionales, para despachos en otras ciudades el cliente asumirá los gastos de envío.</ol>
              <ol><b>5.</b>Al momento de realizar su proceso de orden de compra, el costo de envío aparecerá como un rubro adicional a los productos seleccionados y se mostrará en detalle al finalizar su pedido.</ol>
              <ol><b>6.</b>Debido a dificultades logísticas que se puedan presentar para realizar envíos, YOKELO se reserva el derecho a cancelar su pedido y/o aplicar costos adicionales de envío adicionales a dicho pedido. Nuestro servicio de atención al cliente le notificará la cancelación y/o la aplicación de estas novedades, si se llegan a dar, después de que realice su pedido.</ol>
              <ol><b>Escríbenos</b></ol>
              <ol><b>Correo:</b> carnesfriasyokelo@gmail.com</ol>
              <ol><b>Contáctanos al celular: +57 </b>320 778 82 69</ol>

            </ul>
            </p>
          </div>

        </div>
      </div>

    

    <div class="distancia_alto"></div>







    <!-- 
      ======================
      TABLA VALOR
      ====================== 
      -->

      <div class="total-price">
        <table>
          <tr>
            <td><b>Subtotal</b></td>
            <td>${{pedido_enc.subtotal|number_format(0)}}</td>
          </tr>
          <tr>
            <td><b>Envío</b></td>
            <td>${{pedido_enc.total_transporte|number_format(0)}}</td>
          </tr>
          <tr>
            <td><b>Valor Total</b></td>
            <td>${{pedido_enc.total|number_format(0)}}</td>
          </tr>
        </table>
        <a href="#" onclick="Pago.finalizar();">Ir a pagar</a>
      </div>
    </form>
  </div>
</div>

{% include 'altamar/src/app/includes/footer.php' %}
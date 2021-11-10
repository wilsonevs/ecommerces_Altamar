<?php require_once("src/app/includes/head.php") ?>
<?php require_once("src/app/includes/header.php") ?>


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
    <form id="contact-us" method="post" action="#">

      <h1>Verificar</h1>
      <div class="linea_larga"></div>
      <div class="distancia_alto"></div>
      <div class="contenido_uno">

        <div class="wrapper">

          <div class="wrapper_form_uno">

            <h2>Detalle de facturación</h2>
            <h2></h2>
            <input type="text" name="nombre" id="nombre" required="required" class="form" placeholder="Nombre" />
            <input type="text" name="apellido" id="apellido" required="required" class="form" placeholder="Apellidos" />
            <select name="tipo_cedula" id="tipo_cedula" class="color_select">
              <option value="-1" selected>Tipo de documento</option>
              <option value="cc">Cedula de Ciudadania</option>
              <option value="tx">Tarjeta de extranjería </option>
              <option value="ce">Cédula de extranjería</option>
              <option value="nit">NIT</option>
              <option value="pass">Pasaporte</option>
            </select>

            <input type="text" name="numero_documento" id="numero_documento" required="required" class="form" placeholder="Número de documento" />
            <input type="text" name="celular" id="celular" required="required" class="form" placeholder="Número Celular o Teléfono" />
            <select name="pais" id="pais" class="color_select">
              <option value="-2" selected>País</option>
              <option value="co">Colombia</option>
              <option value="ec">Ecuador</option>
              <option value="eu">Estados Unidos</option>
              <option value="pr">Peru</option>
              <option value="vn">Venezuela</option>
            </select>

            <select name="departamento" id="departamento" class="color_select">
              <option value="-3" selected>Departamentos</option>
              <option value="Antioquia">Antioquia</option>
              <option value="Bogotá">Bogotá</option>
              <option value="Caldas">Caldas</option>
              <option value="Santander">Santander</option>
              <option value="Valle del Cauca">Valle del Cauca</option>
            </select>
            <select name="ciudad" id="ciudad" class="color_select">
              <option value="-4" selected>Ciudad</option>
              <option value="medellin">Medellín</option>
              <option value="Barranquilla">Barranquilla</option>
              <option value="Bello">Bello</option>
              <option value="Bello">Caldas</option>
              <option value="Cali">Cali</option>
              <option value="Bello">Copacabana</option>
              <option value="Bello">Girardota</option>
              <option value="Bello">Envigado</option>
              <option value="Bello">Itagui</option>
              <option value="Bello">La Estrella</option>
              <option value="Bello">Sabaneta</option>
              <option value="Bello">Rionegro</option>
            </select>
            <input type="text" name="barrio" id="barrio" required="required" class="form" placeholder="Barrio" />

            <input type="text" name="direccion" id="direccion" required="required" class="form" placeholder="Dirección" />

            <label>¿Enviar a la misma dirección? Sí <input type="checkbox" id="cbox1" value="first_checkbox"> </label><br>
            <h2>Dirección de entrega</h2>
            <h2></h2>

            <input type="text" name="nombre_uno" id="nombre_uno" required="required" class="form" placeholder="Nombre" />
            <input type="text" name="apellido_uno" id="apellido_uno" required="required" class="form" placeholder="Apellidos" />
            <select name="tipo_cedula" id="tipo_cedula" class="color_select">
              <option value="-1" selected>Tipo de documento</option>
              <option value="cc">Cedula de Ciudadania</option>
              <option value="tx">Tarjeta de extranjería </option>
              <option value="ce">Cédula de extranjería</option>
              <option value="nit">NIT</option>
              <option value="pass">Pasaporte</option>
            </select>

            <input type="text" name="numero_documento_uno" id="numero_documento_uno" required="required" class="form" placeholder="Número de documento" />
            <input type="text" name="celular_uno" id="celular_uno" required="required" class="form" placeholder="Número Celular o Teléfono" />
            <select name="pais" id="pais" class="color_select">
              <option value="-2" selected>País</option>
              <option value="co">Colombia</option>
              <option value="ec">Ecuador</option>
              <option value="eu">Estados Unidos</option>
              <option value="pr">Peru</option>
              <option value="vn">Venezuela</option>
            </select>

            <select name="departamento" id="departamento" class="color_select">
              <option value="-3" selected>Departamentos</option>
              <option value="Antioquia">Antioquia</option>
              <option value="Bogotá">Bogotá</option>
              <option value="Caldas">Caldas</option>
              <option value="Santander">Santander</option>
              <option value="Valle del Cauca">Valle del Cauca</option>
            </select>
            <select name="ciudad" id="ciudad" class="color_select">
              <option value="-4" selected>Ciudad</option>
              <option value="medellin">Medellín</option>
              <option value="Barranquilla">Barranquilla</option>
              <option value="Bello">Bello</option>
              <option value="Bello">Caldas</option>
              <option value="Cali">Cali</option>
              <option value="Bello">Copacabana</option>
              <option value="Bello">Girardota</option>
              <option value="Bello">Envigado</option>
              <option value="Bello">Itagui</option>
              <option value="Bello">La Estrella</option>
              <option value="Bello">Sabaneta</option>
              <option value="Bello">Rionegro</option>
            </select>
            <input type="text" name="barrio_uno" id="barrio_uno" required="required" class="form" placeholder="Barrio" />

            <input type="text" name="direccion_uno" id="direccion_uno" required="required" class="form" placeholder="Dirección" />


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

    </form>

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
          <td>$200</td>
        </tr>
        <tr>
          <td><b>Envío</b></td>
          <td>$50</td>
        </tr>
        <tr>
          <td><b>Valor Total</b></td>
          <td>$300</td>
        </tr>
      </table>
      <a href="pago_realizado_transferencia.php" class="checkout btn">Ir a pagar</a>
    </div>
  </div>
</div>

<?php include_once("src/app/includes/footer.php") ?>
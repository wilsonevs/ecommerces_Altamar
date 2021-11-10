<?php require_once("src/app/includes/head.php") ?>
<?php require_once("src/app/includes/header.php") ?>


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

      <div class="informacion_entrega">
        <h1 class="informacion_entrega__titulo">Contra Entrega</h1>
        <p class="informacion_entrega__parrafo">Gracias por comprar, recuerda que el tiempo estimado de entrega es de 1 día habil.</p>
        <div style="height: 55px;"></div>
        <h2 class="h2_pedido">Pedido</h2>
        <h3>00000-89</h3>
      </div>


      <div class="linea_larga"></div>
      <div class="informacion_cliente">
        <div class="informacion_cliente__info">
          <h2>Facturar a:</h2>
          <p>WILSON EMILIO VALENCIA</p>
          <p>101764536888</p>
          <p>3510134 / 3058258679</p>
          <p>Colombia / Antioquia / Medellin / Guayabal</p>
          <p>CALLE 92 #57-29 PISO 1</p>
        </div>
        <div class="informacion_cliente__info">
          <h2>Enviar a:</h2>
          <p>WILSON EMILIO VALENCIA</p>
          <p>101764536888</p>
          <p>3510134 / 3058258679</p>
          <p>Colombia / Antioquia / Medellin / Guayabal</p>
          <p>CALLE 92 #57-29 PISO 1</p>
        </div>
        <div class="informacion_cliente__info">
          <h2>Fecha Compra:</h2>
          <p>2021-11-06 19:20:02</p>
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
            <td><b>$</b>50</td>
          </tr>
          <tr>
            <td><b>Subtotal</b></td>
            <td><b>$</b>250</td>
          </tr>
          <tr>
            <td><b>Valor Total</b></td>
            <td><b>$</b>300</td>
          </tr>
        </table>
        <a href="pago_realizado.php" class="checkout btn">Ir a pagar</a>
      </div>
    </div>
  </div>

  </div>
</section>


<?php include_once("src/app/includes/footer.php") ?>
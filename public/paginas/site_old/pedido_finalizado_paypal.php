{% include 'header.php' %}
<div class="content">

  <div class="row">
    <div class="column small-24 text-center">
      <h2 class="font-12 negro bold">Redireccionando a la pasarela de pagos</h2>
    </div>
    <div class="column small-24 text-center">
      <img src="{{template_url}}/img/icon-cargando.gif" alt="" />
    </div>
  </div>

  <!-- CAMPOS EN PRODUCCIÓN -->
  <form name="paypal" method="post" action="{{url}}" style="display:none;">

    <input name="cmd" type="hidden" value="_cart" />
    <input name="upload" type="hidden" value="1" />
    <input name="business" type="hidden" value="{{correo_paypal}}" />
    <input name="shopping_url" type="hidden" value="{{shopping_url}}" />
    <input name="currency_code" type="hidden" value="{{currency}}" />
    <input name="return" type="hidden" value="{{responseUrl}}" />
    <input name="notify_url" type="hidden" value="{{confirmationUrl}}" />

    <input name="rm" type="hidden" value="2" />
    <input name="item_number_1" type="hidden" value="{{referenceCode}}" />
    <input name="item_name_1" type="hidden" value="{{description}}" />
    <input name="amount_1" type="hidden" value="{{amount}}" />
    <input name="quantity_1" type="hidden" value="1" />
    <input type="hidden" name="cbt" value="Return to The Store">
    <input type="hidden" name="lc" value="US">
  </form>
  <script>
  $(document).ready(function(){
    setTimeout(function(){
      $('form[name=paypal]').submit();
    },1000);
  });
  </script>

</div>
{% include 'footer.php' %}

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
  <form name="payu" method="post" action="{{url}}" style="display:none;">
    <input name="merchantId" type="hidden"  value="{{merchantId}}"   />
    <input name="accountId"    type="hidden"  value="{{accountId}}" />
    <input name="description"   type="hidden"  value="{{description}}"  />
    <input name="referenceCode" type="hidden"  value="{{referenceCode}}" />
    <input name="amount"        type="hidden"  value="{{amount}}"   />
    <input name="tax"           type="hidden"  value="{{tax}}"  />
    <input name="taxReturnBase" type="hidden"  value="{{taxReturnBase}}" />
    <input name="currency"      type="hidden"  value="{{currency}}" />
    <input name="signature"     type="hidden"  value="{{signature}}"  />
    <input name="test"          type="hidden"  value="{{text}}" />
    <input name="buyerFullName"    type="hidden"  value="{{buyerFullName}}" />
    <input name="buyerEmail"    type="hidden"  value="{{buyerEmail}}" />
    <input name="responseUrl"    type="hidden"  value="{{responseUrl}}" />
    <input name="confirmationUrl"    type="hidden"  value="{{confirmationUrl}}" />
    <input name="Submit" type="submit"  value="Enviar" />
  </form>
  <script>
  $(document).ready(function(){
    setTimeout(function(){
      $('form[name=payu]').submit();
    },1000);
  });
  </script>

</div>
{% include 'footer.php' %}

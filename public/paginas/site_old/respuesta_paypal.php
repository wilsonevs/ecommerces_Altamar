{% include 'header.php' %}
<div class="content">
  <div class="row">
    <div class="column small-24 text-center">
      <h1 class="font-18 bold gris-oscuro">{{estado}}</h1>
      {% if img == 1 %}
      <img src="{{template_url}}/img/icon-succes-pago.png" alt="" width="60"/>
      {% elseif img == 2 %}
      <img src="{{template_url}}/img/icon-error-pago.png" alt="" width="60"/>
      {% elseif img == 3 %}
      <img src="{{template_url}}/img/icon-error-pago.png" alt="" width="60"/>
      {% else %}
      <img src="{{template_url}}/img/icon-pendiente-pago.png" alt="" width="60"/>
      {% endif %}
    </div>
  </div>
  <br/>
  <br/>
  <div class="row">
    <div class="column small-24 text-center">
      <div class="font-14 gris">
        {% if idioma=='espanol' %}
          Codigo de Referencia
        {% else %}  
          Reference code
        {% endif %}
      </div>
      <div class="gris-oscuro font-16 bold">
        {{referenced_code}}
      </div>
      <br/>
      <div class="gris font-14">
        {% if idioma=='espanol' %}
          Metodo de Pago
        {% else %}  
          Payment method
        {% endif %}
      </div>
      <div class="gris-oscuro font-16">
        {% if idioma=='espanol' %}
         Tarjeta de credito y/o debito
        {% else %}  
          Credit and / or Debit card
        {% endif %}
      </div>
    </div>
  </div>
</div>
{% include 'footer.php' %}

{% include 'header.php' %}
<div class="content ">

  <div class="row pedido-header">
    <div class="column small-24 medium-12">
      {% if idioma=='espanol' %}
        <h1 class="titulo">Pago Realizado</h1>
      {% else %} 
        <h1 class="titulo">Payment Made</h1>   
      {% endif %}
    </div>
    <div class="column small-24 medium-12">
        <div class="text-right">
          <a class="web" href="{{web}}">{{web}}</a>
        </div>
    </div>
  </div>

  {% if forma_pago.attrs.codigo.data[0] == 'consignacion'  %}
    {% include 'part_consignacion.php' %}
  {% endif %}

  {% if forma_pago.attrs.codigo.data[0] == 'contraentrega'  %}
    {% include 'part_contraentrega.php' %}
  {% endif %}

  <br/>
  <br/>
  <div class="row pedido-header margin-top-2">
    <div class="column small-24">
      {% if idioma=='espanol' %}
        <h1 class="titulo">Factura</h1>
      {% else %} 
        <h1 class="titulo">Bill</h1>   
      {% endif %}
    </div>
  </div>
  <div class="row pedido-header margin-top-2">
    <div class="column small-24 medium-9 large-9">
      {% if idioma=='espanol' %}
        <p><span class="bold">Facturar a:</span></p>
      {% else %} 
        <p><span class="bold">Bill To:</span></p>
      {% endif %}
        <p>
          {{enc.com_nombres}} {{enc.com_apellidos}}<br/>
          {{enc.com_identificacion}}<br/>
          {{enc.com_telefono_fijo}} / {{enc.com_telefono_celular}}<br/>
          {{enc.com_ciudad}}<br/>
          {{enc.com_direccion}}
        </p>
    </div>
    <div class="column small-24 medium-9 large-9">
      {% if idioma=='espanol' %}
        <p><span class="bold">Enviar a</span></p>
      {% else %} 
        <p><span class="bold">Send To</span></p>
      {% endif %}
        <p>
          {{enc.ent_nombres}} {{enc.ent_apellidos}}<br/>
          {{enc.ent_identificacion}}<br/>
          {{enc.ent_telefono}} / {{enc.ent_telefono_celular}}<br/>
          {{enc.ent_ciudad}}<br/>
          {{enc.ent_direccion}}
        </p> 
    </div>
    <div class="column small-24 medium-6 large-6 medium-text-right">
        <p>
          {% if idioma=='espanol' %}
            <span class="bold">Fecha Compra</span>
          {% else %}
            <span class="bold">Purchase Date</span>
          {% endif %}
        </p>
        <p>
          {{enc.i_ts}}
        </p>
        
    </div>
  </div>
  <div class="row">
    <div class="column small-24 medium-offset-10 large-offset-12 medium-14 large-12 padding-none">
			<div class="row">
        <div class="column large-offset-7 large-8 medium-offset-5 medium-8 small-12 text-right">
          <span class="label-total bold gris">Total</span>
        </div>
        <div class="column large-9 medium-10 small-12 text-right">
          <span class="content-total bold">${{enc.total|number_format()}} COP</span>
        </div>
      </div>
      <div class="row">
        <div class="column large-offset-7 large-8 medium-offset-5 medium-8 small-12 text-right">
          <span class="label-total bold gris">Total USD</span>
        </div>
        <div class="column large-9 medium-10 small-12 text-right">
          <span class="content-total bold">${{enc.total_usd|number_format()}} USD</span>
        </div>
      </div>
    </div>
  </div>
</div>
{% include 'footer.php' %}

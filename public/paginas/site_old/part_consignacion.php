<div class="part_consignacion">
    <div class="row">
        <div class="column small-24">
            {% if idioma == 'espanol' %}
                {{forma_pago.attrs.contenido_finalizacion.data[0]|raw}}
            {% else %}
                {{forma_pago.attrs.contenido_finalizado_ingles.data[0]|raw}}
            {% endif %}
        </div>
    </div>
    <br/>
    <br/>
    <div class="row">
        <div class="column small-24 text-center">
            <h1 class="tipo_cuenta">
            {% if idioma == 'espanol' %}
                {{forma_pago.attrs.tipo_de_cuenta.data[0]}}
            {% else %}
                {{forma_pago.attrs.tipo_de_cuenta_ingles.data[0]}}
            {% endif %}
            
            </h1>
        </div>
        <div class="column small-24 text-center">
            <h1 class="cuenta">{{forma_pago.attrs.numero_de_cuenta.data[0]}}</h1>
        </div>
    </div>
</div>
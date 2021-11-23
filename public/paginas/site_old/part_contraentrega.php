<div class="part_contraentrega">
    <div class="row">
        <div class="column small-24 text-center">
            <h1 class="titulo">
                {% if idioma == 'espanol' %}
                    Contra Entrega
                {% else %}
                    Upon Delivery
                {% endif %}
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="column small-24">
            {% if idioma == 'espanol' %}
                {{forma_pago.attrs.contenido_finalizacion.data[0]|raw}}
            {% else %}
                {{forma_pago.attrs.contenido_finalizado_ingles.data[0]|raw}}
            {% endif %}
        </div>
    </div>
</div>
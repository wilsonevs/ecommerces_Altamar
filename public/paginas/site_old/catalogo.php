{% include 'header.php' %}
<div class="content">
    <div class="banner-descuentos">
        <!-- <img src="{{template_url}}/img/banner-oferta.png" alt="Ofertas"/> -->
    </div>
    <div class="row">
        <div class="column small-24 medium-6 large-6">
            {% include 'filtros_catalogo.php' %}
        </div>
        <div class="column small-24 medium-18 large-18 padding-right-none padding-left-none">

            {% if items.records|length > 0 %}
            <div class="row small-up-2 medium-up-2 large-up-3">
                {% for r in items.records  %}
                <div class="column ">
                    {% include 'item_producto.php' %}
                </div>
                {% endfor %}
            </div>
            <div class="row">
                <div class="columns small-24 text-center">
                    {{paginador_html|raw}}
                </div>
            </div>
            {% else %}
            <div class="row">
                <div class="columns small-24">
                    <div class="text-center">
                        <h1 class="font-16 verde bold margin-top-3">
                            {% if idioma=='espanol' %}
                            No Hay Ningun Producto Todavía.
                            {% else %}
                            There is no product yet.
                            {% endif %}
                        </h1>
                    </div>
                </div>
            </div>
            {% endif %}
        </div>
    </div>
    <br/>
</div>
{% include 'footer.php' %}

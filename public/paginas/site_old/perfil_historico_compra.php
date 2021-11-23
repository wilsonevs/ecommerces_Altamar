{% include 'header.php' %}
<script>{{ source('predeterminado.js') }}</script>
<div class="content">
    <div class="row">
        <div class="column small-24">
            <h2 class="font-14 gris bold">
            {% if idioma=='espanol' %}
                Histórico de compras
            {% else %}
                Purchase history
            {% endif %}
            </h2>
        </div>
        <div class="column small-24">
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="column medium-8 large-6 show-for-medium">
            {% include 'menu_interno.php' %}
        </div>
        <div class="column medium-16 large-18">

            {% if historico_compras | length == 0 %}
                <div class="text-center">
                    <h1 class="font-16 gris bold margin-top-3">                 
                    {% if idioma=='espanol' %}
                        No tienes compras todavia.
                    {% else %}
                        You have no purchases yet.
                    {% endif %}
                    </h1>
                    <a class="gris underline no-hover-gris" href="{{site_url}}/catalogo">
                    {% if idioma=='espanol' %}
                        Ir a comprar
                    {% else %}
                        Go shopping
                    {% endif %}
                    </a>
                </div>
            {% endif %}
            {% for r in historico_compras %}
            <div class="items">
                <div class="row">
                    <div class="column small-24 medium-7 large-6 padding-left-none padding-right-none">
                        {% if r.item.attrs.imagen_1.data[0] %}
                        <a href="{{site_url}}/catalogo/{{r.slug}}">
                            <img src="{{r.item.attrs.imagen_1.data[0]|image}}" alt="{{r.item.attrs.titulo.data[0]}}" title="{{r.item.attrs.titulo.data[0]}}" />
                        </a>
                        {% else %}
                          <img src="https://via.placeholder.com/640x400" alt="{{r.item.attrs.titulo.data[0]}}" title="{{r.item.attrs.titulo.data[0]}}" />
                        {% endif %}
                        <div class="estado text-center margin-top-1 margin-bottom-small-1">
                             <span class="blanco font-10">{{r.estado}}</span>
                        </div>
                    </div>
                    <div class="column small-24 medium-17 large-18 padding-small-none">
                        <div class="row">
                            <div class="column small-24">
                                <div class="titulo-articulo">
                                    <h1 class="bold font-12 negro margin-bottom-none">                                   
                                    {% if idioma=='espanol' %}
                                        {{r.item.attrs.titulo.data[0]|slice(0,20)~'...'}}
                                    {% else %}
                                        {{r.item.attrs.titulo_ingles.data[0]|slice(0,20)~'...'}}
                                    {% endif %}
                                    </h1>
                                </div>
                            </div>
                            <div class="column small-12">
                                <div class="referencia">
                                    <span class="gris font-8 bold">
                                    {% if idioma=='espanol' %}
                                        REFERENCIA
                                    {% else %}
                                        REFERENCE
                                    {% endif %}
                                    </span><br/>
                                    <span class="negro font-8">{{r.item.attrs.referencia.data[0]}}</span>
                                </div>
                            </div>
                            <div class="column small-12">
                                <div class="text-right">
                                    <span class="gris font-8 bold">
                                    {% if idioma=='espanol' %}
                                        PEDIDO
                                    {% else %}
                                        ORDER
                                    {% endif %}</span><br/>
                                    <span class="negro font-8">{{r.id_pedido}}</span>
                                </div>
                            </div>
                            <div class="column small-24 medium-24 large-12">
                                <div class="">
                                    <span class="gris font-8 bold">
                                    {% if idioma=='espanol' %}
                                        FECHA DE COMPRA
                                    {% else %}
                                        DATE OF PURCHASE
                                    {% endif %}
                                    </span><br/>
                                    <span class="negro font-8">{{r.fechahora}}</span>
                                </div>
                            </div>
                            <div class="column small-24 medium-24 large-12">
                                <div class="text-right-large">
                                    <span class="gris font-8 bold">
                                    {% if idioma=='espanol' %}
                                        FORMA DE PAGO
                                    {% else %}
                                        WAY TO PAY
                                    {% endif %}
                                    </span><br/>
                                    <span class="negro font-8">{{r.nombre_forma_pago}}</span>
                                </div>
                            </div>
                            <div class="column small-8 medium-8 large-12">
                                <div class="">
                                    <span class="gris font-8 bold">
                                    {% if idioma=='espanol' %}
                                        UNIDADES
                                    {% else %}
                                        UNITS
                                    {% endif %}</span><br/>
                                     <span class="negro font-8">{{r.unidades}}</span>
                                </div>
                            </div>
                            <div class="column small-16 medium-16 large-12">
                                <div class="text-right">
                                    <span class="gris font-8 bold">
                                    {% if idioma=='espanol' %}
                                        PRECIO X UND
                                    {% else %}
                                        PRICE X UNT
                                    {% endif %}
                                    </span><br/>
                                     <span class="negro font-8">COP ${{r.precio|number_format()}}</span><br/>
                                     <span class="negro font-8">USD ${{r.precio_usd|number_format()}}</span>
                                </div>
                            </div>
                            <!-- <div class="column small-24">
                                <div class="descripcion-articulo show-for-medium">
                                    <p class="negro font-8">
                                        {{r.item.attrs.description_corta.data[0] | raw}}
                                    </p>
                                </div>
                            </div> -->
                            <!-- <div class="column small-24">
                                <div class="text-right">
                                     <span class="gris font-8 bold">TOTAL TRANSPORTE: &nbsp;</span>
                                     <span class="negro font-8 bold">${{r.total_transporte|number_format()}}</span>
                                </div>
                            </div> -->
                            <div class="column small-24">
                                <div class="text-right margin-bottom-1">
                                     <span class="gris font-12 bold">TOTAL: &nbsp;</span>
                                     <span class="negro font-12 bold">COP ${{r.total|number_format()}}</span><br/>
                                     <span class="negro font-12 bold">USD ${{r.total_usd|number_format()}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {% endfor %}
        </div>
    </div>
</div>
{% include 'footer.php' %}

{% include 'header.php' %}
<script>{{ source('item_amp.js') }}</script>
<script>{{ source('carro_lateral.js') }}</script>
<div class="content">
  <div class="row">
    <div class="column small-24 medium-12 large-15">
      <div class="row" id="gallery">
        {% if ampliacion.attrs.descuento.data[0] %}
        <div class="descuento">
          {{ampliacion.attrs.descuento.data[0]}}
        </div>
        {% endif %}
        <div class="column small-4 gallery padding-none hide-for-medium">
          <div class="row" id="thumbs-small">
            {% for i in 1..4 %}
            {% if attribute(ampliacion.attrs, 'imagen_' ~ i).data[0] %}
            <div class="column small-24 imagenes padding-left-none">
              <img src="{{attribute(ampliacion.attrs, 'imagen_' ~ i).data[0]|image}}" alt="" title="" id="{{ i - 1 }}"/>
            </div>
            {% endif %}
            {% endfor %}
          </div>
        </div>
        <div class="column small-20 medium-24 gallery">
          <div id="panel">
            <div id="imagen-amp" class="owl-carousel owl-theme">
              {% for i in 1..3 %}
              {% if attribute(ampliacion.attrs, 'imagen_' ~ i).data[0] %}
              <div class="item cambio">
                <img src="{{attribute(ampliacion.attrs, 'imagen_' ~ i).data[0]|image}}" alt="" title="" id="{{ i - 1 }}" />
              </div>
              {% endif %}
              {% endfor %}
            </div>
          </div>
        </div>
        <div class="column small-24 gallery padding-none show-for-medium">
          <div class="row" id="thumbs">
            {% for i in 1..3 %}
            {% if attribute(ampliacion.attrs, 'imagen_' ~ i).data[0] %}
            <div class="column small-12 medium-8 large-6 imagenes end">
              <img src="{{attribute(ampliacion.attrs, 'imagen_' ~ i).data[0]|image}}" alt="" title="" id="{{ i - 1 }}"/>
            </div>
            {% endif %}
            {% endfor %}
          </div>
        </div>
      </div>
      <br/>

    </div>
    <div class="column small-24 medium-12 large-9">
      <h1 class="font-18 bold primary title">
        {% if idioma=='espanol' %}
          {{ampliacion.attrs.titulo.data[0]}}
        {% else %}
          {{ampliacion.attrs.titulo_ingles.data[0]}}
        {% endif %}
      </h1>
      <div class="padding-bottom-1">
        {% if moneda=='cop' %}
          <div class="">
            <!-- <span class="negro font-11 "> - {{ampliacion.attrs.descuento.data[0]}}%</span> -->
            {% if ampliacion.attrs.precio_anterior.data[0] > 0 %}
              <span class="gris font-11 line-through">COP: ${{ampliacion.attrs.precio_anterior.data[0]|number_format(0, ',', '.')}}</span>
            {% endif %}
            <span class="negro bold font-16">COP: ${{ampliacion.attrs.precio.data[0]|number_format(0, ',', '.')}}</span>
          </div>
        {% else %}
          <div class="">
            <!-- <span class="negro font-11 "> - {{ampliacion.attrs.descuento.data[0]}}%</span> -->
            {% if ampliacion.attrs.precio_anterior_usd.data[0] > 0 %}
              <span class="gris font-11 line-through">USD: ${{ampliacion.attrs.precio_anterior_usd.data[0]|number_format(2, ',', '.')}}</span>
            {% endif %}
            <span class="negro bold font-16">USD: ${{ampliacion.attrs.precio_usd.data[0]|number_format(2, ',', '.')}}</span>
          </div>
        {% endif %}
      </div>
      <form class="" action="" method="">
        <div class="">
          <label for="" class="font-10">
            {% if idioma=='espanol' %}
              Color
            {% else %}
              Colour
            {% endif %}
          </label>
          <select class="color margin-bottom-0" name="color" onchange="CarroLateral.onColor('{{ampliacion.slug}}');">
              {% if idioma=='espanol' %}
                <option value="">Seleccione</option>
              {% else %}
                <option value="">Select</option>
              {% endif %}
              {% for r in ampliacion.colores %}
                <option value="{{r}}">{{r|capitalize}}</option>
              {% endfor %}
          </select>
        </div>
        <br/>
        <div class="">
          <label for="" class="font-10">
            {% if idioma=='espanol' %}
              Talla
            {% else %}
              Size
            {% endif %}
          </label>
          <select class="color margin-bottom-0" name="talla">
              {% if idioma=='espanol' %}
                <option value="">Seleccione</option>
              {% else %}
                <option value="">Select</option>
              {% endif %}
              
          </select>
        </div>
        <br/>
        <div class="">
          <a class="button submit" onclick="CarroLateral.agregarItem({{ampliacion.item_id}});">
            {% if idioma=='espanol' %}
              Agregar al Carrito
            {% else %}             
              Add to cart
            {% endif %}
          </a>
        </div>

      </form>
      {% if idioma=='espanol' %}
        {% if ampliacion.attrs.descripcion_larga.data[0] %}
        <div class="descripcion">
          <h1 class="primary">
            Descripción
          </h1>
        </div>
        <div>
          {{ampliacion.attrs.descripcion_larga.data[0]|raw}}
        </div>
        {% endif %}
      {% else %} 
        {% if ampliacion.attrs.descripcion_larga_ingles.data[0] %}
        <div class="descripcion">
          <h1 class="primary">
            Description
          </h1>
        </div>
        <div>
          {{ampliacion.attrs.descripcion_larga_ingles.data[0]|raw}}
        </div>
        {% endif %}
      {% endif %}
    </div>
  </div>
</div>
<br/>
{% include 'footer.php' %}

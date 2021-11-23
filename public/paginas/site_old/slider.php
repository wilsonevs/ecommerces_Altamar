<div id="slider" class="owl-carousel owl-theme ">

    {% for r in slider %}
      <div class="item">
        <a href="{{r.attrs.url.data[0]}}" target="{{r.attrs.abrir_en.data[0]}}">
          {% if r.attrs.imagen_pequena.data[0] %}
          <img src="{{r.attrs.imagen_pequena.data[0]|image}}" class="hide-for-medium" />
          {% else %}
          <img src="https://via.placeholder.com/640x200" class="hide-for-medium"/>
          {% endif %}

          {% if r.attrs.imagen_mediana.data[0] %}
          <img src="{{r.attrs.imagen_mediana.data[0]|image}}" class="show-for-medium hide-for-large" />
          {% else %}
          <img src="https://via.placeholder.com/1023x300" class="show-for-medium hide-for-large"/>
          {% endif %}

          {% if r.attrs.imagen_grande.data[0] %}
          <img src="{{r.attrs.imagen_grande.data[0]|image}}" class="show-for-large" />
          {% else %}
          <img src="https://via.placeholder.com/1900x400" class="show-for-large"/>
          {% endif %}

          <!-- <img data-interchange="[{{r.attrs.imagen_pequena.data[0]|image}}, small],[{{r.attrs.imagen_mediana.data[0]|image}}, medium],[{{r.attrs.imagen_grande.data[0]|image}}, large]" alt=""> -->

        </a>
      </div>
    {% endfor %}


</div>
<div class="modo-pago text-center">
  <a class="">
    <img src="{{template_url}}/img/icon-dinero.png" alt="" />
    {% if idioma=='espanol' %}
      Todos los metodos de pago
    {% else %}
      All payment methods
    {% endif %} 
  </a>
</div>

{% include 'altamar/src/app/includes/head.php' %}
{% include 'altamar/src/app/includes/header.php' %}
<script>{{ source('/altamar/src/public/js/carro_lateral.js') }}</script>

<!-- 
=================
Hero
=================  
-->

<div class="hero">
  <div class="row">
    <div class="swiper-container slider-1">
      <div class="swiper-wrapper">
        {% for r in slider %}
        <div class="swiper-slide">
          <a href="{{r.attrs.enlace.data[0]}}" target="{{r.attrs.abrir_en.data[0]}}">
            <img src="{{r.attrs.imagen_grande.data[0]|image}}" alt="" />
          </a>
          <div class="content">
            {{r.attrs.promocion.data[0]|raw}}
            <a href="{{r.attrs.enlace.data[0]}}" target="{{r.attrs.abrir_en.data[0]}}">Compra ya</a>
          </div>
        </div>
        {% endfor %}
      </div>
    </div>
  </div>


<!--
======================
Carrusel Navegación
====================== 
-->

<div class="arrows d-flex">
        <div class="swiper-prev d-flex">
          <i class="bx bx-chevrons-left swiper-icon"></i>
        </div>
        <div class="swiper-next d-flex">
          <i class="bx bx-chevrons-right swiper-icon"></i>
        </div>
    </div>
  </div>


<!-- 
======================
Facility 
====================== 
-->

<section class="facility section" id="facility">
  <div class="facility-contianer container">
    <!-- 1 -->
    <div class="facility-box">
      <div class="facility-icon">
        <i class="fas fa-plane"></i>
      </div>
      <p>ENVÍO A NIVEL NACIONAL</p>
    </div>
    <!-- 2 -->
    <div class="facility-box">
      <div class="facility-icon">
        <i class="fas fa-credit-card"></i>
      </div>
      <p>100% GARANTIZADO</p>
    </div>
    <!-- 3 -->
    <div class="facility-box">
      <div class="facility-icon">
        <i class="far fa-credit-card"></i>
      </div>
      <p>DIFERENTES METODOS DE PAGÓ</p>
    </div>
    <!-- 4 -->
    <div class="facility-box">
      <div class="facility-icon">
        <i class="fas fa-headset"></i>
      </div>
      <p>24/7 ONLINE SUPPORT</p>
    </div>

  </div>
</section>


<!-- 
======================
Productos 
====================== 
-->

<!-- Featured -->
{% if items_des|length > 0 %}
  <section class="section featured">
    <div class="title">
      <h2>Featured Products</h2>
      <span>Select from the premium product brands and save plenty money</span>
    </div>

    <div class="row container">
      <div class="swiper-container slider-2">
        <div class="swiper-wrapper">

          {% for r in items_des %}
          <div class="swiper-slide">
            <div class="product">
              <div class="img-container">
                <a href="{{site_url}}/catalogo/{{r.slug}}">
                  <img src="{{r.attrs.imagen_1.data[0]|image}}" alt="" />
                </a>
                {% if r.attrs.descuento.data[0] > 0 %}
                <span class="discount">{{r.attrs.descuento.data[0]}}%</span>
                {% endif %}
                <div class="addCart" onclick="CarroLateral.agregarItem({{r.item_id}});">
                  <i class="fas fa-shopping-cart"></i>
                </div>
              </div>
              <div class="bottom">
                <a href="{{site_url}}/catalogo/{{r.slug}}">{{r.attrs.titulo.data[0]}}</a>
                <div class="price">
                  <span>${{r.attrs.precio.data[0]|number_format(0, ',', '.')}}</span>
                  {% if r.attrs.precio_anterior.data[0] > 0 %}
                  <span class="cancel">${{r.attrs.precio_anterior.data[0]|number_format(0, ',', '.')}}</span>
                  {% endif %}
                </div>
              </div>
            </div>
          </div>
          {% endfor %}

        </div>
      </div>
    </div>
    <!-- 
======================
CAROUSEL NAVIGATION 
====================== 
-->

     <!-- Carousel Navigation -->
    <div class="arrows d-flex">
       <div class="custom-next d-flex">
          <i class="bx bx-chevrons-right swiper-icon"></i>
        </div>
        <div class="custom-prev d-flex">
          <i class="bx bx-chevrons-left swiper-icon"></i>
        </div>
    </div>
  </section>

{% endif %}
<!-- 
======================
Varios Productos 
====================== 
-->
{% if items_new|length > 0 %}
<section class="section products">
  
  <div class="title">
    <h2>Nuevos Productos</h2>
    <span>Select from the premium product marcas and save plenty money</span>
  </div>

  <div class="product-layout">

      {% for r in items_new %}
      <div class="product">
        <div class="img-container">
          <a href="{{site_url}}/catalogo/{{r.slug}}">
            <img src="{{r.attrs.imagen_1.data[0]|image}}" alt="" />
          </a>
          {% if r.attrs.descuento.data[0] > 0 %}
          <span class="discount">{{r.attrs.descuento.data[0]}}%</span>
          {% endif %}
          <div class="addCart" onclick="CarroLateral.agregarItem({{r.item_id}});">
            <i class="fas fa-shopping-cart"></i>
          </div>
        </div>
        <div class="bottom">
          <a href="{{site_url}}/catalogo/{{r.slug}}">{{r.attrs.titulo.data[0]}}</a>
          <div class="price">
            <span>${{r.attrs.precio.data[0]|number_format(0, ',', '.')}}</span>
            {% if r.attrs.precio_anterior.data[0] > 0 %}
            <span class="cancel">${{r.attrs.precio_anterior.data[0]|number_format(0, ',', '.')}}</span>
            {% endif %}
          </div>
        </div>
      </div>
      {% endfor %}

  </div>
</section>

{% endif %}
<!-- 
======================
Testimonios 
====================== 
-->

<section class="section">
  <div class="title">
    <h2>Testimonios</h2>
    <span>Select from the premium product marcas and save plenty money</span>
  </div>
  <div class="testimonio-center container">
    {% for r in testimonios %}
    <div class="testimonio">
      <span>&ldquo;</span>
      <p>
        {{r.attrs.descripcion.data[0]}}
      </p>
      <div class="raking">

        {% if r.attrs.calificacion.data[0] == 1 %}
          <i class="bx bxs-star"></i>
          <i class="bx bx-star"></i>
          <i class="bx bx-star"></i>
          <i class="bx bx-star"></i>
          <i class="bx bx-star"></i>
        {% endif %}

        {% if r.attrs.calificacion.data[0] == 2 %}
          <i class="bx bxs-star"></i>
          <i class="bx bxs-star"></i>
          <i class="bx bx-star"></i>
          <i class="bx bx-star"></i>
          <i class="bx bx-star"></i>
        {% endif %}

        {% if r.attrs.calificacion.data[0] == 3 %}
          <i class="bx bxs-star"></i>
          <i class="bx bxs-star"></i>
          <i class="bx bxs-star"></i>
          <i class="bx bx-star"></i>
          <i class="bx bx-star"></i>
        {% endif %}


        {% if r.attrs.calificacion.data[0] == 4 %}
          <i class="bx bxs-star"></i>
          <i class="bx bxs-star"></i>
          <i class="bx bxs-star"></i>
          <i class="bx bxs-star"></i>
          <i class="bx bx-star"></i>
        {% endif %}


        {% if r.attrs.calificacion.data[0] == 5 %}
          <i class="bx bxs-star"></i>
          <i class="bx bxs-star"></i>
          <i class="bx bxs-star"></i>
          <i class="bx bxs-star"></i>
          <i class="bx bxs-star"></i>
        {% endif %}

      </div>
      <div class="img-cover">
        <img src="{{r.attrs.imagen.data[0]|image}}" />
      </div>
      <h4>{{r.attrs.nombre.data[0]}}</h4>
    </div>
    {% endfor %}
  </div>
</section>

<!-- 
======================
Marcas
====================== 
-->

<section class="section marcas">
  <div class="title">
    <h2>Nuestros Aliados</h2>
    <span>Seleccione entre las marcas de productos premium y ahorre mucho dinero</span>
  </div>

  <div class="brand-layout container">
    <div class="swiper-container slider-3">
      <div class="swiper-wrapper">

        {% for r in aliados %}

          <div class="swiper-slide">
            <img src="{{ r.attrs.imagen.data[0]|image}}" alt="{{r.attrs.titulo.data[0]}}">
          </div>

        {% endfor %}
      </div>
    </div>
  </div>
</section>


{% include 'altamar/src/app/includes/footer.php' %}
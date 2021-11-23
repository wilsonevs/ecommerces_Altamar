{% include 'altamar/src/app/includes/head.php' %}
{% include 'altamar/src/app/includes/header.php' %}


<!-- PRODUCTS -->

<section class="section products">
  <div class="products-layout container">
    <div class="col-1-of-4">
      <div>
        <div class="block-title">
          <h3>Categoria</h3>
        </div>


        <ul class="block-content">
          {% for r in categorias_items %}
          <li>
              <a href="{{site_url}}/tienda?categoria={{r.attrs.titulo.data[0]|lower}}" title="{{r.attrs.titulo.data[0]}}" class="{% if categoria == r.attrs.titulo.data[0]|lower %} bold {% endif %}">{{r.attrs.titulo.data[0]}}</a>
          </li>
          {% endfor %}
          <li>
              <a href="{{site_url}}/tienda?categoria=" class="{% if categoria == '' %} bold {% endif %}">
                Todas
              </a>
          </li>

        </ul>
      </div>
<!-- Rango de precios -->
      <div class="continer__wrapper_rango">
        <form action="{{site_url}}/tienda" method="get">
          <div class="wrapper_rango">
            <div class="block-title">
              <h3>Rango Precio</h3>
            </div>
            <div class="values">
              <span id="range1">
                0
              </span>
              <span> &dash; </span>
              <span id="range2">
                100
              </span>
            </div>
            <div class="container_rango">
              <div class="slider-track"></div>
              <input type="range" min="0" max="999999" name="desde" value="{{filtros.desde}}" id="slider-1" oninput="slideOne()">
              <input type="range" min="0" max="999999" name="hasta" value="{{filtros.hasta}}" id="slider-2" oninput="slideTwo()">
              <input type="hidden" name="categoria" value="{{categoria}}">
              <input type="hidden" name="orderby" value="{{filtros.orderby}}">
              <input type="hidden" name="order" value="{{filtros.order}}">
              <input type="hidden" name="establecer" value="1">
            </div>
            <div class="buttons xbutton-establecer">
              <button type="submit" class="signupbtn">Establecer</button>
            </div>
          </div>
        </form>
      </div>

    </div>
    <div class="col-3-of-4">
      <form id="form-id" action="{{site_url}}/tienda" method="get">
        <div class="item">
          <label for="orderby">Ordenar por</label>
          <select name="orderby" id="orderby">
            <option value="titulo" {% if filtros.orderby == 'titulo' %} selected {% endif %}>Nombre</option>
            <option value="precio" {% if filtros.orderby == 'precio' %} selected {% endif %}>Precio</option>
          </select>
        </div>
        <div class="item">
          <label for="order">Ordenar</label>
          <select name="order" id="order">
            <option value="asc" {% if filtros.order == 'asc' %} selected {% endif %}>Ascender</option>
            <option value="desc" {% if filtros.order == 'desc' %} selected {% endif %}>Descender</option>
          </select>
        </div>
        <input type="hidden" name="desde" value="{{filtros.desde}}">
        <input type="hidden" name="hasta" value="{{filtros.hasta}}">
        <input type="hidden" name="categoria" value="{{categoria}}">
        <input type="hidden" name="establecer" value="1">
        <a onclick="document.getElementById('form-id').submit();">Filtrar</a>
      </form>

      <!-- 
            ======================
            Productos 
            ====================== 
            -->
      {% if items.records|length > 0 %}
      <div class="product-layout">

        {% for r in items.records  %}
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

      <!-- PAGINATION -->
      <!-- <ul class="pagination">
        <span class="last">« Atras</span>
        <span>1</span>
        <span>2</span>
        <span class="icon">››</span>
        <span class="last">Siguiente »</span>
      </ul> -->

      {{paginador_html|raw}}


      {% endif %}

    </div>
  </div>
</section>

{% include 'altamar/src/app/includes/footer.php' %}
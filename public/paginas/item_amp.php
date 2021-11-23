{% include 'altamar/src/app/includes/head.php' %}
{% include 'altamar/src/app/includes/header.php' %}
<script>{{ source('/altamar/src/public/js/carro_lateral.js') }}</script>
  <!-- Product Details -->
  <section class="section product-detail">
    <div class="details container">
      <div class="left">
        <div class="main">
          <img src="{{ampliacion.attrs.imagen_1.data[0]|image}}" alt="" />
        </div>
        <div class="thumbnails">
          <div class="thumbnail">
            <img src="{{ampliacion.attrs.imagen_1.data[0]|image}}" alt="" />
          </div>
          <div class="thumbnail">
            <img src="{{ampliacion.attrs.imagen_2.data[0]|image}}" alt="" />
          </div>
          <div class="thumbnail">
            <img src="{{ampliacion.attrs.imagen_3.data[0]|image}}" alt="" />
          </div>
          <div class="thumbnail">
            <img src="{{ampliacion.attrs.imagen_4.data[0]|image}}" alt="" />
          </div>
        </div>
      </div>
      <div class="right">
        <h1>{{ampliacion.attrs.titulo.data[0]}}</h1>
        <div class="price"><b>Precio: $</b>{{ampliacion.attrs.precio.data[0]|number_format(0, ',', '.')}}</div>
        <!-- <form>
          <div>
            <select>
              <option value="Select Quantity" selected disabled>
                Select Quantity
              </option>
              <option value="1">32</option>
              <option value="2">42</option>
              <option value="3">52</option>
              <option value="4">62</option>
            </select>
            <span><i class="fas fa-chevron-down"></i></span>
          </div>
        </form> -->

        <form class="form">
          <div class="grid_form">
            <div class="grid_form_dos">
              <a href="{{site_url}}/tienda" class=" price">Seguir Comprando</a>
              <a href="#" class="price" onclick="CarroLateral.agregarItem({{ampliacion.item_id}});">Agregar al carrito</a>
            </div>
          </div>
        </form>
        <h3>Descripción</h3>
        <p>
          {{ampliacion.attrs.descripcion_larga.data[0]}}
        </p>
      </div>
    </div>
  </section>

  <!-- Related productos -->

  <section class="section related-products">
    <div class="title">
      <h2>Productos Relacionados</h2>
    </div>
    <div class="product-layout container">

      {% for r in items %}
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


{% include 'altamar/src/app/includes/footer.php' %}
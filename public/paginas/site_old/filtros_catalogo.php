<div class="filtros_catalogo">
  <div class="accordion" data-accordion data-multi-expand="true">

    {% if generos_items %}
    <div class="accordion-item is-active" data-accordion-item>
        <a href="#" title="search" class="accordion-title categoria font-12 bold">
            {% if idioma=='espanol' %}
              Genero
            {% else %}
              Gender
            {% endif %}
            <img class="icon-filtro hide-for-large" src="{{template_url}}/img/icon-filtro.png" title="search" alt="search" />
        </a>
        <div class="accordion-content scroll" data-tab-content>

            <ul class="menu vertical marcas">
                {% for r in generos_items %}
                <li>
                    <a href="{{site_url}}/catalogo?genero={{r.attrs.titulo.data[0] | lower}}&categoria={{categoria}}" title="{{r.attrs.titulo.data[0]}}" class="{% if genero == r.attrs.titulo.data[0]|lower %} primary bold {% endif %}">
                      {% if idioma=='espanol' %}
                      {{r.attrs.titulo.data[0]}}
                      {% else %}
                      {{r.attrs.titulo_ingles.data[0]}}
                      {% endif %} 
                  </a>
                </li>
                {% endfor %}
                <li>
                  <a href="{{site_url}}/catalogo?genero=&categoria={{categoria}}" class="{% if genero == '' %} primary bold {% endif %}">
                    {% if idioma=='espanol' %}
                      Todos
                    {% else %}
                      Everybody
                    {% endif %}
                  </a>
                </li>
            </ul>
        </div>
    </div>
    {% endif %}

      {% if categorias_items %}
      <div class="accordion-item is-active" data-accordion-item>
          <a href="#" title="search" class="accordion-title categoria font-12 bold">
              {% if idioma=='espanol' %}
                Categorias
              {% else %}
                Categories
              {% endif %}
              <img class="icon-filtro hide-for-large" src="{{template_url}}/img/icon-filtro.png" title="search" alt="search" />
          </a>
          <div class="accordion-content scroll" data-tab-content>

              <ul class="menu vertical marcas">

                  {% for r in categorias_items %}
                  <li>
                      <a href="{{site_url}}/catalogo?categoria={{r.attrs.titulo.data[0]|lower}}&genero={{genero}}" title="{{r.attrs.titulo.data[0]}}" class="{% if categoria == r.attrs.titulo.data[0]|lower %} primary bold {% endif %}">
                      {% if idioma=='espanol' %}
                        {{r.attrs.titulo.data[0]}}
                      {% else %}
                        {{r.attrs.titulo_ingles.data[0]}}
                      {% endif %}
                    </a>
                  </li>
                  {% endfor %}
                  <li>
                    <a href="{{site_url}}/catalogo?categoria=" class="{% if categoria == '' %} primary bold {% endif %}">
                      {% if idioma=='espanol' %}
                        Todos
                      {% else %}
                        Everybody
                      {% endif %}
                    </a>
                  </li>
              </ul>
          </div>
      </div>
      {% endif %}

      <div class="accordion-item is-active" data-accordion-item>
          <a href="#" title="icono busqueda" class="accordion-title categoria font-12 bold">
              {% if idioma=='espanol' %}
                Precios
              {% else %}
                Price
              {% endif %}
              <img class="icon-filtro hide-for-large" src="{{template_url}}/img/icon-filtro.png" title="icono busqueda" alt="icono busqueda" />
          </a>
          <div class="accordion-content" data-tab-content>
              <form class="form" id="form-filtro" action="{{site_url}}/catalogo" method="get">
                  <label class="negro bold font-9">
                    {% if idioma=='espanol' %}
                        Desde:
                    {% else %}
                        Since:
                    {% endif %}
                  </label>
                  <input type="number" name="desde" value="{{filtros.desde}}" />
                  <label class="negro bold font-9">
                    {% if idioma=='espanol' %}
                        Hasta:
                    {% else %}
                        To:
                    {% endif %}
                  </label>
                  <input type="number" name="hasta" value="{{filtros.hasta}}" />
                  <input type="hidden" name="genero" value="{{genero}}">
                  <input type="hidden" name="categoria" value="{{categoria}}">
                  {% if idioma=='espanol' %}
                    <input class="button black" type="submit" name="establecer" value="Establecer">
                  {% else %}
                    <input class="button black" type="submit" name="establecer" value="Set">
                  {% endif %}
                  
              </form>
          </div>
      </div>

      <!-- <div class="accordion-item is-active" data-accordion-item>
          <a href="#" title="search" class="accordion-title categoria font-12 bold">
              {% if idioma=='espanol' %}
                Nombre Producto
              {% else %}
                Name Product
              {% endif %}
              <img class="icon-filtro hide-for-large" src="{{template_url}}/img/icon-filtro.png" title="search" alt="search" />
          </a>
          <div class="accordion-content" data-tab-content>
              <form class="form" id="form-filtro" action="{{site_url}}/catalogo" method="get">
                  <label class="negro bold font-9">
                    {% if idioma=='espanol' %}
                        Nombre Producto:
                    {% else %}
                        Name Product:
                    {% endif %}
                  </label>
                  <input type="text" name="palabra" value="{{filtros.palabra_clave}}">
                  <input class="button black" type="submit" name="buscar" value="
                    {% if idioma=='espanol' %}
                        Buscar:
                    {% else %}
                        Search:
                    {% endif %}
                  ">
              </form>
          </div>
      </div> -->

  </div>
</div>

<!-- Menú Navegación -->
<nav class="nav">
  <div class="wrapper container">
    <div class="logo"><a href="{{site_url}}">
        <img src="{{plantilla.attrs.logo.data[0]|image}}" style="width: 80%; height: 80%;" alt="">
      </a>
    </div>
    <ul class="nav-list">
      <div class="top">
        <label for="" class="btn close-btn"><i class="fas fa-times"></i></label>
      </div>
      <li><a href="{{site_url}}">Inicio</a></li>
      <li>
        <a href="{{site_url}}/tienda" class="desktop-item">Tienda <span><i class="fas fa-chevron-down"></i></span></a>
        <input type="checkbox" id="showdrop1" />
        <label for="showdrop1" class="mobile-item">Tienda <span><i class="fas fa-chevron-down"></i></span></label>
        <ul class="drop-menu1">
          {% for r in categorias %}
          <li><a href="{{site_url}}/tienda?categoria={{r.attrs.titulo.data[0]}}">{{r.attrs.titulo.data[0]}}</a></li>
          {% endfor %}
        </ul>
      </li>
      <!-- <li>
        <a href="{{site_url}}/tienda" class="desktop-item">Tienda</a> -->
        <!-- <input type="checkbox" id="showMega" />
        <label for="showMega" class="mobile-item">Tienda <span><i class="fas fa-chevron-down"></i></span></label>
        <div class="mega-box">
          <div class="content">
            <div class="row">
              <img src="{{template_url}}/altamar/src/public/img/nav.png" alt="" />
            </div>
            <div class="row">
              <header>Hogar</header>
              <ul class="mega-links">
                <li><a href="#">Shop With Background</a></li>
                <li><a href="#">Shop Mini Categories</a></li>
                <li><a href="#">Shop Only Categories</a></li>
                <li><a href="#">Shop Icon Categories</a></li>
              </ul>
            </div>
            <div class="row">
              <header>Tecnologia</header>
              <ul class="mega-links">
                <li><a href="#">Sidebar</a></li>
                <li><a href="#">Filter Default</a></li>
                <li><a href="#">Filter Drawer</a></li>
                <li><a href="#">Filter Dropdown</a></li>
              </ul>
            </div>
            <div class="row">
              <header>Decoración</header>
              <ul class="mega-links">
                <li><a href="#">Layout Zoom</a></li>
                <li><a href="#">Layout Sticky</a></li>
                <li><a href="#">Layout Sticky 2</a></li>
                <li><a href="#">Layout Scroll</a></li>
              </ul>
            </div>
          </div>
        </div> -->
      <!-- </li> -->
      <li><a href="{{site_url}}/contactenos">Contactanos</a></li>
      <li>
        <a href="#" class="desktop-item">Cuenta <span><i class="fas fa-chevron-down"></i></span></a>
        <input type="checkbox" id="showdrop2" />
        <label for="showdrop2" class="mobile-item">Cuenta <span><i class="fas fa-chevron-down"></i></span></label>
        <ul class="drop-menu2">
          {% if not si.usuario %}
          <li><a href="{{site_url}}/account">Ingresar</a></li>
          <li><a href="{{site_url}}/registro">Crear tu cuenta</a></li>
          {% endif %}
          {% if si.usuario %}
          <li><a href="{{site_url}}/account/perfil">Mi cuenta</a></li>
          <li><a href="{{site_url}}/account/compras">Históricos Compras</a></li>
          <li><a href="{{site_url}}/account/cambiar-clave">Cambiar Clave</a></li>
          <li><a href="{{site_url}}/cerrarsesion">Cerrar sesión</a></li>
          {% endif %}
        </ul>
      </li>
      <!-- icono -->
      <!-- <li class="icono">
        <a href="src/app/cart.php">
          <span>
            <img src="{{template_url}}/altamar/src/public/img/login.svg" alt="" />
            <small class="count d-flex">0</small>
          </span>
        </a>
      </li> -->
      <li class="icono">
        <a href="{{site_url}}/carro">
          <span>
            <img src="{{template_url}}/altamar/src/public/img/shoppingBag.svg" alt="" />
            <small class="count d-flex">{{num_items}}</small>
          </span>
        </a>
      </li>
    </ul>
    <label for="" class="btn open-btn"><i class="fas fa-bars"></i></label>
  </div>
</nav>
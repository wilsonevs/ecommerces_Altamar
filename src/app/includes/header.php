<!-- Menú Navegación -->
<nav class="nav">
  <div class="wrapper container">
    <div class="logo"><a href="index.php">
        <img src="src/public/img/logo.png" style="width: 80%; height: 80%;" alt="">
      </a>
    </div>
    <ul class="nav-list">
      <div class="top">
        <label for="" class="btn close-btn"><i class="fas fa-times"></i></label>
      </div>
      <li><a href="index.php">Inicio</a></li>
      <li>
        <a href="productos.php" class="desktop-item">Categorias <span><i class="fas fa-chevron-down"></i></span></a>
        <input type="checkbox" id="showdrop1" />
        <label for="showdrop1" class="mobile-item">Categorias <span><i class="fas fa-chevron-down"></i></span></label>
        <ul class="drop-menu1">
          <li><a href="">Hogar</a></li>
          <li><a href="">Tecnología</a></li>
          <li><a href="">Decoración</a></li>
          <li><a href="">Hobbies</a></li>
          <li><a href="">Cosméticos</a></li>
          <li><a href="">Fitness</a></li>
          <li><a href="">Relojes</a></li>
        </ul>
      </li>
      <li>
        <a href="" class="desktop-item">Tienda <span><i class="fas fa-chevron-down"></i></span></a>
        <input type="checkbox" id="showMega" />
        <label for="showMega" class="mobile-item">Tienda <span><i class="fas fa-chevron-down"></i></span></label>
        <div class="mega-box">
          <div class="content">
            <div class="row">
              <img src="src/public/img/nav.png" alt="" />
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
        </div>
      </li>
      <li><a href="contactanos.php">Contactanos</a></li>
      <li>
        <a href="#" class="desktop-item">Perfil <span><i class="fas fa-chevron-down"></i></span></a>
        <input type="checkbox" id="showdrop2" />
        <label for="showdrop2" class="mobile-item">Perfil <span><i class="fas fa-chevron-down"></i></span></label>
        <ul class="drop-menu2">
          <li><a href="login.php">Ingresar</a></li>
          <li><a href="registrarse.php">Crear tu cuenta</a></li>
          <li><a href="perfil.php">Mi cuenta</a></li>
          <li><a href="historicos_compras.php">Históricos Compras</a></li>
          <li><a href="cambio_clave.php">Cambiar Clave</a></li>
          <li><a href="#">Cerrar sesión</a></li>
        </ul>
      </li>
      <!-- icono -->
      <!-- <li class="icono">
        <a href="src/app/cart.php">
          <span>
            <img src="src/public/img/login.svg" alt="" />
            <small class="count d-flex">0</small>
          </span>
        </a>
      </li> -->
      <li class="icono">
        <a href="carrito.php">
          <span>
            <img src="src/public/img/shoppingBag.svg" alt="" />
            <small class="count d-flex">0</small>
          </span>
        </a>
      </li>
    </ul>
    <label for="" class="btn open-btn"><i class="fas fa-bars"></i></label>
  </div>
</nav>
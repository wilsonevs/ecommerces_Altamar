<?php require_once("src/app/includes/head.php") ?>
<?php require_once("src/app/includes/header.php") ?>


<!-- PRODUCTS -->

<section class="section products">
  <div class="products-layout container">
    <div class="col-1-of-4">
      <div>
        <div class="block-title">
          <h3>Categoria</h3>
        </div>

        <ul class="block-content">
          <li>
            <input type="checkbox" name="" id="">
            <label for="">
              <span>Hogar</span>
              <small>(10)</small>
            </label>
          </li>

          <li>
            <input type="checkbox" name="" id="">
            <label for="">
              <span>Tecnología</span>
              <small>(7)</small>
            </label>
          </li>

          <li>
            <input type="checkbox" name="" id="">
            <label for="">
              <span> Decoración</span>
              <small>(3)</small>
            </label>
          </li>

          <li>
            <input type="checkbox" name="" id="">
            <label for="">
              <span>Hobbies</span>
              <small>(3)</small>
            </label>
          </li>
          <li>
            <input type="checkbox" name="" id="">
            <label for="">
              <span>Fitness</span>
              <small>(7)</small>
            </label>
          </li>

          <li>
            <input type="checkbox" name="" id="">
            <label for="">
              <span> Relojes</span>
              <small>(3)</small>
            </label>
          </li>

        </ul>
      </div>
<!-- Rango de precios -->
      <div class="continer__wrapper_rango">
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
            <input type="range" min="0" max="999999" value="150000" id="slider-1" oninput="slideOne()">
            <input type="range" min="0" max="999999" value="350000" id="slider-2" oninput="slideTwo()">
          </div>
        </div>
      </div>

    </div>
    <div class="col-3-of-4">
      <form action="">
        <div class="item">
          <label for="sort-by">Ordenar por</label>
          <select name="sort-by" id="sort-by">
            <option value="title" selected="selected">Nombre</option>
            <option value="number">Price</option>
            <option value="search_api_relevance">Relevance</option>
            <option value="created">Newness</option>
          </select>
        </div>
        <div class="item">
          <label for="order-by">Ordenar</label>
          <select name="order-by" id="sort-by">
            <option value="ASC" selected="selected">Ascender</option>
            <option value="DESC">Descender</option>
          </select>
        </div>
        <a href="">Filtrar</a>
      </form>

      <!-- 
            ======================
            Productos 
            ====================== 
            -->

      <div class="product-layout">
        <div class="product">
          <div class="img-container">
            <a href="productosDetalles.php"><img src="src/public/img/product-1.jpg" alt="" /></a>
            <span class="discount">50%</span>
            <div class="addCart">
              <i class="fas fa-shopping-cart"></i>
            </div>
          </div>
          <div class="bottom">
            <a href="productDetails.html">Bambi Print Mini Backpack</a>
            <div class="price">
              <span>$150</span>
              <span class="cancel">$160</span>
            </div>
          </div>
        </div>

        <div class="product">
          <div class="img-container">
            <a href="productosDetalles.php"><img src="src/public/img/product-2.jpg" alt="" /></a>
            <span class="discount">50%</span>
            <div class="addCart">
              <i class="fas fa-shopping-cart"></i>
            </div>
          </div>
          <div class="bottom">
            <a href="">Bambi Print Mini Backpack</a>
            <div class="price">
              <span>$150</span>
              <span class="cancel">$160</span>
            </div>
          </div>
        </div>

        <div class="product">
          <div class="img-container">
            <a href="productosDetalles.php"><img src="src/public/img/product-3.jpg" alt="" /></a>
            <span class="discount">50%</span>
            <div class="addCart">
              <i class="fas fa-shopping-cart"></i>
            </div>
          </div>
          <div class="bottom">
            <a href="">Bambi Print Mini Backpack</a>
            <div class="price">
              <span>$150</span>
              <span class="cancel">$160</span>
            </div>
          </div>
        </div>

        <div class="product">
          <div class="img-container">
            <a href="productosDetalles.php"><img src="src/public/img/product-4.jpg" alt="" /></a>
            <span class="discount">50%</span>
            <div class="addCart">
              <i class="fas fa-shopping-cart"></i>
            </div>
          </div>
          <div class="bottom">
            <a href="">Bambi Print Mini Backpack</a>
            <div class="price">
              <span>$150</span>
              <span class="cancel">$160</span>
            </div>
          </div>
        </div>

        <div class="product">
          <div class="img-container">
            <a href="productosDetalles.php"><img src="src/public/img/product-5.jpg" alt="" /></a>
            <span class="discount">50%</span>
            <div class="addCart">
              <i class="fas fa-shopping-cart"></i>
            </div>
          </div>
          <div class="bottom">
            <a href="">Bambi Print Mini Backpack</a>
            <div class="price">
              <span>$150</span>
              <span class="cancel">$160</span>
            </div>
          </div>
        </div>

        <div class="product">
          <div class="img-container">
            <a href="productosDetalles.php"><img src="src/public/img/product-6.jpg" alt="" /></a>
            <span class="discount">50%</span>
            <div class="addCart">
              <i class="fas fa-shopping-cart"></i>
            </div>
          </div>
          <div class="bottom">
            <a href="">Bambi Print Mini Backpack</a>
            <div class="price">
              <span>$150</span>
              <span class="cancel">$160</span>
            </div>
          </div>
        </div>

      </div>

      <!-- PAGINATION -->
      <ul class="pagination">
        <span class="last">« Atras</span>
        <span>1</span>
        <span>2</span>
        <span class="icon">››</span>
        <span class="last">Siguiente »</span>
      </ul>
    </div>
  </div>
</section>

<?php include_once("src/app/includes/footer.php") ?>
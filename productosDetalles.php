<?php require_once("src/app/includes/head.php")?>
<?php require_once("src/app/includes/header.php")?>

  <!-- Product Details -->
  <section class="section product-detail">
    <div class="details container">
      <div class="left">
        <div class="main">
          <img src="src/public/img/product-1.jpg" alt="" />
        </div>
        <div class="thumbnails">
          <div class="thumbnail">
            <img src="src/public/img/product-2.jpg" alt="" />
          </div>
          <div class="thumbnail">
            <img src="src/public/img/product-3.jpg" alt="" />
          </div>
          <div class="thumbnail">
            <img src="src/public/img/product-4.jpg" alt="" />
          </div>
          <div class="thumbnail">
            <img src="src/public/img/product-5.jpg" alt="" />
          </div>
        </div>
      </div>
      <div class="right">
        <h1>Bambi Print Mini Backpack</h1>
        <div class="price"><b>Precio: $</b>78.990</div>
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
            <div class="grid_form_uno">
              <input type="text" placeholder="1" />
            </div>
            <div class="grid_form_dos">
              <a href="productos.php" class=" price">Seguir Comprando</a>
              <a href="#" class=" price">Agregar al carrito</a>
            </div>
          </div>
        </form>
        <h3>Descripción</h3>
        <p>
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Vero minima
          delectus nulla voluptates nesciunt quidem laudantium, quisquam
          voluptas facilis dicta in explicabo, laboriosam ipsam suscipit!
        </p>
      </div>
    </div>
  </section>

  <!-- Related productos -->

  <section class="section related-products">
    <div class="title">
      <h2>Related Products</h2>
      <span>Select from the premium product brands and save plenty money</span>
    </div>
    <div class="product-layout container">

      <div class="product">
        <div class="img-container">
          <img src="src/public/img/product-1.jpg" alt="" />
          <div class="addCart">
            <i class="fas fa-shopping-cart"></i>
          </div>
        </div>
        <div class="bottom">
          <a href="">Bambi Print Mini Backpack</a>
          <div class="price">
            <span>$150</span>
          </div>
        </div>
      </div>

      <div class="product">
        <div class="img-container">
          <img src="src/public/img/product-2.jpg" alt="" />
          <div class="addCart">
            <i class="fas fa-shopping-cart"></i>
          </div>
        </div>
        <div class="bottom">
          <a href="">Bambi Print Mini Backpack</a>
          <div class="price">
            <span>$150</span>
          </div>
        </div>
      </div>

      <div class="product">
        <div class="img-container">
          <img src="src/public/img/product-3.jpg" alt="" />
          <div class="addCart">
            <i class="fas fa-shopping-cart"></i>
          </div>
        </div>
        <div class="bottom">
          <a href="">Bambi Print Mini Backpack</a>
          <div class="price">
            <span>$150</span>
          </div>
        </div>
      </div>

      <div class="product">
        <div class="img-container">
          <img src="src/public/img/product-4.jpg" alt="" />
          <div class="addCart">
            <i class="fas fa-shopping-cart"></i>
          </div>
        </div>
        <div class="bottom">
          <a href="">Bambi Print Mini Backpack</a>
          <div class="price">
            <span>$150</span>
          </div>
        </div>
      </div>
    </div>
  </section>


  <?php include_once("src/app/includes/footer.php")?>
<?php require_once("src/app/includes/head.php")?>
<?php require_once("src/app/includes/header.php")?>

    <!-- Cart Items -->
    <div class="container cart">
      <table>
        <tr>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Tarifa</th>
        </tr>
        <tr>
          <td>
            <div class="cart-info">
              <img src="src/public/img/product-1.jpg" alt="" />
              <div>
                <p>Bambi Print Mini Backpack</p>
                <span>Precio: $500.00</span>
                <br />
                <a href="#"><b>Eliminar</b></a>
              </div>
            </div>
          </td>
          <td><input type="number" value="1" min="1" /></td>
          <td>$50.00</td>
        </tr>
        <tr>
          <td>
            <div class="cart-info">
              <img src="src/public/img/product-2.jpg" alt="" />
              <div>
                <p>Bambi Print Mini Backpack</p>
                <span>Precio: $900.00</span>
                <br />
                <a href="#"><b>Eliminar</b></a>
              </div>
            </div>
          </td>
          <td><input type="number" value="1" min="1" /></td>
          <td>$90.00</td>
        </tr>
        <tr>
          <td>
            <div class="cart-info">
              <img src="src/public/img/product-3.jpg" alt="" />
              <div>
                <p>Bambi Print Mini Backpack</p>
                <span>Precio: $700.00</span>
                <br />
                <a href="#"><b>Eliminar</b></a>
              </div>
            </div>
          </td>
          <td><input type="number" value="1" min="1" /></td>
          <td>$60.00</td>
        </tr>
        <tr>
          <td>
            <div class="cart-info">
              <img src="src/public/img/product-4.jpg" alt="" />
              <div>
                <p>Bambi Print Mini Backpack</p>
                <span>Precio: $600.00</span>
                <br />
                <a href="#"><b>Eliminar</b></a>
              </div>
            </div>
          </td>
          <td><input type="number" value="1" min="1" /></td>
          <td>$60.00</td>
        </tr>
        <tr>
          <td>
            <div class="cart-info">
              <img src="src/public/img/product-5.jpg" alt="" />
              <div>
                <p>Bambi Print Mini Backpack</p>
                <span>Precio: $600.00</span>
                <br />
                <a href="#"><b>Eliminar</b></a>
              </div>
            </div>
          </td>
          <td><input type="number" value="1" min="1" /></td>
          <td>$60.00</td>
        </tr>
      </table>

<!-- 
======================
tabla
====================== 
-->

      <div class="total-price">
        <table>
          <tr>
            <td><b>Subtotal</b></td>
            <td>$200</td>
          </tr>
          <tr>
            <td><b>Valor Total</b></td>
            <td>$250</td>
          </tr>
        </table>
        <a href="verificar.php" class="checkout btn">Confirmar pedido</a>
      </div>
    </div>


<?php include_once("src/app/includes/footer.php")?>
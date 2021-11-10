<?php require_once("src/app/includes/head.php") ?>
<?php require_once("src/app/includes/header.php") ?>


<!-- Login -->
<section class="section">
  <div class="details container">
    <div class="container">
      <div class="login-form">
        <form action="">
          <h1>Cambiar la contraseña</h1>
          <div style="height: 25px;"></div>

          <label for="psw">Contraseña Actual</label>
          <input type="password" placeholder="Ingresar" name="psw" required />

          <label for="psw">Nueva Contraseña</label>
          <input type="password" placeholder="Nueva Contraseña" name="psw" required />

          <label for="psw">Confirmar Contraseña</label>
          <input type="password" placeholder="Confirmar Contraseña" name="psw" required />

          <div class="buttons">
            <button type="submit" class="signupbtn">Modificar Contraseña</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>


<?php include_once("src/app/includes/footer.php") ?>
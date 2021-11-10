<?php require_once("src/app/includes/head.php") ?>
<?php require_once("src/app/includes/header.php") ?>


<!-- Login -->
<section class="section">
  <div class="details container">
    <div class="container">
      <div class="login-form">
        <form action="">
          <h1>Iniciar sesión</h1>
          <div style="height: 25px;"></div>
          <label for="email">Correo Electrónico</label>
          <input type="text" placeholder="¡Hola! Ingresa tu correo electrónico" name="email" required minlength="5"/>

          <label for="psw">Contraseña</label>
          <input type="password" placeholder="Ahora, tu contraseña de importaciones altamar" name="psw" required required minlength="5"/>

          <label>
            <input type="checkbox" checked="checked" name="remember" style="margin-bottom: 15px" />
            Recuerdeme
          </label>

          <p>
            <a href="#">¿Olvidaste la contraseña?</a>.
          </p>
          <p>
            ¿Ya tienes una cuenta?
            <a href="signup.html">Crea tu cuenta</a>
          </p>

          <div class="buttons">
            <button type="submit" class="signupbtn">Ingresar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>


<?php include_once("src/app/includes/footer.php") ?>
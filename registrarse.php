<?php require_once("src/app/includes/head.php") ?>
<?php require_once("src/app/includes/header.php") ?>


<!-- Login -->

<section class="section ">
  <div class="details container">
    <div class="container">
      <div class="login-form">
        <form action="">
          <h1>Regístrarse</h1>
          <div style="height: 25px;"></div>
          <label for="email">Correo Electrónico</label>
          <input type="text" placeholder="¡Hola! Ingresa tu correo electrónico" name="email" required />

          <label for="psw">Contraseña</label>
          <input type="password" placeholder="Ingresa tu correo eletroníco" name="psw" required />

          <label for="psw">Contraseña</label>
          <input type="password" placeholder="Ingresa nuevamente tu correo eletroníco" name="psw" required />

          <p>
            ¿Ya tienes una cuenta?
            <a href="login.php">Iniciar sesión</a>
          </p>

          <p>
            Al crear una cuenta, acepta nuestro
            <a href="#">Términos & Privacidad</a>.
          </p>

          <div class="buttons">
            <button type="submit" class="signupbtn">Registrarse</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include_once("src/app/includes/footer.php") ?>
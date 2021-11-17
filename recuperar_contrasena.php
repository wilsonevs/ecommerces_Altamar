<?php require_once("src/app/includes/head.php") ?>
<?php require_once("src/app/includes/header.php") ?>


<!-- Login -->
<section class="section">
  <div class="details container">
    <div class="container">
      <div class="login-form">
        <form action="">
          <h1>Recuperar Contraseña</h1>
          <div style="height: 25px;"></div>

          <label for="psw">Ingresa tu correo electrónico asociado a la cuenta, te enviremos las instrucciones para restablecer la contraseña.</label>
          <input type="password" placeholder="Ingresar tu correo electrónico " name="psw" required />

          <p>
            <a href="index.php">Volver al inicio de sesión</a>.
          </p>
          <div class="buttons">
            <button type="submit" class="signupbtn">Enviar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>


<?php include_once("src/app/includes/footer.php") ?>
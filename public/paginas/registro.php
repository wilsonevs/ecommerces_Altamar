{% include 'altamar/src/app/includes/head.php' %}
{% include 'altamar/src/app/includes/header.php' %}
<script>{{ source('/altamar/src/public/js/registro.js') }}</script>

<!-- Login -->

<section class="section">
  <div class="details container">
    <div class="container">
      <div class="login-form">
        <form id="form-registro" onsubmit="return false;">
          <h1>Regístrarse</h1>
          <div style="height: 25px;"></div>
          <label for="correo_electronico">Correo Electrónico</label>
          <input type="text" placeholder="Ingresa tu correo electrónico" name="correo_electronico" required />

          <label for="contrasena">Contraseña</label>
          <input type="password" placeholder="Ingresa tu contraseña" name="contrasena" required />

          <label for="repite_contrasena">Confirmar Contraseña</label>
          <input type="password" placeholder="Ingresa nuevamente tu contraseña" name="repite_contrasena" required />

          <p>
            ¿Ya tienes una cuenta?
            <a href="{{site_url}}/account">Iniciar sesión</a>
          </p>

          <p>
            Al crear una cuenta, acepta nuestro
            <a href="{{site_url}}/terminos-y-condiciones">Términos & Privacidad</a>.
          </p>

          <div class="buttons">
            <button onclick="FormRegistro.formSubmit()" type="button" class="signupbtn">Registrarse</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
{% include 'altamar/src/app/includes/footer.php' %}
{% include 'altamar/src/app/includes/head.php' %}
{% include 'altamar/src/app/includes/header.php' %}
<script>{{ source('/altamar/src/public/js/login.js') }}</script>

<!-- Login -->
<section class="section">
  <div class="details container">
    <div class="container">
      <div class="login-form">
        <form  id="form-autenticacion" onsubmit="return false;">
          <h1>Iniciar sesión</h1>
          <div style="height: 25px;"></div>
          <label for="correo_electronico">Correo Electrónico</label>
          <input type="text" placeholder="Ingresa tu correo electrónico" name="correo_electronico" required/>

          <label for="contrasena">Contraseña</label>
          <input type="password" placeholder="Ahora, tu contraseña de importaciones altamar" name="contrasena" required/>

<!--           <label>
            <input type="checkbox" checked="checked" name="remember" style="margin-bottom: 15px" />
            Recuerdeme
          </label>
 -->
          <p>
            <a href="{{site_url}}/recuperar-contrasena">¿Olvidaste la contraseña?</a>.
          </p>
          <p>
            ¿Ya tienes una cuenta?
            <a href="{{site_url}}/registro">Crea tu cuenta</a>
          </p>

          <div class="buttons">
            <button type="button" onclick="FormLogin.iniciarSesion('{{ref}}')" class="signupbtn">Ingresar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>


{% include 'altamar/src/app/includes/footer.php' %}







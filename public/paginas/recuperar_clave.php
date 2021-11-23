{% include 'altamar/src/app/includes/head.php' %}
{% include 'altamar/src/app/includes/header.php' %}

{% include 'altamar/src/app/includes/head.php' %}
{% include 'altamar/src/app/includes/header.php' %}

<script>{{ source('/altamar/src/public/js/recuperar_clave.js') }}</script>

<!-- Login -->
<section class="section">
  <div class="details container">
    <div class="container">
      <div class="login-form">
        <form id="form-autenticacion" onsubmit="return false;">
          <h1>Recuperar Contraseña</h1>
          <div style="height: 25px;"></div>

          <label for="psw">Ingresa tu correo electrónico asociado a la cuenta, te enviremos las instrucciones para restablecer la contraseña.</label>
          <input type="text" placeholder="Ingresar tu correo electrónico" name="correo_electronico" />

          <p>
            <a href="{{site_url}}/login">Volver al inicio de sesión</a>.
          </p>
          <div class="buttons">
            <button type="button" class="signupbtn" onclick="PageRecuperarClave.enviar()">Enviar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>


{% include 'altamar/src/app/includes/footer.php' %}
{% include 'altamar/src/app/includes/head.php' %}
{% include 'altamar/src/app/includes/header.php' %}
<script>{{ source('/altamar/src/public/js/perfil_clave.js') }}</script>

<!-- Login -->
<section class="section">
  <div class="details container">
    <div class="container">
      <div class="login-form">
        <form action="" id="form-clave" onsubmit="return false;">
          <h1>Cambiar la contraseña</h1>
          <div style="height: 25px;"></div>

          <label for="contrasena_ant">Contraseña Actual</label>
          <input type="password" placeholder="Contraseña Actual" name="contrasena_ant" required />

          <label for="contrasena">Nueva Contraseña</label>
          <input type="password" placeholder="Nueva Contraseña" name="contrasena" required />

          <label for="contrasena2">Confirmar Contraseña</label>
          <input type="password" placeholder="Confirmar Contraseña" name="contrasena2" required />

          <div class="buttons">
            <button type="button" onclick="PagePerfilClave.cambiarClave();" class="signupbtn">Modificar Contraseña</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>


{% include 'altamar/src/app/includes/footer.php' %}

<div class="reveal modal_registro" id="modal_registro" data-reveal>
  <a class="close-button" data-close aria-label="Close modal" class=" float-right"><img src="{{template_url}}/img/icon-close.png" alt=""></a>
  <div class="row">
    <div class="column small-24 text-center">
      <h1 class="title bold">¡Registrate!</h1>
      <p class="subtitle bold negro">Tenemos los mejores descuentos.</p>
    </div>
  </div>
  <form class="">
    <div class="row">
      <div class="column medium-12">
        <label class="font-10 gris-oscuro bold">Número de identificación <span class="negro">*</span></label>
        <input name="identificacion" type="text" class="" />
      </div>
      <div class="column medium-12">
        <label class="font-10 gris-oscuro bold">Correo <span class="negro">*</span></label>
        <input name="correo_electronico" type="text" class="" />
      </div>
      <div class="column medium-12">
        <label class="font-10 gris-oscuro bold">Nombres <span class="negro">*</span></label>
        <input name="nombres" type="text" class="" />
      </div>
      <div class="column medium-12">
        <label class="font-10 gris-oscuro bold">Apellidos <span class="negro">*</span></label>
        <input name="apellidos" type="text" class="" />
      </div>
      <div class="column medium-12">
        <label class="font-10 gris-oscuro bold">Teléfono celular <span class="negro">*</span></label>
        <input name="telefono_celular" type="text" class="" />
      </div>
      <div class="column medium-12">
        <label class="font-10 gris-oscuro bold">Dirección <span class="negro">*</span></label>
        <input name="direccion" type="text" class="" />
      </div>

      <div class="column large-12">
        <label class="font-10 gris-oscuro bold">Contraseña</label>
        <input name="contrasena" type="password" class="" />
      </div>
      <div class="column large-12">
        <label class="font-10 gris-oscuro bold">Confirmar contraseña</label>
        <input name="repite_contrasena" type="password" class="" />
      </div>

      <div class="column small-24 text-left">
        <label class="font-10 gris-oscuro" for="terminos_y_condiciones">
          <input class="inline" type="checkbox" name="acepto_terminos" id="terminos_y_condiciones" value="1">&nbsp;
          <span class="bold gris-oscuro">Acepto</span><br/>
          Al hacer Click en el botón registar aseguras haber leído y estar de acuerdo on los <a href="{{site_url}}/terminos-y-condiciones" class="negro bold">términos y condiciones</a> y la política de privacidad de este sitio
        </label>
        <br/>
      </div>

    </div>

    <div class="row">
      <div class="column large-24">
        <div class="text-center">
          <input type="button"  onclick="FormRegistro.formSubmit()" class="button submit" value="REGISTRARME"/>
        </div>
      </div>
    </div>
  </form>
</div>

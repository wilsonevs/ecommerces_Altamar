{% include 'altamar/src/app/includes/head.php' %}
{% include 'altamar/src/app/includes/header.php' %}
<script>{{ source('/altamar/src/public/js/perfil_datos.js') }}</script>
<!-- 
======================
FORMULARIO
====================== 
-->

<div class="container cart">
  <div class="contact-form"></div>

  <form id="form-datos" onsubmit="return false;">

    <h1>Datos de usuario</h1>
    <div class="linea_larga"></div>
    <div class="distancia_alto"></div>
    <h2>{{si.usuario.attrs.nombres.data[0]}} {{si.usuario.attrs.apellidos.data[0]}}</h2>
    <div class="distancia_alto"></div>
    <div class="perfil-content">
      <select name="tipo_identificacion" class="color_select">
        <option value="" selected>Tipo de documento</option>
        <option value="cc" {{ "cc"==si.usuario.attrs.tipo_identificacion.data[0] ? "selected":"" }}>Cedula de Ciudadania</option>
        <option value="tx" {{ "tx"==si.usuario.attrs.tipo_identificacion.data[0] ? "selected":"" }}>Tarjeta de extranjería </option>
        <option value="ce" {{ "ce"==si.usuario.attrs.tipo_identificacion.data[0] ? "selected":"" }}>Cédula de extranjería</option>
        <option value="nit" {{ "nit"==si.usuario.attrs.tipo_identificacion.data[0] ? "selected":"" }}>NIT</option>
        <option value="pass" {{ "pass"==si.usuario.attrs.tipo_identificacion.data[0] ? "selected":"" }}>Pasaporte</option>
      </select>
      <input type="text" name="identificacion" class="form text_negro" placeholder="Número de documento" value="{{si.usuario.attrs.identificacion.data[0]}}" />
      <input type="text" name="nombres" class="form text_negro" placeholder="Nombre" value="{{si.usuario.attrs.nombres.data[0]}}"/>
      <input type="text" name="apellidos" class="form text_negro" placeholder="Apellidos" value="{{si.usuario.attrs.apellidos.data[0]}}"/>
      <input type="text" name="telefono_celular" class="form text_negro" placeholder="Número Celular" value="{{si.usuario.attrs.telefono_celular.data[0]}}" />
      <input type="text" name="telefono_fijo" class="form text_negro" placeholder="Número Fijo" value="{{si.usuario.attrs.telefono_fijo.data[0]}}" />
      <select name="pais" onchange="PagePerfilDatos.onPais();" class="color_select">
        {% for r in paises %}
          <option value="{{r.data}}" {{ r.data==si.usuario.attrs.pais.data[0] ? "selected":"" }} >{{r.label}}</option>
        {% endfor %}
      </select>
      <select name="departamento" onchange="PagePerfilDatos.onDepartamento();" class="color_select">
        <option value="">Departamentos</option>
        {% for r in departamentos %}
          <option value="{{r.data}}" {{ r.data==si.usuario.attrs.departamento.data[0] ? "selected":"" }}>{{r.label}}</option>
        {% endfor %}
      </select>
      <select name="ciudad" class="color_select">
        <option value="">Ciudad</option>
        {% for r in ciudades %}
          <option value="{{r.data}}" {{ r.data==si.usuario.attrs.ciudad.data[0] ? "selected":"" }}>{{r.label}}</option>
        {% endfor %}
      </select>
      <input class="form text_negro" placeholder="Dirección" name="direccion" type="text" value="{{si.usuario.attrs.direccion.data[0]}}" />
      <input class="form text_negro" placeholder="Correo Electrónico" name="correo_electronico" type="text" value="{{si.usuario.attrs.correo_electronico.data[0]}}"/>
    </div>

    <div class="buttons">
      <button type="button"  onclick="PagePerfilDatos.actualizar();" class="signupbtn">Actualizar Datos</button>
    </div>

  </form>
  <div class="distancia_alto"></div>
</div>


{% include 'altamar/src/app/includes/footer.php' %}
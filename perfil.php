<?php require_once("src/app/includes/head.php") ?>
<?php require_once("src/app/includes/header.php") ?>


<!-- 
======================
FORMULARIO
====================== 
-->

<div class="container cart">
  <div class="contact-form"></div>

  <form id="contact-us" method="post" action="#">

    <h1>Datos de usuario</h1>
    <div class="linea_larga"></div>
    <div class="distancia_alto"></div>
    <h2>WILSON VALENCIA</h2>
    <div class="distancia_alto"></div>
    <div class="perfil">
      <select name="tipo_cedula" id="tipo_cedula" class="color_select">
        <option value="-1" selected>Tipo de documento</option>
        <option value="cc">Cedula de Ciudadania</option>
        <option value="tx">Tarjeta de extranjería </option>
        <option value="ce">Cédula de extranjería</option>
        <option value="nit">NIT</option>
        <option value="pass">Pasaporte</option>
      </select>
      <input type="text" name="numero_documento" id="numero_documento" required="required" class="form" placeholder="Número de documento" />
      <input type="text" name="nombre" id="nombre" required="required" class="form" placeholder="Nombre" />
      <input type="text" name="apellido" id="apellido" required="required" class="form" placeholder="Apellidos" />
      <input type="text" name="celular" id="celular" required="required" class="form" placeholder="Número Celular o Teléfono" />
      <select name="pais" id="pais" class="color_select">
        <option value="-2" selected>País</option>
        <option value="co">Colombia</option>
        <option value="ec">Ecuador</option>
        <option value="eu">Estados Unidos</option>
        <option value="pr">Peru</option>
        <option value="vn">Venezuela</option>
      </select>
      <select name="departamento" id="departamento" class="color_select">
        <option value="-3" selected>Departamentos</option>
        <option value="Antioquia">Antioquia</option>
        <option value="Bogotá">Bogotá</option>
        <option value="Caldas">Caldas</option>
        <option value="Santander">Santander</option>
        <option value="Valle del Cauca">Valle del Cauca</option>
      </select>
      <select name="ciudad" id="ciudad" class="color_select">
        <option value="-4" selected>Ciudad</option>
        <option value="medellin">Medellín</option>
        <option value="Barranquilla">Barranquilla</option>
        <option value="Bello">Bello</option>
        <option value="Bello">Caldas</option>
        <option value="Cali">Cali</option>
        <option value="Bello">Copacabana</option>
        <option value="Bello">Girardota</option>
        <option value="Bello">Envigado</option>
        <option value="Bello">Itagui</option>
        <option value="Bello">La Estrella</option>
        <option value="Bello">Sabaneta</option>
        <option value="Bello">Rionegro</option>
      </select>
      <input type="text" name="barrio" id="barrio" required="required" class="form" placeholder="Barrio" />
      <input type="text" name="direccion" id="direccion" required="required" class="form" placeholder="Dirección" />
      <input type="text" name="direccion" id="direccion" required="required" class="form" placeholder="Correo Electrónico" />
    </div>

    <div class="buttons">
      <button type="submit" class="signupbtn">Actualizar Datos</button>
    </div>

  </form>
  <div class="distancia_alto"></div>
</div>


<?php include_once("src/app/includes/footer.php") ?>
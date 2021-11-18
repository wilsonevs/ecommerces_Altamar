<?php require_once("src/app/includes/head.php") ?>
<?php require_once("src/app/includes/header.php") ?>


<!-- Login -->
<section class="section">
    <div class="container">
      <div class="contactanos">

        <div class="contactanos__izq">
          <input type="text" name="nombre" id="nombre" required="required" class="form text_negro" placeholder="Nombre"/>
          <input type="email" name="celular" id="celular" required="required" class="form text_negro" placeholder="Tu correo"/>
          <input type="text" name="numero_documento" id="numero_documento" required="required" class="form text_negro" placeholder="Número Celular o Teléfono"/>
          <textarea name="mensaje" id="mensaje" cols="30" rows="10" class="form text_negro" placeholder="Mensaje"></textarea>
          <div class="buttons">
            <button type="submit" class="signupbtn">Enviar</button>
          </div>
        </div>

        <div class="contactanos__der">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15865.526155919471!2d-75.58501196533908!3d6.213307909746465!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e4428299b5aa6d9%3A0x2020c055ff96b671!2sEl%20Poblado%2C%20Medell%C3%ADn%2C%20Antioquia!5e0!3m2!1ses!2sco!4v1637197049039!5m2!1ses!2sco" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
      </div>
    </div>
</section>


<?php include_once("src/app/includes/footer.php") ?>
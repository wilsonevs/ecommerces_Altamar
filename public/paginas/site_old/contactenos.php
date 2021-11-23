{% include 'header.php' %}
{% include 'slider.php' %}
<script>{{ source('contactenos.js') }}</script>
<div class="content">
  <div class="row">
      <div class="column small-24">
          <h2 class="font-16 primary bold">
            {% if idioma=='espanol' %}
              CONTACTENOS
            {% else %}
              CONTACT US
            {% endif %}
          </h2>
      </div>
      <div class="column small-24">
          <hr/>
      </div>
  </div>
  <br/>
  <div class="row">
    <div class="column small-24 medium-24 large-9 x-info-contacto show-for-medium ">
        {% if idioma=='espanol' %}
          {{contactenos.attrs.contenido.data[0]|raw}}
        {% else %}
          {{contactenos.attrs.contenido_ingles.data[0]|raw}}
        {% endif %}
    </div>
    <div class="  column small-24 medium-24 large-15 padding-left-none padding-right-none">
        <form class="contacto" id="contacto" action="" method="">
          <div class="row">
            <div class="column small-24 medium-12">
              <label class="font-10 gris-oscuro bold">
                {% if idioma=='espanol' %}
                Nombre 
                {% else %}
                Name 
                {% endif %}
                <span class="negro">*</span></label>
              <input type="text" value="" name="nombre">

            </div>
            <div class="column small-24 medium-12">
              <label class="font-10 gris-oscuro bold">
                {% if idioma=='espanol' %}
                Correo Electrónico 
                {% else %}
                Email 
                {% endif %}
                <span class="negro">*</span></label>
              <input type="text" value="" name="correo_electronico">
            </div>
          </div>
          <div class="row">
            <div class="column small-24 medium-8">
              <label class="font-10 gris-oscuro bold">
                {% if idioma=='espanol' %}
                Teléfono
                {% else %}
                Phone 
                {% endif %}
                <span class="negro">*</span></label>
              <input type="text" value="" name="telefono">
            </div>
            <div class="column small-24 medium-16">
              <label class="font-10 gris-oscuro bold">
                {% if idioma=='espanol' %}
                Area de interes
                {% else %}
                Area of ​​interest 
                {% endif %}
                <span class="negro">*</span>
                <select name="area_de_interes">
                  {% if idioma=='espanol' %}
                  <option value="">Selecciona el área de tu interés</option>
                  <option value="Quejas">Quejas</option>
                  <option value="Reclamos">Reclamos</option>
                  <option value="Sugerencia">Sugerencias</option>
                  <option value="Información del Pedido">Información del Pedido</option>
                  {% else %}
                  <option value="">Select the area of ​​your interest</option>
                  <option value="Complaints">Complaints</option>
                  <option value="Claims">Claims</option>
                  <option value="Suggestions">Suggestions</option>
                  <option value="Order Information">Order Information</option>
                  {% endif %}
                  
                </select>
              </label>
            </div>
          </div>
          <div class="row">
            <div class="column small-24">
              <label class="font-10 gris-oscuro bold">
                {% if idioma=='espanol' %}
                Mensaje
                {% else %}
                Message
                {% endif %} <span class="negro">*</span>
                <textarea rows="5" cols="50" name="mensaje"></textarea>
              </label>
            </div>
          </div>
          <div class="row">
            <div class="column small-24 medium-10 terminos-acepto">
              <div class="acepto text-left">
                <input id="acepto" type="checkbox" name="acepto_terminos_y_condiciones" value="1"><label for="acepto" class="margin-right-0 gris-oscuro">
                {% if idioma=='espanol' %}
                Acepto Términos y Condiciones
                {% else %}
                I accept Terms and Conditions
                {% endif %}</label>
              </div>
            </div>
            <div class="column small-24 medium-14 terminos">
              <a href="{{site_url}}/terminos-y-condiciones" target="_blank" class="bold font-8 gris-oscuro underline" title="terminos y condiciones">
                {% if idioma=='espanol' %}
                Términos y Condiciones - Políticas de uso
                {% else %}
                Terms and Conditions - Use policies
                {% endif %}</a>
            </div>
          </div>
          <div class="row">
            <div class="column small-24 medium-24 large-24">
              <div class="float-left">
                {% if idioma=='espanol' %}
                <input class="button" type="button" onclick="Contacto.submit()" value="ENVIAR">
                {% else %}
                <input class="button" type="button" onclick="Contacto.submit()" value="SEND">
                {% endif %}
              </div>
            </div>
          </div>
        </form>
    </div>
  </div>

</div>
{% include 'footer.php' %}

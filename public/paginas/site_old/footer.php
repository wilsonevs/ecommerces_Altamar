          <script>{{ source('footer.js') }}</script>
          <div class="icon-ir-arriba">
            <a href="{{plantilla.attrs.link_whatsapp.data[0]}}" title="whatsapp zaecko" target="_blank"><img src="{{template_url}}/img/whatsapp.png" alt="whatsapp zaecko" title="whatsapp zaecko" width="60" /></a>
          </div>

          <!-- <div class="icon-ir-arriba hide-for-large">
            <a href="{{site_url}}/carro"><img src="{{template_url}}/img/icon-carro-small.png" alt=""/></a>
          </div> -->

          <div class="footer">
            <div class="row">
              <div class="column small-12 medium-6 large-5">
                <ul class="vertical menu">
                    <li>
                        <a href="{{site_url}}/inicio" class="logo-footer" title="zaecko">

                          {% if plantilla.attrs.logo_footer.data[0] %}
                          <img src="{{plantilla.attrs.logo_footer.data[0]|image}}" title="zaecko" alt="zaecko" />
                  				{% else %}
                  				<img src="https://via.placeholder.com/250x50"/>
                  				{% endif %}

                        </a>
                    </li>
                  {{menu_inferior.html|raw}}

                </ul>
              </div>
              <div class="column small-12 medium-9 large-10 padding-top-1">
                  <div class="info font-9 negro bold">
                    {% if idioma=='espanol' %}
                      Síguenos
                    {% else %}
                      Follow us
                    {% endif %}
                  </div>
                  <div class="redes">
                      {% for r in redes_sociales %}
                      <a class="red" href="{{r.attrs.enlace.data[0]}}" target="{{r.attrs.abrir_en.data[0]}}" title="social links">
                        {% if r.attrs.imagen_red_social.data[0] %}
                				<img src="{{r.attrs.imagen_red_social.data[0]|image}}" alt="{{r.attrs.titulo.data[0]}}" title="{{r.attrs.titulo.data[0]}}">
                				{% else %}
                				<img src="https://via.placeholder.com/32"/>
                				{% endif %}
                      </a>
                      {% endfor %}
                  </div>
                <!-- <div class="font-8 bold">
                  Algunos Clientes
                </div>
                <div class="row clientes">
                  <div class="column small-6 large-4 pd-none">
                    <a href="" target="_blank"><img src="http://via.placeholder.com/40x30" alt=""></a>
                  </div>
                  <div class="column small-6 large-4 pd-none">
                    <a href="" target="_blank"><img src="http://via.placeholder.com/40x30" alt=""></a>
                  </div>
                  <div class="column small-6 large-4 pd-none">
                    <a href="" target="_blank"><img src="http://via.placeholder.com/40x30" alt=""></a>
                  </div>
                  <div class="column small-6 large-4 pd-none">
                    <a href="" target="_blank"><img src="http://via.placeholder.com/40x30" alt=""></a>
                  </div>
                  <div class="column show-for-large large-4 pd-none end">
                    <a href="" target="_blank"><img src="http://via.placeholder.com/40x30" alt=""></a>
                  </div>
                  <div class="column show-for-large large-4 pd-none end">
                    <a href="" target="_blank"><img src="http://via.placeholder.com/40x30" alt=""></a>
                  </div>
                </div> -->
                <div class="font-8">
                  {% if idioma=='espanol' %}
                    {{plantilla.attrs.informacion_footer.data[0]|raw}}
                  {% else %}
                    {{plantilla.attrs.informacion_footer_ingles.data[0]|raw}}
                  {% endif %} 
                </div>
                <br/>
                {% if plantilla.attrs.camara_de_comercio.data[0] %}
                <a href="https://www.ccce.org.co/" target="_blank" title="Camara de comercio electronico"><img src="{{plantilla.attrs.camara_de_comercio.data[0]|image}}" alt="Camara de comercio electronico" title="Camara de comercio electronico" width="90"> </a>
                {% else %}
                {% endif %}
                <!-- <a href="#"><img src="https://via.placeholder.com/50x50" alt=""> </a> -->
              </div>
              <div class="column small-24 medium-9 large-9 padding-top-1">
                <div class="font-9 bold negro">
                  {% if idioma=='espanol' %}
                    Suscribirse
                  {% else %}
                    Suscribe
                  {% endif %}
                </div>
                <form class="suscribirse" method="post" onsubmit="return false;">
                  <div class="input-group">
                    {% if idioma=='espanol' %}
                    <input class="input-group-field" type="text" name="suscribe" placeholder="Correo Electrónico">
                    {% else %}
                    <input class="input-group-field" type="text" name="suscribe" placeholder="Email">
                    {% endif %}
                    <div class="input-group-button">
                      <input type="button" class="button" onclick="FooterSuscribe.enviar()" value="
                      {% if idioma=='espanol' %}
                      Enviar
                      {% else %}
                      Send
                      {% endif %} 
                      ">
                    </div>
                  </div>
                </form>
                <div class="row">
                  <div class="column small-24 padding-left-none padding-right-none">
                    <div class="info font-8">
                      <span class="bold font-9 negro">
                        {% if idioma=='espanol' %}
                          Llamanos
                        {% else %}
                          Call us
                        {% endif %}
                      </span>
                      <hr/>
                      {{plantilla.attrs.telefonos.data[0]|raw}}
                    </div>
                    <div class="info font-8">
                      <span class="bold font-9 negro">
                        {% if idioma=='espanol' %}
                          Contactenos
                        {% else %}
                          Contact us
                        {% endif %}
                      </span>
                      <hr/>
                      <a class="gris-oscuro font-8 negro" href="mailto:info@{{host}}" title="email">info@{{host}}</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="creditos">
              <div class="row">
                <div class="column small-24">
                  <div class="text-center font-10 blanco">
                    {% if idioma=='espanol' %}
                      {{plantilla.attrs.creditos.data[0]}}
                    {% else %}
                      {{plantilla.attrs.creditos_ingles.data[0]}}
                    {% endif %}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    {% include 'modals.php' %}
    <script>


      $(document).ready(function() {

        $(document).foundation();

        $('#slider').owlCarousel({
          autoHeight : true,
          singleItem : true,
          navigation : true,
          autoPlay : true,
          stopOnHover : true,
          slideSpeed : 300,
          paginationSpeed : 800,
          rewindSpeed : 2000,
          navigationText:['<img src="{{template_url}}/img/flecha-izquierda.png"/>','<img src="{{template_url}}/img/flecha-derecha.png"/>']
        });


        $("#carro-lateral").click(function() {
            $('.carro_lateral').addClass("show_carro_lateral");
            $('.off-canvas-wrapper').addClass("overflow-none");
            $('.inicio-sesion').addClass("margin-inicio-sesion");

        });

        $("#cerrar-carro").click(function() {
            $('.carro_lateral').removeClass("show_carro_lateral");
            $('.off-canvas-wrapper').removeClass("overflow-none");
            $('.inicio-sesion').removeClass("margin-inicio-sesion");
        });

        // Foundation modal
        // $('#modal_registro').foundation('open');



        setTimeout(function(){
          $("body").removeClass("bodyloader");
          $(".loader").addClass("hiden");
        }, 5000);

      });

    </script>
    {{plantilla.attrs.javascript_body_start.data[0]|raw}}
  </body>
</html>

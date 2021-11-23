  <!-- Footer -->
  <footer id="footer" class="section footer">
    <div class="container">
      <div class="footer-container">
        <div class="footer-center">
          <h3>MENU</h3>
          {{menu_inferior.html|raw}}
        </div>
   
        <div class="footer-center">
          <h3>CONTÁCTANOS</h3>
          <div>
            <a href="#">
              <span><i class="fas fa-map-marker-alt"></i></span>{{plantilla.attrs.direccion.data[0]}}
            </a>
          </div>
          <div>
            <a href="mailto:{{plantilla.attrs.email.data[0]}}">
              <span><i class="far fa-envelope"></i></span>{{plantilla.attrs.email.data[0]}}
            </a>
          </div>
        </div>
        <div class="footer-center">
          <h3>TELÉFONOS</h3>
          <div>
            <a href="https://wa.me/{{plantilla.attrs.whatsapp.data[0]}}" target="_blank">
              <span><i class="fab fa-whatsapp"></i></span>{{plantilla.attrs.whatsapp.data[0]}}
            </a>
          </div>
          <div>
            <a href="tel:{{plantilla.attrs.telefono.data[0]}}">
              <span><i class="fas fa-mobile-alt"></i></span>{{plantilla.attrs.telefono.data[0]}}
            </a>
          </div>
        </div>
        <div class="footer-center">
          <h3>REDES SOCIALES</h3>
          <div class="footer-center">
            <a href="{{plantilla.attrs.instagram.data[0]}}" target="_blank"><i class='bx bxl-instagram-alt'></i> Instagram</a>
            <a href="{{plantilla.attrs.facebook.data[0]}}" target="_blank"><i class='bx bxl-facebook-square'></i> Facebook</a>
          </div>
        </div>
      </div>
    </div>
    </div>
  </footer>
  <!-- End Footer -->

  <div class="copyright text-center-footer">
    <div class="container">
      <div class="row">
        {{plantilla.attrs.creditos.data[0]|raw}}
      </div>
    </div>
  </div>

  {% include 'modals.php' %}

  <!-- ======== SwiperJS ======= -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/6.7.5/swiper-bundle.min.js"></script>
<!--   <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script> -->
  <!-- Custom Scripts -->
  <script src="{{template_url}}/altamar/src/public/js/slider.js"></script>
  <script src="{{template_url}}/altamar/src/public/js/slider_progress.js"></script>
  <script src="{{template_url}}/altamar/src/public/js/index.js"></script>
  <script>
    $(document).ready(function() {
        $(document).foundation();
    });
  </script>
  </body>
  </html>
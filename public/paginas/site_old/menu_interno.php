<div class="menu_interno">
    <ul class="menu vertical">
        {% if idioma=='espanol' %}
            <li>
                <a href="{{site_url}}/account"><!--img src="{{template_url}}/img/icon-usuario.png" alt=""--> Mi Cuenta</a>
            </li>
            <li>
                <a href="{{site_url}}/account/compras"><!--img src="{{template_url}}/img/icon-historial.png" alt=""--> Histórico Compras</a>
            </li>
            <li>
                <a href="{{site_url}}/account/cambiar-clave"><!--img src="{{template_url}}/img/icon-cambiar.png" alt=""--> Cambiar Clave</a>
            </li>
            <li class="cerrar-sesion">
                <a href="{{site_url}}/cerrarsesion" ><img src="{{template_url}}/img/icon-cerrar-sesion.png" alt=""> Cerrar sesión</a>
            </li>
        {% else %}          
            <li>
                <a href="{{site_url}}/account"><!--img src="{{template_url}}/img/icon-usuario.png" alt=""--> My account</a>
            </li>
            <li>
                <a href="{{site_url}}/account/compras"><!--img src="{{template_url}}/img/icon-historial.png" alt=""--> Historical Purchases</a>
            </li>
            <li>
                <a href="{{site_url}}/account/cambiar-clave"><!--img src="{{template_url}}/img/icon-cambiar.png" alt=""--> Change Password</a>
            </li>
            <li class="cerrar-sesion">
                <a href="{{site_url}}/cerrarsesion" ><img src="{{template_url}}/img/icon-cerrar-sesion.png" alt=""> Sign off</a>
            </li>
        {% endif %}
        
    </ul>

</div>

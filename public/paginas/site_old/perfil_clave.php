{% include 'header.php' %}
<!-- <script>{{ source('predeterminado.js') }}</script> -->
<script>{{ source('perfil_clave.js') }}</script>
<div class="content">
    <div class="row">
        <div class="column small-24">
            <h2 class="font-14 primary bold">
            {% if idioma=='espanol' %}
                Cambio de contraseña
            {% else %}
                Change of password
            {% endif %}
            </h2>
        </div>
        <div class="column small-24">
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="column medium-8 large-6 show-for-medium">
            {% include 'menu_interno.php' %}
        </div>
        <div class="column medium-16 large-18 padding-none">
            <form action="" class="form" id="form-clave" onsubmit="return false;">
                <div class="row">
                    <div class="column small-24 medium-centered medium-20 large-14">
                        <label class="font-10 gris-oscuro bold">
                        {% if idioma=='espanol' %}
                            Contraseña anterior
                        {% else %}
                            Old Password
                        {% endif %}
                        </label>
                        <input type="password" name="contrasena_ant" value=""/>
                    </div>
                </div>
                <div class="row">
                    <div class="column small-24 medium-centered medium-20 large-14">
                        <label class="font-10 gris-oscuro bold">
                        {% if idioma=='espanol' %}
                            Nueva contraseña
                        {% else %}
                            New Password
                        {% endif %}
                        </label>
                        <input type="password" name="contrasena" value=""/>
                    </div>
                </div>
                <div class="row">
                    <div class="column small-24 medium-centered medium-20 large-14">
                        <label class="font-10 gris-oscuro bold">
                        {% if idioma=='espanol' %}
                            Repita la nueva contraseña
                        {% else %}
                            Repeat new password
                        {% endif %}
                        </label>
                        <input type="password" name="contrasena2" value=""/>
                    </div>
                </div>
                <div class="row">
                    <div class="column small-24 medium-centered medium-20 large-14">
                        <div class="text-center">
                            {% if idioma=='espanol' %}
                                <input type="button"  onclick="PagePerfilClave.cambiarClave();" class="button submit" value="ACTUALIZAR DATOS"/>
                            {% else %}
                                <input type="button"  onclick="PagePerfilClave.cambiarClave();" class="button submit" value="UPDATE DATA"/>
                            {% endif %}
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{% include 'footer.php' %}

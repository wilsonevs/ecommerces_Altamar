{% include 'header.php' %}
<!-- <script>{{ source('predeterminado.js') }}</script> -->
<script>{{ source('perfil_datos.js') }}</script>
<div class="content">
    <div class="row">
        <div class="column small-24">
            <h2 class="font-14 gris bold">
                {% if idioma=='espanol' %}
                    Datos de usuario
                {% else %}          
                    User Data
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
            <form class="form" id="form-datos" onsubmit="return false;">
                <div class="row">
                    <div class="column large-12">
                        <label class="font-10 gris-oscuro bold">
                            {% if idioma=='espanol' %}
                                Tipo de identificación
                            {% else %}          
                                ID Type
                            {% endif %}
                         </label>
                        <select class="" name="tipo_identificacion">
                            <option value="-1">Seleccione</option>
                            <option value="cc" selected>Cedula de Ciudadania</option>
                        </select>
                    </div>
                    <div class="column large-12">
                        <label class="font-10 gris-oscuro bold">
                            {% if idioma=='espanol' %}
                                Número de identificación
                            {% else %}          
                                Identification number 
                            {% endif %}
                              <span class="negro">*</span></label>
                        <input name="identificacion" type="text" value="{{si.usuario.attrs.identificacion.data[0]}}" />
                    </div>
                    <div class="column large-12">
                        <label class="font-10 gris-oscuro bold">
                            {% if idioma=='espanol' %}
                                Nombres
                            {% else %}          
                                Names
                            {% endif %}
                             <span class="negro">*</span></label>
                        <input name="nombres" type="text" value="{{si.usuario.attrs.nombres.data[0]}}" />
                    </div>
                    <div class="column large-12">
                        <label class="font-10 gris-oscuro bold">
                            {% if idioma=='espanol' %}
                                Apellidos
                            {% else %}          
                                Surnames
                            {% endif %}
                             <span class="negro">*</span></label>
                        <input name="apellidos" type="text" value="{{si.usuario.attrs.apellidos.data[0]}}" />
                    </div>
                    <div class="column large-12">
                        <label class="font-10 gris-oscuro bold">
                            {% if idioma=='espanol' %}
                                Teléfono celular
                            {% else %}          
                                Cell Phone
                            {% endif %}
                              <span class="negro">*</span></label>
                        <input name="telefono_celular" type="text" value="{{si.usuario.attrs.telefono_celular.data[0]}}" />
                    </div>
                    <div class="column large-12">
                        <label class="font-10 gris-oscuro bold">
                            {% if idioma=='espanol' %}
                                Teléfono fijo
                            {% else %}          
                                Phone
                            {% endif %}
                         </label>
                        <input name="telefono_fijo" type="text" value="{{si.usuario.attrs.telefono_fijo.data[0]}}" />
                    </div>
                  </div>
                  <div class="row">
                    <div class="column large-12">
                        <label class="font-10 gris-oscuro bold">
                            {% if idioma=='espanol' %}
                                Pais
                            {% else %}          
                                Country
                            {% endif %}
                        </label>
                        <select name="pais" onchange="PagePerfilDatos.onPais();">
                            {% for r in paises %}
                              <option value="{{r.data}}" {{ r.data==si.usuario.attrs.pais.data[0] ? "selected":"" }} >{{r.label}}</option>
                            {% endfor %}
                        </select>
                    </div>

                    <div class="column large-12">
                        <label class="font-10 gris-oscuro bold">
                            {% if idioma=='espanol' %}
                                Departamento
                            {% else %}          
                                State
                            {% endif %}
                        </label>
                        <select name="departamento" onchange="PagePerfilDatos.onDepartamento();">
                          {% for r in departamentos %}
                            <option value="{{r.data}}" {{ r.data==si.usuario.attrs.departamento.data[0] ? "selected":"" }}>{{r.label}}</option>
                          {% endfor %}
                        </select>
                    </div>

                    <div class="columns large-12">
                        <label class="font-10 gris-oscuro bold">
                            {% if idioma=='espanol' %}
                                Ciudad
                            {% else %}          
                                City
                            {% endif %}
                        </label>
                        <select name="ciudad">
                          {% for r in ciudades %}
                            <option value="{{r.data}}" {{ r.data==si.usuario.attrs.ciudad.data[0] ? "selected":"" }}>{{r.label}}</option>
                          {% endfor %}
                        </select>
                    </div>

                    <div class="column large-12">
                        <label class="font-10 gris-oscuro bold">
                            {% if idioma=='espanol' %}
                                Dirección
                            {% else %}          
                                Address
                            {% endif %}
                             <span class="negro">*</span></label>
                        <input name="direccion" type="text" value="{{si.usuario.attrs.direccion.data[0]}}" />
                        <br/>
                    </div>
                </div>
                <div class="row">
                    <div class="column small-24">
                        <h2 class="font-14 gris bold">
                            {% if idioma=='espanol' %}
                                Datos de sesión
                            {% else %}          
                                Session data
                            {% endif %}
                        </h2>
                    </div>
                    <div class="column small-24">
                        <hr/>
                    </div>
                    <div class="column large-24">
                        <label class="font-10 gris-oscuro bold">
                            {% if idioma=='espanol' %}
                                Correo Electrónico
                            {% else %}          
                                Email
                            {% endif %}
                             <span class="negro">*</span></label>
                        <input name="correo_electronico" type="text" value="{{si.usuario.attrs.correo_electronico.data[0]}}" />
                    </div>

                </div>

                <div class="row">
                    <div class="column large-24">
                        <div class="text-center">
                            {% if idioma=='espanol' %}
                                <input type="button"  onclick="PagePerfilDatos.actualizar();" class="button submit" value="ACTUALIZAR DATOS"/>
                            {% else %}          
                                <input type="button"  onclick="PagePerfilDatos.actualizar();" class="button submit" value="UPDATE DATA"/>
                            {% endif %}
                            
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{% include 'footer.php' %}

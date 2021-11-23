{% include 'header.php' %}
<script>{{ source('registro.js') }}</script>
<div class="content">

	<div class="centro text-center">
		<div class="registro">
					<!-- <div class="row">
					<div class="column small-24">
					<div class="logo text-center">
					<img src="{{template_url}}/img/icon-usuario-login.png" width="90" alt="" />
				</div>
			</div>
		</div> -->
			<div class="row">
				<form class="form" id="form-registro" onsubmit="return false;">
					<div class="column small-24 medium-19 large-16 medium-centered">
						<div class="row">
							<div class="column small-24">
								<h2 class="font-14 primary bold">
									{% if idioma=='espanol' %}
					                	Registrarme
						            {% else %}          
						               	Sign Up
						            {% endif %}
								</h2>
							</div>
							<div class="column small-24">
								<hr/>
							</div>
						</div>
						<div class="row">
							<div class="column medium-12">
								<label class="font-10 gris-oscuro bold">
									{% if idioma=='espanol' %}
					                	Tipo de identificación
						            {% else %}          
						               	ID Type
						            {% endif %}
								</label>
								<select class="" name="tipo_identificacion">
									{% if idioma=='espanol' %}
					                	<option value="">Seleccione</option>
						            {% else %}          
						               	<option value="">Select</option>
						            {% endif %}
									<option value="cc">Cedula de Ciudadania</option>
								</select>
							</div>
							<div class="column medium-12">
								<label class="font-10 gris-oscuro bold">
									{% if idioma=='espanol' %}
					                	Número de identificación 
						            {% else %}          
						               	Identification number
						            {% endif %}
								<span class="negro">*</span></label>
								<input name="identificacion" type="text" class="" />
							</div>
						</div>
						<div class="row">
							<div class="column medium-12">
								<label class="font-10 gris-oscuro bold">
									{% if idioma=='espanol' %}
					                	Nombres
						            {% else %}          
						               	Names
						            {% endif %}
								<span class="negro">*</span></label>
								<input name="nombres" type="text" class="" />
							</div>
							<div class="column medium-12">
								<label class="font-10 gris-oscuro bold">
									{% if idioma=='espanol' %}
					                	Apellidos
						            {% else %}          
						               	Surnames
						            {% endif %}<span class="negro">*</span></label>
								<input name="apellidos" type="text" class="" />
							</div>
						</div>
						<div class="row">
							<div class="column medium-12">
								<label class="font-10 gris-oscuro bold">
									{% if idioma=='espanol' %}
					                	Teléfono Celular
						            {% else %}          
						               	Cell Phone
						            {% endif %}
									<span class="negro">*</span></label>
								<input name="telefono_celular" type="text" class="" />
							</div>
							<div class="column medium-12">
								<label class="font-10 gris-oscuro bold">
									{% if idioma=='espanol' %}
					                	Teléfono Fijo
						            {% else %}          
						               	Phone
						            {% endif %}
								</label>
								<input name="telefono_fijo" type="text" class="" />
							</div>
						</div>
						<div class="row">
							<div class="column medium-12">
								<label class="font-10 gris-oscuro bold">
									{% if idioma=='espanol' %}
					                	País
						            {% else %}          
						               	Country
						            {% endif %}
								</label>
								<select name="pais" onchange="FormRegistro.onPais();">
									{% for r in paises %}
									<option value="{{r.data}}">{{r.label}}</option>
									{% endfor %}
								</select>
							</div>

							<div class="column medium-12">
								<label class="font-10 gris-oscuro bold">
									{% if idioma=='espanol' %}
					                	Departamento
						            {% else %}          
						               	State
						            {% endif %}
								</label>
								<select name="departamento" onchange="FormRegistro.onDepartamento();">
								</select>
							</div>
						</div>
						<div class="row">
							<div class="column medium-12">
								<label class="font-10 gris-oscuro bold">
									{% if idioma=='espanol' %}
					                	Ciudad
						            {% else %}          
						               	City
						            {% endif %}
								</label>
								<select name="ciudad">
								</select>
							</div>

							<div class="column medium-12">
								<label class="font-10 gris-oscuro bold">
									{% if idioma=='espanol' %}
					                	Dirección
						            {% else %}          
						               	Address
						            {% endif %}
									<span class="negro">*</span></label>
								<input name="direccion" type="text" class="" />
								<br/>
							</div>
						</div>
						<div class="row">
							<div class="column small-24">
								<h2 class="font-14 primary bold">
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
						</div>
						<div class="row">
							<div class="column large-24">
								<label class="font-10 gris-oscuro bold">
									{% if idioma=='espanol' %}
					                	Correo Electrónico
						            {% else %}          
						               	Email
						            {% endif %}
									<span class="negro">*</span></label>
								<input name="correo_electronico" type="text" class="" />
							</div>
						</div>
						<div class="row">
							<div class="column large-12">
								<label class="font-10 gris-oscuro bold">
									{% if idioma=='espanol' %}
					                	Contraseña
						            {% else %}          
						               	Password
						            {% endif %}
								</label>
								<input name="contrasena" type="password" class="" />
							</div>
							<div class="column large-12">
								<label class="font-10 gris-oscuro bold">
									{% if idioma=='espanol' %}
					                	Confirmar contraseña
						            {% else %}          
						               	Confirm Password
						            {% endif %}
								</label>
								<input name="repite_contrasena" type="password" class="" />
							</div>
						</div>
						<div class="row">
							<div class="column small-24">
								<br/>
							</div>

							<div class="column small-24">
								<h2 class="font-14 primary bold">
									{% if idioma=='espanol' %}
					                	Políticas de privacidad
						            {% else %}          
						               	Privacy policies
						            {% endif %}
								</h2>
							</div>
							<div class="column small-24">
								<hr/>
							</div>

							<div class="column small-24">
								<div class="text-politicas text-left">
									{% if idioma=='espanol' %}
					                	{{terminos.attrs.contenido.data[0]|raw}}
						            {% else %}          
						               	{{terminos.attrs.contenido_ingles.data[0]|raw}}
						            {% endif %}
								</div>
							</div>

							<div class="column small-24 text-left">
								<label class="font-10 gris-oscuro" for="terminos_y_condiciones">
									<input class="inline" type="checkbox" name="acepto_terminos" id="terminos_y_condiciones" value="1">&nbsp;
									<span class="primary">
									{% if idioma=='espanol' %}
					                	Acepto
						            {% else %}          
						               	I agree
						            {% endif %}
									</span><br/>
									{% if idioma=='espanol' %}
					                	Al hacer Click en el botón registar aseguras haber leído y estar de acuerdo con los términos, condiciones y la política de privacidad de Zaecko
						            {% else %}          
						               By clicking on the register button you ensure that you have read and agree to the terms, conditions and privacy policy of Zaecko
						            {% endif %}
								</label>
								<br/>
							</div>


						</div>

						<div class="row">
							<div class="column large-24">
								<div class="text-center">
									{% if idioma=='espanol' %}
					                	<input type="button"  onclick="FormRegistro.formSubmit()" class="button submit" value="REGISTRARME"/>
						            {% else %}          
						               	<input type="button"  onclick="FormRegistro.formSubmit()" class="button submit" value="SIGN UP"/>
						            {% endif %}
								</div>
							</div>
						</div>

						<div class="row">
							<div class="column large-24">
								<!-- <div class="inicio">
								<a class="gris-oscuro underline" href="{{site_url}}/account">¿Ya tienes cuenta?</a>
							</div> -->
							<!-- <div class="xxrecuperar text-center">
							<a class="primary underline no-hover-primary" href="{{site_url}}/contactenos">¿Tienes problemas?</a>
						</div> -->
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>
{% include 'footer.php' %}

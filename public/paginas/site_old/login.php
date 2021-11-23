{% include 'header.php' %}
<script>{{ source('login.js') }}</script>
<div class="content">

	<div class="centro text-center">
		<div class="login">
			<!-- <div class="row">
				<div class="column small-24">
					<div class="logo text-center">
						<img src="{{template_url}}/img/icon-usuario-login.png" width="90" alt="" />
					</div>
				</div>
			</div> -->
			<div class="row">
				<form class="form" id="form-autenticacion" onsubmit="return false;">
					<div class="column small-24">
						<div class="row">
							<div class="column large-10 small-centered">
								<h2 class="font-14 primary bold">
									{% if idioma=='espanol' %}
					                	Ingresa Ya
						            {% else %}          
						                Enter Now
						            {% endif %}
					        	</h2>
							</div>
							<div class="column large-10 small-centered">
								<hr/>
							</div>
						</div>
						<div class="row">
							<div class="column large-10 small-centered">
								<label class="font-10 gris-oscuro">
									{% if idioma=='espanol' %}
					                	Correo Electrónico
						            {% else %}          
						               	Email
						            {% endif %}
								</label>
								<input type="text" name="correo_electronico" class="" />
							</div>
						</div>
						<div class="row">
							<div class="column large-10 small-centered">
								<label class="font-10 gris-oscuro">
									{% if idioma=='espanol' %}
					                	Contraseña
						            {% else %}          
						               	Password
						            {% endif %}
								</label>
								<input type="password" name="contrasena" class="" />
							</div>
						</div>

						<div class="row">
							<div class="column large-10 small-centered">
								<div class="text-center">
									{% if idioma=='espanol' %}
										<input type="button"  onclick="FormLogin.iniciarSesion('{{ref}}')" class="button submit" value="INICIAR SESION"/>
						            {% else %}          
						               	<input type="button"  onclick="FormLogin.iniciarSesion('{{ref}}')" class="button submit" value="LOGIN"/>
						            {% endif %}
								</div>
							</div>
						</div>
						<div class="row">
							<div class="column large-10 small-centered">
								<div class="cuenta">
									<a class="primary underline no-hover-primary" href="{{site_url}}/registro">
									{% if idioma=='espanol' %}
					                	¿Quieres registrarte?
						            {% else %}          
						               	Register
						            {% endif %}
									</a>
								</div>
								<div class="recuperar">
									<a class="primary underline no-hover-primary" href="{{site_url}}/recuperar-contrasena">
										{% if idioma=='espanol' %}
					                		¿Olvidaste tu contraseña?
							            {% else %}          
							               	Forgot password
							            {% endif %}
									</a>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
{% include 'footer.php' %}

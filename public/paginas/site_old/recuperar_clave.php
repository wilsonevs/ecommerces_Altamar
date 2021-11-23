{% include 'header.php' %}
<script>{{ source('recuperar_clave.js') }}</script>
<div class="content">
    <div class="centro text-center">
		<div class="login">
			<div class="row">
				<form class="form" id="form-autenticacion" onsubmit="return false;">
					<div class="column small-24">
                        <div class="row">
							<div class="column large-10 small-centered">
								<h2 class="font-14 negro bold">Recuperar contraseña</h2>
							</div>
							<div class="column large-10 small-centered">
								<hr/>
							</div>
						</div>
						<div class="row">
							<div class="column large-10 small-centered">
								<label class="font-10 gris-oscuro">Correo</label>
								<input type="text" name="correo_electronico" class="" />
							</div>
						</div>

						<div class="row">
							<div class="column large-10 small-centered">
								<div class="text-center">
									<input type="button"  onclick="PageRecuperarClave.enviar()" class="button submit" value="RECUPERAR"/>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="column large-10 small-centered">
								<div class="sesion">
									<a class="gris underline" href="{{site_url}}/account">Ir a iniciar sesión</a>
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

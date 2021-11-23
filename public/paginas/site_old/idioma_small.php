<script>{{ source('idioma.js') }}</script>
<div class="part_idioma">
	<form class="idioma" id="idiomasmall" action="" method="">
		<div class="row">
			<div class="">
				<div class="">
					<label class="font-8 gris-oscuro bold">
						{% if idioma=='espanol' %}
						Idioma
						{% else %}
						Language
						{% endif %}
						<select name="idiomasmall" onchange="Idioma.submitsmall();" class="select-idioma">
							{% if idioma=='espanol' %}
							<option value="espanol" {% if idioma == 'espanol' %}selected{% endif %}>Español</option>
							<option value="ingles" {% if idioma == 'ingles' %}selected{% endif %}>Inglés</option>
							{% else %}
							<option value="espanol" {% if idioma == 'espanol' %}selected{% endif %}>Spanish</option>
							<option value="ingles" {% if idioma == 'ingles' %}selected{% endif %}>English</option>
							{% endif %}
						</select>
					</label>
				</div>
			</div>
		</div>
	</form>
</div>


<script>{{ source('part_moneda.js') }}</script>
<div class="part_moneda">
	<form class="moneda" id="monedaform" action="" method="">
		<div class="row">
			<div class="">
				<div class="">
					<label class="font-8 negro bold">
						{% if idioma=='espanol' %}
						Moneda
						{% else %}
						Currency
						{% endif %}
						<select name="moneda" onchange="Moneda.submit();" class="select-moneda show-for-large">
							<option value="cop" {% if moneda == 'cop' %}selected{% endif %}>COP</option>
							<option value="usd" {% if moneda == 'usd' %}selected{% endif %}>USD</option>
                        </select>
					</label>
				</div>
			</div>
		</div>
	</form>
</div>


<script>{{ source('part_moneda.js') }}</script>
<div class="part_moneda">
	<form class="moneda" id="monedaformsmall" action="" method="">
		<div class="row">
			<div class="">
				<div class="">
					<label class="font-8 negro bold">
						{% if idioma=='espanol' %}
						Moneda
						{% else %}
						Currency
						{% endif %}
                        <select name="monedasmall" onchange="Moneda.submitsmall();" class="select-moneda hide-for-large">
							<option value="cop" {% if moneda == 'cop' %}selected{% endif %}>COP</option>
							<option value="usd" {% if moneda == 'usd' %}selected{% endif %}>USD</option>
						</select>
					</label>
				</div>
			</div>
		</div>
	</form>
</div>


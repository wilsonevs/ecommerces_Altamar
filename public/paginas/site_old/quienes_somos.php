{% include 'header.php' %}
{% include 'slider.php' %}
<div class="content">
	<div class="row">
		<div class="column small-24">
			<h2 class="font-16 primary bold">
				{% if idioma=='espanol' %}
				QUIENES SOMOS
				{% else %}
				ABOUT US
				{% endif %}
			</h2>
		</div>
		<div class="column small-24">
			<hr/>
		</div>
	</div>
	<br/>
    <div class="row">
        <div class="column small-24">
        	{% if idioma=='espanol' %}
				{{quienes_somos.attrs.descripcion.data[0]|raw}}
			{% else %}
				{{quienes_somos.attrs.descripcion_ingles.data[0]|raw}}
			{% endif %}
        </div>
    </div>
</div>
{% include 'footer.php' %}

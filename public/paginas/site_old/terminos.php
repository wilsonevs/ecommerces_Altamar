{% include 'header.php' %}
<div class="content">
    <div class="row">
        <div class="column small-24">
        	{% if idioma=='espanol' %}
                {{terminos.attrs.contenido.data[0]|raw}}
            {% else %}
                {{terminos.attrs.contenido_ingles.data[0]|raw}}
            {% endif %}
        </div>
    </div>
</div>
{% include 'footer.php' %}

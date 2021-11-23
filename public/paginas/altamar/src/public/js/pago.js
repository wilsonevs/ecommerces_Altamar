Pago = {
	init:function(){
		if( typeof this.__init__ !=="undefined" ) return;
		this.__init__ = true;

		this.ui={};
		this.form = $('#form-compra');
		this.ui.containerDatosEntrega = $('.container-datos-entrega');


		this.ui.ent_identificacion = this.form.find('input[name=ent_identificacion]');
		this.ui.ent_nombres = this.form.find('input[name=ent_nombres]');
		this.ui.ent_apellidos = this.form.find('input[name=ent_apellidos]');
		this.ui.ent_direccion = this.form.find('input[name=ent_direccion]');
		this.ui.ent_telefono_celular = this.form.find('input[name=ent_telefono_celular]');
		this.ui.ent_telefono = this.form.find('input[name=ent_telefono]');
		this.ui.ent_id_pais = this.form.find('select[name=ent_id_pais]');
		this.ui.ent_id_departamento = this.form.find('select[name=ent_id_departamento]');
		this.ui.ent_id_ciudad = this.form.find('select[name=ent_id_ciudad]');

	},


	mismaDireccion:function(checkbox){
		this.init();

		var checked = $(checkbox).is(":checked");
		var dr = this.form.cmGetRecord();

		if( checked ){

			rpc.call("TiendaData.setDestino",{
				id_pais:dr.com_id_pais,
				id_departamento:dr.com_id_departamento,
				id_ciudad:dr.com_id_ciudad
			}).then(function(res,ex){

				if(ex){
					$.cmDialogError(ex);
					return;
				}

				function formatNumber(n) {
					n = String(n).replace(/\D/g, "");
					return n === '' ? n : Number(n).toLocaleString();
				}

				$("#envio").text('$'+formatNumber(Math.round(parseFloat(res.total_transporte))+' COP'));
				$("#subtotal").text('$'+formatNumber(Math.round(parseFloat(res.subtotal))+' COP'));
				$("#total").text('$'+formatNumber(Math.round(parseFloat(res.total))+' COP'));
				// $("#total_usd").text('$'+res.total_usd+' USD');

			});

			this.ui.ent_identificacion.val(dr.com_identificacion);
			this.ui.ent_nombres.val(dr.com_nombres);
			this.ui.ent_apellidos.val(dr.com_apellidos);
			this.ui.ent_direccion.val(dr.com_direccion);
			this.ui.ent_telefono.val(dr.com_telefono_fijo);
			this.ui.ent_telefono_celular.val(dr.com_telefono_celular);
			this.ui.ent_id_pais.val(dr.com_id_pais);
			this.ui.ent_id_departamento.val(dr.com_id_departamento);
			this.ui.ent_id_ciudad.val(dr.com_id_ciudad);

		}
		else {
			this.ui.ent_identificacion.val("");
			this.ui.ent_nombres.val("");
			this.ui.ent_apellidos.val("");
			this.ui.ent_direccion.val("");
			this.ui.ent_telefono.val("");
			this.ui.ent_telefono_celular.val("");
			this.ui.ent_id_pais.val("");
			this.ui.ent_id_departamento.val("");
			this.ui.ent_id_ciudad.val("");
		}
	},

	record:function(){
		var dr = this.form.cmGetRecord();
		return dr;
	},

	pais:function(){
		this.init();
		var countryId = this.ui.ent_id_pais.cmGetValue();

		this.ui.ent_id_departamento.html('');
		this.ui.ent_id_ciudad.html('');

		rpc.call("TiendaData.departamentos",{country_id:countryId}).then(function(res,ex){

			if(ex){
				$.cmDialogError({
					message:ex.message
				});
				return;
			}

			this.ui.ent_id_departamento.cmPopulate(res);

		}.bind(this));
	},

	departamento:function(){
		this.init();
		var countryId = this.ui.ent_id_pais.cmGetValue();
		var stateId = this.ui.ent_id_departamento.cmGetValue();

		this.ui.ent_id_ciudad.html('');

		rpc.call("TiendaData.ciudades",{country_id:countryId,state_id:stateId, delivery:true}).then(function(res,ex){

			if(ex){
				alert(ex);
				return;
			}

			this.ui.ent_id_ciudad.cmPopulate(res);

		}.bind(this));
	},

	ciudad:function(){
		this.init();
		var dr = this.form.cmGetRecord();

		rpc.call("TiendaData.setDestino",{
			id_pais:this.ui.ent_id_pais.val(),
			id_departamento:this.ui.ent_id_departamento.val(),
			id_ciudad:this.ui.ent_id_ciudad.val()
		}).then(function(res,ex){

			if(ex){
				$.cmDialogError(ex);
				return;
			}

			function formatNumber (n) {
				n = String(n).replace(/\D/g, "");
			  return n === '' ? n : Number(n).toLocaleString();
			}

			$("#envio").text('$'+formatNumber(Math.round(parseFloat(res.total_transporte))));
			$("#subtotal").text('$'+formatNumber(Math.round(parseFloat(res.subtotal))));
			$("#total").text('$'+formatNumber(Math.round(parseFloat(res.total))));
			// window.location = site_url+"/checkout";
		});

	},

	finalizar:function(){
		this.init();
		var dr = this.record();

		rpc.call("TiendaData.finalizarCompra",dr).then(function(res,ex){
			if(ex){
				$.cmDialogError(ex);
				return;
			}

			window.location.href = site_url + res.url;
		});

	}

};

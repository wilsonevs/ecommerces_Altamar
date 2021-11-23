Carro = {
	init:function(){
		if( typeof this.__init__ !=="undefined" ) return;
		this.__init__ = true;

		this.ui={};

		//this.form = $('#form-registro');

		this.ui.pais = $("select[name=pais]");
		this.ui.departamento = $("select[name=departamento]");
		this.ui.ciudad = $("select[name=ciudad]");

		// this.ui.formTarjetaRegalo = $('form[name=tarjeta_regalo]');
	},

	finalizar:function(){
		var urlCarro = window.location.href.indexOf('finalizar');

		if( urlCarro > 0){
			$('.ubicacion-geografica').css('display','block');
		}
		return;
	},

	// onPais:function(){
	// 	this.init();
	// 	var countryId = this.ui.pais.cmGetValue();
	//
	// 	this.ui.departamento.html('');
	// 	this.ui.ciudad.html('');
	//
	// 	rpc.call("TiendaData.departamentos",{country_id:countryId}).then(function(res,ex){
	//
	// 		if(ex){
	// 			alert(ex);
	// 			return;
	// 		}
	//
	// 		this.ui.departamento.cmPopulate(res);
	//
	// 	}.bind(this));
	// },
	//
	// onDepartamento:function(){
	// 	this.init();
	// 	var countryId = this.ui.pais.cmGetValue();
	// 	var stateId = this.ui.departamento.cmGetValue();
	//
	// 	this.ui.ciudad.html('');
	//
	// 	rpc.call("TiendaData.ciudades",{country_id:countryId,state_id:stateId, delivery:true}).then(function(res,ex){
	//
	// 		if(ex){
	// 			alert(ex);
	// 			return;
	// 		}
	//
	// 		this.ui.ciudad.cmPopulate(res);
	//
	// 	}.bind(this));
	// },
	//
	// onCiudad:function(){
	// 	this.init();
	//
	// 	// $('body').hide();
	//
	// 	rpc.call("TiendaData.setDestino",{
	// 		id_pais:this.ui.pais.val(),
	// 		id_departamento:this.ui.departamento.val(),
	// 		id_ciudad:this.ui.ciudad.val()
	// 	}).then(function(res,ex){
	//
	// 		if(ex){
	// 			$.cmDialogError(ex);
	// 			return;
	// 		}
	//
	// 		window.location = site_url+"/carro?finalizar=1";
	// 		//window.location.reload(true);
	// 	});
	//
	// },

	onFinalizar:function(){
		// if( $('.ubicacion-geografica').css('display')=='none' ){
		// 	$('.ubicacion-geografica').css('display','block');
		// 	return;
		// }

		rpc.call("TiendaData.verificarPedido",{

		}).then(function(res,ex){

			if(ex){
				$.cmDialogError(ex);
				return;
			}

			window.location = site_url+"/checkout";
		});
	},


	// agregarTarjetaRegalo:function(){
	// 	this.init();

	// 	var dr = this.ui.formTarjetaRegalo.cmGetRecord();

	// 	rpc.call("TiendaData.agregarTarjetaRegalo",dr)

	// 	.then(function(res,ex){
	// 		if(ex){
	// 			$.cmDialogError(ex);
	// 			return;
	// 		}

	// 		window.location.reload(true);
	// 	});
	// },

	// removerTarjetaRegalo:function(referencia){
	// 	rpc.call("TiendaData.removerTarjetaRegalo",{referencia:referencia})

	// 	.then(function(res,ex){
	// 		if(ex){
	// 			$.cmDialogError(ex);
	// 			return;
	// 		}

	// 		window.location.reload(true);
	// 	});

	// }
};

cmDeferJs(function(){
	Carro.finalizar();
});

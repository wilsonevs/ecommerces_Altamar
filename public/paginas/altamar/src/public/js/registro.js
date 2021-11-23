var FormRegistro = {
	init:function(){
		if( typeof this.__init__ !=="undefined" ) return;
		this.__init__ = true;

		this.ui={};

		this.form = $('#form-registro');

		// this.ui.pais = this.form.find("select[name=pais]");
		// this.ui.departamento = this.form.find("select[name=departamento]");
		// this.ui.ciudad = this.form.find("select[name=ciudad]");

	},

	// onPais:function(){
	// 	this.init();
	// 	var countryId = this.ui.pais.cmGetValue();

	// 	this.ui.departamento.html('');
	// 	this.ui.ciudad.html('');

	// 	rpc.call("CuentaData.departamentos",{country_id:countryId}).then(function(res,ex){

	// 		if(ex){
	// 			$.cmDialogError({
	// 				message:ex.message
	// 			});
	// 			return;
	// 		}

	// 		this.ui.departamento.cmPopulate(res);

	// 	}.bind(this));
	// },

	// onDepartamento:function(){
	// 	this.init();
	// 	var countryId = this.ui.pais.cmGetValue();
	// 	var stateId = this.ui.departamento.cmGetValue();

	// 	this.ui.ciudad.html('');

	// 	rpc.call("CuentaData.ciudades",{country_id:countryId,state_id:stateId}).then(function(res,ex){

	// 		if(ex){
	// 			alert(ex);
	// 			return;
	// 		}

	// 		this.ui.ciudad.cmPopulate(res);

	// 	}.bind(this));
	// },


	formSubmit:function(ref = null){
		this.init();

		var dr = this.form.cmGetRecord();

		rpc.call("CuentaData.registrar",dr).then(function(res,ex){
			if(ex){
				$.cmDialogError({
					message:ex.message
				});
				return;
			}
			rpc.call("CuentaData.iniciarSesion",dr).then(function(res,ex){
				if(ex){
					$.cmDialogError(ex);
					return;
				}
				if(ref !== null){
					window.location = site_url+ref;
				}
			});
			$.cmDialogInfo({
				message:res.message,
				callback:function(){
					window.location = site_url+"/account";
				}
			});
		});
	}
};

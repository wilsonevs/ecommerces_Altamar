var FormLogin = {
	init:function(){
		if( typeof this.__init__ !=="undefined" ) return;
		this.__init__ = true;

		//this.ui={};
		this.form = $('#form-autenticacion');

	},

	iniciarSesion:function(ref){
		this.init();

		//var dr = this.form.cmGetRecord();
		var dr = $('#form-autenticacion').cmGetRecord();


		rpc.call("CuentaData.iniciarSesion",dr).then(function(res,ex){
			if(ex){
				$.cmDialogError(ex);
				return;
			}

			//window.location.reload(true);
			window.location = site_url+ref;
		});

	},
}


cmDeferJs(function(){
	if( '{{_GET.e26ee7b57a3d7f953de0818fae0b795}}' == '0' ){

		$.cmDialogInfo({
			message:'Debes iniciar sesion o registrarte para finalizar la compra.'
		});
	}
});

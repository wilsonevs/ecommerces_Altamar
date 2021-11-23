FooterSuscribe = {
	init:function(){
		if( typeof this.__init__ !=="undefined" ) return;
		this.__init__ = true;


		this.ui={};

	},

	enviar:function(){
		this.init();
		var correo_electronico = $('input[name=suscribe]').val();

		rpc.call("CuentaData.suscribe",{correo_electronico: correo_electronico}).then(function(res,ex){

			if(ex){
				$.cmDialogError(ex);
				return;
			}


			$.cmDialogInfo(res);
			// window.location = site_url+"/account";
		}.bind(this));

	}

};

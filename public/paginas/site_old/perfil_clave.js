var PagePerfilClave = {
	init:function(){
		if( typeof this.__init__ !=="undefined" ) return;
		this.__init__ = true;
		this.ui={};

	},
	cambiarClave:function(){
		this.init();
		var dr = $('#form-clave').cmGetRecord();

		rpc.call("CuentaData.cambiarClave",dr).then(function(res,ex){
			if(ex){

				$.cmDialogError({
					message:ex.message
				});
				return;
			}

			$.cmDialogInfo({
				message:res.message,
				callback:function(){
					window.location.reload(true);
				}
			});

		});
	}
};

var CarroLateral = {

	__updateTimeout:null,

	init:function(){
		if( this.__init !== undefined ) return;

		this.__init = true;
		this.ui = {};

		this.ui.pais = $('select[name=pais]');
		this.ui.departamento = $('select[name=departamento]');
		this.ui.ciudad = $('select[name=ciudad]');

		this.ui.color = $('select[name=color]');
		this.ui.talla = $('select[name=talla]');
	},

	agregarItem:function(item_id){

		this.init();
		var color = this.ui.color.cmGetValue();
		var talla = this.ui.talla.cmGetValue();

		rpc.call("TiendaData.agregarItem", {item_id:item_id,color:color,talla:talla})
		.then(function(res,ex){
			if(ex){
				$.cmDialogError(ex);
				return;
			}

			// window.location.href = site_url+"/carro";
			window.location.reload(true);
		});
	},

	removerItem:function(item_id,color,talla){
		rpc.call("TiendaData.removerItem", {item_id:item_id,color:color,talla:talla})
		.then(function(res,ex){
			if(ex){
				$.cmDialogError(ex);
				return;
			}

			window.location.reload(true);
		});
	},

	actualizarUnidades:function(p){
		rpc.call("TiendaData.actualizarUnidades",p)
		.then(function(res,ex){
			if(ex){
				$.cmDialogError(ex);
				return;
			}

			window.location.reload(true);
		});

	},

	modificarUnidades:function(p){
		clearTimeout(CarroLateral.__updateTimeout);
		CarroLateral.__updateTimeout = setTimeout(function(){
			CarroLateral.actualizarUnidades({
				item_id: p.item_id,
				variacion:p.variacion,
				unidades:$('.unidades_'+p.item_id+'_'+p.color+'_'+p.talla).val(),
				talla: p.talla,
				color: p.color
			});

		},600);

	},

	masUnidades:function(p){
		var sel = '.unidades_'+p.item_id+'_'+p.color+'_'+p.talla;
		$(sel).val( parseInt( $(sel).val() ) +1 );

		CarroLateral.modificarUnidades(p);
	},

	menosUnidades:function(p){
		var sel = '.unidades_'+p.item_id+'_'+p.color+'_'+p.talla;
		$(sel).val( parseInt( $(sel).val() ) - 1 );
		CarroLateral.modificarUnidades(p);
	},


	onPais:function(){
		this.init();
		var countryId = this.ui.pais.cmGetValue();

		this.ui.departamento.html('');
		this.ui.ciudad.html('');

		rpc.call("TiendaData.departamentos",{country_id:countryId}).then(function(res,ex){

			if(ex){
				$.cmDialogError(ex);
				return;
			}

			this.ui.departamento.cmPopulate(res);

		}.bind(this));
	},

	onDepartamento:function(){
		this.init();
		var countryId = this.ui.pais.cmGetValue();
		var stateId = this.ui.departamento.cmGetValue();

		this.ui.ciudad.html('');

		rpc.call("TiendaData.ciudades",{country_id:countryId,state_id:stateId, delivery:true}).then(function(res,ex){

			if(ex){
				$.cmDialogError(ex);
				return;
			}

			this.ui.ciudad.cmPopulate(res);

		}.bind(this));
	},

	onColor:function(slug){
		this.init();
		var color = this.ui.color.cmGetValue();

		this.ui.talla.html('');

		rpc.call("TiendaData.tallas",{color:color,slug:slug}).then(function(res,ex){

			if(ex){
				$.cmDialogError({
					message:ex.message
				});
				return;
			}

			this.ui.talla.cmPopulate(res);

		}.bind(this));
	},

};

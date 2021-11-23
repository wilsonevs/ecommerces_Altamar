angular.module('cm.service')

.service('cm', function($window, $ionicPopup, $ionicLoading, $cordovaToast, jsonrpc, $q, cfg) {
	var self = this;

	//angular.extend(this, $service('cm.ui', {$scope: $scope}));

	this.set = function(key, value) {
		if (value === undefined || value === "undefined") value = null;

		$window.localStorage.setItem(key, JSON.stringify(value));
	}

	this.get = function(key, defaultValue) {

		var value = $window.localStorage.getItem(key)
		if (value !== null && value !== undefined && value !== "undefined") {

			try {
				return JSON.parse(value);
			} catch (e) {
				return defaultValue;
			}
		}

		return defaultValue;
	}

	this.rpc = function(method, params,options) {

		var opt = angular.extend({
			loading:false
		},options);

		if (params === undefined) {
			params = {};
		}

		try {
			params.token = JSON.parse($window.localStorage.getItem('token'));
			params.version = cfg.version;
		} catch (e) {
			console.log('Failed parsing json token');
		}

		if( opt.loading ){
			self.showLoading();
		}

		return jsonrpc.request(method, params)

		.then(function(res){
			if( opt.loading ){
				self.hideLoading();
			}

			return res;
		})

		.catch(function(ex){
			if( opt.loading ){
				self.hideLoading();
			}

			//propagate exception
			throw ex;
		});
	}


	//jsonRequest deprecated
	this.jsonRequest = this.rpc;

	this.showLoading = function(p) {
		p = p || {};
		p.template = p.message || "";
		//http://loading.io/
		p.template += '<br/><img src="img/loading.svg" style="max-width:70px;"/>';
		//p.template += '<br/><ion-spinner class="cm-loading-spinner" icon="lines"></ion-spinner>';
		p.hideOnStateChange = true;
		//p.delay = p.delay || 300;
		$ionicLoading.show(p);

		/*
		$ionicLoading.show({
			template: 'Cotizando...',
			delay: 300
		});
		*/
	}

	this.hideLoading = function() {
		$ionicLoading.hide();
	}

	this.localNotification = function(p) {
		if (window.cordova && window.cordova.plugins && window.cordova.plugins.notification) {
			cordova.plugins.notification.local.schedule(p);
		} else {
			console.log('notification', p);
		}
	}



	this.info = function(p) {
		return $ionicPopup.alert({
			title: p.title || "Información",
			template: p.message,
			okText: p.okText || 'Aceptar'
		});
	}


	this.error = function(p) {
		return $ionicPopup.alert({
			title: p.title || "Error",
			template: p.message,
			okText: p.okText || 'Aceptar'
		});
	}

	this.confirm = function(p) {
		return $ionicPopup.confirm({
			title: p.title || "Confirmación",
			template: p.message,
			okText: p.okText || 'Aceptar',
			cancelText: p.cancelText || 'Cancelar'
		});
	}

	this.range = function(min, max, step) {
		step = step || 1;
		var input = [];
		for (var i = min; i <= max; i += step) input.push(i);
		return input;
	};


	this.toDateString = function(date) {
		var y = date.getFullYear();
		var m = String(date.getMonth() + 1);
		var d = String(date.getDate());

		while (m.length < 2) m = "0" + m;
		while (d.length < 2) d = "0" + d;
		return y + "-" + m + "-" + d;
	}

	//metodos de fecha y hora
	this.dateDmyToYmd = function(dateString) {
		var tmp = dateString.split('/');
		while (tmp[0].length < 2) tmp[0] = "0" + tmp[0];
		while (tmp[1].length < 2) tmp[1] = "0" + tmp[1];
		return tmp[2] + "-" + tmp[1] + "-" + tmp[0];
	}


	this.currentTimeString = function() {
		//return (new Date()).toLocaleTimeString();
		var date = new Date()
		var h = date.getHours();
		var m = date.getMinutes();
		var s = date.getSeconds();
		return h + ":" + m + ":" + s;
	}

	this.currentDateString = function() {
		return this.toDateString(new Date());
		//return this.dateDmyToYmd((new Date()).toLocaleDateString().substring(0, 10));
	}

	this.currentTimestampString = function() {
		return this.currentDateString() + " " + this.currentTimeString();
	}

	/*
	this.localeTimestampString = function() {
		return this.localeDateString() + " " + this.localeTimeString();
	}

	this.dateToLocaleDateString = function(dateObject) {
		return this.dateDmyToYmd(dateObject.toLocaleDateString().substring(0, 10));
	}

	this.timeToLocaleTimeString = function(timeObject) {
		return (timeObject).toLocaleTimeString();
	}
	*/

	this.timezoneOffset = function() {
		return (new Date()).getTimezoneOffset();
	}

	/*
	this.currentTimestampString = function(){
		//toISOString da la hora GMT, no la local
		cm.error({message:"Unimplemented currentTimestampString"});
		return null;
		//return (new Date()).toISOString();
	}
	*/

	//utilidades

	this.isEmpty = function(value) {
		if (value === null) return true;
		if (value === '') return true;
		if (angular.isString(value) && value.trim() === '') return true;
		if (angular.isArray(value) && value.length == 0) return true;
		if (angular.isObject(value) && JSON.stringify(value) == '{}') return true;

		return false;
	}

	this.arrayColumm = function(arr, field) {
		var res = [];
		for (var i = 0; i < arr.length; i++) {
			res.push(arr[i][field]);
		}

		return res;
	}

	this.toAsciiText = function() {
		var acentos = /[áéíóúñäëïöü]/g;
		var tr = {
			'á': 'a',
			'é': 'e',
			'í': 'i',
			'ó': 'o',
			'ú': 'u'
		};

		return t.replace(acentos,
			function($1) {
				//console.log('$1',$1);
				return tr[$1];
			}
		)
	};



	this.pouchdb = {};
	/*
	this.pouch.queryOld = function(db,options){
		var $scope = options.scope;

		options = angular.extend({
			fn: null
		}, options);



		function quitaacentos(t) {
			var acentos = /[áéíóúñäëïöü]/g;
			var tr = {
				'á':'a',
				'é':'e',
				'í':'i',
				'ó':'o',
				'ú':'u'
			};

			return t.replace(acentos,
				function($1) {
					//console.log('$1',$1);
					return tr[$1];
				}
			)
		}

		return $q(function(resolve, reject) {


			if (options.fn === null) {

				options.fn = function(doc, emit) {
					var field = null;
					var value = null;


					if( !options.filterFn(doc) ) return;

					//busqueda de texto
					var filter = options.filter.toLowerCase().replace(/\s+/, '');
					filter =quitaacentos(filter);

					var text = '';
					for (var k in options.filterFields) {
						//if( !angular.isDefined( doc[ options.filterFields[k] ] ) ) return;
						text += doc[options.filterFields[k]];
					}
					text = text.toLowerCase().replace(/\s+/, '');
					text =quitaacentos(text);

					if (text.indexOf(filter) === -1) return;

					//ordenamiento
					var emitFields = [];
					for (var k in options.orderBy) {
						emitFields.push(doc[options.orderBy[k]]);
					}

					//console.log("emit");
					emit(emitFields);
				}
			}


			db.query(options.fn, {
				include_docs: true
			})

			.then(function(res) {
				var rl = [];
				var doc = null;

				//console.log(res.rows);

				for (var k in res.rows) {
					doc = res.rows[k].doc;

					rl.push(options.transform(doc))
				}

				resolve(rl);
			})

			.catch(function(ex) {
				reject(ex);
			});

		}).

	};
	*/

	//query2

	this.pouchdb.query = function(db, p) {
		var pageOptions = {
			include_docs: true,
			descending: false
		};

		p = angular.extend({
			page: null,
			reset: false, //reset paginacion
			fn: null, //funcion de filtrado completa
			filterFn: null, //funcion de filtrado boleano
			filterValue: '',
			transform: null
		}, p);

		p.filterValue = p.filterValue !== null ? p.filterValue : '';

		if (p.page) {
			p.page.last = true;

			if (p.reset) {
				p.page.rows = [];
				delete p.page.options.startkey;
				delete p.page.options.skip;
				delete p.page.options.offset;
			}
		}



		if (p.page !== null) {
			pageOptions = angular.extend(pageOptions, p.page.options);
		}

		if (p.desc) {
			pageOptions.descending = p.desc;
		}


		if (p.fn === null) {

			p.fn = function(doc, emit) {
				//console.log('cm.pouch.query p.fn');
				if (!p.filterFn(doc)) return;


				var field = null;
				var value = null;


				//busqueda de texto
				var filter = p.filterValue.toLowerCase().replace(/\s+/, '');
				//filter = self.toAsciiText(filter);


				var text = '';
				for (var k in p.filterFields) {
					//if( !angular.isDefined( doc[ p.filterFields[k] ] ) ) return;
					text += doc[p.filterFields[k]];
				}
				text = text.toLowerCase().replace(/\s+/, '');
				//text = self.toAsciiText(text);

				//console.log("text=",text,"filter=",filter, text.indexOf(filter));

				if (text.indexOf(filter) === -1) return;

				//ordenamiento
				var emitFields = [];
				for (var k in p.orderBy) {
					emitFields.push(doc[p.orderBy[k]]);
				}

				emitFields.push(doc._id);

				//console.log("emit",emitFields);
				emit(emitFields);
			}
		}

		function hashCode(s) {
			return s.split("").reduce(function(a, b) {
				a = ((a << 5) - a) + b.charCodeAt(0);
				return a & a
			}, 0);
		}

		var indexName = "index" + hashCode(p.filterFn.toString());
		//console.log('indexName', indexName);

		var myIndex = '{"_id": "_design/' + indexName + '","views": {"' + indexName + '":{"map": null}}}';
		//console.log('myIndex',myIndex);

		myIndex = JSON.parse(myIndex);
		myIndex.views[indexName].map = p.fn.toString();


		//console.log('myIndex',myIndex);

		/*
		db.put(myIndex)

		.then(function(res) {
			console.log('indice Creado',res);
		})

		.catch(function(ex){
			console.log('Fallo creando indice',ex,myIndex);
		});
		*/


		//db.get(myIndex._id).then(function(res){
		//console.log(res);

		//db.remove(res);

		//el indice no existe
		/*
		if( res.status == 404 || res.views[indexName].map != myIndex.views[indexName].map  ){

			//creo el indice
			db.put(myIndex).then(function() {
				console.log('indice Creado');
			});
		}
		*/

		//});


		return $q(function(resolve, reject) {
			db.query((p.view || p.fn), pageOptions)

			.then(function(res) {
				//console.log("then","total_rows=",res.total_rows," rows=",res.rows.length);
				/*
				if( p.view ){
					for(var i=res.rows.length-1; i>=0;i--){
						if( !p.filterFn(res.rows[i].doc) ){
							res.rows.splice(i,1);
						}
					}
				}
				*/

				if (p.page !== null) {

					if (res.rows.length > 0) {
						p.page.options.startkey = res.rows[res.rows.length - 1].key;
						p.page.options.skip = 1;
					}
				}

				if (p.transform === null) {

					if (p.page !== null) {
						p.page.rows = p.page.rows.concat(res.rows);
						p.page.last = res.rows.length == 0 || res.rows.length == res.total_rows;
					}

					resolve(res);
					return;
				}

				var rl = [];
				var doc = null;

				//console.log(res.rows);

				for (var k in res.rows) {
					doc = res.rows[k].doc;

					rl.push(p.transform(doc))
				}

				resolve(rl);
			})

			.catch(function(ex) {
				reject(ex);
			});
		});

	}

	this.ui = {};
	this.ui.toast = {};

	this.ui.toast.showCenter = function(message) {

		if( ionic.Platform.isWebView() && $cordovaToast !== undefined ){
			$cordovaToast.showLongCenter(message).then(function(success) {
				// success
			}, function(error) {
				// error
			});
			return;
		}


		var popup = $ionicPopup.show({
			template: '<center>'+message+'</center>',
			title: 'Mensaje',
			//scope: {},
			buttons: []
		});

		setTimeout(function(){
			if( popup.close ){
				popup.close();
			}

		},2200);
	}



})

;

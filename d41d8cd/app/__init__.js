// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'starter' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'
// 'starter.controllers' is found in controllers.js
angular.module('starter', [
	'ionic',
	'cm.service',
	'cm.directives',
	'starter.controllers',
	'starter.services',
	'angular-jsonrpc-client',
	'ui.tree',
	'ui.tinymce',
	'ngCordova'
])


.constant('cfg', {
	// env: 'www',
	env: 'loc',
	version: '5.00.05',

	oneSignal: {
		appId: "", //
		googleProjectNumber: "" //ID:
	}
})


.run(function($ionicPlatform) {
	$ionicPlatform.ready(function() {
		// Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
		// for form inputs)
		if (window.cordova && window.cordova.plugins.Keyboard) {
			cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
			cordova.plugins.Keyboard.disableScroll(true);

		}
		if (window.StatusBar) {
			// org.apache.cordova.statusbar required
			StatusBar.styleDefault();
		}
	});
})

.config(function($stateProvider, $urlRouterProvider, $ionicConfigProvider, jsonrpcConfigProvider, cfg) {

	//native scrolling

	$ionicConfigProvider.scrolling.jsScrolling(false);
	if (navigator.userAgent.indexOf("Mobile") == -1) {
		$ionicConfigProvider.views.transition('android');
	}

	var url = null;

	url = 'rpc/1.0/server.php';

	jsonrpcConfigProvider.set({
		url: url,
		returnHttpPromise: false
	});

	$ionicConfigProvider.backButton.text(''); //.icon('ion-ios7-arrow-left');
	$ionicConfigProvider.backButton.previousTitleText(false);

	$stateProvider

	.state('app', {
		url: '/app',
		abstract: true,
		templateUrl: 'app/Menu.html',
		controller: 'App'
	})

	/*
	.state('app.Login', {
		url: '/login',
		views: {
			'menuContent': {
				templateUrl: 'app/Login.html'
			}
		}
	})
	*/

	.state('Login', {
		url: '/login',
		templateUrl: 'app/Login.html',
		controller: 'Login'

		/*views: {
			'menuContent': {
				templateUrl: 'app/Login.html'
			}
		}*/
	})

	.state('app.Inicio', {
		url: '/inicio',
		views: {
			'menuContent': {
				templateUrl: 'app/Inicio.html',
				controller: 'Inicio'
			}
		}
	})

	.state('app.Admin', {
		url: '/admin'
	})

	.state('app.Admin_UsuarioL', {
		url: '/usuario-l',
		views: {
			'menuContent': {
				templateUrl: 'app/Admin/UsuarioL.html',
				controller: 'Admin_UsuarioL'
			}
		}
	})

	.state('app.Admin_UsuarioE', {
		url: '/usuario-e/:user_id',
		views: {
			'menuContent': {
				templateUrl: 'app/Admin/UsuarioE.html',
				controller: 'Admin_UsuarioE'
			}
		}
	})

	.state('app.Admin_GrupoL', {
		url: '/grupo-l',
		views: {
			'menuContent': {
				templateUrl: 'app/Admin/GrupoL.html',
				controller: 'Admin_GrupoL'
			}
		}
	})

	.state('app.Admin_GrupoE', {
		url: '/grupo-e/:group_id',
		views: {
			'menuContent': {
				templateUrl: 'app/Admin/GrupoE.html',
				controller: 'Admin_GrupoE'
			}
		}
	})


	.state('app.EavDesign', {
		url: '/eav-design'
	})



	.state('app.EavDesign.EavTypeL', {
		url: '/type-l/:page',
		views: {
			'menuContent@app': {
				templateUrl: 'app/EavDesign/EavTypeL.html',
				controller: 'EavTypeL'
			}
		}
	})

	.state('app.EavDesign.EavTypeE', {
		url: '/type-e/:type_id',
		views: {
			'menuContent@app': {
				templateUrl: 'app/EavDesign/EavTypeE.html',
				controller: 'EavTypeE'
			}
		}
	})

	.state('app.EavDesign.EavAttrE', {
		url: '/type-e/:type_id/attr-e/:attr_id',
		views: {
			'menuContent@app': {
				templateUrl: 'app/EavDesign/EavAttrE.html',
				controller: 'EavAttrE'
			}
		}
	})


	.state('app.EavDesign_EavMenuL', {
		url: '/menu-l',
		views: {
			//'menuContent@app': {
			'menuContent': {
				templateUrl: 'app/EavDesign/EavMenuL.html',
				controller: 'EavDesign_EavMenuL'
			}
		}
	})

	.state('app.EavDesign_EavMenuE', {
		url: '/menu-e/:category_id',
		views: {
			'menuContent': {
				templateUrl: 'app/EavDesign/EavMenuE.html',
				controller: 'EavDesign_EavMenuE'
			}
		}
	})



	.state('app.EavRuntime', {
		url: '/eav-runtime'
	})


	.state('app.EavRuntime_EavMenuView', {
		url: '/menu',
		views: {
			'menuContent': {
				templateUrl: 'app/EavRuntime/EavMenuView.html',
				controller: 'EavRuntime_EavMenuView'
			}
		}
	})


	.state('app.EavRuntime_EavItemL', {
		url: '/item-l/:category_id',
		views: {
			'menuContent': {
				templateUrl: 'app/EavRuntime/EavItemL.html',
				controller: 'EavRuntime_EavItemL'
			}
		}
	})

	.state('app.EavRuntime_EavItemE', {
		url: '/item-e/category-:category_id/item-:item_id',
		views: {
			'menuContent': {
				templateUrl: 'app/EavRuntime/EavItemE.html',
				controller: 'EavRuntime_EavItemE'
			}
		}
	})

	.state('app.EavRuntime_MenuE', {
		url: '/rte/menu-e/:category_id',
		cache:false,
		views: {
			'menuContent': {
				templateUrl: 'app/EavRuntime/EavMenuE.html',
				controller: 'EavRuntime_MenuE'
			}
		}
	})




	.state('app.Tienda_PedidosL', {
		url: '/tienda/pedidos-l',
		views: {
			'menuContent': {
				templateUrl: 'app/Tienda/PedidosL.html',
				controller: 'Tienda_PedidosL'
			}
		}
	})

	.state('app.Tienda_PedidosE', {
		url: '/tienda/pedido-e/:id_carro',
		views: {
			'menuContent': {
				templateUrl: 'app/Tienda/PedidosE.html',
				controller: 'Tienda_PedidosE'
			}
		}
	})


    .state('app.Importacion', {
        url: '/importacion',
        views: {
            'menuContent': {
                templateUrl: 'app/Importacion/Importacion.html',
                controller: 'Importacion'
            }
        }
    })

	;
	// if none of the above states are matched, use this as the fallback
	$urlRouterProvider.otherwise('/login');
});


angular.module('starter.controllers', []);
angular.module('starter.services', []);

angular.module('starter.controllers')

.controller('Login', function($scope, $state,cm,cfg) {

	$scope.appVersion = cfg.version;

	$scope.dr = {
		login:'',
		password:''
	};

	$scope.$on('$ionicView.beforeEnter',function(){
		$scope.dr.password='';
	});

	$scope.autenticar = function(){
		//$state.go('app.Inicio');

		var dr=angular.copy($scope.dr);

		cm.rpc('App.signin',dr)

		.then(function(res){
			$scope.$root.si = res.account;
			console.log(res);
			$state.go('app.EavRuntime_EavMenuView');
		})

		.catch(function(ex){
			cm.error(ex);
		});
	}
	return;
});

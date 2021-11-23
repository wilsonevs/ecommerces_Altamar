angular.module('starter.controllers').controller('App', function($scope, $ionicModal,cm, $timeout, $state, cfg) {

	$scope.$root.appVersion = cfg.version;

	$scope.$root.si = {
		user_type:'architect'
	};


	//para el copyright
	$scope.year = (new Date()).getFullYear();


	/*
	if( $scope.$root.si === undefined ){
		cm.rpc('App.checkSession')

		.then(function(res){
			if( res.account === undefined ){
				$state.go('Login');
				return;
			}

			console.log('res',res);
			$scope.$root.si = res.account;

		})

		.catch(function(ex){
		});

	}
	*/


	$scope.$on('$ionicView.beforeEnter', function(e) {

	});


	$scope.logout = function() {
		$state.go('Login');
	}

	$scope.hasModule = function(name){
		return cmCfg.modules.indexOf(name) != -1;
	}

})

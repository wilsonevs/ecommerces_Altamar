angular.module('starter.controllers')

.controller('Inicio', function($scope, $state) {


	$scope.guardar = function(){
		console.log('guardar');
		$state.go('app.EavRuntime.EavItemsL');
	}
	return;
});

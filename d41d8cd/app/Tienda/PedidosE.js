angular.module('starter.controllers')

.controller('Tienda_PedidosE', function($scope, $state, cm, $ionicSideMenuDelegate) {

	$scope.d = {

	};

	$scope.ers = {

	};

	$scope.dr = {
		id_pedido:null,
		estado:'reserva',
		notas:''
	};

	$scope.$on('$ionicView.afterEnter',function(){
		console.log( $state.params );

		cm.rpc("Tienda_PedidosE.load",{
			id_carro: $state.params.id_carro
		},{loading:true})

		.then(function(res){
			angular.extend($scope.ers,res.ers);
			delete res.ers;

			angular.extend($scope.d,res);
			$scope.dr.estado=res.enc.estado;
			$scope.dr.notas = res.enc.notas;
		})

		.catch(function(ex){
			cm.error(ex);
		});
	});

	$scope.save = function(){

		var dr = angular.copy($scope.dr);
		dr.id_pedido = $scope.d.enc.id_pedido;
		dr.estado = dr.estado;

		cm.rpc('Tienda_PedidosE.save',dr,{loading:true})

		.then(function(res){
			$state.go('app.Tienda_PedidosL');
		})

		.catch(function(ex){
			cm.error(ex);
		});

	}

	$scope.eliminar = function(){

		var dr = angular.copy($scope.dr);
		dr.id_pedido = $scope.d.enc.id_pedido;
		dr.id_carro = $state.params.id_carro;

		cm.rpc('Tienda_PedidosE.eliminar',dr,{loading:true})

		.then(function(res){
			$state.go('app.Tienda_PedidosL');
		})

		.catch(function(ex){
			cm.error(ex);
		});

	}

	$scope.print = function(){
		//$ionicSideMenuDelegate.toggleLeft(false);
		window.print();
	}

	$scope.cancel = function(){
		$state.go('app.Tienda_PedidosL');
	}
});

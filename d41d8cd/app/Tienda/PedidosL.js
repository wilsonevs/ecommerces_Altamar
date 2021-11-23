angular.module('starter.controllers')

.controller('Tienda_PedidosL', function($scope, $state, cm) {
	$scope.title="Pedidos";

	$scope.d = {
	};

	$scope.dr = {
		filter: ""
	};

	$scope.page = {
		next: 1,
		count: 40,
		rows: [],
		last: false
	};

	$scope.$on('$ionicView.afterEnter', function() {
		$scope.aplicarFiltros(true, 'init');
	});

	$scope.aplicarFiltros = function(reset, source) {
		clearTimeout($scope.page.timeout);
		$scope.page.timeout = setTimeout(function() {
			$scope._aplicarFiltros(reset, source);
		}, 600);
	}

	$scope._aplicarFiltros = function(reset, source) {

		if (reset) {
			$scope.page.next = 1;
			$scope.page.rows = [];
			$scope.page.last = false;
		}

		cm.rpc('PedidosL.page', {
			page: $scope.page.next++,
			count: $scope.page.count,
			filter: $scope.dr.filter
		}, {
			loading: false
		})

		.then(function(res) {
			$scope.page.last = res.currentPage >= res.pageCount;
			$scope.page.rows = $scope.page.rows.concat(res.records);
		})

		.catch(function(ex) {
			$scope.page.last = true;
			cm.error(ex);
		})

		.finally(function() {
			$scope.$broadcast('scroll.refreshComplete');
			$scope.$broadcast('scroll.infiniteScrollComplete');
		});

	}

	$scope.editar = function(r){
		$state.go('app.Tienda_PedidosE', {
			id_carro: r.id_carro
		});
	}
});

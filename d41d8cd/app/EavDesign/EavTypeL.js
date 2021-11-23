angular.module('starter.controllers')

.controller('EavTypeL', function($scope, $state, cm) {
	$scope.title="Tipos de Datos";

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

		cm.rpc('EavTypeL.page', {
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



	$scope.nuevo = function() {
		$state.go('app.EavDesign.EavTypeE', {
			type_id: 'add'
		});
	}

	$scope.editar = function(r){
		$state.go('app.EavDesign.EavTypeE', {
			type_id: r.type_id
		});
	}
});

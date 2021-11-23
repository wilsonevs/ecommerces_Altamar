angular.module('starter.controllers')

.controller('Admin_UsuarioE', function($scope, $state, cm) {

	$scope.ers = {
		user_types: []
	};

	$scope.dr = {
		login: '',
		password: '',
		user_name: '',
		user_type: {
			data: null
		},
		email: '',
		notes: ''
	};

	$scope.$on('$ionicView.afterEnter', function() {

		if ($state.params.user_id == 'add') {
			cm.rpc('Admin_UsuarioE.ers', {}, {
				loading: true
			})

			.then(function(res) {
				angular.extend($scope.ers, res);
			})

			.catch(function(ex) {
				cm.error(ex);
			});

			return;
		}

		cm.rpc("Admin_UsuarioE.load", {
			user_id: $state.params.user_id
		}, {
			loading: true
		})

		.then(function(res) {
			console.log(res);
			angular.extend($scope.ers, res.ers);
			delete res.ers;

			angular.extend($scope.dr, res);
			$scope.dr.user_type = {
				data: res.user_type
			};
		})

		.catch(function(ex) {
			cm.error(ex);
		});
	});

	$scope.save = function() {

		var dr = angular.copy($scope.dr);
		dr.user_type = dr.user_type.data;

		cm.rpc('Admin_UsuarioE.save', dr, {
			loading: true
		})

		.then(function(res) {
			$state.go('app.Admin_UsuarioL');
		})

		.catch(function(ex) {
			cm.error(ex);
		});

	}

	$scope.cancel = function() {
		$state.go('app.Admin_UsuarioL');
	}



	$scope.acGroups = function(filter) {

		return cm.rpc("Admin_UsuarioE.acGroups", {
			filter: filter
		})

		.catch(function(ex) {
			cm.error(ex);
		});
	}

});

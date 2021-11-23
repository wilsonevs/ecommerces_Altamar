angular.module('starter.controllers')

.controller('EavDesign_EavMenuE', function($scope, $state, cm) {

	$scope.d = {

	};

	$scope.ers = {
		types: [],
		targers: []
	};

	$scope.dr = {
		category_name: '',
		type_id: null,
		slug:'',
		url: '',
		target: {data: '_self'},
		events:{
			onclick:'',
			onmouseover:'',
			onmouseout:''
		},
		custom_export: '',
		notes: ''
	};

	$scope.$on('$ionicView.afterEnter', function() {

		if ($state.params.category_id == 'add') {
			cm.rpc('EavDesign_EavMenuE.ers', {}, {
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

		cm.rpc("EavDesign_EavMenuE.load", {
			category_id: $state.params.category_id
		}, {
			loading: true
		})

		.then(function(res) {
			console.log(res.ers);

			angular.extend($scope.ers, res.ers);
			delete res.ers;


			angular.extend($scope.dr, res);
			//$scope.dr.type_id = {data:res.type_id};
			$scope.dr.target = {
				data: res.target
			};
		})

		.catch(function(ex) {
			cm.error(ex);
		});
	});

	$scope.save = function() {
		var dr = angular.copy($scope.dr);
		console.log(dr);
		//return;

		dr.type_id = dr.type_id.length >0 ? dr.type_id[0].data : -1;
		dr.target = dr.target.data;

		cm.rpc('EavDesign_EavMenuE.save', dr, {
			loading: true
		})

		.then(function(res) {
			$state.go('app.EavDesign_EavMenuL');
		})

		.catch(function(ex) {
			cm.error(ex);
		});

	}

	$scope.cancel = function() {
		$state.go('app.EavDesign_EavMenuL');
	}


	$scope.acTypes = function(filter) {

		return cm.rpc("EavDesign_EavMenuE.acTypes", {
			filter: filter
		})

		.catch(function(ex) {
			cm.error(ex);
		});
	}


});

angular.module('starter.controllers')

.controller('EavRuntime_MenuE', function($scope, $state, cm) {

	$scope.d = {

	};

	$scope.ers = {

	};

	$scope.dr = {
		category_name:'',
		url:'',
		target:null,
		events:{
			onclick:'',
			onmouseover:'',
			onmouseout:''
		},
		notes:''
	};

	$scope.$on('$ionicView.enter',function(){

		cm.rpc("EavRuntime_MenuE.load",{
			category_id:$state.params.category_id
		},{loading:true})

		.then(function(res){
			angular.extend($scope.ers,res.ers);
			delete res.ers;

			angular.extend($scope.dr,res);
			$scope.dr.target = {
				data: res.target
			};

		})

		.catch(function(ex){
			cm.error(ex);
		});
	});

	$scope.save = function(){

		var dr = angular.copy($scope.dr);
		dr.target = dr.target.data;

		cm.rpc('EavRuntime_MenuE.save',dr,{loading:true})

		.then(function(res){
			$state.go('app.EavRuntime_EavMenuView');
		})

		.catch(function(ex){
			cm.error(ex);
		});

	}

	$scope.cancel = function(){
		$state.go('app.EavRuntime_EavMenuView');
	}
});

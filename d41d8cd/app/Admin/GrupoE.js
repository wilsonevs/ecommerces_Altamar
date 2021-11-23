angular.module('starter.controllers')

.controller('Admin_GrupoE', function($scope, $state, cm) {


	$scope.init = function(){
		$scope.ers = {

		};

		$scope.dr = {
			group_name:'',
			notes:''
		};

	}

	$scope.$on('$ionicView.afterEnter',function(){
		$scope.init();


		if( $state.params.group_id == 'add' ){
			cm.rpc('Admin_GrupoE.ers',{},{loading:true})

			.then(function(res){
				angular.extend($scope.ers,res);
			})

			.catch(function(ex){
				cm.error(ex);
			});

			return;
		}

		cm.rpc("Admin_GrupoE.load",{group_id:$state.params.group_id},{loading:true})

		.then(function(res){

			angular.extend($scope.ers,res.ers);
			delete res.ers;

			angular.extend($scope.dr,res);
		})

		.catch(function(ex){
			cm.error(ex);
		});
	});

	$scope.save = function(){

		var dr = angular.copy($scope.dr);
		cm.rpc('Admin_GrupoE.save',dr,{loading:true})

		.then(function(res){
			$state.go('app.Admin_GrupoL');
		})

		.catch(function(ex){
			cm.error(ex);
		});

	}

	$scope.cancel = function(){
		$state.go('app.Admin_GrupoL');
	}
});

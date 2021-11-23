angular.module('starter.controllers')

.controller('EavRuntime_EavItemE', function($scope, $state, cm,$ionicHistory) {

	$scope.title = "Nuevo Item";

	$scope.d = {
		id_field:'item_id',
		eav_api:{}
	};

	$scope.ers = {
		eav:null
	};

	$scope.dr = {
		category_id:null,
		item_id:null
	};

	$scope.$on('$ionicView.afterEnter',function(){
		$scope.dr.category_id = $state.params.category_id;
		$scope.dr.item_id = $state.params.item_id;

		$scope.load();
	});

	$scope.refresh = function(){
		$scope.load();
	}

	$scope.load = function(){

		if( $state.params.item_id == 'add' ){

			cm.rpc('EavRuntime_EavItemE.ers',{
				'category_id': $state.params.category_id,
				'item_id':''
			},{loading:true})

			.then(function(res){
				angular.extend($scope.ers,res);
			})

			.catch(function(ex){
				cm.error(ex);
			});

			return;
		}


		cm.rpc("EavRuntime_EavItemE.load",{
			'category_id': $state.params.category_id,
			'item_id': $state.params.item_id
		},{loading:true})

		.then(function(res){
			//console.log('ers',res.ers);return;
			angular.extend($scope.ers,res.ers);
			delete res.ers;

			angular.extend($scope.dr,res);
			$scope.title = "Item en "+$scope.ers.eav.st.category.category_path;

			//$scope.dr.field = {data:res.field};
		})

		.catch(function(ex){
			cm.error(ex);
		});
	}

	$scope.save = function(callback){

		var dr = $scope.d.eav_api.getRecord();
		dr.category_id = $scope.dr.category_id;
		dr.item_id = $scope.dr.item_id;

		cm.rpc('EavRuntime_EavItemE.save',dr,{loading:true})

		.then(function(res){
			$scope.dr.item_id = res.item_id;

			if( angular.isFunction(callback) ){
				callback();
			}

		})

		.catch(function(ex){
			cm.error(ex);
		});

	}

	$scope.saveAndBack = function(){
		$scope.save(function(){
			//$ionicHistory.goBack();
			$state.go('app.EavRuntime_EavItemL',{category_id:$state.params.category_id});
		});
	}

	$scope.saveAndContinue = function(){
		$scope.save(function(){
			$state.go('app.EavRuntime_EavItemE',{
				category_id:$state.params.category_id,
				item_id:'add'
			},{
				reload:true
			});
		});
	}

	$scope.remove = function(){

		cm.rpc('EavRuntime_EavItemE.remove',{item_id:$state.params.item_id})

		.then(function(){
			$state.go('app.EavRuntime_EavItemL',{
				category_id:$state.params.category_id
			});
		})

		.catch(function(ex){
			cm.error(ex);
		});
	}

	$scope.duplicate = function(){
		cm.error({message:"Pendiente de implementación"});
	}

	$scope.cancel = function(){
		//$ionicHistory.goBack();
		$state.go('app.EavRuntime_EavItemL',{
			category_id:$state.params.category_id
		});
	}
});

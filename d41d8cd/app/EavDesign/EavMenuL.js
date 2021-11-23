angular.module('starter.controllers')

.controller('EavDesign_EavMenuL', function($scope, $state, cm, $ionicSideMenuDelegate,$filter) {
	$ionicSideMenuDelegate.canDragContent(false);
	$scope.title = "EavMenuL";

	$scope.d = {
		tree_options: {
			dropped:function(event){
				$scope.save();
			}
		},
		menu: [],
		reorder: false,
		filter: ''
	};


	$scope.$on('$ionicView.loaded', function() {

	});

	$scope.$on('$ionicView.beforeEnter', function() {
		$scope.loadMenu();
	});


	$scope.buildMenuStruct = function(res) {
		$scope.d.menu = [];

		var r = null;
		var nodeArr = null;
		var parent = null;

		for(var i=0;i<res.length;i++){
			r = res[i];

			if(r.parent_id == '-1') continue;

			for(var j=0;j<res.length;j++){
				if( r.parent_id == res[j].category_id ){
					res[j].nodes = res[j].nodes || [];
					res[j].nodes.push(r);
				}
			}
		}

		for(var i=0;i<res.length;i++){
			r = res[i];
			if(r.parent_id != '-1') continue;

			$scope.d.menu.push(r);
		}

	}

	$scope.loadMenu = function() {
		cm.rpc("EavDesign_EavMenuL.load", {}, {
			loading: true
		})

		.then(function(res) {
			$scope.d.menu = [];
			$scope.buildMenuStruct(res);
		})

		.catch(function(ex) {
			cm.error(ex);
		});
	}

	$scope.refresh = function() {
		$scope.loadMenu();
	}



	$scope.save = function() {
		//console.log(JSON.stringify($scope.d.menu));
		var dr = angular.copy($scope.d.menu);
		cm.rpc('EavDesign_EavMenuL.reorder',{items:dr})

		.then(function(res){

		})

		.catch(function(ex){
			cm.error(ex);
		});
	}

	$scope.itemEdit = function(r) {
		$state.go('app.EavDesign_EavMenuE', {
			category_id: r.category_id
		});
	}


	$scope.itemRemove = function(scope) {
		var nodeData = scope.$modelValue;

		cm.confirm({message:"Seguro que desea eliminar la categoria?"})

		.then(function(res) {
			if (!res) return;
			cm.rpc('EavDesign_EavMenuL.delete',{category_id:nodeData.category_id},{loading:true})
			scope.remove();
		}).catch(function(ex){
				cm.error(ex);
		});
	};

	/*
	$scope.toggle = function(scope) {
		scope.toggle();
	};

	$scope.moveLastToTheBeginning = function() {
		var a = $scope.data.pop();
		$scope.data.splice(0, 0, a);
	};
	*/

	$scope.newItem = function(){

		var dr = {
			automatic:true,
		};

		cm.rpc('EavDesign_EavMenuE.save', dr, {
			loading: true
		})

		.then(function(res) {
			$scope.refresh();
		})

		.catch(function(ex) {
			cm.error(ex);
		});

	}

	$scope.newSubItem = function(scope) {
		var nodeData = scope.$modelValue;
		//var parent = scope.$parent.$modelValue;

		var dr = {
			automatic:true,
			parent_id: nodeData.category_id,
		};

		//console.log('parent',parent);
		//console.log(scope);
		//console.log(dr);

		cm.rpc('EavDesign_EavMenuE.save', dr, {
			loading: true
		})

		.then(function(res) {
			$scope.refresh();
		})

		.catch(function(ex) {
			cm.error(ex);
		});
	}

	$scope.itemFilter = function(r) {
		if( r === null ||r === undefined ) return true;
		$scope.d.filter = $scope.d.filter ||'';
		return $filter('filter')([r], $scope.d.filter).length > 0;
	}


	$scope.collapseAll = function() {
		$scope.$broadcast('angular-ui-tree:collapse-all');
	};

	$scope.expandAll = function() {
		$scope.$broadcast('angular-ui-tree:expand-all');
	};

	$scope.viewItems = function(node){
		if($scope.d.reorder) return;
		if( node.type_id < 0) return;

		$state.go('app.EavRuntime_EavItemL',{category_id:node.category_id});
	}

});

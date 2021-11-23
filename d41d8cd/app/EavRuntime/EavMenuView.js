angular.module('starter.controllers')

.controller('EavRuntime_EavMenuView', function($scope, $state,cm, $ionicSideMenuDelegate,$filter) {
	$ionicSideMenuDelegate.canDragContent(false);
	$scope.title = "EavMenuL";


	$scope.d ={
		tree_options:{},
		menu:[],
		reorder:false,
		filter:''
	};


	$scope.$on('$ionicView.loaded',function(){
		$scope.loadMenu();
	});

	$scope.$on('$ionicView.afterEnter',function(){

	});





	$scope.findNodeParent = function(menu,r){
		//console.log('menu',menu,'r',r);

		var parent = null;

		for(var i=0;i<menu.length;i++){
			if( r.parent_id == menu[i].category_id ){
				return menu[i];
			}

			if( menu[i].nodes !== undefined ){

				for(var j=0;j<menu[i].nodes.length;j++){
					parent = $scope.findNodeParent(menu[i].nodes,r);
					if( parent!==null){
						return parent;
					}
				}

			}
		}

		return null;
	}

	$scope.buildMenuStruct = function(res){
		$scope.d.menu = [];

		var r = null;
		var nodeArr = null;
		var parent = null;

		for(var i=0;i<res.length;i++){
			r = res[i];

			if( r.parent_id=='-1'){
				$scope.d.menu.push(r);
				continue;
			}

			parent = $scope.findNodeParent($scope.d.menu,r);
			if( parent === null ){
				continue;
			}

			if( parent.nodes === undefined ){
				parent.nodes = [];
			}

			parent.nodes.push(r);
		}
	}

	$scope.loadMenu = function(){
		cm.rpc("EavRuntime_EavMenuView.load",{},{loading:true})

		.then(function(res){
			$scope.d.menu = [];
			$scope.buildMenuStruct(res);
		})

		.catch(function(ex){
			cm.error(ex);
		});
	}

	$scope.refresh = function(){
		$scope.loadMenu();
	}

	$scope.itemFilter = function(r) {
		if( r === null || r === undefined ) return true;
		$scope.d.filter = $scope.d.filter || '';
		return $filter('filter')([r], $scope.d.filter).length > 0;
	}





	$scope.viewItems = function(node){
		if($scope.d.reorder) return;
		if( node.type_id=='-1') return; //categorias expandibles

		if( node.type_id=='-2'){ //enlaces
			$state.go('app.EavRuntime_MenuE',{category_id:node.category_id});
			return;
		}

		$state.go('app.EavRuntime_EavItemL',{category_id:node.category_id});
	}

	$scope.collapseAll = function() {
		$scope.$broadcast('angular-ui-tree:collapse-all');
	};

	$scope.expandAll = function() {
		$scope.$broadcast('angular-ui-tree:expand-all');
	};


});

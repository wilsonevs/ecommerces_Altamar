angular.module('starter.controllers')

.controller('EavRuntime_EavItemL', function($scope, $state, cm, $ionicScrollDelegate, $timeout) {
	$scope.title = "Contenidos";

	console.log('$state.params',$state.params);

	$scope.d = {
		id_field: 'item_id',

		select:false,
		order: false,
		stt:false,
		timeout:null
	};

	$scope.dr = {
		filter: ""
	};

	$scope.page = {
		next: 1,
		count: 40,
		rows: [],
		last: false,
		record_count:0,
		timeout:null
	};


	$scope.$on('$ionicView.afterEnter', function() {
		$scope.aplicarFiltros(true, 'init');
	});

	//fix Exception: Undefined property: stdClass::$category_id, file: EavItemL, line: 32
	$scope.$on('$ionicParentView.beforeLeave',function(){
		clearTimeout($scope.page.timeout);
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

			$ionicScrollDelegate.scrollTop();
		}

		cm.rpc('EavRuntime_EavItemL.page', {
			page: $scope.page.next++,
			count: $scope.page.count,
			filter: $scope.dr.filter,
			category_id: $state.params.category_id,
			archived: false
		}, {
			loading: false
		})

		.then(function(res) {
			$scope.page.last = res.currentPage >= res.pageCount;
			$scope.page.rows = $scope.page.rows.concat(res.records);
			$scope.page.record_count = res.recordCount;
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
		$state.go('app.EavRuntime_EavItemE',{
			category_id: $state.params.category_id,
			item_id:'add'
		});
	}

	$scope.editItem = function(r) {
		if( $scope.d.reorder ) return;

		var params = {
			category_id:r.category_id,
			item_id:r.item_id
		};
		//params[$scope.d.id_field] = r[ $scope.d.id_field];
		$state.go('app.EavRuntime_EavItemE',params);
	}


	$scope.hex2rgba= function(hex,alpha){
		var r = parseInt( hex.slice(1,3), 16 ),
		g = parseInt( hex.slice(3,5), 16 ),
		b = parseInt( hex.slice(5,7), 16 );

		return "rgb(" + r + ", " + g + ", " + b + alpha + ")";
	}

	$scope.moveItem = function(item, fromIndex, toIndex) {
		$scope.page.rows.splice(fromIndex, 1);
		$scope.page.rows.splice(toIndex, 0, item);

		var items = [];
		for(var i=0;i<$scope.page.rows.length;i++){
			items.push( $scope.page.rows[i].item_id);
		}

		cm.rpc('EavRuntime_EavItemL.reorder',{items:items})

		.then(function(res){

		})

		.catch(function(ex){
			cm.error(ex);
		});
	}

	/*
	$scope.removeItem = function(r,index){
		$scope.page.rows.splice(index, 1);
	}
	*/

	$scope.selectAll = function(){
		for(var i=0;i<$scope.page.rows.length;i++){
			$scope.page.rows[i].selected = true;
		}
	}

	$scope.deselectAll = function(){
		for(var i=0;i<$scope.page.rows.length;i++){
			$scope.page.rows[i].selected = false;
		}
	}

	$scope.deleteItems = function(){
		var items = [];

		for(var i=0;i<$scope.page.rows.length;i++){
			if( $scope.page.rows[i].selected ){
				items.push( $scope.page.rows[i].item_id);
			}
		}

		cm.rpc('EavRuntime_EavItemL.delete',{items:items},{loading:true})

		.then(function(res){
			$scope.aplicarFiltros(true,'delete');
		})

		.catch(function(ex){
			cm.error(ex);
		});

	}

	$scope.archiveItems = function(){
		var items = [];
		for(var i=0;i<$scope.page.rows.length;i++){
			if( $scope.page.rows[i].selected ){
				items.push( $scope.page.rows[i].item_id);
			}
		}

		cm.rpc('EavRuntime_EavItemL.archive',{items:items},{loading:true})

		.then(function(res){
			$scope.aplicarFiltros(true,'archive');
		})

		.catch(function(ex){
			cm.error(ex);
		});
	}


	$scope.scrollToTop = function() {
		$ionicScrollDelegate.scrollTop(true);
		$scope.d.stt = false;
	}

	$scope.monitorScrollPosition = function() {
		$scope.$apply(function() {
			$scope.d.stt = $ionicScrollDelegate.getScrollPosition().top > 150;
		});
	}

});

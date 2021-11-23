(function() {
	'use strict';
	var csPath = currentScriptPath();

	angular.module('cm.directives').directive('cmSearchDialog', function($ionicModal) {

		return {
			restrict: 'E',
			scope: {
				value: '=ngModel',
				itemsMethod:'&',
				addMethod:'&',
				itemsSelected:'&',
				multiple:'=',
				options:'=',
				search:'=',
				itemsLoad:'=',
				itemsTemplate:'=?'
			},
			replace: true,
			//require: 'ngModel',
			templateUrl: csPath.replace('.js', '.html'),
			controller: function($scope,$q) {
				//console.log('cmSearchDialog.controller');
				//console.log($scope);

				$scope.page = {
					last:true
				};

				$scope.search = cmLang.coalesceBool( $scope.search, true);
				$scope.multiple = cmLang.coalesceBool( $scope.multiple , false);
				$scope.itemsLoad = cmLang.coalesceBool( $scope.itemsLoad , false);
				$scope.itemsTemplate = angular.isDefined($scope.itemsTemplate) ? $scope.itemsTemplate : null;

				$scope.timeout = null;
				$scope.items = [
					{data:1,label:"Uno"},
					{data:2,label:"Dos"},
					{data:3,label:"Tres"}
				];

				$scope.items = [];
				$scope.selectedItems = [];

				if( $scope.options ){
					$scope.items = $scope.options;
				}

				$scope.viewModel = {
					query:'',
					multiple: $scope.multiple,
					search: $scope.search,
					addMethod: $scope.addMethod
				};


				$scope.applyFilters = function(reset,source){
					$scope.__scheduleSearch();
				}


				$scope.__scheduleSearch = function(e){
					//console.log('scheduleSearch', $scope.viewModel);
					clearTimeout($scope.timeout);
					$scope.timeout = setTimeout($scope.__search,600);
				}

				$scope.__search = function(){
					//console.log('search',$scope.viewModel);
					$scope.page.last = false;

					var qObject = {query:$scope.viewModel.query};


					var promise = $q.when( $scope.itemsMethod(qObject) );

					promise.then(function (promiseData) {
						//console.log('promise.then',promiseData);
						$scope.items = promiseData;
						$scope.page.last = true;

						//pull to refresh
						$scope.$broadcast('scroll.refreshComplete');
					});

				}

				$scope.clearSearchInput = function(){
					$scope.viewModel.query = '';
					$scope.__scheduleSearch();
				}

				$scope.isSelected = function(item){
					return $scope.selectedItems.indexOf( item.data ) >= 0;

				}


				$scope.selectItem = function(item){
					//console.log('selectItem',item);

					var index = $scope.selectedItems.indexOf( item.data );
					if( index == -1 ){
						$scope.selectedItems.push( item.data );
					}
					else {
						$scope.selectedItems.splice(index,1);
					}

					if( !$scope.multiple ) {
						$scope.done();
					}
				}


				$scope.done = function(){
					var items = [];
					var item = null;
					for(var k in $scope.items ){
						item = $scope.items[k];

						if( $scope.selectedItems.indexOf( item.data ) !== -1 ){
							items.push(item);
						}
					}

					var qObject = {items:items};
					$scope.itemsSelected(qObject);
					$scope.close();
				}

				if( $scope.itemsLoad ){
					//$scope.__scheduleSearch();
					$scope.__search();
				}



			},
			link: function(scope, elem, attrs,ngModelCtrl) {
				/*
				elem.on('click',function(){
					console.log('click');
				});
				*/

				scope.elem = elem;

				scope.close = function(){
					elem.remove();
				}

			}
		};
	});


})();

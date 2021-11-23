(function() {
	'use strict';

	var scripts = document.getElementsByTagName("script")
	var csPath = scripts[scripts.length - 1].src;

	angular.module('cm.directives').directive('cmTokenfield', function($ionicGesture) {
		return {
			scope: {
				value: '=ngModel',
				readonly:'=?ngReadonly',
				options: '=', //static options
				placeholder: '@',
				//itemsMethod:'='
				itemsMethod:'&',
				addMethod:'&',
				multiple:'=', //seleccion multiple
				search:'=?', //desactivar el campo de busqueda
				itemsLoad:'=?',
				itemsSelected:'&?',
				itemsTemplate:'=?'

			},
			restrict: 'E',
			replace: true,
			templateUrl: csPath.replace('.js', '.html'),

			controller: function($scope,$rootScope,$document,$compile,$q) {
				//console.log('cmTokenfield.controller',$scope);
				//console.log('cmTokenfield.options',$scope.options);

				console.log('$scope.itemsTemplate1',$scope.itemsTemplate);

				$scope.readonly =  cmLang.coalesceBool( $scope.readonly, false);

				$scope.value = cmLang.coalesceArray( $scope.value );
				$scope.value = angular.isArray($scope.value) ? $scope.value : [];


				$scope.search = cmLang.coalesceBool( $scope.search, true);
				$scope.multiple = cmLang.coalesceBool( $scope.multiple , false);

				$scope.itemsLoad = cmLang.coalesceBool( $scope.itemsLoad , false);

				//console.log($scope.itemsMethod);

				$scope.__itemsMethod = null;
				if( angular.isDefined( $scope.itemsMethod ) ){
					$scope.__itemsMethod = function(query){
						return $scope.itemsMethod({query:query});
					}
				}

				$scope.__itemsSelected = function(items){

					if( !$scope.multiple ){
						$scope.value = [ items[0] ];
						return;
					}

					for(var k in items){
						$scope.value.push( items[k] )
					}

					if( angular.isFunction( $scope.itemsSelected ) ){
						$scope.itemsSelected($scope.value);
					}
				}

				/*
				if (angular.isDefined($scope.itemsMethod)) {
					var promise = $q.when($scope.itemsMethod({query:'abc123'}));
				}
				*/

				$scope.test = function(){
					alert('test');
				};

				$scope.itemsContainerClick = function($event){
					console.log('itemsContainerClick');
					$event.preventDefault();
					$event.stopPropagation();
				}

				$scope.openSearchDialog = function($event){
					console.log('openSearchDialog',$event)

					if( $scope.readonly ) return;

					var searchDialog = angular.element('<cm-search-dialog></cm-search-dialog>');
					searchDialog.attr('items-method','__itemsMethod(query)');
					searchDialog.attr('items-selected','__itemsSelected(items)');
					searchDialog.attr('multiple','multiple');
					searchDialog.attr('search','search');
					searchDialog.attr('options','options');
					searchDialog.attr('add-method','addMethod()');
					searchDialog.attr('items-load','itemsLoad');
					searchDialog.attr('items-template','itemsTemplate');

					var searchDialogScope = $scope.$new(true);

					searchDialogScope.__itemsMethod  = $scope.__itemsMethod;
					searchDialogScope.__itemsSelected = $scope.__itemsSelected;
					searchDialogScope.multiple = $scope.multiple;
					searchDialogScope.options = $scope.options;
					searchDialogScope.search  = $scope.search;
					searchDialogScope.addMethod = $scope.addMethod;
					searchDialogScope.itemsLoad = $scope.itemsLoad;
					searchDialogScope.itemsTemplate = $scope.itemsTemplate;

					angular.element($document[0].body).append(searchDialog);
					$compile(searchDialog)(searchDialogScope);
				};

				$scope.removeItem = function(index){
					if( $scope.readonly ) return;
					$scope.value.splice(index,1);
				}

				/*
				$scope.$watch('value', function() {

					console.log('hey, myVar has changed!',$scope.value);

					if( !angular.isFunction($scope.itemsSelected) ) return;

					$scope.itemsSelected( $scope.value );

				});
				*/


			},

			link: function(scope, elem, attrs) {
				/*
				elem.on('click',function(){
					console.log('cmTokenfield.click');
				});
				*/
			}
		};
	});

})();

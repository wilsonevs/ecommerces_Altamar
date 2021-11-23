(function() {
	'use strict';
	var scripts = document.getElementsByTagName("script")
	var csPath = scripts[scripts.length - 1].src;

	angular.module('cm.directives').directive('cmTimepickerInput', ['$ionicPopup', function($ionicPopup) {
		return {
			scope: {
				ngModel: '=',
				displayMode:'='
			},
			require: 'ngModel',
			restrict: 'E',
			replace: true,
			templateUrl: csPath.replace('.js', '.html'),
			link: function($scope, $element, $attrs, ngModelCtrl) {
				console.log($scope);
				/*
				scope.$watch("value", function(value) {
					scope.updateDisplayValue();
				});
				*/


				$scope.ngModel = $scope.ngModel || "";
				$scope.displayMode = $scope.displayMode || "12";

				$scope.d = {
					displayValue:"",
					timepicker: ""
				};

				$scope.updateValue = function(){
					$scope.ngModel = $scope.d.timepicker;
					//console.log('$scope.d',$scope.d);

					if( $scope.displayMode=="12"){
						var tmp = $scope.ngModel.split(":");

						var hInt = parseInt(tmp[0]);
						if(  hInt == 0 ){
							tmp[0]="12";
						}

						if( hInt > 12 ){
							tmp[0] = hInt - 12;
						}

						tmp[2] = hInt < 12 ? "AM" : "PM";

						$scope.d.displayValue =tmp[0]+":"+tmp[1]+" "+tmp[2];
						return;
					}

					$scope.d.displayValue = $scope.d.timepicker;
				}


				$scope.openTimePicker = function() {

					$ionicPopup.show({
						cssClass: 'cm-timepicker-popup',
						template: '<cm-timepicker ng-model="d.timepicker"></cm-timepicker>',
						scope:$scope,
						buttons: [


							{
								text: 'Cancelar',
								onTap: function(e) {
									//e.preventDefault();
								}
							},
							{
								text: 'Aceptar',
								onTap: function(e) {
									$scope.updateValue();
								}
							}
						]

					});

				}
			}
		};
	}]);

})();

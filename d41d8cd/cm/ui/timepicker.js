(function() {
	'use strict';
	var scripts = document.getElementsByTagName("script")
	var csPath = scripts[scripts.length - 1].src;

	angular.module('cm.directives').directive('cmTimepicker', function($ionicGesture) {
		return {
			scope: {
				//value: '=ngModel'
				ngModel: '='
			},
			require: 'ngModel',
			restrict: 'E',
			replace: true,
			templateUrl: csPath.replace('.js', '.html'),
			link: function($scope, $element, $attrs, ngModelCtrl) {
				/*
				if (ngModelCtrl.$modelValue === undefined ||  ngModelCtrl.$modelValue === null) {
					ngModelCtrl.$modelValue = "";
				}


				function updateView(value) {
					ngModelCtrl.$viewValue = value;
					ngModelCtrl.$render();
				}

				function updateModel(value) {
					//console.log('updateModel',value);
					ngModelCtrl.$modelValue = value;
					$scope.ngModel = value; // overwrites ngModel value
				}
				*/


				//updateModel("23:00");
				//updateView("11:00 AM");


				/*
				scope.$watch("value", function(value) {
					scope.updateValue();
				});
				*/

				$scope.stage = 'hour';

				$scope._h = "12";
				$scope._m = "00";
				$scope._ap = "AM";

				$scope.updateValue = function() {

					var h=$scope._h;
					var hInt = parseInt($scope._h);

					if( $scope._ap=="AM" && $scope._h=="12"){
						h="00";
					}

					if( $scope._ap=="PM" && hInt < 12 ){
						h = hInt + 12;
					}

					$scope.ngModel = h + ":" + $scope._m;
				}

				$scope.changeStage = function(stage) {
					$scope.stage = stage;
					$scope.updateValue();
				}

				$scope.selectHour = function(h) {
					$scope._h = h;
					$scope.stage = 'minutes';
					$scope.updateValue();
				}

				$scope.selectMinutes = function(m) {
					$scope._m = m;
					$scope.updateValue();
				}

				$scope.changeAp = function(ap) {
					$scope._ap = ap;
					$scope.updateValue();
				}

				$scope.updateValue();
			}
		};
	});

})();

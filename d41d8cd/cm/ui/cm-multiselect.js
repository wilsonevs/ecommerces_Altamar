(function() {
	'use strict';


	var scripts = document.getElementsByTagName("script")
	var csPath = scripts[scripts.length - 1].src;

	angular.module('cm.directives').directive('cmMultiselect', function($ionicGesture) {
		return {
			scope: {
				options: '=',
				value: '=ngModel'
			},
			restrict: 'AE',
			replace: false,
			//template: '<h3>Hello World!!</h3>'
			//templateUrl: 'lib/cm-multiselect/cm-multiselect.html'
			//templateUrl: cmMultiselectTemplate,
			templateUrl: csPath.replace('.js', '.html'),

			controller: function($scope) {
				//console.log('cmMultiselect.controller');

				$scope.itemStates = {};
				$scope.value = [];
				//console.log('scope.options', $scope.options);

				$scope.toggle = function(r) {
					$scope.value = [];

					var value = null;
					for (var k in $scope.itemStates) {
						value = $scope.itemStates[k];
						if (value === null) continue;

						$scope.value.push(angular.copy($scope.options[k]));
					}
				}
			},

			link: function(scope, elem, attrs) {

				/*
				scope.$watch('options', function(value) {
					if (value) {
						console.log(value);
					}
				});
				*/

				return;
			}
		};
	});

})();

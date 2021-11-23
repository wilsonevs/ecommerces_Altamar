function CmDate(y, m, d) {

	this._y = y;
	this._m = m;
	this._d = d;

	this.__pad = function(val, len) {
		val = String(val);
		while (val.length < len) val = "0" + val;
		return val;
	}

	this.toISOString = function() {
		this._y + "-" + this._m + "-" + this._d;
	}
}


(function() {
	'use strict';
	var scripts = document.getElementsByTagName("script")
	var csPath = scripts[scripts.length - 1].src;



	function link($scope, $element, $attrs) {
		/*
		scope.$watch("value", function(value) {
			//console.log("Changed", value);
			scope.displayValue = value;

		});
		*/
	}


	angular.module('cm.directives').directive('cmDatepickerInput', ['ionicDatePicker', function(ionicDatePicker) {
		return {
			scope: {
				ngModel: '='
			},
			restrict: 'E',
			replace: true,
			templateUrl: csPath.replace('.js', '.html'),

			link: function($scope,$element,$attrs) {
				console.log('cmDatepicker.controller');

				$scope.d = {
					displayValue:""
				};

				$scope.from = ((new Date()).getFullYear() - 100) + "-01-01";
				$scope.to = ((new Date()).toISOString().substring(0, 10));


				$scope.d.displayValue = $scope.ngModel;

				$scope.$watch('ngModel',function(new_value,old_value){
					//console.log('ngModel',new_value,old_value);
					$scope.d.displayValue = angular.copy(new_value);
				})

				$scope.__dateFromISOString = function(value) {
					if (value === undefined ||value === null) return new Date();

					var tmp = value.substring(0, 10);
					tmp = tmp.split("-");
					return new Date(parseInt(tmp[0]), parseInt(tmp[1]) - 1, parseInt(tmp[2]));
				}

				$scope.openDatePicker = function() {
					var options = {
						callback: function(val) {
							//val milisegundos

							var date = new Date(val);
							var y = date.getFullYear();
							var m = String(parseInt(date.getMonth()) + 1);
							var d = String(parseInt(date.getDate()));


							while (m.length < 2) m = "0" + m;
							while (d.length < 2) d = "0" + d;

							var localDate = y + "-" + m + "-" + d;

							$scope.ngModel = localDate;
							$scope.d.displayValue = localDate;
							console.log($scope.d);
						},

						//disabledDates: [],
						//from: new Date(2012, 1, 1),
						from: $scope.__dateFromISOString($scope.from),
						//to: new Date(2016, 10, 30),
						//to: $scope.__dateFromISOString($scope.to),

						//inputDate: new Date(),
						inputDate: $scope.__dateFromISOString($scope.ngModel),
						mondayFirst: true,
						//disableWeekdays: [],
						showTodayButton: false,
						closeOnSelect: true,
						templateType: 'popup',
						weeksList: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
						monthsList: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
						setLabel: 'Aceptar',
						closeLabel: 'Cancelar'
					};

					ionicDatePicker.openDatePicker(options);
				}
			}
		};
	}]);

})();

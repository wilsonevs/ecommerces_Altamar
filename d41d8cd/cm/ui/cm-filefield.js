(function() {
	'use strict';
	var scripts = document.getElementsByTagName("script")
	var csPath = scripts[scripts.length - 1].src;


	function controller($scope, cm) {

		//$scope.inputId = "input-file-" + Math.ceil((new Date()).getTime() + Math.random());
        $scope.inputId = "input-file-" + Math.random().toString(36).substr(2, 16);

		$scope.defaultValue = {
			rsid: 0,
			rsname: '...',
			rstype: '',
			rssize: '0',
			tmp_name: '',
			src: 'img/image-placeholder.png'
		};

		$scope.d = {
			progress:0,
			visible_rssize: '0',
			//preview:'',
			preview:$scope.defaultValue.src
		};

		if( !$scope.value ){
			$scope.value = angular.copy($scope.defaultValue);
		}



		//$scope.$watch('value',$scope.update);



		//implementado en el htmk
		$scope.upload = function() {}

		$scope.download = function() {
			alert("Unimplemented");
		}

		$scope.remove = function() {
			$scope.value = angular.copy($scope.defaultValue);
			$scope.update();
		}

		$scope.onFileSelect = function(files) {

			$scope.__addFile(files[0]);
		}

		$scope.__addFile = function(file) {
			$scope.d.progress = 20;

			var reader = new FileReader();
			reader.onload = function(e) {
				$scope.d.progress = 80;

				cm.rpc("App.uploadBytes", {
					bytes: e.target.result
				})

				.then(function(res) {

					var value = {
						rsid: 0,
						rsname: file.name,
						rstype: file.type,
						rssize: file.size,
						tmp_name: res.tmp_name,
						src: $scope.defaultValue.src
					};

					$scope.value = value;
					$scope.d.progress = 100;

					$scope.update();
				})

				.catch(function(ex) {
					cm.error(ex);
				});


			};

			reader.readAsDataURL(file);
		}


		$scope.update = function() {
			console.log('$scope.update');

			//if ($scope.value === null || $scope.value === undefined) return;


			//if ( $scope.value.rssize > 0 ) {
			if ($scope.value.rssize > 1024 * 1024) {
				$scope.d.visible_rssize = (Math.round($scope.value.rssize * 100 / (1024 * 1024)) / 100).toString() + 'MB';
			} else {
				$scope.d.visible_rssize = (Math.round($scope.value.rssize * 100 / 1024) / 100).toString() + 'KB';
			}
			//}

			$scope.d.preview = $scope.value.src;

		}

		$scope.update();

	}

	function link($scope, $element, $attrs) {
		/*
		scope.$watch("value", function(value) {
			//console.log("Changed", value);
			scope.displayValue = value;

		});
		*/
	}

	angular.module('cm.directives').directive('cmFilefield', [function() {
		return {
			scope: {
				value: '=ngModel',
			},
			restrict: 'E',
			replace: true,
			templateUrl: csPath.replace('.js', '.html'),

			controller: controller,
			link: function($scope, $element, $attrs) {

			}
		};
	}]);

})();

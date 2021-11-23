angular.module('starter.controllers')

//.controller('EavAttrE', function($scope, $state,cm, $ionicHistory,$ionicScrollDelegate) {
.controller('EavAttrE', ['$scope', '$injector', function($scope, $injector) {
	cm = $injector.get('cm');
	$state = $injector.get('$state');
	$ionicHistory = $injector.get('$ionicHistory');
	$ionicScrollDelegate = $injector.get('$ionicScrollDelegate');

	$scope.ers = {

	};

	$scope.dr = {
		type_id: null,
		attr_id: null,
		attr_name: '',
		attr_label: '',
		attr_vfield: {data: null},
		attr_vorder: null,
		attr_type: 'textfield',
		attr_ewidth: {data: null},
		attr_eheight:'',
		attr_eorder: null,
		notes:''
	};

	$scope.$watch('dr.attr_label',function(){
		console.log('watch');
		if( $scope.dr.attr_id < 0 ){
			$scope.dr.attr_name = $scope.__normalizeName($scope.dr.attr_label);
		}

	});

	$scope.$on('$ionicView.enter', function() {
		$ionicScrollDelegate.scrollTop();

		var pr = cm.get('app.EavDesign.EavAttrE.params');

		angular.extend($scope.dr, pr);
		$scope.dr.attr_vfield = {data: pr.attr_vfield ||''};
		$scope.dr.attr_type = {data: pr.attr_type ||null};
		$scope.dr.attr_ewidth = {data: pr.attr_ewidth ||'12'};
		$scope.dr.ds_type = {data: pr.ds_type ||null};
		$scope.dr.ds_multiple = {data: pr.ds_multiple ||null};

		console.log('pr', pr);
		console.log('dr', $scope.dr);

		cm.rpc('EavAttrE.ers', {})

		.then(function(res) {
			angular.extend($scope.ers, res);
		})

		.catch(function(ex) {
			cm.error(ex);
		});
	});

	$scope.accept = function() {
		var dr = angular.copy($scope.dr);

		console.log('dr',dr);
		dr.attr_vfield = dr.attr_vfield.data;
		dr.attr_type = dr.attr_type.data;
		dr.attr_ewidth = dr.attr_ewidth.data;
		dr.ds_type = dr.ds_type.data;
		dr.ds_multiple = dr.ds_multiple.data;

		cm.set('app.EavDesign.EavAttrE.dr', dr);
		$ionicHistory.goBack();
	}

	$scope.cancel = function() {
		//$ionicHistory.goBack();
		$state.go('app.EavDesign.EavTypeE', {
			type_id: $scope.dr.type_id
		});
	}

	$scope.acTypes = function(filter) {

		return cm.rpc("EavAttrE.acTypes", {
			filter: filter
		})
		.catch(function(ex) {
			cm.error(ex);
		});
	}

	$scope.acRelAttrs = function(filter) {

		console.log('acRelAttrs', $scope.dr.rel_type_id);

		if (!angular.isDefined($scope.dr.rel_type_id[0].data)) {
			cm.info({
				message: "Debe seleccionar un tipo de dato primero"
			});
			return;
		}

		return cm.rpc("EavAttrE.acRelAttrs", {
			type_id: $scope.dr.rel_type_id[0].data,
			filter: filter
		})

		.catch(function(ex) {
			cm.error(ex);
		});
	}


	$scope.__normalizeName = function(str) {
		var from = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç",
			to = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc",
			mapping = {};

		for (var i = 0, j = from.length; i < j; i++)
			mapping[from.charAt(i)] = to.charAt(i);

		var ret = [];
		for (var i = 0, j = str.length; i < j; i++) {
			var c = str.charAt(i);
			if (mapping.hasOwnProperty(str.charAt(i)))
				ret.push(mapping[c]);
			else
				ret.push(c);
		}

		ret = ret.join('');
		//remove non ascii 0-9a-zA-Z
		//ret = ret.replace(/[^\x00-\x7A]/g, "");
		ret = ret.replace(/[^\s+\x30-\x39\x61-\x7A\x41-\x5A]/g, "");
		ret = ret.trim().replace(/\s+/g, "_");
		ret = ret.replace('+','').replace('-','');

		return ret.toLowerCase();
	}

}]);

angular.module('starter.controllers')

.controller('EavTypeE', function($scope, $state, cm, $ionicSideMenuDelegate, $filter, $ionicModal,$ionicHistory,$ionicScrollDelegate) {
	$ionicSideMenuDelegate.canDragContent(false);

	$scope.title = "EavTypeE";

	$scope.d = {
		attr_sort: false,
		attr_filter: '', //filtro para atributos
		tree_options: null
	};

	$scope.d.tree_options = {
		dragStart: function(e) {
			console.log('dragStart', e);
		},
		beforeDrop: function(e) {
			console.log('beforeDrop');
		},
		dropped: function(e) {
			console.log(JSON.stringify($scope.data));
		}
	};


	$scope.ers = {

	};

	$scope.dr = {
		type_id:null,
		slug_attrs:'',
		attrs: [],
		notes:''
	};

	$scope.drAttrs = {
		attr_name:'',
		attr_label:''
	};




	$scope.$on('$ionicView.enter', function() {
		$ionicScrollDelegate.scrollTop();

		if( !angular.isDefined($state.params.type_id) ){
			cm.error({message:"Url Invalida, parametros incompletos"});
			return;
		}


		var drAttr = cm.get('app.EavDesign.EavAttrE.dr') || null;
		if( drAttr !== null ){

			for(var i=0;i<$scope.dr.attrs.length;i++){
				if( drAttr.attr_id == $scope.dr.attrs[i].attr_id ){
					$scope.dr.attrs[i] = drAttr;
					cm.set('app.EavDesign.EavAttrE.dr',null);
					break;
				}
			}
		}

		if($state.params.type_id == $scope.dr.type_id || $state.params.type_id=='add' ) return;

		$scope.load();

	});


	$scope.refresh = function(){
		$scope.load();
	}

	$scope.load = function(){
		cm.rpc('EavTypeE.load', {type_id: $state.params.type_id},{loading:true})

		.then(function(res) {
			angular.extend($scope.dr, res);
		})

		.catch(function(ex) {
			cm.error(ex);
		});
	}

	$scope.attrFilter = function(r) {
		if( r === null ||r === undefined ) return true;
		$scope.d.attr_filter = $scope.d.attr_filter ||'';
		//return $filter('uiTreeFilter')(nodeObject, pattern, ['attr_name', 'attr_type']);
		return $filter('filter')([r], $scope.d.attr_filter).length > 0;

		console.log(tmp);
		return true;
	}



	$scope.attrAdd = function(scope, index) {
		var nodeData = $scope.dr.attrs[$scope.dr.attrs.length - 1];

		var attrId=((new Date()).getTime() + Math.random()) * -1;
		var item = {
			attr_id: attrId,
			attr_name: "untitled",
			attr_label: 'Untitled',
			attr_type: 'textfield',
			attr_vfield: '',
			attr_ewidth: '12',
			section_id:-1
		};

		$scope.dr.attrs.splice(index + 1, 0, item);
	}


	/*
	setTimeout(function() {
		$scope.attrEdit($scope.dr.attrs[0]);
	}, 1000);
	*/


	$scope.modalAttrEAccept = function() {
		$scope.modal.remove();
	}

	$scope.modalAttrEReject = function() {
		$scope.modal.remove();
	}

	$scope.attrEdit = function(r) {
		/*
		$ionicHistory.nextViewOptions({
			disableAnimate: true
		});
		*/

		var params = angular.copy(r.$modelValue);
		params.type_id = $state.params.type_id;

		cm.set('app.EavDesign.EavAttrE.params',params);

		$state.go('app.EavDesign.EavAttrE', {
			type_id: $scope.dr.type_id,
			attr_id: params.attr_id
		});
		return;
		//alert("attrEdit");
		//var d = angular.element('<cm-search-dialog></cm-search-dialog>');

		$ionicModal.fromTemplateUrl('app/EavDesign/EavAttrE.html', {
			scope: $scope,
			animation: 'slide-in-up'
		}).then(function(modal) {
			$scope.modal = modal;
			$scope.modal.show();
		});

		/*
		$scope.openModal = function() {
			$scope.modal.show();
		};
		$scope.closeModal = function() {
			$scope.modal.hide();
		};
		//Cleanup the modal when we're done with it!
		$scope.$on('$destroy', function() {
			$scope.modal.remove();
		});
		*/
	}

	$scope.attrRemove = function(scope) {
		cm.confirm({
			message: "Seguro que desea eliminar el atributo"
		})

		.then(function(res) {
			if (!res) return;
			scope.remove();
		});
	}


	$scope.save = function(callback) {
		var dr=angular.copy($scope.dr);

		cm.rpc('EavTypeE.save',dr,{loading:true})

		.then(function(res){
			$scope.dr.type_id = res.type_id;
			if( angular.isFunction(callback) ){
				callback();
				return;
			}


			$state.params.type_id = res.type_id;
			$scope.load();
		})

		.catch(function(ex){
			cm.error(ex);
		});

	}

	$scope.saveAndBack = function() {
		$scope.save(function(){
			$scope.back();
		});
	}

	$scope.back = function() {
		$ionicHistory.goBack();
		//$state.go('app.EavDesign.EavTypeL');
	}

	/*
	$scope.moveItem = function(item, fromIndex, toIndex) {
		$scope.items.splice(fromIndex, 1);
		$scope.items.splice(toIndex, 0, item);
	};
	*/

});

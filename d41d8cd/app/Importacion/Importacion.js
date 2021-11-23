angular.module('starter.controllers')

.controller('Importacion', function($scope, $state, cm, $ionicSideMenuDelegate, $window) {


    $scope.dr = {
        fld: ''
    };

    $scope.$on('$ionicView.afterEnter', function() {
	});


    $scope.save = function(){
		cm.rpc('Importacion.save',$scope.dr,{loading:true})
		.then(function(res){
            cm.info({ message: res.message });

            setInterval(function(){
                $window.location.reload();
            }, 5000);

		})
		.catch(function(ex){
			cm.error(ex);
		});


	}

});

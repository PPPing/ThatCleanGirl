(function(){
	var app = angular.module('thatCleanGirl', [ ]);

	app.factory('MenuService',function(){
		var components=[
				{
					id:"topMenu1",
					name:"Client Management",
					modules:[
						{
							id:"client-list",
							name:"Client List",
							url:"",
							active:true
						},
						{
							id:"new-client",
							name:"New Client",
							active:false
						}
					]
				},
				{
					id:"topMenu2",
					name:"Staff Management",
					modules:[
						{
							id:"staff-list",
							name:"Staff List",
							url:"",
							active:true
						}
					]
				}
			];
		var curComponentIndex = 0;
		var curModuleIndex = 0;
		var MenuService = {
			getComponents:function(){
				return components;
			},
			getModules:function(){
				return components[curComponentIndex].modules;
			},
			getActiveModule:function(){
				//console.log(components[curComponentIndex].modules[curModuleIndex]);
				return components[curComponentIndex].modules[curModuleIndex];
			},
			changeComponents:function(index){
				curComponentIndex = index;
			},
			changeModule:function(index){
				curModuleIndex = index;
				console.log(components[curComponentIndex].modules[curModuleIndex]);
			}
		}
		return MenuService;
	});
	app.controller('TopMenuController',['$scope','MenuService',function($scope,MenuService){
		this.components = MenuService.getComponents();
		$scope.changeComponents = MenuService.changeComponents;
		console.log(this.components);

	}]);	
	app.controller('SiderController',['$scope','MenuService',function($scope,MenuService){
		$scope.getModules = MenuService.getModules;
		$scope.changeModule = MenuService.changeModule;
	}]);
	
	app.directive('moduleContainer',function($compile) {
		return {
			restrict: 'E',
			template: '<div></div>',
			scope: {},
			controller:function($scope,$element,MenuService) {
				$scope.getActiveModule = MenuService.getActiveModule;
				$scope.$watch(
                    function( $scope ) {
                        console.log( "Function watched" );
                        // This becomes the value we're "watching".
                        return $scope.getActiveModule().id;
                    },
                    function( newValue ) {
                        var el = $compile( "<module-"+newValue+"></module-"+newValue+">" )( $scope );
						$element.html( el );
                    }
                );				
			}
		};
	});
	app.directive('moduleClientList',function() {
		return {
			restrict: 'E',
			template: '<div>module Client List</div>',
			scope: {}
		};
	});
	app.directive('moduleNewClient',function() {
		return {
			restrict: 'E',
			template: '<div>module New Client</div>',
			scope: {}
		};
	});
	app.directive('moduleStaffList',function() {
		return {
			restrict: 'E',
			template: '<div>module Staff List</div>',
			scope: {}
		};
	});
})();
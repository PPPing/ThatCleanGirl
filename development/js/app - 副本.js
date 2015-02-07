(function(){
	var app = angular.module('thatCleanGirl', [ ]);	

	app.factory('UserService',function(){
		var UserService = {
			account:"jessica",
			name:"Jessica",
			role:"Manger"
		}
		return UserService;
	});
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
							isSubModule:false
						},
						{
							id:"new-client",
							name:"New Client",
							isSubModule:false
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
							isSubModule:false
						}
					]
				}
			];
		var subModules=[
			{
				
			}
		]
		var curComponentIndex = 0;
		var curModuleIndex = 0;
		var modulesStack=[];
		var pushModulesStack=function(selectedIndex,modules){
				var modulesInfoNode={
					curModuleIndex:0,
					modules:modules
				}
				modulesStack.push(modulesInfoNode);
				console.log(modulesStack);
		};
		pushModulesStack(curModuleIndex,components[curComponentIndex].modules);
		var MenuService = {
			initMenuService:function(){
				console.log("initMenuService");
				pushModulesStack(0,components[curComponentIndex].modules);
			},
			getComponentIndex:function(){
				return curComponentIndex;
			},
			getModuleIndex:function(){
				return curModuleIndex;
			}, 
			getComponents:function(){
				return components;
			},
			getModules:function(){
				return modulesStack[modulesStack.length-1].modules;
			},
			getActiveModule:function(){
				var moduleStackNode = modulesStack[modulesStack.length-1];
				console.log(moduleStackNode);
				return moduleStackNode.modules[moduleStackNode.curModuleIndex];
			},
			getModulesStackDepth:function(){
				return modulesStack.length;
			},
			changeComponents:function(index){
				curComponentIndex = index;
				curModuleIndex=0;
				modulesStack=[];
				pushModulesStack(0,components[curComponentIndex].modules);
			},
			changeModule:function(index){
				modulesStack[modulesStack.length-1].curModuleIndex = index;
			},
			pushModulesStack:function(modules){
				pushModulesStack(0,modules);
			},
			popModulesStack:function(){
				if(modulesStack.length>1){
					modulesStack.pop();
				}
			}
		}
		//MenuService.initMenuService();
		return MenuService;
	});
	app.controller('TopMenuController',['$scope','MenuService',function($scope,MenuService){
		this.components = MenuService.getComponents();
		$scope.changeComponents = MenuService.changeComponents;
		$scope.getComponentIndex = MenuService.getComponentIndex;
		console.log(this.components);

	}]);	
	app.controller('SiderController',['$scope','MenuService',function($scope,MenuService){
		$scope.getModules = MenuService.getModules;
		$scope.changeModule = MenuService.changeModule;
		$scope.getModuleIndex = MenuService.getModuleIndex;
		$scope.back= MenuService.popModulesStack;
		$scope.modulesStackDepth = MenuService.getModulesStackDepth;
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
						console.log($scope.getActiveModule());
                        return $scope.getActiveModule().id;
                    },
                    function( newValue ) {
						if($scope.getActiveModule().isSubModule==false){
							var el = $compile( "<module-"+newValue+"></module-"+newValue+">" )( $scope );
							$element.html( el );
						}	
                    }
                );				
			}
		};
	});
	app.directive('moduleClientList',function() {
		return {
			restrict: 'E',
			scope: {},
			controller: function($scope,$http,MenuService,UserService) {
				$scope.curSubmodule="client-list";
				$http.get('/github/ThatCleanGirl/development/sampleData/clientList.json')
				.then(function(result) {
					console.log(result);
					$scope.clients = result.data;
				});	
				var clientDetailmodules=[
					{
						id:"client-detail",
						name:"Client Detail",
						isSubModule:true
					}
				];
				
				this.viewClientDetail=function(clientId){
					console.log("viewClientDetail : "+clientId);
					clientId="111";
					$http.get('/github/ThatCleanGirl/development/sampleData/clientDetail-'+clientId+'.json?timestamp='+ new Date())
					.then(function(result) {
						console.log(result);
						$scope.clientDetail = result.data;
						$scope.curSubmodule = "client-detail";
						$scope.UserService = UserService;
						$scope.postComment = function(){
							console.log(this.newComment);
						}
						MenuService.pushModulesStack(clientDetailmodules);
				});
				}
			},
			templateUrl:'templates/clientList.html',
			controllerAs: 'clientList'
		};
	});
	app.directive('moduleNewClient',function() {
		return {
			restrict: 'E',
			/* template: '<div>module New Client</div>', */
			templateUrl:'templates/createClient.html',
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
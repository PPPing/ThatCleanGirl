(function(){
	var app = angular.module('thatCleanGirl', [ ]);
	
	app.controller('MainController',['$scope',function($scope){
		$scope.components=[
				{
					id:"topMenu1",
					name:"Client Management",
					modules:[
						{
							id:"clientList",
							name:"Client List",
							url:"",
							active:true
						},
						{
							id:"newClient",
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
							id:"staffList",
							name:"Staff List",
							url:"",
							active:true
						}
					]
				}
			];
		$scope.curComponentIndex=1;
		$scope.modules=$scope.components[$scope.curComponentIndex].modules;;
		$scope.$on('someEvent', function(event, data){
			console.log('someEvent');
		});
	}]);
	app.factory('MenuService',function(){
		var components=[
				{
					id:"topMenu1",
					name:"Client Management",
					modules:[
						{
							id:"clientList",
							name:"Client List",
							url:"",
							active:true
						},
						{
							id:"newClient",
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
							id:"staffList",
							name:"Staff List",
							url:"",
							active:true
						}
					]
				}
			];
		var curComponentIndex = 0;
		var MenuService = {
			getComponents:function(){
				return components;
			},
			getModules:function(){
				return components[curComponentIndex].modules;
			},
			changeComponents:function(id){
				curComponentIndex = id;
			}
		}
		return MenuService;
	});
	app.controller('TopMenuController',['$scope','MenuService',function($scope,MenuService){
		this.components = MenuService.getComponents();
		$scope.changeComponents = MenuService.changeComponents;
		console.log(this.components);
		/* $scope.curComponentIndex=0;
		this.switchComponent = function(index){
			$scope.modules = this.components[index].modules;
			$scope.curComponentIndex = index;
			MenuService.updateId();
			console
		};
		
		this.switchComponent(0); */
	}]);	
	app.controller('SiderController',['$scope','MenuService',function($scope,MenuService){
		$scope.getModules = MenuService.getModules;
		//$scope.updateId = MenuService.updateId;
		//this.switchModule = function(index){
			//console.log($scope.curComponentIndex);
			/* alert("Switch Module. \nIndex : " +  index +"\n");
			this.modules = modules;
			this.curModuleIndex = index; */
		//};
	}]);
	
})();
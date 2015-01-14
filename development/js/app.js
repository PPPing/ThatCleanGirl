(function(){
	var app = angular.module('thatCleanGirl', [ ]);
	
	var components = [
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
	var modules = [
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
	];
	app.service('MenuService', function () {
		this.components=[
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
		this.module = [
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
		];
	});
	app.controller('TopMenuController',['MenuService',function(MenuService){
		$scope.test = "test";
		this.components = components;
		this.curComponentIndex = 0;
		this.switchComponent = function(index){
			alert("Switch Component. \nIndex : " +  index + " - "+components[index].name);
			this.curComponentIndex = index;
			modules = components[index].modules;
		};
	}]);
	
	app.controller('SiderController',['MenuService',function(MenuService){
		this.modules = modules;
		this.curModuleIndex = 0;
		this.switchModule = function(index){
			alert("Switch Module. \nIndex : " +  index +"\n"+$scope.test);
			this.modules = modules;
			this.curModuleIndex = index;
		};
	}]);
	
})();
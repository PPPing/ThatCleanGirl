(function(){
	var app = angular.module('thatCleanGirl', ['pluginDirectives']).config(function($interpolateProvider,$httpProvider){
        $interpolateProvider.startSymbol('{[{').endSymbol('}]}');

        $httpProvider.defaults.headers.post['Content-Type'] =
            'application/x-www-form-urlencoded;charset=utf-8';

        var param = function(obj) {
            var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

            for(name in obj) {
                value = obj[name];

                if(value instanceof Array) {
                    for(i=0; i<value.length; ++i) {
                        subValue = value[i];
                        fullSubName = name + '[' + i + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                }
                else if(value instanceof Object) {
                    for(subName in value) {
                        subValue = value[subName];
                        fullSubName = name + '[' + subName + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                }
                else if(value !== undefined && value !== null)
                    query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
            }

            return query.length ? query.substr(0, query.length - 1) : query;
        };

        // Override $http service's default transformRequest
        $httpProvider.defaults.transformRequest = [function(data) {
            return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
        }];
    });

    app.filter('parseBoolean', function() {
        return function(input) {
            return input ? 'Yes' : 'No';
        };
    });

	app.directive('dMultiline',function() {
		return {			
			restrict: 'A',
			require: 'ngModel',
			scope: {
				bindModel : '=ngModel'
			},
			link: function ($scope, element, attrs) {
				$scope.$watch(
                    function( $scope ) {	
                        if($scope.bindModel){
							return $scope.bindModel;
						}else{
							return "";
						}
                    },
                    function( newValue ) {						
						if(newValue!=""){
							element.html(newValue.replace(/\n/g,"<br>"));
						}
                    }
                );	
			}
		};
	});
	
	app.directive('dFormControlRotationGroup',function() {
		return {			
			restrict: 'A',
			require: 'ngModel',
			scope: {
				bindModel : '=ngModel'
			},
			link: function ($scope, element, attrs) {
				$scope.$watch(
                    function( $scope ) {	
                        if($scope.bindModel){
							return $scope.bindModel;
						}else{
							return "";
						}
                    },
                    function( newValue ) {						
						if(newValue!=""){
							element.html(newValue.replace(/\n/g,"<br>"));
						}
                    }
                );	
			}
		};
	});

    app.factory('ValidationService',function($http){
        var ValidationService = {
            check:function(element){
                var isValid = true;
                angular.forEach(element.find("input[required]"), function (value, key) {
                    var eObject = angular.element(value);

                    var inputValue = eObject.val();
                    if ( inputValue == null || inputValue == "") {
                        isValid = false;
                    }else{
                    }
                });

                return isValid;
            }
        };

        return ValidationService;
    });
	
	app.factory('UserService',function($http){
		var UserService = {
            profile:function(){
                var profile = {account:"",
                    name:"",
                    role:""};
                $http.get('/api/user_profile')
                    .then(function(result) {
                        console.log(result);
                        profile = result.data;
                    });
                return profile;
            }
		};

		return UserService;
	});
	app.factory('MenuService',function() {
        var components = [
            /*{
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
             }*/
        ];

        var curComponentIndex = 0;
        var curModuleIndex = 0;
        var modulesStack = [];
        var pushModulesStack = function (selectedIndex, modules) {
            var modulesInfoNode = {
                curModuleIndex: 0,
                modules: modules
            }
            modulesStack.push(modulesInfoNode);
            //console.log(modulesStack);
        };
        if (components.length > 0) {
            pushModulesStack(curModuleIndex, components[curComponentIndex].modules);
        }
		var MenuService = {
			initMenuService:function(data){
				console.log("initMenuService");
                components = data;
				pushModulesStack(0,components[curComponentIndex].modules);
			},
			getComponentIndex:function(){
				return curComponentIndex;
			},
			getModuleIndex:function(){
				return modulesStack[modulesStack.length-1].curModuleIndex;
			}, 
			getComponents:function(){
				return components;
			},
			getModules:function(){
                if (components.length <= 0) {
                    return[];
                }
				return modulesStack[modulesStack.length-1].modules;
			},
			getActiveModule:function(){
                if (components.length <= 0) {
                    return;
                }
				var moduleStackNode = modulesStack[modulesStack.length-1];
				//console.log(moduleStackNode);
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

    app.controller('TestController',['$scope','$http',function($scope, $http){
        this.testWebAPI = function() {
            $http.get('/api/test')
                .then(function(result) {
                    console.log(result);
                    //$scope.clientListData = result.data;
                });
        };

    }]);

	app.controller('TopMenuController',['$scope','$http','MenuService',function($scope,$http,MenuService){
        $http.get('/api/menu_info')
            .then(function(result) {
                console.log(result);
                MenuService.initMenuService(result.data);
                $scope.getComponents = MenuService.getComponents;
                $scope.changeComponents = MenuService.changeComponents;
                $scope.getComponentIndex = MenuService.getComponentIndex;
            });
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

                        return $scope.getActiveModule()?$scope.getActiveModule().id:null;
                    },
                    function( newValue ) {
						if($scope.getActiveModule()&&$scope.getActiveModule().isSubModule==false){
							var el = $compile( "<module-"+newValue+"></module-"+newValue+">" )( $scope );
							$element.html( el );
						}	
                    }
                );
			}
		};
	});
    app.directive('moduleDashboard',function() {
        return {
            restrict: 'E',
            scope: {},
            controller: function($scope) {
                $scope.moduleInfo={
                    curSubModule:"dashboard-main"
                    //clientDetail_clientId:null
                };
            },
            templateUrl:'directives/modules/dashboard.html'
        };
    });

    app.directive('dashboardMainTmpl',function(){
        return {
            restrict: 'E',
            controller: function($scope,$http) {

            },
            templateUrl:'directives/templates/dashboardMainTmpl.html',
            controllerAs: 'dashboardMain'
        };
    });

    app.directive('serviceUnconfirmedTmpl',function(){
        return {
            restrict: 'E',
            controller: function($scope,$http) {
                $http.get('/job/serviceUnconfirmed?timestamp='+ new Date())
                    .then(function(result) {
                        console.log(result.data);
                        $scope.unconfirmedServices = result.data;
                    });
                $scope.ConfirmService=function(index){
                    console.log(index);

                    $scope.moduleInfo.curSubModule="service-editor";
                    $scope.moduleInfo.serviceInfo = $scope.unconfirmedServices[index];

                    console.log($scope.moduleInfo.serviceInfo);
                };
            },
            templateUrl:'directives/templates/serviceUnconfirmedTmpl.html',
            controllerAs: 'service'
        };
    });

    app.directive('serviceListTmpl',function(){
        return {
            restrict: 'E',
            controller: function($scope,$http) {
                $scope.searchWord="";
                $scope.filtersMask = 1111;
                $scope.filters={
                    pending:true,
                    processing:true,
                    completed:true,
                    reviewed:true
                };
                $scope.$watch(
                    function( $scope ) {
                        $scope.filtersMask = 0;
                        if($scope.filters.pending){
                            $scope.filtersMask += 1;
                        }
                        if($scope.filters.processing){
                            $scope.filtersMask += 10;
                        }
                        if($scope.filters.completed){
                            $scope.filtersMask += 100;
                        }
                        if($scope.filters.reviewed){
                            $scope.filtersMask += 1000;
                        }
                        return $scope.filtersMask;
                    },
                    function( newValue ) {
                        console.log(newValue);
                        loadServiceList();
                    }
                );

                $scope.parseStatus=function(status){
                  var statusStr = "";
                  switch (status){
                      case 0 :  statusStr = "Pending";break;
                      case 1 :  statusStr = "Processing";break;
                      case 2 :  statusStr = "Completed";break;
                      case 3 :  statusStr = "Reviewed";break;
                  };
                    return statusStr;
                };

                function loadServiceList(){
                    $http.post('/job/serviceConfirmed', {"filters":$scope.filters}).
                        success(function(data, status, headers, config) {
                            console.log("[Update] - JobDetail - SUCCESS");
                            console.log(config);
                            console.log(data);
                            $scope.serviceInfoList = data;

                        }).
                        error(function(data, status, headers, config) {

                        });
                }
                $scope.EditService = function(index){
                    console.log(index);
                    $scope.moduleInfo.curSubModule="service-editor";
                    $scope.moduleInfo.serviceInfo = $scope.serviceInfoList[index];
                };
            },
            templateUrl:'directives/templates/serviceListTmpl.html',
            controllerAs: 'service'
        };
    });

    app.directive('serviceEditorTmpl',function(){
        return {
            restrict: 'E',
            controller: function($scope,$http,$element,MenuService,UserService,ValidationService){
                console.log("ServiceEditorTmpl");
                $scope.editMode=false;
                $scope.serviceInfo={};
                var clientDetailModules=[
                    {
                        id:"service-editor",
                        name:"Service",
                        isSubModule:true
                    }
                ];
                $scope.$watch(
                    function( $scope ) {
                        return $scope.moduleInfo.serviceInfo;
                    },
                    function( newValue ) {
                        $scope.serviceInfo = newValue;
                        console.log( $scope.serviceInfo);
                        loadClientInfo($scope.serviceInfo.clientId);
                        $scope.UserService = UserService;
                        MenuService.pushModulesStack(clientDetailModules);

                    }
                );

                function loadClientInfo(clientId){
                    $http.get('/api/getClientInfo/'+clientId+'?timestamp='+ new Date())
                        .then(function(result) {
                            console.log(result.data);
                            $scope.clientInfo = result.data;
                        });
                }

                function saveService( info ,callback){
                    $http.post('job/saveService', {"serviceInfo":$scope.serviceInfo}).
                        success(function(data, status, headers, config) {
                            if(data==="SUCCESS"){
                                if (callback && typeof(callback) === "function") {
                                    // execute the callback, passing parameters as necessary
                                    callback();
                                }
                            }
                        }).
                        error(function(data, status, headers, config) {
                        });
                }
                $scope.Confirm = function(){
                    console.log($scope.serviceInfo);
                    console.log($scope.serviceInfo.serviceDate);
                    console.log($scope.serviceInfo.teamId);
                    $scope.serviceInfo.isConfirmed=true;
                    if(!$scope.serviceInfo.serviceDate || !$scope.serviceInfo.teamId ){
                        alert("Please input all required information.");
                    }
                    saveService($scope.serviceInfo,function(){
                        MenuService.popModulesStack();
                        //console.log("Confirmed");
                    });
                };
                $scope.Edit=function(){
                    console.log("edit");
                    $scope.editMode = true;
                };
                $scope.Save = function(){
                    console.log($scope.serviceInfo);
                    console.log($scope.serviceInfo.serviceDate);
                    console.log($scope.serviceInfo.teamId);
                    if(!$scope.serviceInfo.serviceDate || !$scope.serviceInfo.teamId ){
                        alert("Please input all required information.");
                    }

                    saveService($scope.serviceInfo,function(){
                        //MenuService.popModulesStack();
                        console.log("save");
                        $scope.editMode = false;
                    });
                };


            },
            templateUrl:'directives/templates/serviceEditorTmpl.html',
            controllerAs: 'serviceEditor'
        };
    });

    app.directive('moduleClientList',function() {
		return {
			restrict: 'E',
			scope: {},
			controller: function($scope) {
				$scope.moduleInfo={
					curSubModule:"client-list"
					//clientDetail_clientId:null
				};
			},
			templateUrl:'directives/modules/clientList.html',
			controllerAs: 'clientListModule'
		};
	});
	
	app.directive('clientListTmpl',function(){
		return {
			restrict: 'E',
			controller: function($scope,$http) {
				$http.get('/api/getClientList?timestamp='+ new Date())
				.then(function(result) {
					console.log(result.data);
					$scope.clientListData = result.data;
				});
				this.viewClientDetail=function(clientId){
					console.log(clientId);
					console.log($scope.moduleInfo);
					$scope.moduleInfo.curSubModule="client-detail";
					$scope.moduleInfo.clientDetail_clientId = clientId;
				};
			},
			templateUrl:'directives/templates/clientListTmpl.html',
			controllerAs: 'clientListTmpl'
		};
	});
	
	app.directive('clientDetailTmpl',function(){
		return {
			restrict: 'E',
			controller: function($scope,$http,MenuService,UserService) {
				var clientDetailModules=[
					{
						id:"client-detail",
						name:"Client Detail",
						isSubModule:true
					}
				];
				$scope.$watch(
                    function( $scope ) {
                        return $scope.moduleInfo.clientDetail_clientId;
                    },
                    function( newValue ) {
						//newValue = "111";
                        console.log(newValue);
						$http.get('api/getClientInfo/'+newValue)
						.then(function(result) {
                                console.log(result);
							$scope.clientDetail = result.data;
							$scope.UserService = UserService;

							MenuService.pushModulesStack(clientDetailModules);
						});
                    }
                );
			},
			templateUrl:'directives/templates/clientDetailTmpl.html',
			controllerAs: 'clientDetailTmpl'
		};
	});
	
 	app.directive('clientInfoSectionTmpl',function(){
		return{
			restrict: 'E',
			require: 'ngModel',
			scope: {
				clientDetail : '=ngModel',
				editMode:'=editMode'
			},
			controller: function($scope,$http,$element,MenuService,ValidationService) {
				console.log($scope.editMode);
				$scope.submit=function() {

                    if(!ValidationService.check($element)){
                        alert("Please input all required information.");
                        return;
                    }

					$scope.editMode=false;
                    console.log("submit");
                    //console.log($scope.clientDetail);
                    var clientInfo = angular.copy($scope.clientDetail);
                    $http.post('api/updateClientInfo', {"clientInfo":clientInfo}).
                        success(function(data, status, headers, config) {
                            console.log("[Update] - ClientInfo - SUCCESS");
                            console.log(data);
                        }).
                        error(function(data, status, headers, config) {
                        });
				};

                $scope.delete=function(){
                    console.log('api/deleteClientInfo/'+$scope.clientDetail.clientId);
                    $http.get('api/deleteClientInfo/'+$scope.clientDetail.clientId)
                        .then(function(result) {
                            console.log(result);
                            MenuService.changeComponents(0);
                        });
                };
				$scope.$on('setEidtMode', function (event,eidtMode) {
					console.log("on setEidtMode : "+eidtMode); // 'Data to send'
					$scope.editMode = eidtMode;
                    if($scope.editMode){
                        $scope.submit();
                    }
				});
			},
			templateUrl:'directives/templates/clientInfoSectionTmpl.html',
			controllerAs: 'clientInfoSection'
		};
	});
	
	app.directive('jobDetailSectionTmpl',function(){
		return{
			restrict: 'E',
			require: 'ngModel',
			scope: {
				jobDetail : '=ngModel',
				editMode:'=editMode',
                clientId:'@clientId'
			},
			controller: function($scope,$http,$modal) {
                this.curItemIndex=0;

                $scope.itemIndex = null;

                $scope.openEditor = function ($index) {

                    $scope.itemIndex = $index;
                    console.log("Editor Job : "+ $index);
                    console.log($scope.jobDetail.items);
                    if($index==null||$index==-1){
                        $scope.item = {
                            name:"",
                            amount:1,
                            request:"",
                            id:null
                        };
                    }else{
                        if(typeof $scope.jobDetail.items[$index] === 'undefined') {
                           alert("Job : "+$index+" is not existing.");
                            return;
                        }
                        else {
                            $scope.item = $scope.jobDetail.items[$index];
                            console.log($scope.item);
                        }
                    }

                    var modalInstance = $modal.open({
                        animation: true,
                        templateUrl: 'directives/templates/jobItemEditorTmpl.html',
                        controller: function($scope,$modalInstance,item){
                            $scope.item = item;

                            $scope.ok = function () {

                                if( $scope.item.name==""){
                                    alert("Please input all required information.");
                                    return;
                                }

                                $modalInstance.close($scope.item);
                            };

                            $scope.cancel = function () {
                                $modalInstance.dismiss('cancel');
                            };
                        },
                        resolve: {
                            item: function () {
                                return $scope.item;
                            }
                        }
                    });

                    modalInstance.result.then(function (editedItem) {
                        console.log(editedItem);
                        if($scope.itemIndex==-1){
                            $scope.jobDetail.items.push(editedItem);
                        }
                    }, function () {
                       // console.log('Modal dismissed at: ' + new Date());
                    });
                };

                $scope.deleteItem=function($index){
                    this.curItemIndex=0;
                    $scope.jobDetail.items.splice($index, 1);
                };
                $scope.submit=function(){
                    $scope.editMode=false;
                    if($scope.clientId===null) {
                        alert("Invalid Client Id.");
                        return;
                    }
                    console.log("submit");
                    var jobDetail = angular.copy($scope.jobDetail);
                    //console.log(jobDetail);
                    $http.post('api/updateClientJobDetail', {"jobDetail":jobDetail,"clientId":$scope.clientId}).
                        success(function(data, status, headers, config) {

                            console.log("[Update] - JobDetail - SUCCESS");
                            console.log(config);
                            console.log(data);
                            //console.log(headers);
                           // console.log(config);
                        }).
                        error(function(data, status, headers, config) {
                    });
                };

				$scope.$on('setEidtMode', function (event,eidtMode) {
					//console.log("on setEidtMode : "+eidtMode); // 'Data to send'
					$scope.editMode = eidtMode;
                    if($scope.editMode){
                        $scope.submit();
                    }
				});

			},
			templateUrl:'directives/templates/jobDetailSectionTmpl.html',
			controllerAs: 'jobDetailSection'
		};
	}); 
	
	app.directive('paymentSectionTmpl',function(){
		return{
			restrict: 'E',
			require: 'ngModel',
			scope: {
				clientDetail : '=ngModel',
				editMode:'=editMode'
			},
			controller: function($scope,$http) {

                $scope.submit=function(){

                    if($scope.clientDetail.invoiceNeeded ==true && $scope.clientDetail.invoiceTitle==""){
                        alert("Please input Company Titile..");
                        return;
                    }

                    $scope.editMode=false;
                    console.log("submit");
                    console.log($scope.clientDetail);
                    var clientInfo = angular.copy($scope.clientDetail);
                    $http.post('api/updateClientPaymentInfo', {"clientInfo":clientInfo}).
                        success(function(data, status, headers, config) {
                            console.log("[Update] - PaymentInfo - SUCCESS");
                            console.log(data);
                        }).
                        error(function(data, status, headers, config) {
                        });
                };

				$scope.$on('setEidtMode', function (event,eidtMode) {
					console.log("on setEidtMode : "+eidtMode); // 'Data to send'
					$scope.editMode = eidtMode;
                    if($scope.editMode){
                        $scope.submit();
                    }
				});
			},
			templateUrl:'directives/templates/paymentSectionTmpl.html',
			controllerAs: 'paymentSection'
		};
	});
	
	app.directive('serviceHistorySectionTmpl',function(){
		return{
			restrict: 'E',
			require: 'ngModel',
			scope: {
				clientId : '=ngModel',
				readonly:'@readonly'
			},
			controller: function($scope,$http) {
                $scope.serviceHistory = [];
                $scope.$watch(
                    function( $scope ) {

                        return $scope.clientId;
                    },
                    function( newValue ) {
                        if($scope.clientId!=null){
                            loadHistory($scope.clientId);
                        }
                    }
                );
                function loadHistory(clientId){
                    $http.get('/api/serviceHistory/'+clientId+'?timestamp='+ new Date())
                        .then(function(result) {
                            console.log(result.data);
                            $scope.serviceHistory = result.data;
                        });
                }
			},
			templateUrl:'directives/templates/serviceHistorySectionTmpl.html',
			controllerAs: 'serviceSection'
		};
	});
	
	app.directive('commentsSectionTmpl',function($filter){
		return{
			restrict: 'E',
			require: 'ngModel',
			scope: {
				clientId : '=ngModel'
			},
			controller: function($scope,$http,UserService) {
                $scope.comments = [];
                $scope.$watch(
                    function( $scope ) {

                        return $scope.clientId;
                    },
                    function( newValue ) {
                        if($scope.clientId!=null){
                            loadComments($scope.clientId);
                        }
                    }
                );

                function loadComments(clientId){
                    $http.get('/api/getClientComments/'+clientId+'?timestamp='+ new Date())
                        .then(function(result) {
                            console.log(result.data);
                            $scope.comments = result.data;
                        });
                }

                $scope.newComment="";
				$scope.UserService = UserService;
				$scope.postComment=function(){

					console.log($scope.newComment);
                    $http.post('api/postClientComment', {"clientId":$scope.clientId,"content":$scope.newComment}).
                        success(function(data, status, headers, config) {
                            console.log(config);
                            console.log(data);

                            //$scope.comments.push(data);
                        }).
                        error(function(data, status, headers, config) {
                        });

				};
				$scope.deleteComment=function(index){
                    $http.post('api/deleteClientComment', {"comment":$scope.comments[index]}).
                        success(function(data, status, headers, config) {
                            //console.log(config);
                            console.log(data);
                            $scope.comments.splice(index,1);
                            //$scope.comments.push(data);
                        }).
                        error(function(data, status, headers, config) {
                        });

					//$scope.comments = $filter('orderBy')($scope.comments, '-createDateTime');
                    //console.log($scope.comments);
                    //console.log("index : "+index);
					//$scope.comments.splice(index,index+1);
				};
			},
			templateUrl:'directives/templates/commentsSectionTmpl.html',
			controllerAs: 'commentsSection'
		};
	});
	app.directive('moduleNewClient',function() {
		return {
			restrict: 'E',
			templateUrl:'directives/modules/createClient.html',
			controller: function($scope,$http,$location, $anchorScroll,MenuService) {
				$scope.hasSubmit = false;
				$http.post('api/create_client_info')
					.then(function(result) {
                    console.log(result);
					$scope.clientDetail=result.data;
					console.log($scope.clientDetail);
				}); 
				$scope.Create=function(){
					$scope.hasSubmit=true;
					$scope.$broadcast('setEidtMode',true);
					$location.hash('top');
					$anchorScroll();
				};
				$scope.Reset=function(){
					$http.post('api/getClientInfo/'+$scope.clientDetail.clientId)
						.then(function(result) {

						$scope.clientDetail=result.data					
					});
				};
				$scope.Confirm=function(){
					alert("Create new client successfully.");
					MenuService.changeComponents(0);
					$location.hash('top');
					$anchorScroll();
				};
			},
			templateUrl:'directives/modules/createClient.html',
			controllerAs: 'newClientModule'
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
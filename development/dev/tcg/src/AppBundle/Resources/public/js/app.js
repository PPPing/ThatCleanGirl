(function(){
	var app = angular.module('thatCleanGirl', ['pluginDirectives','ui.calendar']).config(function($interpolateProvider,$httpProvider){
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
        var components = [];

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

        var changeAlert = function(){
            var moduleStackNode = modulesStack[modulesStack.length-1];
            //console.log(moduleStackNode);
            var curModule = moduleStackNode.modules[moduleStackNode.curModuleIndex];
            console.log(curModule);

            if(curModule.changeAlart===true){
                if(!confirm(curModule.alartMsg)){
                    return false;
                }
            }
            return true;
        }

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
                if(changeAlert()){
                    curComponentIndex = index;
                    curModuleIndex=0;
                    modulesStack=[];
                    pushModulesStack(0,components[curComponentIndex].modules);
                }
			},
			changeModule:function(index){
                if(changeAlert()){
                    modulesStack[modulesStack.length-1].curModuleIndex = index;
                }

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
    app.directive('serviceCalendar',function($compile) {
        return {
            restrict: 'E',
            scope: {
            },
            controller:function($scope,$compile,$http,$modal,uiCalendarConfig) {
                console.log(uiCalendarConfig);
                var date = new Date();
                var d = date.getDate();
                var m = date.getMonth();
                var y = date.getFullYear();

                $scope.teamList = [
                    {
                        id:'teamA',
                        name:'TeamA'
                    },
                    {
                        id:'teamB',
                        name:'TeamB'
                    },
                    {
                        id:'teamC',
                        name:'TeamC'
                    }
                ];
                $scope.filtersMask = 0
                $scope.filters={};
                $scope.filtersValue={};
                var initFilters = function() {
                    angular.forEach($scope.teamList, function (value, key) {
                        $scope.filters[value.id] = true;
                        $scope.filtersValue[value.id] = Math.pow(10, key);
                        $scope.filtersMask += $scope.filtersValue[value.id];
                       // console.log(key +' : '+$scope.filtersMask);
                    });
                };
                initFilters();

                $scope.$watch(
                    function( $scope ) {
                        $scope.filtersMask = 0;
                        angular.forEach($scope.filters, function (value, key) {
                            if(value===true){
                                $scope.filtersMask += $scope.filtersValue[key];
                            }
                        });
                        return $scope.filtersMask;
                    },
                    function( newValue ) {
                        //console.log(newValue);
                        $scope.refetchEvents();
                    }
                );

                var calendarId = "serviceCalendar1";
                $scope.refetchEvents = function(){
                    if(uiCalendarConfig.calendars[calendarId]){
                        uiCalendarConfig.calendars[calendarId].fullCalendar('refetchEvents');
                    }
                };

                $scope.view = "agendaWeek";
                $scope.changeView = function(view) {
                    $scope.view = view;
                    uiCalendarConfig.calendars[calendarId].fullCalendar('changeView',view);
                };
                $scope.renderCalender = function() {
                    if(uiCalendarConfig.calendars[calendarId]){
                        console.log('renderCalender : ' + calendarId);
                        uiCalendarConfig.calendars[calendarId].fullCalendar('render');
                    }
                };
                $scope.eventRender = function( event, element, view ) {
                    element.attr({'tooltip': event.title,
                        'tooltip-append-to-body': true});
                    $compile(element)($scope);
                };
                /* alert on eventClick */
                $scope.alertOnEventClick = function( data, jsEvent, view){
                    console.log(data.title + ' was clicked ');
                    console.log(data);
                    $scope.openEditor(data);
                };
                /* alert on Drop */
                $scope.alertOnDrop = function(event, delta, revertFunc, jsEvent, ui, view){
                   // $scope.alertMessage = ('Event Droped to make dayDelta ' + delta);
                    console.log('Event Droped to make dayDelta ' + delta);
                };
                /* alert on Resize */
                $scope.alertOnResize = function(event, delta, revertFunc, jsEvent, ui, view ){
                   // $scope.alertMessage = ('Event Resized to make dayDelta ' + delta);
                    console.log('Event Resized to make dayDelta ' + delta);
                };

                var serviceInfoTest = {
                    clientId:'3349-8413',
                    clientName:'Test Client',
                    tel:'12310213123',
                    address:'address',
                    email:'test@email.com',
                    suburb:'suburb',
                    paymentType:'cash',
                    price:998,
                    invoiceNeeded:true,
                    invoiceTitle:'invoiceTitle',
                    serviceDate:'2015-05-30T08:59:48+0000',
                    serviceStartTime:'10:00',
                    serviceEndTime:'12:00',
                    notes:'adfasdfasdf',
                    teamId:'TeamA',
                    jobDetail:{
                        frequency: "weekly",
                        key:{
                            alarmIn: "09:00:AM",
                            alarmOut: "05:00:PM",
                            has: false,
                            hasAlarm: false,
                            notes: "keptByUs"
                        },
                        pet:{
                            has: false,
                            notes: "doesNotMatter"
                        },
                        items:[
                            {amount: 1,name: "Formal lounge", request: ""},
                            {amount: 1,name: "Formal adfasdf", request: "adfasdf"}
                        ]
                    }
                };

                var updateEvent = function(event){
                    var serviceInfo = event.serviceInfo;
                    event.title = "";
                    if(!serviceInfo.isConfirmed){
                        event.color = '#d9534f';
                        event.title = 'Unconfirmed\n';
                    }else{
                        if(serviceInfo.teamId==='teamA'){
                            event.color = '#337ab7';
                        }else if(serviceInfo.teamId==='teamB'){
                            event.color = '#5cb85c';
                        }else if(serviceInfo.teamId==='teamC'){
                            event.color = '#f0ad4e';
                        }
                    }
                    event.title += serviceInfo.clientName+'\n'+ serviceInfo.teamId;
                    event.textColor='#333';
                    return event;
                }

                var eventDataTransform = function(data){
                    //console.log(data);
                    var event={};
                    event.start = new Date(data.serviceDate);
                    var startTime = data.serviceStartTime.split(":");
                    event.start.setHours(startTime[0]);
                    event.start.setMinutes(startTime[1]);

                    event.end = new Date(data.serviceDate);
                    var endTime = data.serviceEndTime.split(":");
                    event.end.setHours(endTime[0]);
                    event.end.setMinutes(endTime[1]);

                    event.allDay=false;
                    event.serviceInfo = data;
                    //console.log(event);
                    event.color= '#e7d836';
                    event.textColor= 'black';

                    event = updateEvent(event);
                    return event;
                };
                var eventDataUpdate = function(event, data){
                    event.title = data.clientName;
                    event.start = new Date(data.serviceDate);
                    var startTime = data.serviceStartTime.split(":");
                    event.start.setHours(startTime[0]);
                    event.start.setMinutes(startTime[1]);

                    event.end = new Date(data.serviceDate);
                    var endTime = data.serviceEndTime.split(":");
                    event.end.setHours(endTime[0]);
                    event.end.setMinutes(endTime[1]);
                    event = updateEvent(event);
                    return event;
                }
                //config object
                $scope.uiConfig = {
                    calendar:{
                        height: 700,
                        editable: false,
                        defaultView:'agendaWeek',
                        header:{
                            left: 'title',
                            center: '',
                            right: 'today prev,next'
                        },
                        eventClick: $scope.alertOnEventClick,
                        eventRender: $scope.eventRender
                    }
                };

                $scope.serviceList=null;
                $scope.serviceEvents = function(start, end, timezone, callback) {
                    if($scope.serviceList===null){
                        $http.get('/api/service/all?timestamp='+ new Date())
                            .then(function(result) {
                                //console.log(result.data);
                                $scope.serviceList = result.data;
                                var events = [];
                                angular.forEach(result.data, function (value, key) {
                                   //console.log($scope.filters[value.teamId]);
                                   if($scope.filters[value.teamId]===true || value.isConfirmed===false){
                                        events.push(eventDataTransform(value));
                                   }
                                });
                                callback(events);
                            });
                    }else{
                        var events = [];
                        angular.forEach($scope.serviceList, function (value, key) {
                            console.log($scope.filters[value.teamId]);
                            if($scope.filters[value.teamId]===true || value.isConfirmed===false){
                                events.push(eventDataTransform(value));
                            }
                        });
                        callback(events);
                    }
                };
                $scope.serviceEventSources = {
                    events:  $scope.serviceEvents,
                    color: '#e7d836',   // an option!
                    textColor: 'black' // an option!
                    // eventDataTransform:eventDataTransform
                };
                $scope.eventSources = [ $scope.serviceEventSources];
                $scope.curEvent = null;
                var saveServiceInfo = function(serviceinfo ,callback){
                    $http.post('api/service/save', {"serviceInfo":serviceinfo}).
                        success(function(data, status, headers, config) {
                            if(data==="SUCCESS"){
                                if (callback && typeof(callback) === "function") {
                                    // execute the callback, passing parameters as necessary
                                    callback();
                                }
                            }
                        }).
                        error(function(data, status, headers, config) {
                            console.log(data);
                            console.log(config);
                            console.log(headers);
                        });
                };
                $scope.openEditor = function (event) {
                    $scope.curEvent = event;
                    console.log($scope.curEvent);
                    var modalInstance = $modal.open({
                        animation: true,
                        templateUrl: 'directives/templates/serviceEditorTmpl.html',
                        controller: function($scope,$modalInstance,serviceInfo){
                           $scope.serviceInfo = serviceInfo;
                           console.log($scope.serviceInfo);
                            $scope.serviceInfo.clientId = "1038-5986";
                            $scope.editMode=!$scope.serviceInfo.isConfirmed;

                            $scope.Confirm = function(){
                                $scope.serviceInfo.isConfirmed=true;
                                if(!$scope.serviceInfo.serviceDate || !$scope.serviceInfo.teamId ){
                                    alert("Please input all required information.");
                                }
                                var serviceInfo = angular.copy($scope.serviceInfo);
                                saveServiceInfo(serviceInfo,function(){
                                    $modalInstance.close($scope.serviceInfo);
                                });
                            };
                            $scope.Edit = function(){
                                $scope.editMode = true;
                            };
                            $scope.Save = function(){
                                $scope.editMode = false;
                                var serviceInfo = angular.copy($scope.serviceInfo);
                                saveServiceInfo(serviceInfo,function(){
                                    $modalInstance.close($scope.serviceInfo);
                                });
                            };
                        },
                        resolve: {
                            serviceInfo: function () {
                                return $scope.curEvent.serviceInfo;
                            }
                        }
                    });
                    modalInstance.result.then(function (serviceInfo) {
                        //console.log(serviceInfo);
                        $scope.curEvent = eventDataUpdate($scope.curEvent,serviceInfo);
                        console.log( $scope.curEvent);
                        uiCalendarConfig.calendars[calendarId].fullCalendar('updateEvent',$scope.curEvent);
                    }, function () {
                        //console.log('Modal dismissed at: ' + new Date());
                    });
                };

            },
            templateUrl:'directives/templates/serviceCalendarTmpl.html',
            controllerAs: 'serviceCalendarC'
        };
    });

    app.directive('serviceEditorTmpl',function(){
        return {
            restrict: 'E',
            require: 'ngModel',
            scope: {
               serviceInfo:'=ngModel'
            },
            controller: function($scope,$http,$element,MenuService,ValidationService){
                /* $scope.editMode=false;
                $scope.serviceInfo={};
                var clientDetailModules=[
                    {
                        id:"service-editor",
                        name:"Service",
                        isSubModule:true
                    }
                ];

               c

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
                };*/
            },
            templateUrl:'directives/templates/serviceEditorTmpl.html',
            controllerAs: 'serviceEditor'
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
                    reviewed:true,
                    cancelled:true
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
                      case 4 :  statusStr = "Cancelled";break;
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
				var clientList = null;
                $http.get('/api/client/getClientList?timestamp='+ new Date())
				.then(function(result) {
                        clientList = result.data.slice(0);
                        console.log(clientList);
                        $scope.clientListData = clientList;
				});
				this.viewClientDetail=function(clientId){
					console.log(clientId);
					console.log($scope.moduleInfo);
					$scope.moduleInfo.curSubModule="client-detail";
					$scope.moduleInfo.clientDetail_clientId = clientId;
				};
                $scope.keyWord="";
                $scope.filter=function(){
                    //console.log($scope.keyWord);
                    var regex = new RegExp( $scope.keyWord, 'i');
                    var newClientList = [];
                    angular.forEach(clientList, function (value, key) {
                        var clientInfo = value;
                        var content = clientInfo.clientName +" "
                                    +clientInfo.tel +" "
                                    +clientInfo.address +" "
                                    +clientInfo.district +" "
                                    +clientInfo.jobDetail.frequency +" "
                                    +clientInfo.price;
                        if(regex.test(content)){
                            newClientList.push(value);
                        }
                    });
                    $scope.clientListData = newClientList;
                }
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
						$http.get('api/client/getClientInfo/'+newValue)
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
                saveSection:'=saveSection',
				editMode:'=editMode'
			},
			controller: function($scope,$http,$element,MenuService,ValidationService) {
                //console.log($scope.editMode);
                //console.log($scope.saveSection);
                //$scope.editMode=true;
				$scope.submit=function() {
                    if(!ValidationService.check($element)){
                        alert("Please input all required information.");
                        return;
                    }
                    var clientInfo = angular.copy($scope.clientDetail);
                    $http.post('api/client/updateClientInfo', {"clientInfo":clientInfo}).
                        success(function(data, status, headers, config) {
                            console.log("[Update] - Client Info - SUCCESS");
                            alert("[SUCCESS] Client Info saved.");
                            $scope.editMode=false;
                        }).
                        error(function(data, status, headers, config) {
                            alert("[ERROR] Save Client Info Error.");
                        });
				};

                $scope.delete=function() {
                    if (confirm("Are sure want to DELETE this client?")) {
                        console.log('api/client/deleteClientInfo/' + $scope.clientDetail.clientId);
                        $http.get('api/client/deleteClientInfo/' + $scope.clientDetail.clientId)
                            .then(function (result) {
                                console.log(result);
                                MenuService.popModulesStack();
                            });
                    }
                };
                $scope.$on('setEditMode', function (event,editMode) {
                    //console.log("clientInfoSectionTmpl on setEditMode : "+editMode); // 'Data to send'
                    $scope.editMode = editMode;
                    if($scope.editMode){
                        //$scope.submit();
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
                saveSection:'=saveSection',
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

                    if($scope.clientId===null) {
                        alert("Invalid Client Id.");
                        return;
                    }
                   // console.log("submit");
                    var jobDetail = angular.copy($scope.jobDetail);
                    //console.log(jobDetail);
                    $http.post('api/client/updateClientJobDetail', {"jobDetail":jobDetail,"clientId":$scope.clientId}).
                        success(function(data, status, headers, config) {
                            console.log("[Update] - JobDetail - SUCCESS");
                            alert("[SUCCESS] Job Details saved.");
                            $scope.editMode=false;
                        }).
                        error(function(data, status, headers, config) {
                            alert("[ERROR] Save Job Details Error..");
                        });
                };

				$scope.$on('setEditMode', function (event,editMode) {
					//console.log("jobDetailSectionTmpl on setEditMode : "+editMode); // 'Data to send'
					$scope.editMode = editMode;
                    if($scope.editMode){
                        //$scope.submit();
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
				editMode:'=editMode',
                saveSection:'=saveSection'
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
                    $http.post('api/client/updateClientPaymentInfo', {"clientInfo":clientInfo}).
                        success(function(data, status, headers, config) {
                            console.log("[Update] - Payment Info - SUCCESS");
                            alert("[SUCCESS] Payment Info saved.");
                            $scope.editMode=false;
                        }).
                        error(function(data, status, headers, config) {
                            alert("[ERROR] Save Payment Info Error.");
                        });
                };

				$scope.$on('setEditMode', function (event,editMode) {
					//console.log("paymentSectionTmpl on setEditMode : "+editMode); // 'Data to send'
					$scope.editMode = editMode;
                    if($scope.editMode){
                        //$scope.submit();
                    }
				});
			},
			templateUrl:'directives/templates/paymentSectionTmpl.html',
			controllerAs: 'paymentSection'
		};
	});

    app.directive('reminderInfoSectionTmpl',function(){
        return{
            restrict: 'E',
            require: 'ngModel',
            scope: {
                reminderInfo : '=ngModel',
                editMode:'=editMode',
                clientId:'@clientId',
                saveSection:'=saveSection'
            },
            controller: function($scope,$http) {

                $scope.submit=function(){
                    $scope.editMode=false;
                    if($scope.clientId===null) {
                        alert("Invalid Client Id.");
                        return;
                    }
                    //console.log("submit");
                    //console.log($scope.reminderInfo);
                    var reminderInfo = angular.copy($scope.reminderInfo);
                    //console.log(reminderInfo);
                    $http.post('api/client/updateClientReminderInfo', {"reminderInfo":reminderInfo,"clientId":$scope.clientId}).
                        success(function(data, status, headers, config) {
                            console.log("[Update] - reminderInfo - SUCCESS");
                            alert("[SUCCESS] reminder Info saved.");
                            $scope.editMode=false;
                        }).
                        error(function(data, status, headers, config) {
                            alert("[ERROR] Save reminder Info Error.");
                        });
                };

                $scope.$on('setEditMode', function (event,editMode) {
                    //console.log("reminderInfoSectionTmpl on setEditMode : "+editMode); // 'Data to send'
                    $scope.editMode = editMode;
                    if($scope.editMode){
                        //$scope.submit();
                    }
                });

            },
            templateUrl:'directives/templates/reminderInfoSectionTmpl.html',
            controllerAs: 'reminderSection'
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
                    $http.get('/api/service/history/'+clientId+'?timestamp='+ new Date())
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
                    $http.get('api/client/getClientComments/'+clientId+'?timestamp='+ new Date())
                        .then(function(result) {
                            //console.log(result.data);
                            $scope.comments = result.data;
                        });
                }

                $scope.newComment="";
				$scope.UserService = UserService;
				$scope.postComment=function(){

					console.log($scope.newComment);
                    var newComment=angular.copy($scope.newComment);
                    $http.post('api/client/postClientComment', {"clientId":$scope.clientId,"content":newComment}).
                        success(function(data, status, headers, config) {
                            //console.log(config);
                            $scope.comments.push(data);
                            alert("[SUCCESS] New comment saved.");
                            //console.log( $scope.comments);
                        }).
                        error(function(data, status, headers, config) {
                            alert("[ERROR] Save new comment Error.");
                        });
				};
				$scope.deleteComment=function(index){
                    $http.post('api/client/deleteClientComment', {"comment":$scope.comments[index]}).
                        success(function(data, status, headers, config) {
                            //console.log(config);
                            console.log(data);
                            $scope.comments.splice(index,1);
                            alert("[SUCCESS]  This comment has been deleted.");
                            //$scope.comments.push(data);
                        }).
                        error(function(data, status, headers, config) {
                            alert("[ERROR]  Delete this comment has been deleted.")
                        });
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
				$http.post('api/client/create_client_info')
					.then(function(result) {
                    console.log(result);
					$scope.clientDetail=result.data;
					console.log($scope.clientDetail);
				}); 
				$scope.Save=function(){
					//$scope.hasSubmit=true;
                   var fullClientInfo = angular.copy($scope.clientDetail);
                    $http.post('api/client/saveFullClientInfo', {"fullClientInfo":fullClientInfo}).
                        success(function(data, status, headers, config) {
                            //console.log("[Update] - Client Info - SUCCESS");
                            alert("[SUCCESS] Client Info saved.");
                            $scope.editMode=false;
                            $scope.hasSubmit=true;
                        }).
                        error(function(data, status, headers, config) {
                            alert("[ERROR] Save Client Info Error.");
                            //console.log("[ERROR] Save Client Info Error.");
                        });

					$scope.$broadcast('setEditMode',false);
					//$location.hash('top');
					//$anchorScroll();
				};
				$scope.Reset=function(){
					$http.post('api/client/getClientInfo/'+$scope.clientDetail.clientId)
						.then(function(result) {
						$scope.clientDetail=result.data					
					});
				};
				$scope.Confirm=function(){
					alert("Create new client successfully.");
					//MenuService.changeComponents(0);
					//$location.hash('top');
					//$anchorScroll();
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
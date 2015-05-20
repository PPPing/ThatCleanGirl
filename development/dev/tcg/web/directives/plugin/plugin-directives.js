/**
 * Created by Mr.Clock on 2015/5/5.
 */
(function() {
    var app = angular.module('pluginDirectives',['ui.bootstrap']);

    app.directive('pluginDatePicker',function() {
        return {
            restrict: 'E',
            scope: {
                dateStr : '=ngModel'
            },
            controller: function($scope,$filter) {


                $scope.open = function($event) {
                    $event.preventDefault();
                    $event.stopPropagation();

                    $scope.opened = true;
                };

                $scope.dateOptions = {
                    formatYear: 'yy',
                    startingDay: 1
                };

                //$scope.formats = ['dd/MM/yyyy','dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
                $scope.format = 'dd/MM/yyyy';
                $scope.dateObject = $scope.dateStr;


                $scope.$watch(
                    function( $scope ) {
                        return $scope.dateStr;
                    },
                    function( newValue ) {
                        $scope.dateObject = $scope.dateStr;
                    }
                );

                $scope.$watch(
                    function( $scope ) {
                        return $scope.dateObject;
                    },
                    function( newValue ) {
                        if(newValue!=null){
                            $scope.dateStr = $filter('date')(newValue, "yyyy-MM-ddTHH:mm:ssZ");
                            console.log($scope.dateStr);
                        }
                    }
                );
            },
            templateUrl:'directives/plugin/datePickerTmpl.html',
            controllerAs: 'datePickerC'
        };
    });

    app.directive('pluginTimePicker',function() {
        return {
            restrict: 'E',
            scope: {
                timeStr : '=ngModel'
            },
            controller: function($scope) {

                $scope.hourOptions=['00','01','02','03','04','05','06','07','08','09','10','11','12'];
                $scope.minOptions=['00','10','20','30','40','50'];
                $scope.amOptions = ["AM","PM"];
                $scope.isAM=true;
                $scope.hour = '08';
                $scope.min = '00';
                $scope.am = "AM";
                $scope.$watch(
                    function( $scope ) {
                        return $scope.timeStr;
                    },
                    function( newValue ) {
                        var hour = '08';
                        var min = '00';
                        var am = "AM";
                        if (newValue != null) {
                            newValue = newValue.trim().split(":");
                            if (newValue[0]) {
                                hour = newValue[0];
                            }
                            if (newValue[1]) {
                                min = newValue[1];
                            }
                            if (newValue[2]) {
                                am = newValue[2];
                            }
                        }
                        $scope.hour = hour;
                        $scope.min = min;
                        $scope.am = am;
                        if( am =='AM'){
                            $scope.isAM=true;
                        }else{
                            $scope.isAM=false;
                        }
                    }
                );
                $scope.toggleAM=function(){
                    $scope.isAM = ! $scope.isAM;
                    $scope.am = $scope.isAM?$scope.amOptions[0]:$scope.amOptions[1];
                    $scope.change();
                };
                $scope.change=function(){
                    $scope.timeStr = $scope.hour+":"+ $scope.min+":"+$scope.am;
                    console.log($scope.timeStr);
                };
            },
            templateUrl:'directives/plugin/timePickerTmpl.html',
            controllerAs: 'timePickerC'
        };
    });

})();
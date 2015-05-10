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

})();
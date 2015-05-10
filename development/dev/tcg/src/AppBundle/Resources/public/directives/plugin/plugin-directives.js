/**
 * Created by Mr.Clock on 2015/5/5.
 */
(function() {
    var app = angular.module('pluginDirectives',[]);

    app.directive('pluginDatePicker',function() {
        return {
            restrict: 'E',
            scope: {},
            controller: function($scope) {

            },
            templateUrl:'directives/plugin/datePickerTmpl.html',
            controllerAs: 'datePickerC'
        };
    });

})();
(function() {
    var app = angular.module('bigcommerce-flickr');

    app.directive('historyRemovable', ['$compile', function($compile) {
        return {
            'restrict': 'A',
            'scope': true,
            'link': function(scope, elem, attrs) {
                scope.confirm = false;

                scope.confirmNo = function() {
                    scope.confirm = false;
                };

                scope.confirmYes = function() {
                    scope.removeHistoryElement(attrs.historyRemovable)
                        .then(function() {
                            elem.remove();
                            scope.info("Item was removed successfully.");
                        }, function() {
                            scope.error("Failed to remove an item.");
                        });
                };

                scope.removeHistoryItem = function() {
                    scope.confirm = true;
                };

                elem.append(
                    $compile(
'<div class="right btn-group btn-group-xs">\
<button type="button" class="btn btn-danger glyphicon glyphicon-remove-circle" aria="Remove" data-ng-click="removeHistoryItem()" data-ng-if="!confirm"></button>\
<button type="button" class="btn btn-danger" data-ng-if="confirm" data-ng-click="confirmYes()">Yes</button>\
<button type="button" class="btn btn-success" data-ng-if="confirm" data-ng-click="confirmNo()">No</button>\
</div>'             )(scope)
                );
            }
        };
    }]);

    app.controller('History', ['$scope', '$http', function ($scope, $http) {
        var showMessage = function(message, type) {
            $scope.message = {'text': message, 'type': "alert-" + type};
        };

        $scope.error = function(message) {
            showMessage(message, "danger");
        };

        $scope.info = function(message) {
            showMessage(message, "info");
        };

        $scope.hideMessage = function() {
            delete $scope.message;
        };

        $scope.removeHistoryElement = function(id) {
            $scope.hideMessage();
            return $http.delete("/delete?id=" + encodeURIComponent(id));
        };
    }]);
})();

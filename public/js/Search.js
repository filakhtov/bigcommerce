(function() {
    var app = angular.module('bigcommerce-flickr');

    app.factory('SearchRepository', ['$http', '$location', function($http, $location) {
        var fetchGalleryForQuery = function(query, page, $scope) {
            var config = {
                'headers': {
                    'X-Api': 'AngularJS'
                }
            };
            return $http.get('/gallery?query=' + encodeURIComponent(query) + '&page=' + encodeURIComponent(page), config)
                .then(function(response) {
                    $location.path('/search').search({'query': query, 'page': page});
                    $scope.response = response.data;
                });
        };

        return fetchGalleryForQuery;
    }]);

    app.controller('Search', ['$scope', 'SearchRepository', '$location', function ($scope, SearchRepository, $location) {
        $scope.error = null;

        $scope.viewImage = function($event, image) {
            $event.preventDefault();
            $scope.imagePreview = image;
        };

        $scope.hideImage = function() {
            delete $scope.imagePreview;
        }

        $scope.showError = function(message) {
            $scope.error = message;
        };

        $scope.hideError = function() {
            $scope.error = null;
        };

        $scope.showLoader = function(show) {
            if(show) {
                $scope.loader = true;
            } else {
                $scope.loader = false;
            }
        }

        $scope.searchFormSubmit = function() {
            if(!$scope.searchForm.$valid) {
                $scope.showError('Please, enter at least 3 characters to search.');
            } else {
                $scope.search($scope.searchRequest);
            }

            return false;
        }

        $scope.search = function(searchRequest) {
            $scope.hideError();
            $scope.showLoader(true);

            SearchRepository(searchRequest.query, searchRequest.page, $scope)
                .then(function() {
                    $scope.images = $scope.response.images;
                    $scope.currentPage = $scope.response.page;
                    $scope.pages = $scope.paginator($scope.page, $scope.response.totalPages);
                    delete $scope.response;
                }, function(response) {
                    if(getObjectVar(response, 'data', 'message')) {
                        $scope.showError(response.data.message);
                    } else {
                        $scope.showError('Sorry, we encountered an unexpected error. Please, try again.');
                    }
                }).finally(function() {
                    $scope.showLoader(false);
                });
        };

        var getObjectVar = function(object) {
            for(var i = 1; i < arguments.length; ++i) {
                var key = arguments[i];
                if(angular.isObject(object) && key in object) {
                    object = object[key];
                } else {
                    object = null;
                    break;
                }
            }

            return object;
        };

        var queryString = $location.search();

        $scope.paginator = function(page, pages) {
            var shownPages = [];
            for(var i = 1; i <= pages; ++i) {
                if(i <= 3) {
                    shownPages.push(i);
                    continue;
                }

                if(i > page - 3 && i < page + 3) {
                    shownPages.push(i);
                    continue;
                }

                if(i > pages - 3) {
                    shownPages.push(i);
                    continue;
                }
            }

            return shownPages;
        }

        $scope.getLastPage = function() {
            var page = 0;
            if($scope.pages) {
                page = $scope.pages[$scope.pages.length - 1];
            }

            return page;
        }

        $scope.searchRequest = {
            'query': getObjectVar(queryString, 'query'),
            'page': getObjectVar(queryString, 'page')
        };

        if(!$scope.searchRequest.page) {
            $scope.searchRequest.page = 1;
        }
        if($scope.searchRequest.query) {
            $scope.search($scope.searchRequest);
        }
    }]);
})();

(function() {
    var app = angular.module('bigcommerce-flickr');

    app.factory('SearchRepository', ['$http', '$location', function($http) {
        var fetchGalleryForQuery = function(query, page, saveToHistory) {
            return $http.get('/gallery?query=' + encodeURIComponent(query) + '&page=' + encodeURIComponent(page) + (saveToHistory ? '&saveToHistory=1' : ''));
        };

        return fetchGalleryForQuery;
    }]);

    app.controller('Search', ['$scope', 'SearchRepository', '$location', function ($scope, SearchRepository, $location) {
        $scope.error = null;
        $scope.noResults = false;

        $scope.viewImage = function($event, image) {
            $event.preventDefault();
            $scope.imagePreview = image;
        };

        $scope.hideImage = function() {
            delete $scope.imagePreview;
        };

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
        };

        $scope.searchFormSubmit = function() {
            if (!$scope.searchForm.$valid) {
                $scope.showError('Please, enter at least 3 characters to search.');
            } else {
                $scope.search($scope.searchRequest, true);
            }

            return false;
        };

        $scope.search = function(searchRequest, saveToHistory) {
            $scope.hideError();
            $scope.showLoader(true);
            $scope.noResults = false;

            SearchRepository(searchRequest.query, searchRequest.page, saveToHistory)
                .then(function(response) {
                    var data = response.data;

                    $scope.noResults = (data.totalPages === 0);
                    $scope.images = data.images;
                    $scope.searchRequest.page = data.page;
                    $scope.pages = $scope.paginator(data.page, data.totalPages);

                    $location.search($scope.searchRequest);
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
            for (var i = 1; i < arguments.length; ++i) {
                var key = arguments[i];
                if (angular.isObject(object) && key in object) {
                    object = object[key];
                } else {
                    object = null;
                    break;
                }
            }

            return object;
        };

        var queryString = $location.search();

        var insertPage = function(pages, pageToInsert) {
            var numberOfPagesToShow = pages.length;
            if (numberOfPagesToShow) {
                var lastPage = pages[--numberOfPagesToShow];

                if (pageToInsert !== ++lastPage) {
                    pages.push(0);
                }
            }
            pages.push(pageToInsert);
        };

        var isPageCloseToCurrent = function(currentPage, page) {
            return (page > (currentPage - 3) && page < (currentPage + 3));
        };

        $scope.isPageCloseToCurrent = function(page) {
            return isPageCloseToCurrent($scope.searchRequest.page, page);
        };

        $scope.paginator = function(page, pages) {
            var shownPages = [];
            for (var i = 1; i <= pages; ++i) {
                if (
                    (i <= 3) ||
                    isPageCloseToCurrent(page, i) ||
                    i > pages - 3
                ) {
                    insertPage(shownPages, i);
                }
            }

            return shownPages;
        };

        $scope.getLastPage = function() {
            var page = 0;

            if ($scope.pages) {
                page = $scope.pages[$scope.pages.length - 1];
            }

            return page;
        };

        $scope.searchRequest = {
            'query': getObjectVar(queryString, 'query'),
            'page': getObjectVar(queryString, 'page')
        };

        $scope.goToPage = function(page) {
            if (page < 1 || page > $scope.getLastPage() || page === $scope.searchRequest.page) {
                return;
            }

            $scope.searchRequest.page = page;
            $scope.search($scope.searchRequest);
        };

        if (!$scope.searchRequest.page) {
            $scope.searchRequest.page = 1;
        }

        if ($scope.searchRequest.query) {
            $scope.search($scope.searchRequest);
        }
    }]);
})();

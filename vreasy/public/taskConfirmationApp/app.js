angular.module('taskConfirmationApp',  ['ui.router', 'ngResource'])
.config(function($stateProvider, $urlRouterProvider, $locationProvider) {
    // Use hashtags in URL
    $locationProvider.html5Mode(false);

    $urlRouterProvider.otherwise("/");
    $stateProvider
    .state('index', {
      url: "/",
      templateUrl: "/taskConfirmationApp/templates/index.html",
      controller: 'TaskCtrl'
    });
})
.factory('Task', function($resource) {
    return $resource('/task/:id?format=json',
        {id:'@id'},
        {
            'get': {method:'GET'},
            'save': {method: 'PUT'},
            'create': {method: 'POST'},
            'query':  {method:'GET', isArray:true},
            'remove': {method:'DELETE'},
            'delete': {method:'DELETE'}
        }
    );
})
.factory('TaskHistory', function($resource) {
    return $resource('/taskhistory/:id?format=json',
        {id:'@id'},
        {
        	'query':  {method:'GET', isArray:true},
        }
    );
})
.controller('TaskCtrl', function($scope, Task, TaskHistory) {
	$scope.currentLog = null;
	$scope.tasks = Task.query();
	$scope.setCurrent = function(id)
	{
		//hide current table 
		$scope.currentLog = null;
		TaskHistory.query({'id':id}, function(response){
			//assign new table with response only and when the response is received
			$scope.currentLog = response;
		});
	};
    $scope.getStateName = function(id)
                        {
                            switch(id)
                            {
                                case 1 : return "Pending";
                                case 2 : return "Refused";
                                case 3 : return "Accepted";
                                case 4 : return "Completed";
                                default : return "Err...";
                            }
        
                        };
                          
                          
});

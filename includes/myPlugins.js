(function ($) {
    $.fn.exists = function () { return this.length > 0; }
    $.fn.filterByData = function (field, val) {
        return $(this).filter(function () {
            return ($(this).data("key")[field] == val)
        });
    }
}(jQuery));

var MyRequestsCompleted = (function () {
    var numRequestToComplete, requestsCompleted, callBacks, singleCallBack, finalCallback;

    return function (options) {
        if (!options) options = {};

        numRequestToComplete = options.numRequest || 0;
        requestsCompleted = options.requestsCompleted || 0;
        finalCallback = options.finalCallback || 0;
        
        callBacks = [];
        var fireCallbacks = function () {
            for (var i = 0; i < callBacks.length; i++) callBacks[i]();
            finalCallback();            
        };
        if (options.singleCallback) callBacks.push(options.singleCallback);

        this.addCallbackToQueue = function (isComplete, callback) {
            if (isComplete) requestsCompleted++;
            if (callback) callBacks.push(callback);
            if (requestsCompleted == numRequestToComplete) fireCallbacks();
        };
        this.requestComplete = function (isComplete) {
            if (isComplete) requestsCompleted++;
            if (requestsCompleted == numRequestToComplete) fireCallbacks();
        };
        this.setCallback = function (callback) {
            callBacks.push(callBack);
        };
    };
})();

 /*
    var loadFinished = function () {
        setTimeout(function () { //delay a little bit to have time watching the land page            
            switchPage(SETTINGS.CURR_PAGE);
        },2)
        
    }
    $(".page[data-page=land]").show();
    var requestCallback = new MyRequestsCompleted({
        numRequest: 2,
        finalCallback: loadFinished
    });
    //usage in request
    $.ajax({
        url: "server/get/getLists.php?user_id=" + SETTINGS.USER_ID,
        success: function (json) {
            requestCallback.addCallbackToQueue(true, function () {
                console.log(json);
                $.map(json, function (list) {
                    renderMainListItem(list);
                })
            });
        }
    });        
    $.ajax({
        type: "GET",
        data: { user_id: SETTINGS.USER_ID },
        url: "server/get/getUserDetails.php",
        success: function (json) {
            requestCallback.addCallbackToQueue(true, function () {                
                console.log(json);
                $(".spanUserName").text(json.firstname + " " + json.lastname);
                $(".spanUserID").text(SETTINGS.USER_ID);
                setNotificationBadger(json.newNotificationsCounter);
            });
        }
    });
    */

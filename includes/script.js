function switchPage(page) {    
    var targetPage;
    if ($.type(page) == 'string')
        targetPage = page;
    else
        targetPage = $(this).attr("data-target");

    var currPage = $(".page:visible").attr("data-page");    
    
    if (SETTINGS.isInProfile == false) {                
        if (targetPage == 'profile') {            
            SETTINGS.isInProfile = true;
            SETTINGS.LAST_PAGE = $(".page:visible").attr("data-page");
        }
    } else { //already navigates in profile        
        if (targetPage == 'profile' && currPage=="profile") {            
            targetPage = SETTINGS.LAST_PAGE;
            SETTINGS.isInProfile = false;            
        } 
    }   

    if (targetPage == 'profile') {
        $("#logoRow").show();
        $("#profileRow").hide();
    } else {
        $("#logoRow").hide();
        $("#profileRow").show();
    }
    
    var effect = $(".page[data-page='" + targetPage + "']").attr("data-effect");
    if (effect === undefined)
        effect = 'fade';
        
    $(".page:visible").hide();
    $(".page[data-page='" + targetPage + "']").show(effect, '', 300);       
}    


function setDynamicField(tableName,field,val,whereField,whereVal,callback) {
    $.ajax({
        type: "GET",
        data: { tableName: tableName, field: field, val: val, whereField: whereField, whereVal: whereVal },
        url: "server/set/setDynamicField.php",
        success: function (data) {                        
            if (callback != undefined)
                callback();
        }
    });
}
/*mainLists*/
    function renderMainLists(lists) {
        $("#mainLists").empty();    
        $.map(lists, function (list) {
            renderMainListItem(list);
        })
    }
    function renderMainListItem(mainListObj) {
        var $mainListItem = $("<div class='row main-list'></div>");
        $mainListItem.data('key', mainListObj);
        $mainListItem.append("<div class='col-6 text-right'>" + mainListObj.listName + "</div>");
        $mainListItem.append("<div class='col-4'><span class='quantity'>" + mainListObj.quantity + "</span> מוצרים</div>");
        $mainListItem.append("<div class='col-2 text-left'><span class='fas fa-angle-left'></span></div>");
        $("#mainLists").append($mainListItem);        
    }
    
/*addMainList*/
    function newList_addShareMember() {                        
        var $newMemberRow = $("<div class='form-group row text-right rowNewMember'></div>");                
        $newMemberRow.append('<div class="col-6 col-md-2"><input type="number" class="form-control" name="txtAddMainListMember[]" placeholder="מספר מזהה" min="1"></div>');                
        $newMemberRow.append('<div class="col-2 col-md-1"><span class="btnNewListShareMinus"><i class="fas fa-minus"></i></span></div>');

        $(".rowShareMemeber").after($newMemberRow);        
    }
    function newList_removeShareMember() {
        var $rowMember = $(this).closest(".rowNewMember");
        $rowMember.remove();
    }
    function mainListClick() {
        var list = $(this).data('key');
        
        $.ajax({
            url: "server/get/getListProducts.php?list_id=" + list.listID,
            success: function (json) {
                console.log(json);
                $("#mainListName").text(list.listName).data("key",list);
                renderListItems(json);

                var totalItems = 0;
                $.map(json.subLists, function (sublist) {
                    totalItems += sublist.items.length;
                })
                setMainListQunatity(list.listID, totalItems);
                switchPage("mainListItems");        
            }
        })               
    }
    function addMainList(event) {
        event.preventDefault();
        var listName = $("input[name='txtAddMainListName']").val();
        var formData = $("#formAddMainList").serialize() + "&user_id=" + SETTINGS.USER_ID;

        $("#formAddMainList .highlightBorder").removeClass("highlightBorder");
        $("#formAddMainList .alert").hide();
        $.ajax({
            type: "GET",
            url: "server/set/insertList.php",
            data: formData,
            success: function (newID) {                
                if (~newID.indexOf("error")) {
                    $("#formAddMainList .alert").text("אירעה שגיאה. בדוק חיבור לרשת").show();
                    return false;
                } else if (~newID.indexOf("notExists")) {
                    var badID = newID.split(":")[1];
                    $("input[name='txtAddMainListMember[]']").filter(function () {
                        return ($(this).val() == badID)
                    }).addClass("highlightBorder");
                    $("#formAddMainList .alert").text("מספר מזהה אינו חוקי").show();
                    return false;
                }                
                renderMainListItem({
                    listID: newID,
                    listName: listName,
                    quantity: 0
                })

                $("#formAddMainList").trigger("reset");
                $(".rowNewMember").remove();
                switchPage("mainLists");
            }
        })                
    }

/*mainListItems*/

    function renderListItems(data) {
        console.log(data.subList);
        $(".page[data-page='mainListItems'] .ul-main-list").empty();
        var subLists = data.subLists;
        $.map(subLists, function (subList) {
            renderSubList(subList);
        })
    }
    function renderSubList(subList) {              
        var $subList = $("<li></li>");
        var $collapseTitle = $('<span data-toggle="collapse" data-target="ul[data-cat=' + subList.subListID + ']"></span>');
        $collapseTitle.append('<i class="fas fa-plus fa-1x"></i> ' + subList.subListName + ' (<span class="subListQuantity" data-cat=' + subList.subListID +'>' + subList.items.length + '</span>)');
        var $subUL = $('<ul class="collapse show ul-main-list-category" data-cat="' + subList.subListID + '"></ul>');

        $.map(subList.items, function (item) {        
            renderItemLI($subUL,item)            
        })        

        $subList.append($collapseTitle).append($subUL);
        $(".page[data-page='mainListItems'] .ul-main-list").append($subList);
        
        /*subList li html example:
            <li>
                <span data-toggle="collapse" data-target="ul[data-cat='2']">
                    <i class="fas fa-plus fa-1x"></i>מטבח (3)
                </span>
                <ul class="ul-main-list-category collapse" data-cat="2">
                    <li>חלב</li>
                    <li>ביצים</li>
                    <li>שמן קנולה</li>
                </ul>
            </li>
        */
    }
    function renderItemLI($ul, item) {        
        var quantity = '';
        
        if (item.quantity !== undefined && item.quantity != '1' && item.quantity!=1)
            quantity = ' <B>('+item.quantity + ')</B>'

        var $newLI = $('<li class="item" data-id="' + item.id + '">' + item.name + quantity + '</li>');
        $newLI.data('key', item);

        if (item.isChecked==1)
            $newLI.addClass("item-checked");

        if (item.isWaiting == 1)
            $newLI.addClass("item-waiting");

        $ul.append($newLI);
        increaseSublistQuantity(item.category);
    }
    function decreaseSublistQuantity(cat) {
        var newSubListQuantity = parseInt($(".subListQuantity[data-cat=" + cat + "]").text()) - 1;
        $(".subListQuantity[data-cat=" + cat + "]").text(newSubListQuantity);
    }
    function increaseSublistQuantity(cat) {
        var newSubListQuantity = parseInt($(".subListQuantity[data-cat=" + cat + "]").text()) + 1;
        $(".subListQuantity[data-cat=" + cat + "]").text(newSubListQuantity);
    }
    function increaseMainListQuantity(listID) {
        var $mainList = $(".main-list").filterByData('listID', listID);
        var newQuantity = $mainList.data("key").quantity + 1;
        $mainList.data("key").quantity = newQuantity;
        $mainList.find(".quantity").html(newQuantity);
    }
    function decreaseMainListQuantity(listID) {
        var $mainList = $(".main-list").filterByData('listID', listID);
        var newQuantity = $mainList.data("key").quantity - 1;
        $mainList.data("key").quantity = newQuantity;
        $mainList.find(".quantity").html(newQuantity);
    }
    function setMainListQunatity(listID, quantity) {
        var $mainList = $(".main-list").filterByData('listID', listID);
        var newQuantity = quantity;
        $mainList.data("key").quantity = newQuantity;
        $mainList.find(".quantity").html(newQuantity);
    }
    
    

    function toggleListView() {        
        var mode = $(this).attr("data-mode");
        if (mode == '1') { //show only items
            $(this).attr("data-mode", '2');
            $("span[data-toggle='collapse']").hide();            
            $("ul.collapse:visible").attr("data-wasVisible", "1");
            $("ul.collapse").collapse('show');            
        } else if (mode == '2') { //show categories too
            $(this).attr("data-mode", '1');
            $("span[data-toggle='collapse']").show();
            $("ul.collapse[data-wasVisible!=1]").collapse('hide');                                                
            $("ul.collapse:visible").attr("data-wasVisible", "0");
        }        
    }
    function shareList() {                
        var str = '';
        var arr = $("ul.ul-main-list-category li");
        arr.each(function (i) {
            str += $(this).text();
            if (i != arr.length - 1)
                str += "%0A"; // <BR> in whatsapp api
        })                
        window.location.href = "whatsapp://send?text=" + str;        
    }     
    function checkItem() {
        var $el = $(this);
        var item = $(this).data('key');        
        var category = $(this).parent().attr("data-cat");
        var list = $("#mainListName").data("key");
        var isChecked;

        if ($(this).hasClass("item-checked")) 
            isChecked = 0;
        else 
            isChecked = 1;

        $.ajax({
            type: "GET",
            data: { item_id: item.id, category: category, isChecked: isChecked },
            url: "server/set/checkItem.php",
            success: function (data) {                
                $el.toggleClass("item-checked");        
                if (isChecked) {
                    decreaseSublistQuantity(item.category);
                    decreaseMainListQuantity(list.listID);
                } else {
                    increaseSublistQuantity(item.category);
                    increaseMainListQuantity(list.listID);
                }
            }
        })        
    }    

/*manuallyAddItem*/
    function addManualItem(event) {
        event.preventDefault();

        var list = $("#mainListName").data("key");

        var itemName = $("#txtAddListItemName").val();
        var itemQuantity = $("#txtAddListItemQuantity").val();

        var formData = $("#formAddManualItem").serialize() + "&list_id=" + list.listID;
        
        $.ajax({
            type: "post",
            data: formData,
            url: "server/set/insertManualItem.php",            
            success: function (newItem) {
                var $theUL = $(".ul-main-list-category[data-cat=" + newItem.category + "]");
                if (!$theUL.exists()) { //IF UL NOT EXISTS
                    var subList = {
                        subListID: '-1',
                        subListName: 'הוספה ידנית',
                        items: []
                    }
                    subList['items'].push(newItem);
                    renderSubList(subList);                    
                } else {
                    renderItemLI($theUL, newItem);
                }                
                increaseMainListQuantity(list.listID);
                switchPage("mainListItems");

                $("#formAddManualItem").trigger("reset");
            }
        })                        
    }

/*profile*/
    function setNotificationBadger(val) {
        if (val == -1) {
            val = Number($(".notificationBadger:first").text()) - Number(1);
        }

        if (val == 0) {
            $(".notificationBadger").hide();
            $(".spanNotificationCounter").hide();
        } else {
            $(".notificationBadger").text(val).show();
            $(".spanNotificationCounter").text(" (" + val + ")").show();
        }
    }
    function showNotifications() {
        $("#notificationsList").empty();
        $.ajax({
            type: "GET",
            data: { user_id: SETTINGS.USER_ID },
            url: "server/get/getNotifications.php",
            success: function (json) {
                $.map(json, function (notification) {
                    renderNotificationRow(notification);                    
                })
                switchPage("notificationsList");
            }
        })
    }
    function showSpecificNotification(notification) {                        
        var mainContent='';
        $("#specificNotificationTopic").text(notification.topic);
                
        if (notification.type_id == '1') { //unknown product was scanned                    
            mainContent = "<h5>ברקוד אינו מזוהה: " + notification.id_1 + "</h4>";
            mainContent += "המוצר שנסרק בשעה " + notification.time + " בתאריך " + notification.date + "<BR>לא נמצא במאגר Gulo.<BR>";
            mainContent += "עזרו לנו ולחברי Gulo להכיר את המוצר <BR> ועל הדרך תרוויחו 10 נק(:<BR><BR>";
            if (notification.isChecked == '0')
                mainContent += "<button class='switchPage addProductToInventory' data-target='addToInventory'>הוספת מוצר<br />למאגר Gulo</button>";
            else
                mainContent += "המוצר ממתין לאישור<BR><B>מערכת Gulo מודה לך על עזרתך.</B>";

            $(".page[data-page='specificNotification'] main").html(mainContent);
            $(".page").hide();
            $(".page[data-page='specificNotification']").show();

            $("#formAddToInventory input[name=productBarcode]").val(notification.id_1);
            $("#formAddToInventory input[name=notification_id]").val(notification.id);
        }
       
        if (notification.isNew == 1) {
            setDynamicField("238_notifications", "isNew", 0, "id", notification.id);
            setNotificationBadger(-1); //decrease notification badger
            $(".notificationRow").filterByData("id", notification.id).removeClass("isNew").data("key").isNew = 0;                                    
        }
    }
    function renderNotificationRow(notification) {                
        var $newRow = $("<div class='row notificationRow'></div>");
        if (notification.isNew == '1')
            $newRow.addClass("isNew");
        $newRow.data("key", notification);
        var $col2 = $("<div class='col-2 text-center'></div>");
        var $col10 = $("<div class='col-10 text-center'></div>");
        $col10.text(notification.topic);
        $col2.html(notification.time + "<BR>" + notification.date);

        $newRow.append($col2).append($col10);

        $("#notificationsList").append($newRow);
    }    
/*addToInventory*/
    function sortBrandsDatalistByCategory() {
        var categoryVal = $(this).val();
        var category = $("#datalist_categories").find("option[value='" + categoryVal + "']").data("key");
        if (category == undefined)
            return false;
        $("#datalist_brands option").filter(function () {
            $(this).attr("disabled", true);
            var isFind = false;            
            $.each($(this).data("categories"), function (index, cat) {
                if (cat == category.category_id)
                    isFind = true;
            });
            if (isFind)                 
                $(this).attr("disabled", false);            
        });
    }
    function renderBrandsDatalist(brands) {
        var $datalist = $("#datalist_brands");
        $datalist.empty();
        $.map(brands, function (brand) {
            renderDatalistBrandsOption(brand, $datalist);
        })
    }
    function renderCategoriesDatalist(categories) {
        var $datalist = $("#datalist_categories");
        $datalist.empty();
        $.map(categories, function (category) {
            renderDatalistCategoriesOption(category, $datalist);
        })
    }
    function renderCapacityUnitsDatalist(capacity_units) {
        var $datalist = $("#datalist_capacity_units");
        $datalist.empty();
        $.map(capacity_units, function (unit) {
            renderDatalistCapacityUnitsOption(unit, $datalist);
        })
    }
    function renderDatalistCategoriesOption(category, $datalist) {
        var $option = $("<option value='" + category.category_name + "'></option>");
        $option.data("key", category);
        $datalist.append($option);
    }
    function renderDatalistBrandsOption(brand, $datalist) {        
        var $option = $("<option value='" + brand.brand_name + "'></option>");
        $option.attr("data-categories", "[" + brand.brand_categories + "]");
        $option.data("key", brand);
        $datalist.append($option);
    }
    function renderDatalistCapacityUnitsOption(unit, $datalist) {                
        var $option = $("<option value='" + unit.symbol + "'></option>");        
        $option.data("key", unit);
        $datalist.append($option);
    }
    function addToInventoryGo(event) {
        event.preventDefault();
        var notification_id = $("#formAddToInventory input[name=notification_id]").val();                
        var notification = $(".notificationRow").filterByData("id", notification_id).data("key");

        var category, category_id, brand, brand_id, capacity_unit, capacity_unit_id;

        category = $("#datalist_categories").find("option[value='" + $("input[name=productCategory]").val() + "']").data("key");
        if (category != undefined)
            category_id = category.category_id;
        else
            category_id = null;
        
        brand = $("#datalist_brands").find("option[value='" + $("input[name=productBrand]").val() + "']").data("key");
        if (brand != undefined)
            brand_id = brand.brand_id;
        else
            brand_id = null;

        capacity_unit = $("#datalist_capacity_units").find("option[value='" + $("input[name=productCapacityUnits]").val() + "']").data("key");
        if (capacity_unit != undefined)
            capacity_unit_id = capacity_unit.id;
        else
            capacity_unit_id = null;

        var formData = $("#formAddToInventory").serialize() + "&category_id=" + category_id + "&brand_id=" + brand_id + "&capacity_unit_id=" + capacity_unit_id + "&user_id=" + SETTINGS.USER_ID+"&list_id="+notification.id_2;
        $.ajax({
            type: "GET",
            data: formData,
            url: "server/set/insertManualToInventory.php",
            success: function (data) {                                    
                increaseMainListQuantity(notification.id_2); //increase listID quantity(insert manual WAITING item to list)

                notification.isChecked = 1;                            
                showSpecificNotification(notification);
                $("#formAddToInventory").trigger("reset");
            }
        })
    }

/*renders*/
    function renderUserData(user) {    
        $(".spanUserName").text(user.firstname + " " + user.lastname);
        $(".spanUserID").text(SETTINGS.USER_ID); 
    }
    function renderNotifications(notifications) {
        $("#notificationsList").empty();
        var newNotifications = notifications.filter(function (notification) {
            return notification.isNew == '1';
        });
        $.map(notifications, function (notification) {
            renderNotificationRow(notification);
        })    
        setNotificationBadger(newNotifications.length);
    }
    function renderSystemData(system) {
        renderBrandsDatalist(system.brands);
        renderCategoriesDatalist(system.categories);
        renderCapacityUnitsDatalist(system.capacity_units);
    }

/*initialization*/
function syncApp(callback) {
    var $sync = $("#sync");
    $sync.find("svg").addClass("fa-spin");

    $.ajax({
        type: "GET",
        data: { user_id: SETTINGS.USER_ID },
        url: "server/get/getInitialData.php",
        success: function (json) {            
            console.log(json);            
            setTimeout(function () {
                $sync.find("svg").removeClass("fa-spin");
            }, 1000);

            /*mainLists data*/
                renderMainLists(json.mainLists);
            /*user data*/
                renderUserData(json.user);                
            /*system data*/
                renderSystemData(json.system);
            /*notifications*/
                renderNotifications(json.notifications);
            
            if (callback != undefined)
                callback();
        }
    });
}
function initializeApp() { 
    $(".page[data-page=land]").show();
    SETTINGS = { //global data
        USER_ID: $("#hdnSettings").attr("data-user_id"),
        CURR_PAGE: 'mainLists',
        LAST_PAGE: '',
        isInProfile: false,
        LAND_TIMEOUT: 2000
    }    
    var callback = function () {
        setTimeout(function () {
            switchPage(SETTINGS.CURR_PAGE);
        }, SETTINGS.LAND_TIMEOUT);        
    }

    syncApp(callback);    
}
$("document").ready(function () {            
    initializeApp();

    $("body").on("click", ".switchPage", switchPage);
    $(".toggleMenu").click(function () { switchPage("profile"); });    

    /*mainLists*/
        $(".page[data-page='mainLists']").on("click", ".main-list", mainListClick);        

    /*addMainList*/
        $(".page[data-page='addMainList'] #btnNewListSharePlus").click(newList_addShareMember);
        $(".page[data-page='addMainList'] #formAddMainList").on("click", ".btnNewListShareMinus", newList_removeShareMember);        
        $(".page[data-page='addMainList'] #formAddMainList").submit(addMainList);

    /*mainListItems*/        
        $(".page[data-page='mainListItems']").on("click", ".item", checkItem);        

    /*manuallyAddItem*/
        $(".page[data-page='addManualItem'] #formAddManualItem").submit(addManualItem);    

    /*profile*/
        $("#sync").click(function () { syncApp(); });

    /*notificationsList*/
        $(".page[data-page='notificationsList'] #notificationsList").on("click", ".notificationRow", function () { showSpecificNotification($(this).data("key")); });    

    /*addToInventory*/
        $("input[list=datalist_categories]").focusout(sortBrandsDatalistByCategory); //datalist will show only relevant brands by selected category
        $("#formAddToInventory").submit(addToInventoryGo);
                
})


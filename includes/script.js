jQuery.fn.exists = function () { return this.length > 0; }
jQuery.fn.filterByData = function (field, val) {
    return $(this).filter(function () {
        return ($(this).data("key")[field] == val)
    });
}

function switchPage(page) {
    var targetPage;
    if ($.type(page) == 'string')
        targetPage = page;
    else
        targetPage = $(this).attr("data-target");

    var currPage = $(".page:visible").attr("data-page");
    
    if (targetPage == 'profile' && currPage == 'profile') //toggle profile up & down by clicking footer
        targetPage = lastPage;
    else if (targetPage != currPage)
        lastPage = $(".page:visible").attr("data-page");

    var effect = $(".page[data-page='" + targetPage + "']").attr("data-effect");
    if (effect === undefined)
        effect = 'fade';

    navigation(targetPage);
    $(".page:visible").hide();
    $(".page[data-page='" + targetPage + "']").show(effect, '', 500);       
}    
function goBack() {
    switchPage(lastPage);
}
function navigation(page) {
    if (page == 'profile') {
        $("#logoRow").show();
        $("#profileRow").hide();        
    } else {
        $("#logoRow").hide();
        $("#profileRow").show();        
    }
}

/*mainLists*/
    function renderMainListItem(mainListObj) {
        var $mainListItem = $("<div class='row main-list'></div>");
        $mainListItem.data('key', mainListObj);
        $mainListItem.append("<div class='col-6 text-right'>" + mainListObj.listName + "</div>");
        $mainListItem.append("<div class='col-4'><span class='quantity'>" + mainListObj.quantity + "</span> מוצרים</div>");
        $mainListItem.append("<div class='col-2 text-left'><span class='fas fa-angle-left'></span></div>");
        $("#mainLists").append($mainListItem);
        console.log(mainListObj);        
    }

    function loadMainListsFromDB() {
        $("#mainLists").empty();
        $.ajax({
            url: "server/get/getLists.php?user_id=" + USER_ID,
            success: function (json) {
                console.log(json);
                $.map(json, function (list) {
                    renderMainListItem(list);
                })        
            }
        })        
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
                switchPage("mainListItems");        
            }
        })               
    }
    function addMainList(event) {
        event.preventDefault();
        var listName = $("input[name='txtAddMainListName']").val();
        var formData = $("#formAddMainList").serialize() + "&user_id=" + USER_ID;

        $(".highlightBorder").removeClass("highlightBorder");
        $.ajax({
            type: "GET",
            url: "server/set/insertList.php",
            data: formData,
            success: function (newID) {                
                if (~newID.indexOf("error")) {
                    alert(newID);
                    alert("אירעה שגיאה. בדוק חיבור לרשת ונסה שנית");
                    return false;
                } else if (~newID.indexOf("notExists")) {
                    var badID = newID.split(":")[1];
                    $("input[name='txtAddMainListMember[]']").filter(function () {
                        return ($(this).val() == badID)
                    }).addClass("highlightBorder");
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
    
$("document").ready(function () {
    USER_ID = 1;
    lastPage = '';     

    $(".goBack").click(goBack);
    $(".switchPage").click(switchPage);
    $(".toggleMenu").click(function () { switchPage("profile"); });
    /*initial functions*/
    loadMainListsFromDB();    

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
    
})


/*JSONS DATA FOR EXAMPLE*/
    function listItemsDataExample() {
        return {            
            subLists: [
                {
                    subListID: '1',
                    subListName: 'מטבח',
                    items: [
                        {
                            id: '1',
                            name: 'חלב',
                            category: '1'
                        },
                        {
                            id: '2',
                            name: 'ביצים',
                            category: '1'
                        },
                        {
                            id: '3',
                            name: 'שמן קנולה',
                            category: '1'
                        },
                    ]
                },
                {
                    subListID: '2',
                    subListName: 'שירותים ואמבטיה',
                    items: [
                        {
                            id: '4',
                            name: 'שמפו - פינוק - לשיער רגיל',
                            category: '2'
                        },
                        {
                            id: '5',
                            name: 'מרכך שיער - פינוק',
                            category: '2'
                        },
                        {
                            id: '6',
                            name: 'סבון גוף - dove',
                            category: '2'
                        },
                        {
                            id: '7',
                            name: 'נייר טואלט - האגיס',
                            category: '2'
                        },
                        {
                            id: '4',
                            name: 'שמפו - פינוק - לשיער רגיל',
                            category: '2'
                        },
                        {
                            id: '5',
                            name: 'מרכך שיער - פינוק',
                            category: '2'
                        },
                        {
                            id: '6',
                            name: 'סבון גוף - dove',
                            category: '2'
                        },
                        {
                            id: '7',
                            name: 'נייר טואלט - האגיס',
                            category: '2'
                        }
                    ]
                },
                {
                    subListID: '3',
                    subListName: 'ציוד משרדי',
                    items: [
                        {
                            id: '8',
                            name: 'עט ירוק',
                            quantity: '5',
                            category: '3'
                        },
                        {
                            id: '9',
                            name: 'עט כחול',
                            quantity: 2,
                            category: '3'
                        },
                        {
                            id: '10',
                            name: 'מהדק',
                            category: '3'
                        },
                        {
                            id: '11',
                            name: 'מספריים',
                            category: '3'
                        },
                        {
                            id: '12',
                            name: 'מחדד',
                            category: '3'
                        },
                        {
                            id: '8',
                            name: 'עט ירוק',
                            quantity: '5',
                            category: '3'
                        },
                        {
                            id: '9',
                            name: 'עט כחול',
                            quantity: 2,
                            category: '3'
                        },
                        {
                            id: '10',
                            name: 'מהדק',
                            category: '3'
                        },
                        {
                            id: '11',
                            name: 'מספריים',
                            category: '3'
                        },
                        {
                            id: '12',
                            name: 'מחדד',
                            category: '3'
                        }
                    ]
                }
            ]
        }
    }

    function mainListsDataExample() {
        return {  
            mainLists: [
                {
                    listID: '1',
                    listName: 'עדיאל בית',
                    quantity: 3
                },
                {
                    listID: '2',
                    listName: 'עדיאל משרד',
                    quantity: 5
                },
                {
                    listID: '3',
                    listName: 'תומר גינה',
                    quantity: 8
                }
            ]            
        }
    }
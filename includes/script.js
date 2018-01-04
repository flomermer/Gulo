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

    $(".page:visible").hide();
    
    $(".page[data-page='" + targetPage + "']").show(effect,'',500);
}    
function goBack() {
    switchPage(lastPage);
}

/*mainLists*/
    function createMainListItem(mainListObj) {
        var $mainListItem = $("<div class='row main-list'></div>");
        $mainListItem.data('key', mainListObj);
        $mainListItem.append("<div class='col-6 text-right'>" + mainListObj.listName + "</div>");
        $mainListItem.append("<div class='col-4'>" + mainListObj.quantity + " מוצרים</div>");
        $mainListItem.append("<div class='col-2 text-left'><span class='fas fa-angle-left'></span></div>");
        $("#mainLists").append($mainListItem);
        console.log(mainListObj);
    }

    function loadMainListsFromDB(data) {        
        $.map(data.mainLists, function (mainList) {
            createMainListItem(mainList);
        })        
    }
    

/*addMainList*/
    function newList_addShareMember() {                        
        var $newMemberRow = $("<div class='form-group row text-right rowNewMember'></div>");                
        $newMemberRow.append('<div class="col-10"><input type="text" class="form-control" name="txtAddMainListMember" placeholder="מספר מזהה"></div>');                
        $newMemberRow.append('<div class="col-2"><span class="btnNewListShareMinus"><i class="fas fa-minus"></i></span></div>');

        $(".rowShareMemeber").after($newMemberRow);        
    }
    function newList_removeShareMember() {
        var $rowMember = $(this).closest(".rowNewMember");
        $rowMember.remove();
    }
    function mainListClick() {
        var list = $(this).data('key');

        /*
            1. ajax request to get list items from db
            2. load response into mainListItems container
        */
        $("#mainListName").text(list.listName);
        renderListItems(listItemsDataExample());
        switchPage("mainListItems");        
    }
    function addMainList(event) {
        event.preventDefault();

        /*ajax call to set new data will return newID*/
        var newID = 100; //newID will be the response from the ajax call
        createMainListItem({
            listID: 30,
            listName: $("#txtAddMainListName").val(),
            quantity: 0
        })

        $("#formAddMainList").trigger("reset");
        $(".rowNewMember").remove();
        switchPage("mainLists");
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
        var $subUL = $('<ul class="ul-main-list-category collapse" data-cat="' + subList.subListID + '"></ul>');

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
    function renderItemLI(ul, item) {
        var quantity = '';
        if (item.quantity !== undefined && item.quantity!='1')
            quantity = item.quantity + ' '

        var $newLI = $('<li class="item" data-id="' + item.id + '">' + quantity + item.name + '</li>');
        $newLI.data('key', item);

        ul.append($newLI);
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
    function addListToCart() {
        switchPage("addToCartConfirmation");
    }
    function checkItem() {
        var item = $(this).data('key');
        
        $(this).toggleClass("item-checked");

        if ($(this).hasClass("item-checked"))
            decreaseSublistQuantity(item.category);
        else
            increaseSublistQuantity(item.category);
    }    

/*manuallyAddItem*/
    function addManualItem(event) {
        event.preventDefault();

        var itemName = $("#txtAddListItemName").val();
        var itemQuantity = $("#txtAddListItemQuantity").val();
        
        /*
            ajax request to set new item
            response will get newItem {id,category,name}
        */

        var newItem = {
            id: '100',
            category: '1',
            name: itemName,
            quantity: itemQuantity
        }

        /*for now, we'll add it to dom manually. later, it will set the state of the data and then reRender*/
        var $theUL = $(".ul-main-list-category[data-cat=" + newItem.category + "]");
        renderItemLI($theUL, newItem);        
        switchPage("mainListItems");

        $("#formAddManualItem").trigger("reset");
    }
    
$("document").ready(function () {
    lastPage = '';

    $(".goBack").click(goBack);
    $(".switchPage").click(switchPage);    

    /*initial functions*/
    loadMainListsFromDB(mainListsDataExample());    

    /*mainLists*/
        $(".page[data-page='mainLists']").on("click", ".main-list", mainListClick);        


    /*addMainList*/
        $(".page[data-page='addMainList'] #btnNewListSharePlus").click(newList_addShareMember);
        $(".page[data-page='addMainList'] #formAddMainList").on("click", ".btnNewListShareMinus", newList_removeShareMember);        
        $(".page[data-page='addMainList'] #formAddMainList").submit(addMainList);

    /*mainListItems*/
        $(".page[data-page='mainListItems'] .listOptions[data-option='toggleListView']").click(toggleListView);
        $(".page[data-page='mainListItems'] .listOptions[data-option='shareList']").click(shareList);
        $(".page[data-page='mainListItems'] .listOptions[data-option='addToCart']").click(addListToCart);
        $(".page[data-page='mainListItems']").on("click", ".item", checkItem);        

    /*manuallyAddItem*/
        $(".page[data-page='addManualItem'] #formAddManualItem").submit(addManualItem);
})


/*JSONS DATA FOR EXAMPLE*/
    function listItemsDataExample() {
        return {
            mainListID: '1',
            listName: 'עדיאל בית',
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
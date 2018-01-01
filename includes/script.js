/*mainLists*/
    function createMainListItem(mainListObj) {
        var $mainListItem = $("<div class='row main-list'></div>");
        $mainListItem.data('key', mainListObj);
        $mainListItem.append("<div class='col-6 text-right'>" + mainListObj.listName + "</div>");
        $mainListItem.append("<div class='col-4'>" + mainListObj.quantity + " מוצרים</div>");
        $mainListItem.append("<div class='col-2 text-left'><span class='fas fa-angle-left'></span></div>");
        $("main .container").append($mainListItem);
        console.log(mainListObj);
    }

    function loadMainListsFromDB() {
        var listItems = [];
        var list_id = 1;

        listItems.push({
            listID: list_id++,
            listName: 'עדיאל בית',
            quantity: 3
        });
        listItems.push({
            listID: list_id++,
            listName: 'עדיאל משרד',
            quantity: 10
        });
        listItems.push({
            listID: list_id++,
            listName: 'תומר גינה',
            quantity: 2
        });

        $.each(listItems, function (index, item) {
            createMainListItem(item);
        });
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
        $("#formShowListItems input[name='listID']").val(list.listID);
        $("#formShowListItems").submit();            
    }

/*mainListItems*/
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
    function shareList(){
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
        alert("list was added to cart...");
    }
    function itemClick() {
        $(this).toggleClass("item-checked");
    }    

$("document").ready(function () {
    var $page = $("body").attr("id");

    if ($page == "pageMainLists") {
        loadMainListsFromDB();            
        $("main").on("click", ".main-list", mainListClick);
    } 
    else if ($page == "pageAddMainList") {
        $("#btnNewListSharePlus").click(newList_addShareMember);
        $("#formAddMainList").on("click", ".btnNewListShareMinus", newList_removeShareMember);        
    }
    else if ($page == "pageMainListItems") {
        $(".listOptions[data-option='toggleListView']").click(toggleListView);   
        $(".listOptions[data-option='shareList']").click(shareList);   
        $(".listOptions[data-option='addToCart']").click(addListToCart);   
        $("ul.ul-main-list-category li").click(itemClick);
    }    
})
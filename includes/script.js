/*mainLists*/
    function createMainListItem(mainListObj) {
        var $mainListItem = $("<div class='row main-list'></div>");
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

$("document").ready(function () {
    var $page = $("body").attr("id");

    if ($page == "pageMainLists") {
        loadMainListsFromDB();            
    } 
    else if ($page == "pageAddMainList") {
        $("#btnNewListSharePlus").click(newList_addShareMember);
        $("#formAddMainList").on("click", ".btnNewListShareMinus", newList_removeShareMember);
    }
})
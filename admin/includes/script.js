function inventoryPage() {    
    function loadTable() {
        $.ajax({
            type: "post",
            dataType: "json",
            url: "server/get/getInventory.php",
            success: function (json) {
                console.log(json);
                $table.bootstrapTable({
                    data: json.inventory,
                    pagination: true,
                    pageSize: 10,
                    onClickRow: function (row, $el, field) {
                        editRowModal(row, field);
                    }
                });
                renderDataLists(json.system);
            }
        })    
    }
    function loadDataLists() {
        $.ajax({
            type: "post",
            dataType: "json",
            url: "server/get/getDataLists.php",
            success: function (json) {
                $("#datalist_categories, #datalist_brands, #datalist_capacity_units").empty();
                renderDataLists(json.system);                
            }
        })    
    }
    function renderDataLists(data) {
        var $option;
        $.map(data.categories, function (category) {
            $option = $("<option value='" + category.category_name + "'></option>");            
            $option.data("key", category);
            $("#datalist_categories").append($option);
        })    
        $.map(data.brands, function (brand) {
            $option = $("<option value='" + brand.brand_name + "'></option>");
            $option.data("key", brand);
            $option.attr("data-categories", "[" + brand.brand_categories + "]");
            $("#datalist_brands").append($option);
        })    
        $.map(data.capacity_units, function (unit) {
            $option = $("<option value='" + unit.symbol + "'></option>");
            $option.data("key", unit);
            $("#datalist_capacity_units").append($option);
        })            
    }  
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
    function addRowModal() {        
        $("#modalRow .modal-header label").text("הוספת מוצר");
        $("#modalRow .btn-submit").text("הוספה");
        $("#formRow input[name='hdnRowID']").val("");        
        $("#formRow input[name='barcode']").attr("readonly", false);

        $("#formRow").trigger("reset");

        $("#modalRow").modal("show");
    }
    function editRowModal(row,field) {
        $("#formRow input[name='hdnRowID']").val(row.rowID);        
        console.log(row);
        $("#formRow input[name='barcode']").val(row.rowID).attr("readonly",true);
        $("#formRow input[name='name']").val(row.name);
        $("#formRow input[name='category']").val(row.category_name);
        $("#formRow input[name='brand']").val(row.brand_name);      
        $("#formRow input[name='capacity']").val(row.capacity);
        $("#formRow input[name='capacity_units']").val(row.capacity_unit_symbol);

        $("#modalRow .modal-header label").text("עריכת מוצר");
        $("#modalRow .btn-submit").text("עריכה");        

        $("#modalRow").modal("show");
    }
    function formSubmit(event) {
        event.preventDefault();
        
        var category, category_id, brand, brand_id, capacity_unit, capacity_unit_id;

        category = $("#datalist_categories").find("option[value='" + $("#formRow input[name=category]").val() + "']").data("key");
        if (category != undefined)
            category_id = category.category_id;
        else
            category_id = null;

        brand = $("#datalist_brands").find("option[value='" + $("#formRow input[name=brand]").val() + "']").data("key");
        if (brand != undefined)
            brand_id = brand.brand_id;
        else
            brand_id = null;

        capacity_unit = $("#datalist_capacity_units").find("option[value='" + $("#formRow input[name=capacity_units]").val() + "']").data("key");
        if (capacity_unit != undefined)
            capacity_unit_id = capacity_unit.id;
        else
            capacity_unit_id = null;
       
        var formData = $("#formRow").serialize() + "&category_id=" + category_id + "&brand_id=" + brand_id + "&capacity_unit_id=" + capacity_unit_id;
        
        $.ajax({
            type: "GET",
            data: formData,
            url: "server/set/editProduct.php",
            success: function (json) {                
                if (json.error == 'alreadyExists') {
                    alert("ברקוד כבר קיים במערכת");
                    return false;
                }
                console.log(json);

                if (json.isNew == '1') {
                    $table.bootstrapTable("prepend", json);                    
                } else {
                    $table.bootstrapTable('updateByUniqueId', {
                        id: json.rowID,
                        row: json
                    });
                }                
                $("#modalRow").modal("hide");                    
                loadDataLists();
            }
        })
    }    
    function deleteRow() {
        var tableName = $(this).attr("data-table");
        var fieldName = $(this).attr("data-field");       
        var rowID = $("#formRow input[name='hdnRowID']").val();
       
        var j = confirm("האם למחוק רשומה זו?");
        if (j == false) return false;

        $.ajax({
            url: "server/set/deleteDynamicRow.php?tableName=" + tableName + "&fieldName=" + fieldName + "&fieldVal=" + rowID,
            success: function (data) {
                if (~data.indexOf("error")) {
                    alert("לא ניתן למחוק רשומה זו");
                    return false;
                }
                $table.bootstrapTable('removeByUniqueId', rowID);
                $("#modalRow").modal("hide");                
            }
        })
    }


    /*main*/
    var $table = $("#tableInventory");
    $("input[list=datalist_categories]").focusout(sortBrandsDatalistByCategory); //datalist will show only relevant brands by selected category
    $("#btnAddRow").click(addRowModal);    
    $("#btnDeleteRow").click(deleteRow);
    $("#formRow").on("submit", formSubmit);
    loadTable();    
}

$("document").ready(function () {
    var page = $("body").attr("data-page");            

    if (page == "inventory")
        inventoryPage();
})
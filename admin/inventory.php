<!DOCTYPE html>
<html>
    <head>        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Gulo Admin</title>
        <!--
         <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>    
        -->

        <!-- Jquery CDN-->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>        

        <!-- Bootstrap CSS -->                
        <script src="includes/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="includes/bootstrap/3.3.5/css/bootstrap.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.1/bootstrap-table.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.1/bootstrap-table.css" />        
        
        <script defer src="https://use.fontawesome.com/releases/v5.0.2/js/all.js"></script>                
                       
        <link rel="stylesheet" href="includes/style.css" />
        <script src="includes/script.js"></script>        
    </head>
    <body data-page="inventory">        
        <header class="mainMenu">
            <?php include('menu.html');?>
        </header>        
        <main class="container">            
            <div id="tableToolbar">
                <div class="form-inline" role="form">
                    <div class="form-group">
                        <button class="btn btn-default" id="btnAddRow">
                            <i class="fas fa-plus-circle"></i> מוצר חדש
                        </button>
                    </div>                    
                </div>
            </div>    
            
            <table class="table table-bordered table-responsive table-hover tableDB" id="tableInventory"
                data-unique-id="rowID" data-search="true"
                data-toolbar="#tableToolbar">
                <caption class="text-center">מאגר מוצרים</caption>
                <thead>
                    <tr>
                        <th class="col-xs-2 text-center" data-field="rowID" data-sortable="true">ברקוד</th>
                        <th class="col-xs-4 text-center" data-field="name" data-sortable="true">שם מוצר</th>
                        <th class="col-xs-2 text-center" data-field="category_name" data-sortable="true">קטגוריה</th>
                        <th class="col-xs-2 text-center" data-field="brand_name" data-sortable="true">מותג</th>
                        <th class="col-xs-2 text-center" data-field="capacityStr" data-sortable="true">קיבולת</th>                            
                        <th data-field="category_id" data-visible="false"></th>
                        <th data-field="brand_id" data-visible="false"></th>
                        <th data-field="capacity" data-visible="false"></th>
                        <th data-field="capacity_unit_id" data-visible="false"></th>
                        <th data-field="capacity_unit_symbol" data-visible="false"></th>
                    </tr>                        
                </thead>
                <tbody>
                    <!--AJAX CONTENT-->
                </tbody>
            </table>                  
        </main>
        
        <datalist id="datalist_categories" dir="rtl"></datalist>
        <datalist id="datalist_brands" dir="rtl"></datalist>
        <datalist id="datalist_capacity_units" dir="rtl"></datalist>
      
        <div class="modal fade" id="modalRow" style="direction:rtl;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        &nbsp;&nbsp;
                        <label></label>
                        <button id="btnDeleteRow" data-table="238_products" data-field="barcode" class="btn btn-xs btn-danger" style="float:left">
                            <i class="fas fa-minus-circle"></i>הסרה
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="content">
                            <form class="form" id="formRow" method="post">
                                <input type="hidden" name="hdnRowID" /><!-- add/edit -->
                                <div class="form-group">
                                    <label>ברקוד:</label>
                                    <input type="number" class="form-control" name="barcode" placeholder="87654321" required />
                                </div>
                                <div class="form-group">
                                    <label>שם המוצר:</label>
                                    <input type="text" class="form-control" name="name" placeholder="למשל: שמפו" required />
                                </div>
                                <div class="form-group">
                                    <label>קטגוריה:</label>
                                    <input type="text" class="form-control" list="datalist_categories" name="category" placeholder="למשל: שירותים ואמבטיה" required />
                                </div>
                                <div class="form-group">
                                    <label>מותג:</label>
                                    <input type="text" class="form-control" list="datalist_brands" name="brand" placeholder="למשל: פינוק" required />
                                </div>
                                <div class="form-group row">
                                    <div class="col-xs-1">
                                        <label>קיבולת:</label>
                                    </div>
                                    <div class="col-xs-2">
                                        <input type="number" class="form-control text-center" name="capacity" placeholder="750" min="0" step="0.01" />
                                    </div>
                                    <div class="col-xs-3">
                                        <input type="text" class="form-control text-center" list="datalist_capacity_units" name="capacity_units" placeholder='מ"ל'/>
                                    </div>
                                </div>                                                         
                                <div class="form-group text-center">
                                    <button class="btn btn-success btn-submit"></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </body>    
</html>

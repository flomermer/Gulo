<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Gulo - Demo Scan</title>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>    
    <script defer src="https://use.fontawesome.com/releases/v5.0.2/js/all.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
    <link rel="stylesheet" href="includes/style.css" />        
    <?php
    include('server/dbDetails.php');

    $conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->query("SET NAMES 'utf8'");


    $sql = "SELECT * FROM 238_lists ORDER BY list_id ASC";
    $result = $conn->query($sql);

    while($rs = $result->fetch_assoc()) {
        $list_id = $rs['list_id'];
        $list_name = $rs['list_name'];
        $datalist_lists .= "<option value='$list_id'>$list_name</option>";
    }

    $sql = "
            SELECT products.*, brands.brand_name,
                   CONCAT(products.capacity,' ',units.unit_symbol) As capacityStr, units.unit_symbol AS capacity_unit_symbol
            FROM 238_products products
            LEFT JOIN 238_products_brands brands ON products.brand_id=brands.brand_id
            LEFT JOIN 238_capacity_units units ON products.capacity_unit_id=units.unit_id
            ";
    $result = $conn->query($sql);
    while($rs = $result->fetch_assoc()) {
        $barcode = $rs['barcode'];
        $product_name = $rs['product_name'];
        $brand = $rs['brand_name'];
        $datalist_products .= "<option value='$barcode'>$product_name $brand</option>";
    }
    ?>

    <script>
         function formSend(event) {
             event.preventDefault();
             var list_id, barcode;
             list_id = $("input[name=list_id]").val();
             barcode = $("input[name=barcode]").val();
             var formData = {
                 list_id: list_id,
                 barcode: barcode
             }             
             $(".alert").removeClass("alert-success").removeClass("alert-danger").html('<i class="fas fa-spinner fa-spin"></i>').css("visibility", "visible");

             $.post('server/set/scanBarcode.php', formData, function (data) {
                 console.log(data);
                 if (data == 'success') { //חיישן זיהה את הברקוד במערכת גולו
                     $(".alert").text("מוצר נוסף בהצלחה לרשימה").addClass("alert-success").css("visibility", "visible");
                 } else if (data == 'mailSent') {
                     $(".alert").html("ברקוד לא קיים במערכת<BR>נשלח מייל למשתמש").addClass("alert-danger").css("visibility", "visible");
                 } else if (data == 'mailNotSent' || data=='notFound') {
                     $(".alert").html("ברקוד לא קיים במערכת").addClass("alert-danger").css("visibility", "visible");
                 } else if (data == 'noList') {
                     $(".alert").html("רשימה אינה קיימת במערכת").addClass("alert-danger").css("visibility", "visible");
                 } else {
                     $(".alert").css("visibility", "hidden");
                 } 
             });             
         }
         $("document").ready(function () {
             $("#formScanDemo").on("submit", formSend);
         })
    </script>

</head>
<body id="scanDemo">    
    <main>        
        <h1>הדמיית סריקה:</h1>
        <form id="formScanDemo" method="post" action="server/set/scanBarcode.php">
            <div class="alert text-center">
                מוצר נוסף בהצלחה לרשימה
            </div>
            <div class="form-group row">
                <lable class="col-4">ברקוד:</lable>
                <input type="text" class="form-control col-8" list="barcode" name="barcode" placeholder="מספר ברקוד..." required />
            </div>            
            <div class="form-group row">
                <label class="col-4 text-right">רשימה:</label>
                <input type="text" class="form-control col-8" list="lists"  name="list_id" placeholder="מספר רשימה..." required />
            </div>            
            <div class="form-group row">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-lg">
                        <i class="fas fa-barcode"></i> סריקה
                    </button>
                </div>
            </div>
        </form>
        <datalist id="barcode">
            <?php echo $datalist_products?>
        </datalist>
        <datalist id="lists">
            <?php echo $datalist_lists?>
        </datalist>
    </main>    
</body>
</html>
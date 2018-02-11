<?php include('../dbDetails.php'); ?>
<?php
$user_id            =   $_GET["user_id"];
$notifiaction_id    =   $_GET["notification_id"];
$list_id            =   $_GET["list_id"];

$barcode            =   $_GET["productBarcode"];
$category_id        =   $_GET["category_id"];
$category_name      =   $_GET["productCategory"];
$brand_id           =   $_GET["brand_id"];
$brand_name         =   $_GET["productBrand"];
$capacity           =   $_GET["productCapacity"];
$capacity_unit_id   =   $_GET["capacity_unit_id"];
$capacity_unit_name =   $_GET["productCapacityUnits"];
$product_name       =   $_GET["productName"];

if($capacity=='')
    $capacity='NULL';

//echo "$category_id - $category_name --- $brand_id - $brand_name --- $capacity_unit_id - $capacity_unit_name ";

$conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("SET NAMES 'utf8'");

/*add to list_manual_products in waiting mode*/
    $productFullName = "$product_name - $brand_name";
    if($capacity>0)
        $productFullName .= " - $capacity";
    if($capacity_unit_name!='')
        $productFullName .= " $capacity_unit_name";
    
    $sql = "INSERT INTO 238_list_manual_products (list_id,product_name,quantity,isWaiting)
                VALUES ($list_id, '$productFullName', 1, 1)";
    $conn->query($sql);


/*add new product to products_to_confirm*/
    $sql =  "INSERT INTO 238_products_to_confirm
                 (barcode,product_name,user_id,category_id,category_name,brand_id,brand_name,capacity,capacity_unit_id,capacity_unit_name)
                  VALUES
                 ($barcode,'$product_name',$user_id,$category_id,'$category_name',$brand_id,'$brand_name',$capacity,$capacity_unit_id,'$capacity_unit_name')";
    $conn->query($sql);
    echo $conn->error;


/*set notification isChecked=1*/
    if($notifiaction_id>0){
        $notification_id = (int)$notifiaction_id;
        $sql = "UPDATE 238_notifications SET isChecked=1 WHERE id=$notification_id";
        $conn->query($sql);
    }


$conn->close();
?>
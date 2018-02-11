<?php include('../dbDetails.php'); ?>
<?php
$item_id    =  $_GET["item_id"];
$category   = $_GET["category"];
$isChecked  = $_GET["isChecked"];

$conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if($category==-1) //manually item -> other table
    $tableName = "238_list_manual_products";
else  //product item
    $tableName = "238_list_products";

$sql = "UPDATE $tableName
        SET isChecked=$isChecked
        WHERE id=$item_id
        ";

$conn->query($sql);

$conn->close();
?>
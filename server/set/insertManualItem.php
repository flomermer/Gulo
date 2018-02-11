<?php include('../dbDetails.php'); ?>
<?php
header('Content-Type: application/json');

$list_id = $_POST["list_id"];
$itemName = $_POST["txtAddListItemName"];
$quantity = $_POST["txtAddListItemQuantity"];

$conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("SET NAMES 'utf8'");

$sql = "INSERT INTO 238_list_manual_products (list_id,product_name,quantity)
        VALUES ($list_id, '$itemName', $quantity)";

$conn->query($sql);
$new_id = $conn->insert_id;

$newItem->id = $new_id;
$newItem->category = '-1';
$newItem->name = $itemName;
$newItem->quantity = $quantity;
$json = json_encode($newItem);

echo $json;

$conn->close();
?>
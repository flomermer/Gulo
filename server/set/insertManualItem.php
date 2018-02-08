<?php
header('Content-Type: application/json');

$list_id = $_POST["list_id"];
$itemName = $_POST["txtAddListItemName"];
$quantity = $_POST["txtAddListItemQuantity"];

//$conn = new mysqli("182.50.133.55","auxstudDB7c","auxstud7cDB1!","auxstudDB7c");
$conn = new mysqli("localhost","mysql_montv","dinoflom","projectDB");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
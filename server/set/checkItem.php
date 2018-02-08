<?php
$item_id    =  $_GET["item_id"];
$category   = $_GET["category"];
$isChecked  = $_GET["isChecked"];

//$conn = new mysqli("182.50.133.55","auxstudDB7c","auxstud7cDB1!","auxstudDB7c");
$conn = new mysqli("localhost","mysql_montv","dinoflom","projectDB");

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
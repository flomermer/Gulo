<?php include('../dbDetails.php'); ?>
<?php
$tableName = $_GET["tableName"];
$field = $_GET["field"];
$val = $_GET["val"];
$whereField = $_GET["whereField"];
$whereVal = $_GET["whereVal"];


$conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "UPDATE $tableName SET $field=$val WHERE $whereField=$whereVal";
echo $sql;
$conn->query($sql);

$conn->close();
?>
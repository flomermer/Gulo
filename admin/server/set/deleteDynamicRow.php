<?php include('../../../server/dbDetails.php'); ?>
<?php

$conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tableName = $_GET["tableName"];
$fieldName = $_GET["fieldName"];
$fieldVal  = $_GET["fieldVal"];

$sql = "DELETE FROM $tableName WHERE $fieldName=$fieldVal";
if($conn->query($sql)==FALSE)
    die("error. cannot delete entity");

$conn->close();
?>

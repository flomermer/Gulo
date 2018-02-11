<?php include('../dbDetails.php'); ?>
<?php
header('Content-Type: application/json');

$user_id = $_GET["user_id"];

$conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("SET NAMES 'utf8'");

$sql = "
        SELECT  lists.list_id, lists.list_name,
        (SELECT COUNT(*) FROM 238_list_products products WHERE products.list_id=lists.list_id AND products.isChecked=0) as quantityProducts,
        (SELECT COUNT(*) FROM 238_list_manual_products manual WHERE manual.list_id=lists.list_id AND manual.isChecked=0) as quantityManual
        FROM 238_lists lists
        WHERE lists.user_id=$user_id
        UNION
        SELECT  lists.list_id, lists.list_name,
        (SELECT COUNT(*) FROM 238_list_products products WHERE products.list_id=lists.list_id AND products.isChecked=0) as quantityProducts,
        (SELECT COUNT(*) FROM 238_list_manual_products manual WHERE manual.list_id=lists.list_id AND manual.isChecked=0) as quantityManual
        FROM 238_lists_shares shares
        LEFT JOIN 238_lists lists ON lists.list_id = shares.list_id
        WHERE shares.user_id=$user_id
        ";
$result = $conn->query($sql);

$json=array();
if ($result->num_rows > 0) {
    while($rs = $result->fetch_assoc()) {
        $json[] = array(
            'listID'    =>      $rs['list_id'],
            'listName'  =>      $rs['list_name'],
            'quantity'  =>      $rs['quantityProducts'] + $rs['quantityManual']
        );
    }
}

$json = json_encode($json);
echo $json;

$conn->close();
?>
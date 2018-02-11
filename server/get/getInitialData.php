<?php include('../dbDetails.php'); ?>
<?php
header('Content-Type: application/json');

$user_id = $_GET["user_id"];

$conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("SET NAMES 'utf8'");

/*MAINLISTS DATA*/
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

$json->mainLists=array();
if ($result->num_rows > 0) {
    while($rs = $result->fetch_assoc()) {
        $json->mainLists[] = array(
            'listID'    =>      $rs['list_id'],
            'listName'  =>      $rs['list_name'],
            'quantity'  =>      $rs['quantityProducts'] + $rs['quantityManual']
        );
    }
}


/*USER DATA*/
$sql = "SELECT  users.*,
        (SELECT COUNT(*) FROM 238_notifications noti WHERE noti.user_id=$user_id AND isNew=1) as newNotificationsCounter
        FROM 238_users users
        WHERE users.user_id=$user_id
        ";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $rs = $result->fetch_assoc();
    $json->user->userID = $rs['user_id'];
    $json->user->mail = $rs['mail'];
    $json->user->firstname = $rs['firstname'];
    $json->user->lastname = $rs['lastname'];
    $json->user->newNotificationsCounter = $rs['newNotificationsCounter'];
}

/*SYSTEM DATA*/
$json->system->categories = array();
$sql = "SELECT * FROM 238_products_categories";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($rs = $result->fetch_assoc()) {
        $json->system->categories[] = array(
            'category_id'        =>      $rs['category_id'],
            'category_name'      =>      $rs['category_name']
        );
    }
}

$json->system->brands = array();
$sql = "
        SELECT brands.brand_id, brands.brand_name,
               GROUP_CONCAT(bc.category_id) AS brand_categories
        FROM 238_products_brands brands
        LEFT JOIN 238_brand_categories bc ON bc.brand_id=brands.brand_id
        GROUP BY brands.brand_id
        ";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($rs = $result->fetch_assoc()) {
        $json->system->brands[] = array(
            'brand_id'          =>      $rs['brand_id'],
            'brand_name'        =>      $rs['brand_name'],
            'brand_categories'  =>      $rs['brand_categories']
        );
    }
}

$sql = "SELECT * FROM 238_capacity_units";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($rs = $result->fetch_assoc()) {
        $json->system->capacity_units[] = array(
            'id'           =>      $rs['unit_id'],
            'symbol'       =>      $rs['unit_symbol'],
            'name'         =>      $rs['unit_name']
        );
    }
}



/*Notifications*/
$sql = "
        SELECT
        notifications.*, DATE_FORMAT(datetime, '%d/%m/%y') DATEONLY, DATE_FORMAT(datetime,'%H:%i') TIMEONLY,
        types.topic
        FROM 238_notifications notifications
        LEFT JOIN 238_notifications_types types ON types.notification_type_id=notifications.notification_type_id
        WHERE user_id=$user_id
        ORDER BY notifications.datetime DESC
        ";
$result = $conn->query($sql);

$json->notifications = array();
if ($result->num_rows > 0) {
    while($rs = $result->fetch_assoc()){
        $json->notifications[] = array(
            'id'       =>   $rs['id'],
            'type_id'  =>   $rs['notification_type_id'],
            'topic'    =>   $rs['topic'],
            'isNew'    =>   $rs['isNew'],
            'time'     =>   $rs['TIMEONLY'],
            'date'     =>   $rs['DATEONLY'],
            'id_1'     =>   $rs['id_1'],
            'id_2'     =>   $rs['id_2'],
            'isChecked'=>   $rs['isChecked']
        );
    };
}


$json = json_encode($json);
echo $json;

$conn->close();
?>
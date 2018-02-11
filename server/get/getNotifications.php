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
        SELECT
        notifications.*, DATE_FORMAT(datetime, '%d/%m/%y') DATEONLY, DATE_FORMAT(datetime,'%H:%i') TIMEONLY,
        types.topic
        FROM 238_notifications notifications
        LEFT JOIN 238_notifications_types types ON types.notification_type_id=notifications.notification_type_id
        WHERE user_id=$user_id
        ";
$result = $conn->query($sql);

$json = array();
if ($result->num_rows > 0) {
    while($rs = $result->fetch_assoc()){
        $json[] = array(
            'id'       =>   $rs['id'],
            'type_id'  =>   $rs['notification_type_id'],
            'topic'    =>   $rs['topic'],
            'isNew'    =>   $rs['isNew'],
            'time'     =>   $rs['TIMEONLY'],
            'date'     =>   $rs['DATEONLY']
        );
    };
}

$json = json_encode($json);
echo $json;

$conn->close();
?>
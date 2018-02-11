<?php include('../dbDetails.php'); ?>
<?php
header('Content-Type: application/json');

$user_id = $_GET["user_id"];

$conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("SET NAMES 'utf8'");
$sql = "SELECT  users.*,
        (SELECT COUNT(*) FROM 238_notifications noti WHERE noti.user_id=$user_id AND isNew=1) as newNotificationsCounter
        FROM 238_users users
        WHERE users.user_id=$user_id
        ";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $rs = $result->fetch_assoc();
    $json->userID = $rs['user_id'];
    $json->mail = $rs['mail'];
    $json->firstname = $rs['firstname'];
    $json->lastname = $rs['lastname'];
    $json->newNotificationsCounter = $rs['newNotificationsCounter'];
}

$json = json_encode($json);
echo $json;

$conn->close();
?>
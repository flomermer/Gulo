<?php include('../dbDetails.php'); ?>
<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$list_id = $_POST["list_id"];
$barcode = $_POST["barcode"];
$conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->query("SET NAMES 'utf8'");
$sql = "SELECT list_id FROM 238_lists WHERE list_id=$list_id";
$result = $conn->query($sql);
if($result->num_rows==0){
    echo "noList";
    exit();
}
$sql = "SELECT barcode FROM 238_products WHERE barcode=$barcode LIMIT 1";
$result = $conn->query($sql);
if($result->num_rows==0){ //barcode not exists-> send mail to insert manually and add notification
    $conn->query($sql);
    echo $conn->error;
    $sql = "SELECT users.mail, users.user_id
            FROM 238_lists lists
            LEFT JOIN 238_users users ON lists.user_id=users.user_id
            WHERE list_id=$list_id
            LIMIT 1
            ";
    $result = $conn->query($sql);
    $rs = $result->fetch_assoc();
    $user_id = $rs['user_id'];
    $to = $rs['mail'];
    $sql="SELECT * FROM 238_notifications WHERE user_id=$user_id AND notification_type_id=1 AND id_1=$barcode";
    $result = $conn->query($sql);

    if($result->num_rows==0){ //first time user scan this product which not exists
        $sql = "INSERT INTO 238_notifications (user_id,notification_type_id,id_1,id_2,datetime)
                VALUES ($user_id,1,$barcode,$list_id,NOW())";
        $conn->query($sql);
    } else { //user already got notification about this scan: just flag isNew and update datetime to now
        $rs = $result->fetch_assoc();
        $notification_id = $rs['id'];

        $sql = "UPDATE 238_notifications SET isNew=1, datetime=NOW() WHERE id=$notification_id";
        $conn->query($sql);
    }
    sendMail($to);
} else {
    $sql = "SELECT barcode FROM 238_list_products WHERE list_id=$list_id AND barcode=$barcode LIMIT 1";
    $result = $conn->query($sql);
    if($result->num_rows==0){ //not exists in list. insert new product to list.
        $sql = "INSERT INTO 238_list_products (list_id,barcode,quantity)
                VALUES ($list_id,$barcode,1)";
    } else { //already exists in list. update the quantity
        $sql = "UPDATE 238_list_products
                SET quantity = quantity+1
                WHERE list_id=$list_id AND barcode=$barcode
            ";
    }
    if($conn->query($sql))
        echo "success";
}
$conn->close();
function sendMail($to){
    require '../PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->CharSet = "utf-8";
    $mail->isSMTP();                                   // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';                    // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                            // Enable SMTP authentication
    $mail->Username = 'flom.tomer@gmail.com';          // SMTP username
    $mail->Password = 'dinoflom'; // SMTP password
    $mail->SMTPSecure = 'tls';                         // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                 // TCP port to connect to
    $mail->setFrom('flom.tomer@gmail.com', 'Gulo');
    $mail->addReplyTo('flom.tomer@gmail.com', 'Gulo');
    $mail->addAddress($to);   // Add a recipient
    $mail->isHTML(true);  // Set email format to HTML
    $bodyContent = '<div style="text-align:center;direction:rtl">
                    <h2>Gulo - מוצר לא זוהה</h2>
                    <p>
                        מוצר שנסרק בשעה ' . date("H:i") . ' בתאריך ' . date("d.m.y") . '
                        <BR>
                        לא נמצא במאגר Gulo.
                        <br><br>
                        עזרו לנו ולחברי Gulo להכיר את המוצר,
                        ועל הדרך תרוויחו 10 נקודות(:
                        <BR><BR>
                        <h3><a href="127.0.0.1/gulo/index.php">היכנסו ל-Gulo</a></h3>
                    </p>
                </div>';
    $mail->Subject = 'מוצר לא זוהה. יש להיכנס למסך ההתראות';
    $mail->Body    = $bodyContent;
    if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'mailSent';
    }
}
?>
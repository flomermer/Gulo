<?php
$list_id = $_GET["list_id"];
$barcode = $_GET["barcode"];

//$conn = new mysqli("182.50.133.55","auxstudDB7c","auxstud7cDB1!","auxstudDB7c");
$conn = new mysqli("localhost","mysql_montv","dinoflom","projectDB");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT barcode FROM 238_products WHERE barcode=$barcode LIMIT 1";
$result = $conn->query($sql);

if($result->num_rows==0){ //barcode not exists-> send mail to insert manually
    $sql = "SELECT users.mail
            FROM 238_lists lists
            LEFT JOIN 238_users users ON lists.user_id=users.user_id
            WHERE list_id=$list_id
            LIMIT 1
            ";
    $result = $conn->query($sql);
    $rs = $result->fetch_assoc();

    $to = $rs['mail'];

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
        echo 'Message has been sent successfully';
    }
}

?>
<?php
// Load Composer's autoloader
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../init.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

spl_autoload_register(function ($class) {
    include '/home/u198566027/domains/app.linkboxy.com/public_html/staging/classes/' . $class . '.class.php';
});

// Instantiation and passing `true` enables exceptions
$stmtnt = $common->mysqli->prepare(
    "SELECT id,recipient_mail, sender, recipient, headers, body, status, try_sent FROM mail_queue where try_sent = 0 LIMIT 50"
);
if (!$stmtnt->execute()) {
    return false;
}
$result = $stmtnt->get_result();
$stmtnt->close();
$rslt = $result->fetch_all(MYSQLI_ASSOC);

foreach ($rslt as $email) {
    try {
        $mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = 0;                                       // Enable verbose debug output
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->CharSet = "UTF-8";
        $mail->SMTPSecure = 'tls';
        $mail->Host = 'smtp.hostinger.co.uk';
        $mail->Port = 587; //465;
        $mail->Username = 'alerts@linkboxy.com';
        $mail->Password = '#cEkocF3'; //'anatoly123upwork';
        $mail->SMTPAuth = true;

        //Recipients
        $mail->setFrom('alerts@linkboxy.com', 'LinkBoxy.com');
        $mail->addAddress($email['recipient_mail'], ''); //$email['recipient_mail']    // Add a recipient
        //$mail->addBcc('videoasistent@gmail.com');//for testing mail template - remove this on live!!!
        //$mail->addAddress('videoasistent@gmail.com', ''); //$email['recipient_mail']    // Add a recipient
        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'LinkBoxy.com Notification';
        //$template = file_get_contents('./mailTemplate.tmp');
        $mail->Body    = $email['body'];
        $mail->AltBody = substr($email['body'], 0, 100);

        $mail->send();

        $stmt = $common->mysqli->prepare("UPDATE mail_queue SET try_sent = 1 WHERE id = ? LIMIT 1;");
        $stmt->bind_param("i", $email['id']);
        if (!$stmt->execute()) return false;
        $stmt->close();
    } catch (Exception $e) {
        file_put_contents('/home/u198566027/domains/app.linkboxy.com/public_html/staging/mailError.log', $e->getMessage() . "\n", FILE_APPEND);
        $stmt = $common->mysqli->prepare("UPDATE mail_queue SET try_sent = 2 WHERE id = ? LIMIT 1;");
        $stmt->bind_param("i", $email['id']);
        if (!$stmt->execute()) return false;
        $stmt->close();
    }
}

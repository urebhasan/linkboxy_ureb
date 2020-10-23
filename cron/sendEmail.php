<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '/home/u361-zrkv5gswcb76/www/app.linkboxy.com/public_html/vendor/autoload.php';

spl_autoload_register(function ($class) {
    include '/home/u361-zrkv5gswcb76/www/app.linkboxy.com/public_html/classes/' . $class . '.class.php';
});

$common = new common();
$user = new user;


if(!isset($_GET['insert']) or !ctype_digit($_GET['insert']) or !$email = $common->getEmailById($_GET['insert'])) exit();

if($email['send'] == 1) exit();

if(strpos($email['subject'], 'Backlinks notification') !== false) exit();

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 0;                                       // Enable verbose debug output
    $mail->isSMTP();                                            // Set mailer to use SMTP
	$mail->CharSet="UTF-8";
	$mail->SMTPSecure = 'ssl';
	//$mail->Host = 'uk1006.siteground.eu';
	$mail->Host = 'linkboxy.com';
	$mail->Port = 465;
	$mail->Username = 'alerts@linkboxy.com';
	$mail->Password = 'anatoly123upwork';
	$mail->SMTPAuth = true;

    //Recipients
    $mail->setFrom('alerts@linkboxy.com', 'LinkBoxy.com');
    $mail->addAddress($email['email'], '');     // Add a recipient
    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $email['subject'];
    //$template = file_get_contents('./mailTemplate.tmp');
    $mail->Body    = $email['text'];
    $mail->AltBody = substr($email['text'],0,100);

    $mail->send();

	$stmt = $common->mysqli->prepare("UPDATE emails_to_send SET send = 1 WHERE id = ? LIMIT 1;");
	$stmt->bind_param("i", $email['id']);
	if(!$stmt->execute()) return false;
	$stmt->close();

} catch (Exception $e) {
	$stmt = $common->mysqli->prepare("UPDATE emails_to_send SET send = 0 WHERE id = ? LIMIT 1;");
	$stmt->bind_param("i", $email['id']);
	if(!$stmt->execute()) return false;
	$stmt->close();
}
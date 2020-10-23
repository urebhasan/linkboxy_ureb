<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require(__DIR__ . '/../config.php');
require($config['vendor_autoload']);

$email = $argv[1];
$subject = $argv[2];
$body = $argv[3];

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

//Server settings
$mail->SMTPDebug = 1; // Enable verbose debug output
$mail->isSMTP(); // Set mailer to use SMTP
$mail->CharSet = "UTF-8";
$mail->SMTPSecure = 'tls';
$mail->Host = 'smtp.hostinger.co.uk';
$mail->Port = 587; //465;
$mail->Username = 'alerts@linkboxy.com';
$mail->Password = '#cEkocF3'; //'anatoly123upwork';
$mail->SMTPAuth = true;

//Recipients
$mail->setFrom('alerts@linkboxy.com', 'LinkBoxy.com');
$mail->addAddress($email); // Add a recipient

// Content
$mail->isHTML(true); // Set email format to HTML
$mail->Subject = $subject;


$mail->Body = $body;
$mail->AltBody = substr($body, 0, 100);

$mail->send();

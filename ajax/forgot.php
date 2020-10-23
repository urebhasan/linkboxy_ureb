<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require('../init.php');
// Load Composer's autoloader
require($config['vendor_autoload']);

if ($user->user_authorized() === true) $common->throwJsonError('Login failed. Already authorized.');

$email = ((isset($_POST['email']) and !empty($_POST['email']) and $user_info = $user->user_info_by_email($_POST['email'])) ? $_POST['email'] : null);

if ($email == null) $common->throwJsonError('Please check your email.');

if ($newPassword = $user->userChangePassword($user_info['id'])) {

    // After transfering to new host, shell command can't be used because of user's permitions 
    //exec('php /var/sites/a/app.linkboxy.com/public_html/cron/sendEmailDirect.php "'.$user_info['email'].'" "Retrieve Password from LinkBoxy.com" "Hi. We have changed your account password. Now your password is: '.$newPassword.'"> /dev/null &');
    $body = "We have reset your password to: $newPassword <br/>";
    $body .= "Please feel free to change it in your dashboard. <br/> Thank you.";
    $email = $user_info['email'];
    $theme = "Retrieve Password from LinkBoxy.com";
    sendEmail($email, $theme, $body);

    $common->throwJsonSuccess('New account password we sent to email.');
} else {
    $common->throwJsonError($newPassword . 'Retrieve password error.');
}

function sendEmail($email, $theme, $body)
{
    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    //Server settings
    $mail->SMTPDebug = 0;                                       // Enable verbose debug output
    $mail->isSMTP();                                            // Set mailer to use SMTP
    $mail->CharSet = "UTF-8";
    $mail->SMTPSecure = 'tls';
    $mail->Host = 'smtp.hostinger.co.uk';
    $mail->Port = 587;
    $mail->Username = 'alerts@linkboxy.com';
    $mail->Password = '#cEkocF3';
    $mail->SMTPAuth = true;

    //Recipients
    $mail->setFrom('alerts@linkboxy.com', 'LinkBoxy.com');
    $mail->addAddress($email);     // Add a recipient
    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $theme;
    //$template = file_get_contents('./mailTemplate.tmp');
    $mail->Body = $body;
    //$mail->AltBody = substr($argv['3'], 0, 100);

    $mail->send();
}

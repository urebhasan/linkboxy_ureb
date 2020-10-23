<?php
return;
// Load Composer's autoloader
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../init.php');
    
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

spl_autoload_register(function ($class) {
    include '/home/u361-zrkv5gswcb76/www/app.linkboxy.com/public_html/classes/' . $class . '.class.php';
});
// Instantiation and passing `true` enables exceptions
file_put_contents('/home/u361-zrkv5gswcb76/www/app.linkboxy.com/public_html/mailError.log', time() . "\n", FILE_APPEND);

/**
 * @param  array  $values
 * @param  mysqli $db
 * @return mysqli_stmt
 */
function bindInValues(array $values, mysqli $db, int $trysent)
{
    $sql = sprintf(
        'UPDATE mail_queue SET try_sent = ' . $trysent . ' WHERE id IN (%s)',
        implode(', ', array_fill(0, count($values), '?'))
    );
    $stmt = $db->prepare($sql);
    $stmt->bind_param(implode('', array_fill(0, count($values), 'i')), ...$values);
    return $stmt;
}

$setsess = $common->mysqli->prepare(
    "SET SESSION group_concat_max_len = 1000000000;"
);
if (!$setsess->execute()) {
    return false;
}

$stmtnt = $common->mysqli->prepare(
    "SELECT group_concat(id) as IDs,recipient_mail as email, group_concat(distinct sender) as senders, group_concat(recipient) as recepients, group_concat(headers) as headers, group_concat(concat(body, '<br/>Last checked: ',create_time)) as body, group_concat(status) as statuses, group_concat(try_sent) as sents FROM mail_queue WHERE try_sent = 0 GROUP by recipient_mail;"
);
if (!$stmtnt->execute()) {
    return false;
}
$result = $stmtnt->get_result();
$stmtnt->close();
$rslt = $result->fetch_all(MYSQLI_ASSOC);

foreach ($rslt as $email) {
    $IDs = explode(',', $email['IDs']);
    try {
        $mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = 1;                                       // Enable verbose debug output
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->CharSet="UTF-8";
        $mail->SMTPSecure = 'ssl';
        //$mail->Host = 'uk1006.siteground.eu';
        /* $mail->Host = 'linkboxy.com';
        $mail->Port = 465;
        $mail->Username = 'alerts@linkboxy.com';
        $mail->Password = 'anatoly123upwork';
        $mail->SMTPAuth = true; */
        $mail->Host = 'smtp.googlemail.com';
        $mail->Port = 465;
        $mail->Username = 'freelancesmtp@gmail.com';
        $mail->Password = '123#kec!';
        $mail->SMTPAuth = true;
        
        //Recipients
        $mail->setFrom('freelancesmtp@gmail.com', 'LinkBoxy.com');
        $mail->addAddress($email['email'], ''); //$email['recipient_mail']    // Add a recipient
        $mail->addBcc('videoasistent@gmail.com');//for testing mail template - remove this on live!!!
        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'LinkBoxy.com Notification';
        //$template = file_get_contents('./mailTemplate.tmp');
        $mail->Body    = "<br/>--<br/>" . $email['body'];
        $mail->AltBody = substr($email['body'], 0, 100);
        $mail->send();
        
        $stmt = bindInValues($IDs, $common->mysqli, 1);
        if (!$stmt->execute()) {
            return false;
        }
        $stmt->close();
    } catch (Exception $e) {
        file_put_contents('/home/u361-zrkv5gswcb76/www/app.linkboxy.com/public_html/mailError.log', $e->getMessage() . "\n", FILE_APPEND);
        $stmt = bindInValues($IDs, $common->mysqli, 2);
        if (!$stmt->execute()) {
            return false;
        }
        $stmt->close();
    }
}

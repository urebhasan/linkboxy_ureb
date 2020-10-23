<?php
// Load Composer's autoloader
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../init.php');
require('template/mail.php');

spl_autoload_register(function ($class) {
    include '/home/u198566027/domains/app.linkboxy.com/public_html/classes/' . $class . '.class.php';
});
// Instantiation and passing `true` enables exceptions
file_put_contents('/home/u198566027/domains/app.linkboxy.com/public_html/mailError.log', time() . "\n", FILE_APPEND);

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
    "SELECT group_concat(id) as IDs,recipient_mail as email, group_concat(distinct sender) as senders, group_concat(recipient) as recepients, group_concat(headers) as headers, group_concat(concat(body, '<div style=\"font-family:inherit;text-align:left\"><span style=\"color:#ffffff;padding:0 !important;font-size:14px;width:100%;\">Last checked: ',create_time, '</span></div>') SEPARATOR '') as body, group_concat(status) as statuses, group_concat(try_sent) as sents FROM mail_queue WHERE try_sent = 0 GROUP by recipient_mail;"
);
if (!$stmtnt->execute()) {
    return false;
}
$result = $stmtnt->get_result();
$stmtnt->close();
$rslt = $result->fetch_all(MYSQLI_ASSOC);

foreach ($rslt as $item) {
  $errorInfo = '';
    $IDs = explode(',', $item['IDs']);
    try {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("alert@linkboxy.com", "LinkBoxy");
        //$email->setFrom("freelancesmtp@gmail.com", "LinkBoxy");
        $email->setSubject("LinkBoxy.com Notification");
        $email->addTo($item['email'], $item['email']);
        //$email->addTo("video.asistent@gmail.com", "Example User");

        //$email->addContent("text/plain", "and easy to do anywhere, even with PHP");
        $email->addContent(
            "text/html",
            $header .
            $item['body'] .
            $footer
        );

        $sendgrid = new \SendGrid('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');
        try {
            $response = $sendgrid->send($email);
            $errorInfo .= $response->statusCode() . "\n";
            $errorInfo .= $response->headers();
            $errorInfo .= $response->body() . "\n";
            file_put_contents('/home/u198566027/domains/app.linkboxy.com/public_html/mailError.log', 'line: 71 -- ' . $errorInfo . "\n", FILE_APPEND);
        } catch (Exception $e) {
            file_put_contents('/home/u198566027/domains/app.linkboxy.com/public_html/mailError.log', 'Caught exception: '. $e->getMessage() . "\n", FILE_APPEND);
            
            $stmt = bindInValues($IDs, $common->mysqli, 2);
            if (!$stmt->execute()) {
                return false;
            }
            $stmt->close();
        }
      
        $stmt = bindInValues($IDs, $common->mysqli, 1);
        if (!$stmt->execute()) {
            return false;
        }
        $stmt->close();
    } catch (Exception $e) {
        file_put_contents('/home/u198566027/domains/app.linkboxy.com/public_html/mailError.log', $e->getMessage() . "\n", FILE_APPEND);
        $stmt = bindInValues($IDs, $common->mysqli, 2);
        if (!$stmt->execute()) {
            return false;
        }
        $stmt->close();
    }
}
